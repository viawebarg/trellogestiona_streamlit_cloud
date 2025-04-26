-- Copyright (C) 2023-2025 TrelloGestiona
-- Este programa es software libre: puede redistribuirlo y/o modificarlo
-- bajo los términos de la Licencia Pública General GNU publicada por
-- la Free Software Foundation, ya sea la versión 3 de la Licencia, o
-- (a su elección) cualquier versión posterior.

-- Script de creación de claves para la tabla de relación entre proyectos de Dolibarr y tableros de Trello

ALTER TABLE llx_trellogestiona_proyecto_tablero ADD INDEX idx_trellogestiona_proyecto_tablero_fk_project (fk_project);
ALTER TABLE llx_trellogestiona_proyecto_tablero ADD CONSTRAINT fk_trellogestiona_proyecto_tablero_fk_project FOREIGN KEY (fk_project) REFERENCES llx_projet(rowid) ON DELETE CASCADE;
ALTER TABLE llx_trellogestiona_proyecto_tablero ADD CONSTRAINT fk_trellogestiona_proyecto_tablero_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_trellogestiona_proyecto_tablero ADD CONSTRAINT fk_trellogestiona_proyecto_tablero_fk_user_modif FOREIGN KEY (fk_user_modif) REFERENCES llx_user(rowid);