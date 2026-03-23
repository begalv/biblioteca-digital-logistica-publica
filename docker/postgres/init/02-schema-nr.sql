--
-- NOU-RAU DATABASE - schema 1.0.1
--

CREATE SEQUENCE nr_category_seq MINVALUE 0;
CREATE SEQUENCE nr_format_seq   MINVALUE 0;
CREATE SEQUENCE nr_document_seq MINVALUE 0;
CREATE SEQUENCE nr_ods_seq MINVALUE 0;
CREATE SEQUENCE type_information_seq  MINVALUE 0;
CREATE SEQUENCE supplementary_files_seq  MINVALUE 0;
CREATE SEQUENCE topic_users_seq  MINVALUE 0;
CREATE SEQUENCE visitas_downloads_seq MINVALUE 0;

CREATE TABLE nr_category (
  id              INT DEFAULT NEXTVAL('nr_category_seq'),
  name            VARCHAR(50) UNIQUE,
  description     VARCHAR(150),
  max_size        INT,
  PRIMARY KEY (id)
);

CREATE TABLE nr_category_format (
  category_id     INT,
  format_id       INT,
  PRIMARY KEY (category_id,format_id)
);

CREATE TABLE nr_document (
  id                INT DEFAULT NEXTVAL('nr_document_seq'),
  title             VARCHAR(1500),
  title_en          VARCHAR(1500),
  author            VARCHAR(3000),
  autor_principal   VARCHAR(800),
  email             VARCHAR(150),
  keywords          VARCHAR(50000),
  keywords_en       VARCHAR(15000),
  abstract          VARCHAR(25000),
  abstract_en       VARCHAR(25000),
  description       VARCHAR(100000),
  code              VARCHAR(50) UNIQUE,
  info              VARCHAR(100000),
  topic_id          INT,
  owner_id          INT,
  category_id       INT,
  status            character(1) DEFAULT 'i'::bpchar,
  filename          VARCHAR(950),
  size              INT,
  format_id         INT,
  visits            INT DEFAULT '0',
  downloads         INT DEFAULT '0',
  created           timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone,
  updated           timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone,
  remote            CHAR DEFAULT 'n',
  curso             VARCHAR(100),
  disciplina        VARCHAR(100),
  professor         VARCHAR(100),
  departamento      VARCHAR(100),
  typeinformation   INT,
  doi               VARCHAR(180),
  capa              VARCHAR(65),
  descricao_fisica  VARCHAR(500),
  acesso_eletronico VARCHAR(800),
  source            VARCHAR(900),
  nlspi             character varying(200),
  typeinform_id     INT,
  nota_versao_ori   character varying(1000),
  tacesso           integer DEFAULT 0,
  edicao            character varying(900),
  event_description character varying(5000),
  avulso            character(1) DEFAULT 'n'::bpchar,
  ods_id            integer[],
  view_document     character(1) DEFAULT '0'::bpchar,
  dados_pesquisa    character varying(800),
  PRIMARY KEY (id)
);

CREATE TABLE nr_format (
  id              INT DEFAULT NEXTVAL('nr_format_seq'),
  name            VARCHAR(50) UNIQUE,
  type            VARCHAR(20),
  subtype         VARCHAR(100),
  extension       VARCHAR(10),
  icon            CHAR(3),
  compress        CHAR,
  verify          CHAR,
  name_pt         VARCHAR(50),
  PRIMARY KEY (id)
);

CREATE TABLE nr_ods(
  id               INT DEFAULT NEXTVAL('nr_ods_seq'),
  description      VARCHAR(100),
  ordem            INT,
  PRIMARY KEY (id)
);

CREATE TABLE supplementary_files (
    id             INT DEFAULT NEXTVAL('supplementary_files_seq'),
    filename       VARCHAR(150) NOT NULL,
    size           INT,
    document_id    INT NOT NULL,
    category_id    INT NOT NULL,
    owner_id       INT NOT NULL,
    format_id      INT NOT NULL,
    remote         VARCHAR(1) DEFAULT 'n'::bpchar NOT NULL,
    topic_id       INT NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE nr_topic_category (
  topic_id        INT,
  category_id     INT,
  PRIMARY KEY (topic_id,category_id)
);

CREATE TABLE topic_users (
  id           INT DEFAULT NEXTVAL('topic_users_seq'),
  users_id     INT,
  topic_id     INT,
  PRIMARY KEY (users_id,topic_id)
);

CREATE TABLE topic_type (
    topic_id integer NOT NULL,
    type_id integer,
    PRIMARY KEY (topic_id,type_id)
);

CREATE TABLE nr_version (
  schema          VARCHAR(10)
);

CREATE TABLE type_information (
  id        INT DEFAULT NEXTVAL('type_information_seq'),
  name      VARCHAR(100),
  PRIMARY KEY (id)
);

CREATE TABLE visitas_downloads (
    id          INT DEFAULT NEXTVAL('visitas_downloads_seq'),
    ip          VARCHAR(15),
    data        timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone,
    code        VARCHAR(50),
    tipo        VARCHAR(1),
    topic_id    integer,
    country     VARCHAR(100),
    user_id     integer,
    origem      VARCHAR(2),
    idsf        integer,
    countryC    VARCHAR(4),
    PRIMARY KEY (id)
);
