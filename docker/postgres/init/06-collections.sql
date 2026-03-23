--
-- Hierarquia de coleções (topics) para a Biblioteca Digital de Logística Pública
-- Baseado no template SBU adaptado ao projeto
--

-- Coleções principais (parent_id = 0 = raiz)
INSERT INTO topic (name, description, parent_id, archieve) VALUES
    ('Eventos', 'Documentos provenientes de eventos acadêmicos e profissionais', 0, 's');
INSERT INTO topic (name, description, parent_id, archieve) VALUES
    ('Livros Digitais', 'Livros e e-books em formato digital', 0, 's');
INSERT INTO topic (name, description, parent_id, archieve) VALUES
    ('Materiais Pedagógicos', 'Materiais didáticos e de apoio ao ensino', 0, 's');
INSERT INTO topic (name, description, parent_id, archieve) VALUES
    ('Trabalhos Acadêmicos', 'Dissertações, teses e trabalhos de conclusão de curso', 0, 's');

-- Subcoleções de Eventos (parent_id = id de "Eventos")
INSERT INTO topic (name, description, parent_id, archieve)
SELECT sub.name, sub.description, t.id, 's'
FROM topic t,
(VALUES
    ('Colóquios', 'Colóquios e debates'),
    ('Conferências', 'Conferências e palestras'),
    ('Congressos', 'Congressos acadêmicos e profissionais'),
    ('Encontros', 'Encontros e reuniões técnicas'),
    ('Fóruns', 'Fóruns de discussão'),
    ('Jornadas', 'Jornadas acadêmicas'),
    ('Mesas redondas', 'Mesas redondas e painéis de discussão'),
    ('Painéis', 'Painéis temáticos'),
    ('Seminários', 'Seminários acadêmicos e profissionais'),
    ('Simpósios', 'Simpósios científicos'),
    ('Workshops', 'Workshops e oficinas')
) AS sub(name, description)
WHERE t.name = 'Eventos' AND t.parent_id = 0;

-- Subcoleções de Livros Digitais
INSERT INTO topic (name, description, parent_id, archieve)
SELECT sub.name, sub.description, t.id, 's'
FROM topic t,
(VALUES
    ('Livro', 'Livros publicados'),
    ('E-book', 'E-books e publicações eletrônicas'),
    ('Livros digitalizados', 'Livros digitalizados a partir de originais impressos')
) AS sub(name, description)
WHERE t.name = 'Livros Digitais' AND t.parent_id = 0;

-- Subcoleções de Materiais Pedagógicos
INSERT INTO topic (name, description, parent_id, archieve)
SELECT sub.name, sub.description, t.id, 's'
FROM topic t,
(VALUES
    ('Apostilas', 'Apostilas e materiais didáticos'),
    ('Aulas', 'Aulas e apresentações'),
    ('Cursos', 'Cursos e programas de capacitação'),
    ('Manuais', 'Manuais técnicos e operacionais'),
    ('Relatórios', 'Relatórios técnicos e de gestão'),
    ('Slides', 'Apresentações em slides'),
    ('Textos de discussão', 'Textos para discussão e debate'),
    ('Tutoriais', 'Tutoriais e guias passo a passo'),
    ('Vídeos', 'Vídeos educativos e instrucionais')
) AS sub(name, description)
WHERE t.name = 'Materiais Pedagógicos' AND t.parent_id = 0;

-- Subcoleções de Trabalhos Acadêmicos
INSERT INTO topic (name, description, parent_id, archieve)
SELECT sub.name, sub.description, t.id, 's'
FROM topic t,
(VALUES
    ('Dissertações', 'Dissertações de mestrado'),
    ('Memoriais docentes', 'Memoriais de docentes e pesquisadores'),
    ('TCCs', 'Trabalhos de conclusão de curso'),
    ('Teses', 'Teses de doutorado')
) AS sub(name, description)
WHERE t.name = 'Trabalhos Acadêmicos' AND t.parent_id = 0;

-- Atualizar topic_path para as coleções principais
INSERT INTO topic_path (topic_id, parent_ids, parent_names)
SELECT id, '0', name FROM topic WHERE parent_id = 0;

-- Atualizar topic_path para as subcoleções
INSERT INTO topic_path (topic_id, parent_ids, parent_names)
SELECT sub.id,
       '0,' || parent.id,
       parent.name || '/' || sub.name
FROM topic sub
JOIN topic parent ON sub.parent_id = parent.id
WHERE parent.parent_id = 0;
