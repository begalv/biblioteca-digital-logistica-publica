--
-- Taxonomia hierárquica da Biblioteca Digital de Logística Pública
-- (Assunto · Categoria · Subcategoria · Microcategoria)
--
-- Estrutura ditada pelo template oficial BDLP_Template_Insercao.xlsx:
--   - Assunto: eixo temático paralelo (16 valores)
--   - Categoria: macroetapas Lei 14.133/2021 (6 valores) — usa nr_category existente
--   - Subcategoria: agrupamento sob Categoria (~47 valores)
--   - Microcategoria: refino sob Subcategoria (~80 valores)
--
-- Adiciona também colunas ano (INT dedicada) e permissao (texto Aberto/Restrito)
-- em nr_document, e backfill de ano a partir de regex sobre source.
--

-- 1. Tabelas de taxonomia ----------------------------------------------------

CREATE TABLE IF NOT EXISTS nr_assunto (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    ordem INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS nr_subcategoria (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    category_id INT NOT NULL REFERENCES nr_category(id) ON DELETE CASCADE,
    ordem INT DEFAULT 0,
    UNIQUE (slug, category_id)
);

CREATE TABLE IF NOT EXISTS nr_microcategoria (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    subcategoria_id INT NOT NULL REFERENCES nr_subcategoria(id) ON DELETE CASCADE,
    ordem INT DEFAULT 0,
    UNIQUE (slug, subcategoria_id)
);

-- 2. Novas colunas em nr_document --------------------------------------------

ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS assunto_id INT REFERENCES nr_assunto(id) ON DELETE SET NULL;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS subcategoria_id INT REFERENCES nr_subcategoria(id) ON DELETE SET NULL;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS microcategoria_id INT REFERENCES nr_microcategoria(id) ON DELETE SET NULL;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS ano INT;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS permissao VARCHAR(20);

-- 3. Índices para os filtros do Acervo ---------------------------------------

CREATE INDEX IF NOT EXISTS idx_nr_document_assunto ON nr_document (assunto_id);
CREATE INDEX IF NOT EXISTS idx_nr_document_subcategoria ON nr_document (subcategoria_id);
CREATE INDEX IF NOT EXISTS idx_nr_document_microcategoria ON nr_document (microcategoria_id);
CREATE INDEX IF NOT EXISTS idx_nr_document_ano ON nr_document (ano);
CREATE INDEX IF NOT EXISTS idx_nr_document_permissao ON nr_document (permissao);

-- 4. Backfill de ano para documentos existentes (regex 19xx-20xx em source) --

UPDATE nr_document
SET ano = CAST(SUBSTRING(source FROM '\m(19|20)\d{2}\M') AS INT)
WHERE ano IS NULL
  AND source IS NOT NULL
  AND source ~ '\m(19|20)\d{2}\M';

-- 5. FTS index expandido — inclui campos LILP --------------------------------
-- Substitui o índice criado em 05-custom-metadata.sql (mais estreito).

DROP INDEX IF EXISTS idx_nr_document_fts;
CREATE INDEX IF NOT EXISTS idx_nr_document_fts ON nr_document
    USING gin(to_tsvector('portuguese',
        coalesce(title, '') || ' ' ||
        coalesce(author, '') || ' ' ||
        coalesce(autor_principal, '') || ' ' ||
        coalesce(keywords, '') || ' ' ||
        coalesce(abstract, '') || ' ' ||
        coalesce(uso_futuro, '') || ' ' ||
        coalesce(metodo, '') || ' ' ||
        coalesce(resultado, '') || ' ' ||
        coalesce(tipologia, '') || ' ' ||
        coalesce(complexidade, '')
    ));
