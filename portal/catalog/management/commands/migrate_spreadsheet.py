"""
Management command para migrar a planilha v3.1 (504 estudos, 4 abas BDU) para o Nou-Rau.

Uso:
    python manage.py migrate_spreadsheet /caminho/para/planilha.xlsx
    python manage.py migrate_spreadsheet /caminho/para/planilha.xlsx --dry-run
    python manage.py migrate_spreadsheet /caminho/para/planilha.xlsx --sheet "Eventos"
"""

import os
import re
import unicodedata

import openpyxl
import psycopg2
from django.core.management.base import BaseCommand, CommandError


# Abas de dados na planilha v3.1 (ordem de processamento)
DATA_SHEETS = ["Eventos", "Livros Digitais", "Trabalhos Acadêmicos", "Materiais Pedagógicos"]

# Mapeamento de colunas da planilha v3.1 para campos internos.
# As chaves são os nomes EXATOS dos cabeçalhos da planilha.
COLUMN_MAP = {
    "Assunto": "assunto",
    "Categoria": "categoria",
    "Subcategoria": "subcategoria",
    "Microcategoria": "microcategoria",
    "Coleção": "colecao",
    "Tipo de informação": "tipo_informacao",
    "Autor Principal": "author",
    "Título Principal": "title",
    "Título variante/outro idioma": "title_en",
    "Autoridade Intelectual": "autor_principal",
    "Assunto em português": "keywords",
    "Assunto em outro idioma": "keywords_en",
    "Resumo": "abstract",
    "Abstract": "abstract_en",
    "Nota": "nota",
    "Edição": "edicao",
    "Apresentações do Evento": "event_description",
    "Imprenta": "source",
    "Descrição Física": "descricao_fisica",
    "ISSN/ISBN": "nlspi",
    "Identificador do Objeto Digital (DOI)": "doi",
    "Acesso Eletrônico": "acesso_eletronico",
    "Inserir uma Capa": "capa",
    "Permissão de acesso ao material": "tacesso_raw",
    "Tipologia": "tipologia",
    "Complexidade": "complexidade",
    "Aplicabilidade": "uso_futuro",
    "Método": "metodo",
    "Resultados": "resultado",
    "Referências": "referencias",
    "Ano": "year",
}


def strip_accents(s):
    """Remove acentos de uma string para comparação fuzzy."""
    return unicodedata.normalize("NFKD", s).encode("ascii", "ignore").decode("ascii")


def normalize_text(val):
    """Limpa e normaliza texto da planilha."""
    if val is None:
        return ""
    return str(val).strip()


def extract_year(val):
    """Extrai ano de um valor."""
    val = normalize_text(val)
    match = re.search(r"\b(19|20)\d{2}\b", val)
    return match.group(0) if match else val


def generate_code(sequence_num):
    """Gera código único para o documento."""
    return f"bdlp-{sequence_num:06d}"


class Command(BaseCommand):
    help = "Migra planilha v3.1 de estudos (4 abas BDU) para o banco Nou-Rau"

    def add_arguments(self, parser):
        parser.add_argument("spreadsheet", type=str, help="Caminho para a planilha .xlsx")
        parser.add_argument("--dry-run", action="store_true", help="Apenas simula, sem inserir dados")
        parser.add_argument("--sheet", type=str, default=None, help="Nome de uma aba específica")

    def _get_write_connection(self):
        """Cria conexão de escrita usando o usuário php (não o portal_reader)."""
        return psycopg2.connect(
            dbname=os.environ.get("POSTGRES_DB", "nourau"),
            user=os.environ.get("POSTGRES_USER", "php"),
            password=os.environ.get("POSTGRES_PASSWORD", "abc123"),
            host=os.environ.get("POSTGRES_HOST", "postgres"),
            port=os.environ.get("POSTGRES_PORT", "5432"),
        )

    def handle(self, *args, **options):
        spreadsheet_path = options["spreadsheet"]
        dry_run = options["dry_run"]
        sheet_name = options.get("sheet")

        try:
            wb = openpyxl.load_workbook(spreadsheet_path, read_only=True)
        except Exception as e:
            raise CommandError(f"Erro ao abrir planilha: {e}")

        # Determinar quais abas processar
        if sheet_name:
            if sheet_name not in wb.sheetnames:
                raise CommandError(f"Aba '{sheet_name}' não encontrada. Disponíveis: {wb.sheetnames}")
            sheets_to_process = [sheet_name]
        else:
            sheets_to_process = [s for s in DATA_SHEETS if s in wb.sheetnames]
            if not sheets_to_process:
                raise CommandError(f"Nenhuma aba de dados encontrada. Disponíveis: {wb.sheetnames}")

        self.stdout.write(f"Abas a processar: {sheets_to_process}")

        # Carregar mapeamentos do banco
        conn = self._get_write_connection()
        try:
            topic_map = self._load_topic_map(conn)
            category_map = self._load_category_map(conn)
            type_info_map = self._load_type_information_map(conn)

            self.stdout.write(f"Coleções raiz: {[k for k, v in topic_map.items() if v['parent_id'] == 0]}")
            self.stdout.write(f"Categorias: {len(category_map)}")
            self.stdout.write(f"Tipos de informação: {len(type_info_map)}")

            global_seq = 1  # Contador global de código
            total_inserted = 0
            total_skipped = 0
            total_errors = []

            for sheet in sheets_to_process:
                ws = wb[sheet]
                rows = []
                for row in ws.iter_rows(values_only=True):
                    rows.append(row)

                if len(rows) < 2:
                    self.stdout.write(self.style.WARNING(f"\n[{sheet}] Aba vazia, pulando."))
                    continue

                # Mapear cabeçalhos
                header = [normalize_text(h) for h in rows[0]]
                col_indices = self._map_headers(header)

                mapped = len(col_indices)
                total_cols = len(COLUMN_MAP)
                self.stdout.write(f"\n[{sheet}] Colunas mapeadas: {mapped}/{total_cols}")
                if missing := set(COLUMN_MAP.values()) - set(col_indices.keys()):
                    # Filtrar opcionais que não existem em todas as abas
                    optional = {"edicao", "event_description", "nlspi"}
                    real_missing = missing - optional
                    if real_missing:
                        self.stdout.write(self.style.WARNING(f"  Colunas não encontradas: {real_missing}"))

                # Filtrar linhas com dados
                data_rows = []
                for row in rows[1:]:
                    if any(v is not None for v in row):
                        data_rows.append(row)

                self.stdout.write(f"  Registros com dados: {len(data_rows)}")

                sheet_inserted = 0
                sheet_skipped = 0
                sheet_errors = []

                for row_num, row in enumerate(data_rows, start=2):
                    try:
                        record = self._parse_row(row, col_indices)
                        if not record.get("title"):
                            sheet_skipped += 1
                            continue

                        topic_id = self._resolve_topic(record, topic_map)
                        category_id = self._resolve_category(record.get("categoria", ""), category_map)
                        type_info_id = self._resolve_type_information(
                            record.get("tipo_informacao", ""), type_info_map
                        )

                        if dry_run:
                            title_preview = record["title"][:80]
                            self.stdout.write(f"  [DRY] #{global_seq} {sheet}/{row_num}: {title_preview}")
                            sheet_inserted += 1
                            global_seq += 1
                            continue

                        code = generate_code(global_seq)
                        self._insert_document(conn, record, code, topic_id, category_id, type_info_id)
                        sheet_inserted += 1
                        global_seq += 1

                    except Exception as e:
                        sheet_errors.append((sheet, row_num, str(e)))
                        if len(sheet_errors) <= 5:
                            self.stdout.write(self.style.ERROR(f"  ERRO {sheet}/L{row_num}: {e}"))

                self.stdout.write(f"  [{sheet}] Inseridos: {sheet_inserted}, Ignorados: {sheet_skipped}, Erros: {len(sheet_errors)}")
                total_inserted += sheet_inserted
                total_skipped += sheet_skipped
                total_errors.extend(sheet_errors)

            if not dry_run:
                conn.commit()

        finally:
            conn.close()

        wb.close()

        self.stdout.write(self.style.SUCCESS(f"\n=== Resultado Final ==="))
        self.stdout.write(f"  Total inseridos: {total_inserted}")
        self.stdout.write(f"  Total ignorados: {total_skipped}")
        self.stdout.write(f"  Total erros: {len(total_errors)}")

        if total_errors:
            self.stdout.write(self.style.ERROR("\nPrimeiros 20 erros:"))
            for sheet, row_num, err in total_errors[:20]:
                self.stdout.write(self.style.ERROR(f"  {sheet}/L{row_num}: {err}"))

        if dry_run:
            self.stdout.write(self.style.WARNING("\n[DRY RUN] Nenhum dado foi inserido."))

    def _map_headers(self, header):
        """Mapeia cabeçalhos da planilha para campos internos com tolerância a acentos."""
        col_indices = {}
        header_normalized = [strip_accents(h.lower()) if h else "" for h in header]

        for col_name, field_name in COLUMN_MAP.items():
            col_norm = strip_accents(col_name.lower())
            for i, h_norm in enumerate(header_normalized):
                if h_norm and col_norm in h_norm:
                    col_indices[field_name] = i
                    break
        return col_indices

    def _parse_row(self, row, col_indices):
        """Extrai e normaliza campos de uma linha da planilha."""
        record = {}
        for field, idx in col_indices.items():
            record[field] = normalize_text(row[idx]) if idx < len(row) else ""
        return record

    def _load_topic_map(self, conn):
        """Carrega mapeamento nome→id de tópicos do banco."""
        with conn.cursor() as cursor:
            cursor.execute("SELECT id, name, parent_id FROM topic")
            rows = cursor.fetchall()
        return {row[1].strip().lower(): {"id": row[0], "parent_id": row[2], "name": row[1]} for row in rows}

    def _load_category_map(self, conn):
        """Carrega mapeamento nome→id de categorias do banco."""
        with conn.cursor() as cursor:
            cursor.execute("SELECT id, name FROM nr_category")
            rows = cursor.fetchall()
        return {row[1].strip().lower(): row[0] for row in rows}

    def _load_type_information_map(self, conn):
        """Carrega mapeamento nome→id de tipos de informação."""
        with conn.cursor() as cursor:
            cursor.execute("SELECT id, name FROM type_information")
            rows = cursor.fetchall()
        result = {}
        for row in rows:
            if row[1]:
                result[strip_accents(row[1].strip().lower())] = row[0]
        return result

    def _resolve_topic(self, record, topic_map):
        """Resolve o topic_id pela coluna Coleção (match direto com coleção raiz)."""
        colecao_nome = record.get("colecao", "").strip().lower()
        if colecao_nome and colecao_nome in topic_map:
            info = topic_map[colecao_nome]
            if info["parent_id"] == 0:
                return info["id"]

        # Fallback: primeira coleção raiz
        for info in topic_map.values():
            if info["parent_id"] == 0:
                return info["id"]
        return None

    def _resolve_category(self, categoria_name, category_map):
        """Resolve o category_id baseado no nome da categoria."""
        if not categoria_name:
            return 7  # Generic
        key = categoria_name.strip().lower()
        if key in category_map:
            return category_map[key]
        # Busca parcial
        for cat_key, cat_id in category_map.items():
            if key in cat_key or cat_key in key:
                return cat_id
        return 7  # Generic fallback

    def _resolve_type_information(self, tipo_info_name, type_info_map):
        """Resolve type_information id com fallback para NULL."""
        if not tipo_info_name:
            return None
        key = strip_accents(tipo_info_name.strip().lower())
        if key in type_info_map:
            return type_info_map[key]
        # Busca parcial para variações como "Artigo de periódico" -> "Artigos"
        # Mapeamento manual de variações conhecidas
        aliases = {
            "artigo de periodico": "artigos",
            "dissertacao": "dissertacoes",
            "tese": "teses",
            "tcc": "tccs",
            "monografia de especializacao": "dissertacoes",
            "livro": "livros",
            "capitulo de livro": "capitulo de livro",
            "artigo de evento": "artigos de eventos",
            "apresentacao de evento": "apresentacoes",
            "relatorio": "relatorios",
            "material pedagogico": "apostilas",
            "pagina web": None,  # não existe no Nou-Rau
            "documento normativo": None,
            "nota tecnica": "nota tecnica",
            "publicacao digital": None,
            "policy brief": "policy brief",
        }
        alias = aliases.get(key)
        if alias and alias in type_info_map:
            return type_info_map[alias]
        return None  # Sem match — será gravado como nome cru no campo info

    def _insert_document(self, conn, record, code, topic_id, category_id, type_info_id):
        """Insere um documento no banco via SQL direto."""
        # Montar description/info com campos auxiliares
        info_parts = []
        if record.get("nota"):
            info_parts.append(record["nota"])
        if record.get("tipo_informacao") and type_info_id is None:
            info_parts.append(f"Tipo de informação: {record['tipo_informacao']}")
        info = "\n".join(info_parts)

        # Autor principal: usar Autoridade Intelectual, fallback para Autor Principal
        autor_principal = record.get("autor_principal", "")
        if not autor_principal:
            autor_principal = record.get("author", "")

        # Etapa do processo licitatório: usar Subcategoria
        etapa = record.get("subcategoria", "")[:255] if record.get("subcategoria") else ""

        # Ano: extrair
        year_raw = record.get("year", "")
        year = extract_year(year_raw)

        # Imprenta/source: se vazio, usar Ano
        source = record.get("source", "")
        if not source and year:
            source = year

        with conn.cursor() as cursor:
            cursor.execute(
                """
                INSERT INTO nr_document (
                    title, title_en, author, autor_principal,
                    abstract, abstract_en, keywords, keywords_en,
                    code, topic_id, category_id, status, remote,
                    acesso_eletronico, doi, nlspi, source,
                    tipologia, etapa_processo_licitatorio, complexidade,
                    uso_futuro, metodo, resultado, referencias,
                    description, info, owner_id,
                    typeinformation, typeinform_id,
                    descricao_fisica, edicao, event_description, capa
                ) VALUES (
                    %s, %s, %s, %s,
                    %s, %s, %s, %s,
                    %s, %s, %s, 'a', 'y',
                    %s, %s, %s, %s,
                    %s, %s, %s,
                    %s, %s, %s, %s,
                    %s, %s, 1,
                    %s, %s,
                    %s, %s, %s, %s
                )
                """,
                [
                    record.get("title", "")[:1500],
                    record.get("title_en", "")[:1500],
                    record.get("author", "")[:3000],
                    autor_principal[:800],
                    record.get("abstract", ""),
                    record.get("abstract_en", ""),
                    record.get("keywords", ""),
                    record.get("keywords_en", ""),
                    code,
                    topic_id,
                    category_id,
                    record.get("acesso_eletronico", "")[:800],
                    record.get("doi", "")[:180],
                    record.get("nlspi", "")[:200],
                    source[:900],
                    record.get("tipologia", "")[:255],
                    etapa,
                    record.get("complexidade", "")[:50],
                    record.get("uso_futuro", ""),
                    record.get("metodo", ""),
                    record.get("resultado", ""),
                    record.get("referencias", ""),
                    "",  # description
                    info,
                    type_info_id,  # typeinformation (INT FK) — NULL if no match
                    type_info_id,  # typeinform_id (INT FK) — same
                    record.get("descricao_fisica", "")[:500],
                    record.get("edicao", "")[:900],
                    record.get("event_description", "")[:5000],
                    record.get("capa", "")[:65],
                ],
            )
