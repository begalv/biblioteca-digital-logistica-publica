--
-- Usuário read-only para o portal Django
-- Garante que o portal nunca altera dados do Nou-Rau
--

DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'portal_reader') THEN
        CREATE ROLE portal_reader WITH LOGIN PASSWORD 'portal_reader_dev';
    END IF;
END
$$;

-- Permissão de leitura em todas as tabelas existentes
GRANT CONNECT ON DATABASE nourau TO portal_reader;
GRANT USAGE ON SCHEMA public TO portal_reader;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO portal_reader;
GRANT SELECT ON ALL SEQUENCES IN SCHEMA public TO portal_reader;

-- Garantir que tabelas futuras também sejam acessíveis
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT ON TABLES TO portal_reader;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT ON SEQUENCES TO portal_reader;
