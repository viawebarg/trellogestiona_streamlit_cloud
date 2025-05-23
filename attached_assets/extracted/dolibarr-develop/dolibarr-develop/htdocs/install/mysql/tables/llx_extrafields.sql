-- ===================================================================
-- Copyright (C) 2011-2012 Regis Houssin        <regis.houssin@inodbox.com>
-- Copyright (C) 2011-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
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
-- ===================================================================

create table llx_extrafields
(
	rowid           integer AUTO_INCREMENT PRIMARY KEY,
	name            varchar(64) NOT NULL,         				-- name of field into extrafields tables
	entity          integer DEFAULT 1 NOT NULL,					-- multi company id
    elementtype     varchar(64) NOT NULL DEFAULT 'member',		-- for which element this extra fields is for
	label           varchar(255) NOT NULL,        				-- label to show for attribute
	type            varchar(8),
	size            varchar(8) DEFAULT NULL,
	fieldcomputed   text,
	fielddefault    text,
	fieldunique     integer DEFAULT 0,
	fieldrequired   integer DEFAULT 0,
	perms			varchar(255),								-- not used yet
	enabled         varchar(255),
	module          varchar(64),
	pos             integer DEFAULT 0,
	alwayseditable  integer DEFAULT 0,							-- 1 if field can be edited whatever is element status
	param			text,										-- extra parameters to define possible values of field
	list			varchar(255) DEFAULT '1',					-- visibility of field. 0=Never visible, 1=Visible on list and forms, 2=Visible on list only. Using a negative value means field is not shown by default on list but can be selected for viewing
	printable		integer DEFAULT 0,					     	-- is the extrafield output on documents
    totalizable     boolean DEFAULT FALSE,                      -- is extrafield totalizable on list
	langs			varchar(64),								-- example: fileofmymodule@mymodule
	help            text,                                       -- to store help tooltip
	aiprompt		text,										-- a prompt to autofill the value with AI
	css             varchar(128),                               -- to store css on create/update forms
	cssview         varchar(128),                               -- to store css on view form
	csslist         varchar(128),                               -- to store css on list
	fk_user_author	integer,									-- user making creation
	fk_user_modif	integer,	                                -- user making last change
	datec			datetime,									-- date de creation
	tms             timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP									-- last modification date
)ENGINE=innodb;
