--
-- Campos customizados para a Biblioteca Digital de Logística Pública
-- Extensões ao schema nr_document do Nou-Rau
--

-- Campos customizados na tabela principal
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS tipologia VARCHAR(255);
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS etapa_processo_licitatorio VARCHAR(255);
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS complexidade VARCHAR(50);
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS uso_futuro TEXT;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS metodo TEXT;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS resultado TEXT;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS referencias TEXT;
ALTER TABLE nr_document ADD COLUMN IF NOT EXISTS publicacao VARCHAR(500);

-- Tabela auxiliar: níveis de complexidade
CREATE TABLE IF NOT EXISTS nr_complexidade (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(50) UNIQUE NOT NULL
);
INSERT INTO nr_complexidade (nome) VALUES ('Baixa'), ('Média'), ('Alta')
ON CONFLICT (nome) DO NOTHING;

-- Tabela auxiliar: etapas do processo licitatório
CREATE TABLE IF NOT EXISTS nr_etapa_licitatorio (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) UNIQUE NOT NULL,
    ordem INT
);
INSERT INTO nr_etapa_licitatorio (nome, ordem) VALUES
    ('Planejamento e elaboração do edital', 1),
    ('Pesquisa de preços', 2),
    ('Fase preparatória', 3),
    ('Publicação e divulgação', 4),
    ('Julgamento e habilitação', 5),
    ('Homologação e adjudicação', 6),
    ('Contratação', 7),
    ('Execução contratual', 8),
    ('Fiscalização e controle', 9)
ON CONFLICT (nome) DO NOTHING;

-- Tabela auxiliar: tipologias documentais
CREATE TABLE IF NOT EXISTS nr_tipologia (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) UNIQUE NOT NULL
);
INSERT INTO nr_tipologia (nome) VALUES
    ('Administrativo'),
    ('Informacional'),
    ('Jurisprudencial'),
    ('Normativo'),
    ('Operacional')
ON CONFLICT (nome) DO NOTHING;

-- Índice full-text para busca em português
CREATE INDEX IF NOT EXISTS idx_nr_document_fts ON nr_document
    USING gin(to_tsvector('portuguese',
        coalesce(title, '') || ' ' ||
        coalesce(author, '') || ' ' ||
        coalesce(keywords, '') || ' ' ||
        coalesce(abstract, '')
    ));

-- Índices adicionais para filtros
CREATE INDEX IF NOT EXISTS idx_nr_document_tipologia ON nr_document (tipologia);
CREATE INDEX IF NOT EXISTS idx_nr_document_complexidade ON nr_document (complexidade);
CREATE INDEX IF NOT EXISTS idx_nr_document_etapa ON nr_document (etapa_processo_licitatorio);
CREATE INDEX IF NOT EXISTS idx_nr_document_status ON nr_document (status);
CREATE INDEX IF NOT EXISTS idx_nr_document_topic ON nr_document (topic_id);
CREATE INDEX IF NOT EXISTS idx_nr_document_category ON nr_document (category_id);
