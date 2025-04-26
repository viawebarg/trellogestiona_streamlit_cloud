-- Copyright (C) 2023-2025 TrelloGestiona
-- Este programa es software libre: puede redistribuirlo y/o modificarlo
-- bajo los términos de la Licencia Pública General GNU publicada por
-- la Free Software Foundation, ya sea la versión 3 de la Licencia, o
-- (a su elección) cualquier versión posterior.

-- Script de creación de la tabla de configuración del módulo TrelloGestiona

CREATE TABLE llx_trellogestiona_config(
    rowid integer AUTO_INCREMENT PRIMARY KEY,
    entity integer DEFAULT 1 NOT NULL,
    streamlit_url varchar(255),
    trello_api_key varchar(255),
    trello_token varchar(255),
    date_creation datetime NOT NULL,
    tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fk_user_creat integer NOT NULL,
    fk_user_modif integer,
    active tinyint DEFAULT 1 NOT NULL
) ENGINE=innodb;