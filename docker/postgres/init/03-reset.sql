--
-- Dados iniciais comuns do Nou-Rau
--

SELECT SETVAL('notice_seq','0');
SELECT SETVAL('topic_seq', '0');
SELECT SETVAL('users_seq', '0');

INSERT INTO users (username,password,name,email,level) VALUES ('admin','nqzva','Administrador','nou-rau@localhost','4');
INSERT INTO users (username,password,name,email,level) VALUES ('colab','nqzva','Colaborador','nou-rau@localhost','1');

INSERT INTO version (schema) VALUES ('3.0.1');
