--
-- Tipos de informação adicionais para logística pública
-- Complementam os 67 tipos padrão do Nou-Rau
--

-- Tipos faltantes identificados no projeto
INSERT INTO type_information (name)
SELECT name FROM (VALUES
    ('Nota Técnica'),
    ('Manual Operacional'),
    ('Relatório de Gestão'),
    ('Estudo de Caso'),
    ('Jurisprudência'),
    ('Parecer'),
    ('Acórdão'),
    ('Decreto'),
    ('Portaria'),
    ('Resolução'),
    ('Instrução Normativa'),
    ('Edital'),
    ('Guia Prático'),
    ('Infográfico'),
    ('Policy Brief'),
    ('White Paper')
) AS new_types(name)
WHERE NOT EXISTS (
    SELECT 1 FROM type_information ti WHERE ti.name = new_types.name
);

-- Associar tipos às coleções principais
INSERT INTO topic_type (topic_id, type_id)
SELECT t.id, ti.id
FROM topic t, type_information ti
WHERE t.parent_id = 0
ON CONFLICT (topic_id, type_id) DO NOTHING;
