-- Copyright (C) 2023-2025 TrelloGestiona
-- Este programa es software libre: puede redistribuirlo y/o modificarlo
-- bajo los términos de la Licencia Pública General GNU publicada por
-- la Free Software Foundation, ya sea la versión 3 de la Licencia, o
-- (a su elección) cualquier versión posterior.

-- Script de creación de claves para la tabla de configuración del módulo TrelloGestiona

ALTER TABLE llx_trellogestiona_config ADD INDEX idx_trellogestiona_config_entity (entity);
ALTER TABLE llx_trellogestiona_config ADD CONSTRAINT fk_trellogestiona_config_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_trellogestiona_config ADD CONSTRAINT fk_trellogestiona_config_fk_user_modif FOREIGN KEY (fk_user_modif) REFERENCES llx_user(rowid);