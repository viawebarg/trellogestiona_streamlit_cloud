-- ===================================================================
-- Copyright (C) 2023-2025 TrelloGestiona
-- ===================================================================

CREATE TABLE llx_trellogestiona_config(
  rowid           integer AUTO_INCREMENT PRIMARY KEY,
  entity          integer DEFAULT 1 NOT NULL,
  streamlit_url   varchar(255),
  trello_api_key  varchar(255),
  trello_token    varchar(255),
  date_creation   datetime NOT NULL,
  tms             timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fk_user_creat   integer,
  fk_user_modif   integer,
  active          integer DEFAULT 1
) ENGINE=innodb;