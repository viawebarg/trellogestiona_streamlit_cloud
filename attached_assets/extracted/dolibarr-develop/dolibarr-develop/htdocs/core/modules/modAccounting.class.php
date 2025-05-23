<?php
/* Copyright (C) 2013-2014	Olivier Geffroy		<jeff@jeffinfo.com>
 * Copyright (C) 2013-2021	Alexandre Spangaro	<aspangaro@open-dsi.fr>
 * Copyright (C) 2014		Ari Elbaz (elarifr)	<github@accedinfo.com>
 * Copyright (C) 2014		Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2016-2017	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2017-2021	Open-DSI			<support@open-dsi.fr>
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
 * \file		htdocs/core/modules/modAccounting.class.php
 * \ingroup		Double entry accounting
 * \brief		Module to activate the double entry accounting module
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Class to describe and enable double entry accounting module
 */
class modAccounting extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
		$this->numero = 50400;

		$this->family = "financial";
		$this->module_position = '61';
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "Double entry accounting management";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or 'dolibarr_deprecated' or version
		$this->version = 'dolibarr';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto = 'accountancy';

		// Data directories to create when module is enabled
		$this->dirs = array('/accounting/temp', '/accounting/export');

		// Config pages
		$this->config_page_url = array('accounting.php?mainmenu=accountancy&leftmenu=accountancy_admin');

		// Dependencies
		$this->depends = array("modFacture", "modBanque", "modTax"); // List of modules id that must be enabled if this module is enabled
		$this->requiredby = array(); // List of modules id to disable if this one is disabled
		$this->conflictwith = array("modComptabilite"); // List of modules are in conflict with this module
		$this->phpmin = array(7, 0); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3, 9); // Minimum version of Dolibarr required by module
		$this->langfiles = array("accountancy", "compta");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = [
			[
				"MAIN_COMPANY_CODE_ALWAYS_REQUIRED",
				"chaine",
				"1",
				"With this constants on, third party code is always required whatever is numbering module behaviour", 0, 'current', 1
			],
			[
				"MAIN_BANK_ACCOUNTANCY_CODE_ALWAYS_REQUIRED",
				"chaine",
				"1",
				"With this constants on, bank account number is always required", 0, 'current', 1
			],
			[
				"ACCOUNTING_ACCOUNT_SUSPENSE",
				"chaine",
				"471",
				"", 0, 'current', 0
			],
			[
				"ACCOUNTING_ACCOUNT_TRANSFER_CASH",
				"chaine",
				"58",
				"", 0, 'current', 0
			],
			[
				"CHARTOFACCOUNTS",
				"chaine",
				"2",
				"", 0, 'current', 0
			],
			[
				"ACCOUNTING_EXPORT_MODELCSV",
				"chaine",
				"1",
				"", 0, 'current', 0
			],
			[
				"ACCOUNTING_LENGTH_GACCOUNT",
				"chaine",
				"",
				"", 0, 'current', 0
			],
			[
				"ACCOUNTING_LENGTH_AACCOUNT",
				"chaine",
				"",
				"", 0, 'current', 0
			],
			[
				"ACCOUNTING_EXPORT_DATE",
				"chaine",
				"%Y-%m-%d",
				"", 0, 'current', 0
			],
			[
				"ACCOUNTING_EXPORT_SEPARATORCSV",
				"string",
				",",
				"", 0, 'current', 0
			],
			[
				"ACCOUNTING_EXPORT_FORMAT",
				"chaine",
				"csv",
				"", 0, 'current', 0
			],
			[
				"BOOKKEEPING_ADDON",
				"chaine",
				"mod_bookkeeping_argon",
				"", 0, 'current', 0
			],
		];

		// Tabs
		$this->tabs = array();

		// Css
		$this->module_parts = array();

		// Boxes
		$this->boxes = array(
			0=>array('file'=>'box_accountancy_last_manual_entries.php', 'enabledbydefaulton'=>'accountancyindex'),
			1=>array('file'=>'box_accountancy_suspense_account.php', 'enabledbydefaulton'=>'accountancyindex')
		);

		// Permissions
		$this->rights_class = 'accounting';

		$this->rights = array(); // Permission array used by this module
		$r = 0;

		$this->rights[$r][0] = 50440;
		$this->rights[$r][1] = 'Manage chart of accounts, setup of accountancy';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'chartofaccount';
		$this->rights[$r][5] = '';
		$r++;

		$this->rights[$r][0] = 50401;
		$this->rights[$r][1] = 'Bind products and invoices with accounting accounts';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bind';
		$this->rights[$r][5] = 'write';
		$r++;

		$this->rights[$r][0] = 50411;
		$this->rights[$r][1] = 'Read operations in Ledger';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'mouvements';
		$this->rights[$r][5] = 'lire';
		$r++;

		$this->rights[$r][0] = 50412;
		$this->rights[$r][1] = 'Write/Edit operations in Ledger';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'mouvements';
		$this->rights[$r][5] = 'creer';
		$r++;

		$this->rights[$r][0] = 50414;
		$this->rights[$r][1] = 'Delete operations in Ledger';
		$this->rights[$r][2] = 'd';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'mouvements';
		$this->rights[$r][5] = 'supprimer';
		$r++;

		$this->rights[$r][0] = 50415;
		$this->rights[$r][1] = 'Delete all operations by year and journal in Ledger';
		$this->rights[$r][2] = 'd';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'mouvements';
		$this->rights[$r][5] = 'supprimer_tous';
		$r++;

		$this->rights[$r][0] = 50418;
		$this->rights[$r][1] = 'Export operations of the Ledger';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'mouvements';
		$this->rights[$r][5] = 'export';
		$r++;

		$this->rights[$r][0] = 50420;
		$this->rights[$r][1] = 'Report and export reports (turnover, balance, journals, ledger)';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'comptarapport';
		$this->rights[$r][5] = 'lire';
		$r++;

		$this->rights[$r][0] = 50430;
		$this->rights[$r][1] = 'Manage fiscal periods, validate movements and close periods';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'fiscalyear';
		$this->rights[$r][5] = 'write';
		$r++;

		// Menus
		//-------
		$this->menu = 1; // This module add menu entries. They are coded into menu manager.

		// Exports
		//--------
		$r = 0;

		$r++;
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = 'Chartofaccounts';
		$this->export_icon[$r] = $this->picto;
		$this->export_permission[$r] = array(array("accounting", "chartofaccount"));
		$this->export_fields_array[$r] = array('ac.rowid'=>'ChartofaccountsId', 'ac.pcg_version'=>'Chartofaccounts', 'aa.rowid'=>'ID', 'aa.account_number'=>"AccountAccounting", 'aa.label'=>"Label", 'aa2.account_number'=>"Accountparent", 'aa.pcg_type'=>"Pcgtype", 'aa.active'=>'Status');
		$this->export_TypeFields_array[$r] = array('ac.rowid'=>'List:accounting_system:pcg_version', 'ac.pcg_version'=>'Text', 'aa.rowid'=>'Numeric', 'aa.account_number'=>"Text", 'aa.label'=>"Text", 'aa2.account_number'=>"Text", 'aa.pcg_type'=>'Text', 'aa.active'=>'Status');
		$this->export_entities_array[$r] = array(); // We define here only fields that use another picto

		$this->export_sql_start[$r] = 'SELECT DISTINCT ';
		$this->export_sql_end[$r]  = ' FROM '.MAIN_DB_PREFIX.'accounting_account as aa';
		$this->export_sql_end[$r] .= ' ,'.MAIN_DB_PREFIX.'accounting_system as ac';
		$this->export_sql_end[$r] .= ' ,'.MAIN_DB_PREFIX.'accounting_account as aa2';
		$this->export_sql_end[$r] .= ' WHERE ac.pcg_version = aa.fk_pcg_version AND aa.entity IN ('.getEntity('accounting').')';
		$this->export_sql_end[$r] .= ' AND aa2.rowid = aa.account_parent AND aa2.active = 1 AND ac.pcg_version = aa2.fk_pcg_version AND aa2.entity IN ('.getEntity('accounting').')';


		// Imports
		//--------
		$r = 0;

		// Chart of accounts
		$r++;
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = "Chartofaccounts"; // Translation key
		$this->import_icon[$r] = $this->picto;
		$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
		$this->import_tables_array[$r] = array('aa'=>MAIN_DB_PREFIX.'accounting_account');
		$this->import_tables_creator_array[$r] = array('aa'=>'fk_user_author'); // Fields to store import user id
		$this->import_fields_array[$r] = array('aa.fk_pcg_version'=>"Chartofaccounts*", 'aa.account_number'=>"AccountAccounting*", 'aa.label'=>"Label*", 'aa.account_parent'=>"Accountparent", "aa.fk_accounting_category"=>"AccountingCategory", "aa.pcg_type"=>"Pcgtype*", 'aa.active'=>'Status*', 'aa.datec'=>"DateCreation");
		$this->import_regex_array[$r] = array('aa.fk_pcg_version'=>'pcg_version@'.MAIN_DB_PREFIX.'accounting_system', 'aa.account_number'=>'^.{1,32}$', 'aa.label'=>'^.{1,255}$', 'aa.account_parent'=>'^.{0,32}$', 'aa.fk_accounting_category'=>'rowid@'.MAIN_DB_PREFIX.'c_accounting_category', 'aa.pcg_type'=>'^.{1,20}$', 'aa.active'=>'^0|1$', 'aa.datec'=>'^\d{4}-\d{2}-\d{2}$');
		$this->import_convertvalue_array[$r] = array(
			'aa.account_number'=>array('rule'=>'accountingaccount'),
			'aa.account_parent'=>array('rule'=>'fetchidfromref', 'classfile'=>'/accountancy/class/accountingaccount.class.php', 'class'=>'AccountingAccount', 'method'=>'fetch', 'element'=>'AccountingAccount'),
			'aa.fk_accounting_category'=>array('rule'=>'fetchidfromcodeorlabel', 'classfile'=>'/accountancy/class/accountancycategory.class.php', 'class'=>'AccountancyCategory', 'method'=>'fetch', 'dict'=>'DictionaryAccountancyCategory'),
		);
		$this->import_examplevalues_array[$r] = array('aa.fk_pcg_version'=>"PCG99-ABREGE", 'aa.account_number'=>"707", 'aa.label'=>"Product sales", 'aa.account_parent'=>"ref:7 or id:1407", "aa.fk_accounting_category"=>"", "aa.pcg_type"=>"PROD", 'aa.active'=>'1', 'aa.datec'=>"2017-04-28");
		$this->import_updatekeys_array[$r] = array('aa.fk_pcg_version'=>'Chartofaccounts', 'aa.account_number'=>'AccountAccounting');

		// General ledger
		$r++;
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = 'ImportAccountingEntries';
		$this->import_icon[$r] = $this->picto;
		$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
		$this->import_tables_array[$r] = array('b'=>MAIN_DB_PREFIX.'accounting_bookkeeping'); // List of tables to insert into (insert done in same order)
		$this->import_fields_array[$r] = array(
			'b.piece_num'=>"TransactionNumShort",
			'b.doc_date'=>"Docdate",
			//'b.doc_type'=>'Doctype',
			'b.doc_ref'=>'Piece',
			'b.code_journal'=>'Codejournal',
			'b.journal_label'=>'JournalLabel',
			'b.numero_compte'=>'AccountAccounting',
			'b.label_compte'=>'LabelAccount',
			'b.subledger_account'=>'SubledgerAccount',
			'b.subledger_label'=>'SubledgerAccountLabel',
			'b.label_operation'=>'LabelOperation',
			'b.debit'=>"Debit",
			'b.credit'=>"Credit",
			'b.sens'=>'Direction'	// This field is still used by accounting export. We can remove it once it has been replaced into accountancyexport.class.php by a detection using ->debit and ->credit
		);
		$this->import_fieldshidden_array[$r] = array('b.doc_type'=>'const-import_from_external', 'b.fk_doc'=>'const-0', 'b.fk_docdet'=>'const-0', 'b.fk_user_author'=>'user->id', 'b.date_creation'=>'const-'.dol_print_date(dol_now(), 'standard')); // aliastable.field => ('user->id' or 'lastrowid-'.tableparent)
		$this->import_regex_array[$r] = array('b.doc_date'=>'^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$');
		$this->import_convertvalue_array[$r] = array(
			'b.numero_compte' => array('rule' => 'accountingaccount'),
			'b.subledger_account' => array('rule' => 'accountingaccount')
		);
		$this->import_examplevalues_array[$r] = array(
			'b.piece_num'=>'123 (!!! use next value not already used)',
			'b.doc_date'=>dol_print_date(dol_now(), "%Y-%m-%d"),
			//'b.doc_type'=>'import',
			'b.doc_ref'=>'My document ABC',
			'b.code_journal'=>"VTE",
			'b.journal_label'=>"Sale journal",
			'b.numero_compte'=>"707",
			'b.label_compte'=>'Product account 707',
			'b.subledger_account'=>'',
			'b.subledger_label'=>'',
			'b.label_operation'=>"Sale of ABC",
			'b.debit'=>"0",
			'b.credit'=>"100",
			'b.sens'=>'C'	// This field is still used by accounting export. We can remove it once it has been replace into accountancyexport.class.php by a detection using ->debit and ->credit
		);

		// General ledger - File FEC
		$r++;
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = 'ImportAccountingEntriesFECFormat';
		$this->import_icon[$r] = $this->picto;
		$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
		$this->import_tables_array[$r] = array('b'=>MAIN_DB_PREFIX.'accounting_bookkeeping'); // List of tables to insert into (insert done in same order)
		$this->import_fields_array[$r] = array(
			'b.code_journal'=>'FECFormatJournalCode*',
			'b.journal_label'=>'FECFormatJournalLabel',
			'b.piece_num'=>'FECFormatEntryNum', // not mandatory (keep empty to get next value of "piece_num" from "llx_accounting_bookkeeping" table)
			'b.doc_date'=>'FECFormatEntryDate*',
			'b.numero_compte'=>'FECFormatGeneralAccountNumber*',
			'b.label_compte'=>'FECFormatGeneralAccountLabel*',
			'b.subledger_account'=>'FECFormatSubledgerAccountNumber',
			'b.subledger_label'=>'FECFormatSubledgerAccountLabel',
			'b.doc_ref'=>'FECFormatPieceRef*',
			'b.date_creation'=>'FECFormatPieceDate',
			'b.label_operation'=>'FECFormatLabelOperation',
			'b.debit'=>'FECFormatDebit*',
			'b.credit'=>'FECFormatCredit*',
			'b.lettering_code'=>'FECFormatReconcilableCode',
			'b.date_lettering'=>'FECFormatReconcilableDate',
			'b.date_validated'=>'FECFormatValidateDate',
			'b.multicurrency_amount'=>'FECFormatMulticurrencyAmount',
			'b.multicurrency_code'=>'FECFormatMulticurrencyCode'
		);
		$this->import_fieldshidden_array[$r] = array(
			'b.doc_type'=>'const-import_from_external',
			'b.fk_doc'=>'const-0',
			'b.fk_docdet'=>'const-0',
			'b.fk_user_author'=>'user->id',
			'b.montant'=>'rule-computeAmount',
			'b.sens'=>'rule-computeDirection'
		); // aliastable.field => ('user->id' or 'lastrowid-'.tableparent)
		$this->import_convertvalue_array[$r]=array(
			'b.piece_num' => array('rule' => 'compute', 'type' => 'int', 'classfile' => '/accountancy/class/accountancyimport.class.php', 'class' => 'AccountancyImport', 'method' => 'computePieceNum', 'element' => 'Accountancy'),
			'b.numero_compte'=>array('rule'=>'accountingaccount'),
			'b.subledger_account'=>array('rule'=>'accountingaccount'),
			'b.debit' => array('rule' => 'compute', 'type' => 'double', 'classfile' => '/accountancy/class/accountancyimport.class.php', 'class' => 'AccountancyImport', 'method' => 'cleanAmount', 'element' => 'Accountancy'),
			'b.credit' => array('rule' => 'compute', 'type' => 'double', 'classfile' => '/accountancy/class/accountancyimport.class.php', 'class' => 'AccountancyImport', 'method' => 'cleanAmount', 'element' => 'Accountancy'),
			'b.multicurrency_amount' => array('rule' => 'compute', 'type' => 'double', 'classfile' => '/accountancy/class/accountancyimport.class.php', 'class' => 'AccountancyImport', 'method' => 'cleanAmount', 'element' => 'Accountancy'),
			'b.montant' => array('rule' => 'compute', 'type' => 'double', 'classfile' => '/accountancy/class/accountancyimport.class.php', 'class' => 'AccountancyImport', 'method' => 'computeAmount', 'element' => 'Accountancy'),
			'b.sens' => array('rule' => 'compute', 'type' => 'varchar', 'classfile' => '/accountancy/class/accountancyimport.class.php', 'class' => 'AccountancyImport', 'method' => 'computeDirection', 'element' => 'Accountancy'),
		);
		$this->import_regex_array[$r] = array(
			//'b.doc_date'=>'^\d{4}\d{2}\d{2}$',
			'b.doc_ref'=>'^.{1,300}$',
			'b.numero_compte'=>'^.{1,32}$',
			'b.label_compte'=>'^.{1,255}$',
			'b.subledger_compte'=>'^.{1,32}$',
			'b.subledger_label'=>'^.{1,255}$',
			'b.label_operation'=>'^.{1,255}$',
			//'b.sens'=>'^[D|C]$',
		);
		$this->import_examplevalues_array[$r] = array(
			'b.code_journal'=>"VT",
			'b.journal_label'=>"Sale journal",
			'b.piece_num'=>'123 (!!! use next value not already used)',
			'b.doc_date'=>dol_print_date(dol_now(), "%Y%m%d"),
			'b.numero_compte'=>"707",
			'b.label_compte'=>'Sale',
			'b.subledger_account'=>'',
			'b.subledger_label'=>'',
			'b.doc_ref'=>'My document ABC',
			'b.date_creation'=>dol_print_date(dol_now(), "%Y%m%d"),
			'b.label_operation'=>"Sale of ABC",
			'b.debit'=>"0",
			'b.credit'=>"100",
			'b.lettering_code'=>'ABC',
			'b.date_lettering'=>dol_print_date(dol_now(), "%Y%m%d"),
			'b.date_validated'=>dol_print_date(dol_now(), "%Y%m%d"),
			'b.multicurrency_amount'=>"90 (Necessary if devise is different than EUR)",
			'b.multicurrency_code'=>"US (Necessary if devise is different than EUR)",
		);
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$result = $this->_load_tables('/install/mysql/', 'accounting');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		$sql = array();
		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
