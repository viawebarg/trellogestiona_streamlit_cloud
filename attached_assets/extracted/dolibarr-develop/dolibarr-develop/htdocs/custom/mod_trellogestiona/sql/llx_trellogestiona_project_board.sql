-- ===================================================================
-- Copyright (C) 2024 VIAWEB S.A.S
-- ===================================================================

CREATE TABLE llx_trellogestiona_project_board (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_project integer NOT NULL,
  board_id varchar(50) NOT NULL,
  board_name varchar(255),
  date_creation datetime NOT NULL,
  tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_trellogestiona_project_board (fk_project),
  CONSTRAINT fk_trellogestiona_project_board_project FOREIGN KEY (fk_project) REFERENCES llx_projet (rowid) ON DELETE CASCADE
) ENGINE=innodb;