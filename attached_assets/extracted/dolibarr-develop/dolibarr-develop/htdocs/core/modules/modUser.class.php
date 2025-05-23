<?php
/* Copyright (C) 2005		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2005-2009	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2024	Regis Houssin			<regis.houssin@inodbox.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \defgroup   user  Module user management
 *  \brief      Module to manage users and usergroups
 *
 *  \file       htdocs/core/modules/modUser.class.php
 *  \ingroup    user
 *  \brief      Description and activation file for the module users
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *	Class to describe and enable module User
 */
class modUser extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $conf;

		$this->db = $db;
		$this->numero = 0;

		$this->family = "hr"; // Family for module (or "base" if core module)
		$this->module_position = '05';
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "Management of users and groups of users (mandatory)";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'dolibarr';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto = 'group';

		// Data directories to create when module is enabled
		$this->dirs = array("/users/temp");

		// Config pages
		$this->config_page_url = array("user.php");

		// Dependencies
		$this->hidden = false; // A condition to hide module
		$this->depends = array(); // List of module class names as string that must be enabled if this module is enabled
		$this->requiredby = array(); // List of module ids to disable if this one is disabled
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with
		$this->phpmin = array(7, 0); // Minimum version of PHP required by module
		$this->langfiles = array("main", "users", "companies", "members", "salaries", "hrm");
		$this->always_enabled = true; // Can't be disabled

		// Constants
		$this->const = array();

		// Boxes
		$this->boxes = array(
			0=>array('file'=>'box_lastlogin.php', 'enabledbydefaulton'=>'Home'),
			1=>array('file'=>'box_birthdays.php', 'enabledbydefaulton'=>'Home'),
			2=>array('file'=>'box_dolibarr_state_board.php', 'enabledbydefaulton'=>'Home')
		);

		// Permissions
		$this->rights = array();
		$this->rights_class = 'user';
		$this->rights_admin_allowed = 1; // Admin is always granted of permission (even when module is disabled)
		$r = 0;

		$r++;
		$this->rights[$r][self::KEY_ID] = 251;
		$this->rights[$r][self::KEY_LABEL] = 'Read information of other users, groups and permissions';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'user';
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'lire';

		$r++;
		$this->rights[$r][self::KEY_ID] = 252;
		$this->rights[$r][self::KEY_LABEL] = 'Read permissions of other users';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'user_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'readperms';

		$r++;
		$this->rights[$r][self::KEY_ID] = 253;
		$this->rights[$r][self::KEY_LABEL] = 'Create/modify internal and external users, groups and permissions';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'user';
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'creer';

		$r++;
		$this->rights[$r][self::KEY_ID] = 254;
		$this->rights[$r][self::KEY_LABEL] = 'Create/modify external users only';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'user_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'write';

		$r++;
		$this->rights[$r][self::KEY_ID] = 255;
		$this->rights[$r][self::KEY_LABEL] = 'Modify the password of other users';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'user';
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'password';

		$r++;
		$this->rights[$r][self::KEY_ID] = 256;
		$this->rights[$r][self::KEY_LABEL] = 'Delete or disable other users';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'user';
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'supprimer';

		$r++;
		$this->rights[$r][self::KEY_ID] = 341;
		$this->rights[$r][self::KEY_LABEL] = 'Read its own permissions';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'self_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'readperms';

		$r++;
		$this->rights[$r][self::KEY_ID] = 342;
		$this->rights[$r][self::KEY_LABEL] = 'Create/modify of its own user';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'self';
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'creer';

		$r++;
		$this->rights[$r][self::KEY_ID] = 343;
		$this->rights[$r][self::KEY_LABEL] = 'Modify its own password';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'self';
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'password';

		$r++;
		$this->rights[$r][self::KEY_ID] = 344;
		$this->rights[$r][self::KEY_LABEL] = 'Modify its own permissions';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'self_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'writeperms';

		$r++;
		$this->rights[$r][self::KEY_ID] = 351;
		$this->rights[$r][self::KEY_LABEL] = 'Read groups';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'group_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'read';

		$r++;
		$this->rights[$r][self::KEY_ID] = 352;
		$this->rights[$r][self::KEY_LABEL] = 'Read permissions of groups';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'group_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'readperms';

		$r++;
		$this->rights[$r][self::KEY_ID] = 353;
		$this->rights[$r][self::KEY_LABEL] = 'Create/modify groups and permissions';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'group_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'write';

		$r++;
		$this->rights[$r][self::KEY_ID] = 354;
		$this->rights[$r][self::KEY_LABEL] = 'Delete groups';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'group_advance'; // Visible if option MAIN_USE_ADVANCED_PERMS is on
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'delete';

		$r++;
		$this->rights[$r][self::KEY_ID] = 358;
		$this->rights[$r][self::KEY_LABEL] = 'Export all users';
		$this->rights[$r][self::KEY_DEFAULT] = 0;
		$this->rights[$r][self::KEY_FIRST_LEVEL] = 'user';
		$this->rights[$r][self::KEY_SECOND_LEVEL] = 'export';


		// Menus
		$this->menu = 1; // This module add menu entries. They are coded into menu manager.


		// Exports
		$r = 0;

		$r++;
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = 'List of users and attributes'; // Translation key (used only if key ExportDataset_user_1 not found)
		$this->export_permission[$r] = array(array("user", "user", "export"));
		$this->export_fields_array[$r] = array(
			'u.rowid'=>"Id", 'u.login'=>"Login", 'u.lastname'=>"Lastname", 'u.firstname'=>"Firstname", 'u.employee'=>"Employee", 'u.job'=>"PostOrFunction", 'u.gender'=>"Gender",
			'u.accountancy_code'=>"UserAccountancyCode",
			'u.address'=>"Address", 'u.zip'=>"Zip", 'u.town'=>"Town",
			'u.office_phone'=>'Phone', 'u.user_mobile'=>"Mobile", 'u.office_fax'=>'Fax',
			'u.email'=>"Email", 'u.note_public'=>"NotePublic", 'u.note_private'=>"NotePrivate", 'u.signature'=>'Signature',
			'u.fk_user'=>'HierarchicalResponsible', 'u.thm'=>'THM', 'u.tjm'=>'TJM', 'u.weeklyhours'=>'WeeklyHours',
			'u.dateemployment'=>'DateEmploymentStart', 'u.dateemploymentend'=>'DateEmploymentEnd', 'u.salary'=>'Salary', 'u.color'=>'Color', 'u.api_key'=>'ApiKey',
			'u.birth'=>'DateOfBirth',
			'u.datec'=>"DateCreation", 'u.tms'=>"DateLastModification",
			'u.admin'=>"Administrator", 'u.statut'=>'Status', 'u.datelastlogin'=>'LastConnexion', 'u.datepreviouslogin'=>'PreviousConnexion',
			'u.fk_socpeople'=>"IdContact", 'u.fk_soc'=>"IdCompany",
			'u.fk_member'=>"MemberId",
			"a.firstname"=>"MemberFirstname",
			"a.lastname"=>"MemberLastname",
			'g.nom'=>"Group"
		);
		$this->export_TypeFields_array[$r] = array(
			'u.rowid'=>'Numeric', 'u.login'=>"Text", 'u.lastname'=>"Text", 'u.firstname'=>"Text", 'u.employee'=>'Boolean', 'u.job'=>'Text',
			'u.accountancy_code'=>'Text',
			'u.address'=>"Text", 'u.zip'=>"Text", 'u.town'=>"Text",
			'u.office_phone'=>'Text', 'u.user_mobile'=>'Text', 'u.office_fax'=>'Text',
			'u.email'=>'Text', 'u.datec'=>"Date", 'u.tms'=>"Date", 'u.admin'=>"Boolean", 'u.statut'=>'Status', 'u.note_public'=>"Text", 'u.note_private'=>"Text", 'u.signature'=>"Text", 'u.datelastlogin'=>'Date',
			'u.fk_user'=>"FormSelect:select_dolusers",
			'u.birth'=>'Date',
			'u.datepreviouslogin'=>'Date',
			'u.fk_socpeople'=>'FormSelect:selectcontacts',
			'u.fk_soc'=>"FormSelect:select_company",
			'u.tjm'=>"Numeric", 'u.thm'=>"Numeric", 'u.fk_member'=>"Numeric",
			'u.weeklyhours'=>"Numeric",
			'u.dateemployment'=>"Date", 'u.dateemploymentend'=>"Date", 'u.salary'=>"Numeric",
			'u.color'=>'Text', 'u.api_key'=>'Text',
			'a.firstname'=>'Text',
			'a.lastname'=>'Text',
			'g.nom'=>"Text"
		);
		$this->export_entities_array[$r] = array(
			'u.rowid'=>"user", 'u.login'=>"user", 'u.lastname'=>"user", 'u.firstname'=>"user", 'u.employee'=>'user', 'u.job'=>'user', 'u.gender'=>'user',
			'u.accountancy_code'=>'user',
			'u.address'=>"user", 'u.zip'=>"user", 'u.town'=>"user",
			'u.office_phone'=>'user', 'u.user_mobile'=>'user', 'u.office_fax'=>'user',
			'u.email'=>'user', 'u.note_public'=>"user", 'u.note_private'=>"user", 'u.signature'=>'user',
			'u.fk_user'=>'user', 'u.thm'=>'user', 'u.tjm'=>'user', 'u.weeklyhours'=>'user',
			'u.dateemployment'=>'user', 'u.dateemploymentend'=>'user', 'u.salary'=>'user', 'u.color'=>'user', 'u.api_key'=>'user',
			'u.birth'=>'user',
			'u.datec'=>"user", 'u.tms'=>"user",
			'u.admin'=>"user", 'u.statut'=>'user', 'u.datelastlogin'=>'user', 'u.datepreviouslogin'=>'user',
			'u.fk_socpeople'=>"contact", 'u.fk_soc'=>"company", 'u.fk_member'=>"member",
			'a.firstname'=>"member", 'a.lastname'=>"member",
			'g.nom'=>"Group"
		);
		$keyforselect = 'user';
		$keyforelement = 'user';
		$keyforaliasextra = 'extra';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		if (!isModEnabled('member')) {
			unset($this->export_fields_array[$r]['u.fk_member']);
			unset($this->export_entities_array[$r]['u.fk_member']);
		}
		$this->export_sql_start[$r] = 'SELECT DISTINCT ';
		$this->export_sql_end[$r]  = ' FROM '.MAIN_DB_PREFIX.'user as u';
		$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'user_extrafields as extra ON u.rowid = extra.fk_object';
		$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'usergroup_user as ug ON u.rowid = ug.fk_user';
		$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'usergroup as g ON ug.fk_usergroup = g.rowid';
		$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'adherent as a ON u.fk_member = a.rowid';
		$this->export_sql_end[$r] .= ' WHERE u.entity IN ('.getEntity('user').')';


		$r++;
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = 'List of security events'; // Translation key (used only if key ExportDataset_user_2 not found)
		$this->export_permission[$r] = array(array("user"));	// Only admin
		$this->export_fields_array[$r] = array(
			'e.rowid'=>"Id", 'e.type'=>"Type",
			'e.dateevent'=>"Date",
			'e.description'=>'Description',
			'e.ip'=>'IPAddress', 'e.user_agent'=>'UserAgent',
			'e.authentication_method' => 'AuthenticationMode',
			'e.fk_user'=>"UserID", 'u.login'=>"Login",
		);
		$this->export_TypeFields_array[$r] = array(
			'e.rowid'=>'Numeric', 'e.type'=>"Text",
			'e.dateevent'=>"Date",
			'e.description'=>'Text',
			'e.ip'=>'Text', 'e.user_agent'=>'Text',
			'e.authentication_method' => 'Text',
			'e.fk_user'=>"Numeric", 'u.login'=>"Text",
		);
		$this->export_entities_array[$r] = array(
			'e.rowid'=>'securityevent', 'e.type'=>"securityevent",
			'e.dateevent'=>"securityevent",
			'e.description'=>'securityevent',
			'e.ip'=>'securityevent', 'e.user_agent'=>'securityevent',
			'e.authentication_method' => 'securityevent',
			'e.fk_user'=>"user", 'u.login'=>"user",
		);
		$this->export_sql_start[$r] = 'SELECT DISTINCT ';
		$this->export_sql_end[$r]  = ' FROM '.MAIN_DB_PREFIX.'events as e';
		$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'user as u ON e.fk_user = u.rowid';
		$this->export_sql_end[$r] .= ' WHERE e.entity IN ('.getEntity('event').')';


		// Imports
		$r = 0;

		// Import list of users attributes
		$r++;
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = 'ImportDataset_user_1';
		$this->import_icon[$r] = 'user';
		$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
		$this->import_tables_array[$r] = array('u'=>MAIN_DB_PREFIX.'user', 'extra'=>MAIN_DB_PREFIX.'user_extrafields'); // List of tables to insert into (insert done in same order)
		$this->import_fields_array[$r] = array(
			'u.login'=>"Login*", 'u.lastname'=>"Name*", 'u.firstname'=>"Firstname", 'u.employee'=>"Employee*", 'u.job'=>"PostOrFunction", 'u.gender'=>"Gender",
			'u.accountancy_code'=>"UserAccountancyCode",
			'u.pass_crypted'=>"Password", 'u.admin'=>"Administrator", 'u.fk_soc'=>"Company*", 'u.address'=>"Address", 'u.zip'=>"Zip", 'u.town'=>"Town",
			'u.fk_state'=>"StateId", 'u.fk_country'=>"CountryCode",
			'u.office_phone'=>"Phone", 'u.user_mobile'=>"Mobile", 'u.office_fax'=>"Fax",
			'u.email'=>"Email", 'u.note_public'=>"NotePublic", 'u.note_private'=>"NotePrivate", 'u.signature'=>'Signature',
			'u.fk_user'=>'HierarchicalResponsible', 'u.thm'=>'THM', 'u.tjm'=>'TJM', 'u.weeklyhours'=>'WeeklyHours',
			'u.dateemployment'=>'DateEmploymentStart', 'u.dateemploymentend'=>'DateEmploymentEnd', 'u.salary'=>'Salary', 'u.color'=>'Color', 'u.api_key'=>'ApiKey',
			'u.birth'=>'DateOfBirth',
			'u.datec'=>"DateCreation",
			'u.statut'=>'Status'
		);
		// Add extra fields
		$sql = "SELECT name, label, fieldrequired FROM ".MAIN_DB_PREFIX."extrafields WHERE type <> 'separate' AND elementtype = 'user' AND entity IN (0,".$conf->entity.")";
		$resql = $this->db->query($sql);
		if ($resql) {    // This can fail when class is used on old database (during migration for example)
			while ($obj = $this->db->fetch_object($resql)) {
				$fieldname = 'extra.'.$obj->name;
				$fieldlabel = ucfirst($obj->label);
				$this->import_fields_array[$r][$fieldname] = $fieldlabel.($obj->fieldrequired ? '*' : '');
			}
		}
		// End add extra fields
		$this->import_fieldshidden_array[$r] = array('u.fk_user_creat'=>'user->id', 'extra.fk_object'=>'lastrowid-'.MAIN_DB_PREFIX.'user'); // aliastable.field => ('user->id' or 'lastrowid-'.tableparent)
		$this->import_convertvalue_array[$r] = array(
			'u.fk_state'=>array('rule'=>'fetchidfromcodeid', 'classfile'=>'/core/class/cstate.class.php', 'class'=>'Cstate', 'method'=>'fetch', 'dict'=>'DictionaryState'),
			'u.fk_country'=>array('rule'=>'fetchidfromcodeid', 'classfile'=>'/core/class/ccountry.class.php', 'class'=>'Ccountry', 'method'=>'fetch', 'dict'=>'DictionaryCountry'),
			'u.salary'=>array('rule'=>'numeric')
		);
		//$this->import_convertvalue_array[$r]=array('s.fk_soc'=>array('rule'=>'lastrowid',table='t');
		$this->import_regex_array[$r] = array(
			'u.employee'=>'^[0|1]',
			'u.datec'=>'^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]( [0-9][0-9]:[0-9][0-9]:[0-9][0-9])?$',
			'u.dateemployment'=>'^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$',
			'u.birth'=>'^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$'
		);
		$this->import_examplevalues_array[$r] = array(
			'u.lastname'=>"Doe", 'u.firstname'=>'John', 'u.login'=>'jdoe', 'u.employee'=>'0 or 1', 'u.job'=>'CTO', 'u.gender'=>'man or woman',
			'u.pass_crypted'=>'Encrypted password',
			'u.fk_soc'=>'0 (internal user) or company name (external user)', 'u.address'=>"61 jump street",
			'u.zip'=>"123456", 'u.town'=>"Big town", 'u.fk_country'=>'US, FR, DE...', 'u.office_phone'=>"0101010101", 'u.office_fax'=>"0101010102",
			'u.email'=>"test@mycompany.com", 'u.salary'=>"10000", 'u.note_public'=>"This is an example of public note for record", 'u.note_private'=>"This is an example of private note for record", 'u.datec'=>"2015-01-01 or 2015-01-01 12:30:00",
			'u.statut'=>"0 (closed) or 1 (active)",
		);
		$this->import_updatekeys_array[$r] = array('u.lastname'=>'Lastname', 'u.firstname'=>'Firstname', 'u.login'=>'Login');
	}


	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
	 *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		// Permissions
		$this->remove($options);

		$sql = array();

		return $this->_init($sql, $options);
	}
}
