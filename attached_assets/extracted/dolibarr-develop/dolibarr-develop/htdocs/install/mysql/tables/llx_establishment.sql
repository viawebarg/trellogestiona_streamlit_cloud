-- ============================================================================
-- Copyright (C) 2015       Alexandre Spangaro  <aspangaro@open-dsi.fr>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <https://www.gnu.org/licenses/>.
--
-- ============================================================================
--
-- Structure de la table llx_establishment
--

CREATE TABLE llx_establishment (
  rowid 			integer NOT NULL auto_increment PRIMARY KEY,
  entity 			integer NOT NULL DEFAULT 1,
  ref				varchar(30),
  label       		varchar(255) NOT NULL,
  name				varchar(128),
  address           varchar(255),
  zip               varchar(25),
  town              varchar(50),
  fk_state          integer DEFAULT 0,
  fk_country        integer DEFAULT 0,
  profid1			varchar(20),
  profid2			varchar(20),
  profid3			varchar(20),
  phone				varchar(30),
  fk_user_author 	integer NOT NULL,
  fk_user_mod		integer,
  datec				datetime NOT NULL,
  tms				timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
  status            tinyint DEFAULT 1
) ENGINE=innodb;
