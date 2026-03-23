"""
Management command para migrar a planilha de 496 estudos para o Nou-Rau.

Uso:
    python manage.py migrate_spreadsheet /caminho/para/planilha.xlsx
    python manage.py migrate_spreadsheet /caminho/para/planilha.xlsx --dry-run
"""

import re

from django.core.management.base import BaseCommand, CommandError
from django.db import connection

import openpyxl


# Mapeamento de colunas da planilha para campos do banco
COLUMN_MAP = {
    "Título": "title",
    "Autor (es)": "author",
    "Ano": "year",
    "Link": "acesso_eletronico",
    "Resumo": "abstract",
    "Palavras-chave": "keywords",
    "Tipologia": "tipologia",
    "Finalidade": "finalidade",
    "Etapa do Processo Licitatório": "etapa_processo_licitatorio",
    "Formato": "formato_raw",
    "Complexidade": "complexidade",
    "Periodicidade": "periodicidade",
    "Uso Futuro": "uso_futuro",
    "Autoridade Intelectual": "autor_principal",
    "DOI": "doi",
    "Série (ISSN e ISBN)": "nlspi",
    "Imprenta": "source",
    "Referências": "referencias",
    "Método": "metodo",
    "Resultados": "resultado",
    "Categoria": "categoria",
    "Subcategoria": "subcategoria",
    "Publicação": "publicacao",
}


def normalize_text(val, max_len=None):
    """Limpa e normaliza texto da planilha."""
    if val is None:
        return ""
    text = str(val).strip()
    if max_len and len(text) > max_len:
        text = text[:max_len]
    return text


def extract_year(val):
    """Extrai ano de um valor."""
    val = normalize_text(val)
    match = re.search(r"\b(19|20)\d{2}\b", val)
    return match.group(0) if match else val


def generate_code(sequence_num):
    """Gera código único para o documento."""
    return f"bdlp-{sequence_num:06d}"


class Command(BaseCommand):
    help = "Migra planilha de estudos para o banco Nou-Rau"

    def add_arguments(self, parser):
        parser.add_argument("spreadsheet", type=str, help="Caminho para a planilha .xlsx")
        parser.add_argument("--dry-run", action="store_true", help="Apenas simula, sem inserir dados")
        parser.add_argument("--sheet", type=str, default=None, help="Nome da aba (padrão: primeira)")

    def handle(self, *args, **options):
        spreadsheet_path = options["spreadsheet"]
        dry_run = options["dry_run"]
        sheet_name = options.get("sheet")

        try:
            wb = openpyxl.load_workbook(spreadsheet_path, read_only=True)
        except Exception as e:
            raise CommandError(f"Erro ao abrir planilha: {e}")

        ws = wb[sheet_name] if sheet_name else wb.active
        rows = list(ws.iter_rows(values_only=True))

        if len(rows) < 2:
            raise CommandError("Planilha vazia ou sem dados.")

        # Mapear cabeçalhos
        header = [normalize_text(h) for h in rows[0]]
        col_indices = {}
        for col_name, field_name in COLUMN_MAP.items():
            for i, h in enumerate(header):
                if h and col_name.lower() in h.lower():
                    col_indices[field_name] = i
                    break

        self.stdout.write(f"Colunas mapeadas: {len(col_indices)}/{len(COLUMN_MAP)}")
        if missing := set(COLUMN_MAP.values()) - set(col_indices.keys()):
            self.stdout.write(self.style.WARNING(f"Colunas não encontradas: {missing}"))

        # Carregar mapeamento de coleções e categorias do banco
        topic_map = self._load_topic_map()
        category_map = self._load_category_map()

        data_rows = rows[1:]
        inserted = 0
        skipped = 0
        errors = []

        for row_num, row in enumerate(data_rows, start=2):
            try:
                record = self._parse_row(row, col_indices)
                if not record.get("title"):
                    skipped += 1
                    continue

                # Mapear coleção via subcategoria
                topic_id = self._resolve_topic(record, topic_map)
                category_id = self._resolve_category(record.get("categoria", ""), category_map)

                if dry_run:
                    self.stdout.write(f"  [DRY] Linha {row_num}: {record['title'][:80]}")
                    inserted += 1
                    continue

                code = generate_code(row_num)
                self._insert_document(record, code, topic_id, category_id)
                inserted += 1

            except Exception as e:
                errors.append((row_num, str(e)))

        wb.close()

        self.stdout.write(self.style.SUCCESS(f"\nResultado:"))
        self.stdout.write(f"  Inseridos: {inserted}")
        self.stdout.write(f"  Ignorados: {skipped}")
        self.stdout.write(f"  Erros: {len(errors)}")

        for row_num, err in errors[:20]:
            self.stdout.write(self.style.ERROR(f"  Linha {row_num}: {err}"))

        if dry_run:
            self.stdout.write(self.style.WARNING("\n[DRY RUN] Nenhum dado foi inserido."))

    def _parse_row(self, row, col_indices):
        """Extrai e normaliza campos de uma linha da planilha."""
        record = {}
        for field, idx in col_indices.items():
            record[field] = normalize_text(row[idx]) if idx < len(row) else ""
        return record

    def _get_writer_connection(self):
        """Retorna conexão com permissão de escrita."""
        from django.db import connections
        return connections["nourau_writer"]

    def _load_topic_map(self):
        """Carrega mapeamento nome→id de tópicos do banco."""
        with connection.cursor() as cursor:
            cursor.execute("SELECT id, name, parent_id FROM topic")
            rows = cursor.fetchall()
        return {row[1].lower(): {"id": row[0], "parent_id": row[2]} for row in rows}

    def _load_category_map(self):
        """Carrega mapeamento nome→id de categorias do banco."""
        with connection.cursor() as cursor:
            cursor.execute("SELECT id, name FROM nr_category")
            rows = cursor.fetchall()
        return {row[1].lower(): row[0] for row in rows}

    def _resolve_topic(self, record, topic_map):
        """
        Resolve o topic_id com base no tipo de documento (formato_raw).
        A planilha não tem coluna de coleção — inferimos pelo formato:
          - Dissertações/Teses/TCCs → Trabalhos Acadêmicos
          - Apostilas/Manuais/Relatórios/Slides → Materiais Pedagógicos
          - Livros/E-books → Livros Digitais
          - Eventos/Congressos/Seminários → Eventos
          - Outros → Materiais Pedagógicos (padrão)
        """
        fmt = record.get("formato_raw", "").lower()

        mapa_formato = {
            "dissertação": "trabalhos acadêmicos",
            "dissertacao": "trabalhos acadêmicos",
            "tese": "trabalhos acadêmicos",
            "tcc": "trabalhos acadêmicos",
            "trabalho de conclusão": "trabalhos acadêmicos",
            "monografia": "trabalhos acadêmicos",
            "livro": "livros digitais",
            "e-book": "livros digitais",
            "ebook": "livros digitais",
            "manual": "materiais pedagógicos",
            "apostila": "materiais pedagógicos",
            "relatório": "materiais pedagógicos",
            "relatorio": "materiais pedagógicos",
            "slides": "materiais pedagógicos",
            "apresentação": "materiais pedagógicos",
            "tutorial": "materiais pedagógicos",
            "vídeo": "materiais pedagógicos",
            "video": "materiais pedagógicos",
            "evento": "eventos",
            "congresso": "eventos",
            "seminário": "eventos",
            "seminario": "eventos",
            "workshop": "eventos",
            "conferência": "eventos",
            "conferencia": "eventos",
            "simpósio": "eventos",
            "simposio": "eventos",
        }

        colecao_alvo = "materiais pedagógicos"  # padrão
        for chave, colecao in mapa_formato.items():
            if chave in fmt:
                colecao_alvo = colecao
                break

        # Buscar o topic_id correspondente
        for nome, info in topic_map.items():
            if info["parent_id"] == 0 and colecao_alvo in nome:
                return info["id"]

        # Fallback absoluto: qualquer coleção raiz
        for info in topic_map.values():
            if info["parent_id"] == 0:
                return info["id"]
        return None

    def _resolve_category(self, categoria_name, category_map):
        """Resolve o category_id baseado no nome da categoria."""
        if not categoria_name:
            return None
        key = categoria_name.lower().strip()

        # Correspondência exata
        if key in category_map:
            return category_map[key]

        # Correspondência normalizada (sem vírgulas/acentos ligeiros)
        key_norm = key.replace(",", "").replace("  ", " ")
        for cat_key, cat_id in category_map.items():
            if cat_key.replace(",", "").replace("  ", " ") == key_norm:
                return cat_id

        # Busca parcial
        for cat_key, cat_id in category_map.items():
            if key[:20] in cat_key or cat_key[:20] in key:
                return cat_id
        return None

    def _insert_document(self, record, code, topic_id, category_id):
        """Insere um documento no banco via SQL direto (usando usuário com permissão de escrita)."""
        # Construir description a partir de campos auxiliares
        description_parts = []
        if record.get("finalidade"):
            description_parts.append(f"Finalidade: {record['finalidade']}")
        if record.get("periodicidade"):
            description_parts.append(f"Periodicidade: {record['periodicidade']}")
        description = "\n".join(description_parts)

        # Primeiro autor como autor_principal
        autor_principal = record.get("autor_principal", "")
        if not autor_principal and record.get("author"):
            # Pegar primeiro autor (separado por ;)
            autores = record["author"].split(";")
            autor_principal = autores[0].strip()

        writer = self._get_writer_connection()
        with writer.cursor() as cursor:
            cursor.execute(
                """
                INSERT INTO nr_document (
                    title, author, autor_principal, abstract, keywords, code,
                    topic_id, category_id, status, remote,
                    acesso_eletronico, doi, nlspi, source,
                    tipologia, etapa_processo_licitatorio, complexidade,
                    uso_futuro, metodo, resultado, referencias, publicacao,
                    description, owner_id
                ) VALUES (
                    %s, %s, %s, %s, %s, %s,
                    %s, %s, 'a', 'y',
                    %s, %s, %s, %s,
                    %s, %s, %s,
                    %s, %s, %s, %s, %s,
                    %s, 1
                )
                """,
                [
                    record.get("title", ""),
                    record.get("author", ""),
                    autor_principal,
                    record.get("abstract", ""),
                    record.get("keywords", ""),
                    code,
                    topic_id,
                    category_id,
                    record.get("acesso_eletronico", ""),
                    record.get("doi", ""),
                    record.get("nlspi", ""),
                    record.get("source", ""),
                    normalize_text(record.get("tipologia", ""), 255),
                    normalize_text(record.get("etapa_processo_licitatorio", ""), 255),
                    normalize_text(record.get("complexidade", ""), 50),
                    record.get("uso_futuro", ""),
                    record.get("metodo", ""),
                    record.get("resultado", ""),
                    record.get("referencias", ""),
                    record.get("publicacao", ""),
                    description,
                ],
            )
