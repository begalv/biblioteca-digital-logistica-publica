--
-- Categorias temáticas para a Biblioteca Digital de Logística Pública
-- Derivadas da planilha de 496 estudos catalogados
--

INSERT INTO nr_category (name, description, max_size) VALUES
    ('Aspectos Jurídicos e Regulatórios', 'Legislação, normas e marcos regulatórios de compras e logística pública', 0),
    ('Capacitação e Gestão de Pessoas', 'Formação profissional, capacitação e gestão de recursos humanos em logística pública', 0),
    ('Compras Municipais e Estaduais', 'Processos de compras e contratações nos âmbitos municipal e estadual', 0),
    ('Controle Auditoria e Combate à Corrupção', 'Controle interno, auditoria, tribunais de contas e combate à corrupção', 0),
    ('Estudos Internacionais e Comparativos', 'Estudos comparativos internacionais e boas práticas globais', 0),
    ('Governança e Transparência', 'Governança pública, transparência, accountability e participação social', 0),
    ('Inovação e Tecnologia', 'Inovação tecnológica aplicada à logística e compras públicas', 0),
    ('Logística e Gestão de Suprimentos', 'Cadeia de suprimentos, gestão de estoques e distribuição no setor público', 0),
    ('Políticas Públicas e Desenvolvimento', 'Políticas públicas, planejamento governamental e desenvolvimento', 0),
    ('Sustentabilidade e ODS', 'Compras sustentáveis, logística reversa e Objetivos de Desenvolvimento Sustentável', 0);

-- Associar todas as categorias a todos os tópicos principais
-- para que documentos de qualquer coleção possam usar qualquer categoria
INSERT INTO nr_topic_category (topic_id, category_id)
SELECT t.id, c.id
FROM topic t, nr_category c
WHERE t.parent_id = 0;

-- Associar formatos comuns às categorias
-- (PDF, DOC, DOCX, HTML, PPT, PPTX, XLS, XLSX, TXT, RTF, ODT, ODS, ODP)
INSERT INTO nr_category_format (category_id, format_id)
SELECT c.id, f.id
FROM nr_category c, nr_format f
WHERE f.extension IN ('pdf', 'doc', 'docx', 'html', 'htm', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'rtf', 'odt', 'ods', 'odp', 'epub');
