-- Copyright (C) 2023-2025 TrelloGestiona
-- Este programa es software libre: puede redistribuirlo y/o modificarlo
-- bajo los términos de la Licencia Pública General GNU publicada por
-- la Free Software Foundation, ya sea la versión 3 de la Licencia, o
-- (a su elección) cualquier versión posterior.

-- Script de creación de la tabla de relación entre proyectos de Dolibarr y tableros de Trello

CREATE TABLE llx_trellogestiona_proyecto_tablero(
    rowid integer AUTO_INCREMENT PRIMARY KEY,
    fk_project integer NOT NULL,
    tablero_id varchar(255) NOT NULL,
    date_creation datetime NOT NULL,
    tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fk_user_creat integer NOT NULL,
    fk_user_modif integer,
    active tinyint DEFAULT 1 NOT NULL
) ENGINE=innodb;