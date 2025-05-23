<?php

/* Copyright (C) 2004       Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2010       Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2019       Alexandre Spangaro      <aspangaro@open-dsi.fr>
 * Copyright (C) 2019-2024  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
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
 * or see https://www.gnu.org/
 */

/**
 *      \file       htdocs/core/modules/societe/mod_codecompta_digitaria.php
 *      \ingroup    societe
 *      \brief      File of class to manage accountancy code of thirdparties with Digitaria rules
 */
require_once DOL_DOCUMENT_ROOT.'/core/modules/societe/modules_societe.class.php';


/**
 *		Class to manage accountancy code of thirdparties with Digitaria rules
 */
class mod_codecompta_digitaria extends ModeleAccountancyCode
{
	/**
	 * @var string model name
	 */
	public $name = 'Digitaria';

	/**
	 * Dolibarr version of the loaded document
	 * @var string Version, possible values are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'''|'development'|'dolibarr'|'experimental'
	 */
	public $version = 'dolibarr'; // 'development', 'experimental', 'dolibarr'

	/**
	 * @var string Prefix customer accountancy code
	 */
	public $prefixcustomeraccountancycode;

	/**
	 * @var string Prefix supplier accountancy code
	 */
	public $prefixsupplieraccountancycode;

	/**
	 * @var int
	 */
	public $position = 30;

	/**
	 * @var string
	 */
	public $code;
	/**
	 * @var string
	 */
	public $customeraccountancycodecharacternumber;
	/**
	 * @var string
	 */
	public $supplieraccountancycodecharacternumber;


	/**
	 * 	Constructor
	 */
	public function __construct()
	{
		global $conf, $langs;
		if (!isset($conf->global->COMPANY_DIGITARIA_MASK_CUSTOMER) || trim($conf->global->COMPANY_DIGITARIA_MASK_CUSTOMER) == '') {
			$conf->global->COMPANY_DIGITARIA_MASK_CUSTOMER = '411';
		}
		if (!isset($conf->global->COMPANY_DIGITARIA_MASK_SUPPLIER) || trim($conf->global->COMPANY_DIGITARIA_MASK_SUPPLIER) == '') {
			$conf->global->COMPANY_DIGITARIA_MASK_SUPPLIER = '401';
		}
		$this->prefixcustomeraccountancycode = getDolGlobalString('COMPANY_DIGITARIA_MASK_CUSTOMER');
		$this->prefixsupplieraccountancycode = getDolGlobalString('COMPANY_DIGITARIA_MASK_SUPPLIER');

		if (!isset($conf->global->COMPANY_DIGITARIA_MASK_NBCHARACTER_CUSTOMER) || trim($conf->global->COMPANY_DIGITARIA_MASK_NBCHARACTER_CUSTOMER) == '') {
			$conf->global->COMPANY_DIGITARIA_MASK_NBCHARACTER_CUSTOMER = '5';
		}
		if (!isset($conf->global->COMPANY_DIGITARIA_MASK_NBCHARACTER_SUPPLIER) || trim($conf->global->COMPANY_DIGITARIA_MASK_NBCHARACTER_SUPPLIER) == '') {
			$conf->global->COMPANY_DIGITARIA_MASK_NBCHARACTER_SUPPLIER = '5';
		}
		$this->customeraccountancycodecharacternumber = getDolGlobalString('COMPANY_DIGITARIA_MASK_NBCHARACTER_CUSTOMER');
		$this->supplieraccountancycodecharacternumber = getDolGlobalString('COMPANY_DIGITARIA_MASK_NBCHARACTER_SUPPLIER');
	}

	/**
	 * Return description of module
	 *
	 * @param	Translate	$langs	Object langs
	 * @return 	string      		Description of module
	 */
	public function info($langs)
	{
		global $conf, $form;

		$tooltip = '';
		$texte = '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte .= '<input type="hidden" name="token" value="'.newToken().'">';
		$texte .= '<input type="hidden" name="page_y" value="">';
		$texte .= '<input type="hidden" name="action" value="setModuleOptions">';
		$texte .= '<input type="hidden" name="param1" value="COMPANY_DIGITARIA_MASK_SUPPLIER">';
		$texte .= '<input type="hidden" name="param2" value="COMPANY_DIGITARIA_MASK_CUSTOMER">';
		$texte .= '<input type="hidden" name="param3" value="COMPANY_DIGITARIA_MASK_NBCHARACTER_SUPPLIER">';
		$texte .= '<input type="hidden" name="param4" value="COMPANY_DIGITARIA_MASK_NBCHARACTER_CUSTOMER">';
		$texte .= '<input type="hidden" name="param5" value="COMPANY_DIGITARIA_CLEAN_WORDS">';
		$texte .= '<table class="nobordernopadding centpercent">';
		$s1 = $form->textwithpicto('<input type="text" class="flat" size="4" name="value1" value="' . getDolGlobalString('COMPANY_DIGITARIA_MASK_SUPPLIER').'">', $tooltip, 1, 'help', 'valignmiddle', 0, 3, $this->name);
		$s2 = $form->textwithpicto('<input type="text" class="flat" size="4" name="value2" value="' . getDolGlobalString('COMPANY_DIGITARIA_MASK_CUSTOMER').'">', $tooltip, 1, 'help', 'valignmiddle', 0, 3, $this->name);
		$s3 = $form->textwithpicto('<input type="text" class="flat" size="2" name="value3" value="' . getDolGlobalString('COMPANY_DIGITARIA_MASK_NBCHARACTER_SUPPLIER').'">', $tooltip, 1, 'help', 'valignmiddle', 0, 3, $this->name);
		$s4 = $form->textwithpicto('<input type="text" class="flat" size="2" name="value4" value="' . getDolGlobalString('COMPANY_DIGITARIA_MASK_NBCHARACTER_CUSTOMER').'">', $tooltip, 1, 'help', 'valignmiddle', 0, 3, $this->name);
		$texte .= '<tr><td>';
		// trans remove html entities
		$texte .= $langs->trans("ModuleCompanyCodeCustomer".$this->name, '{s2}', '{s4}')."<br>\n";
		$texte .= $langs->trans("ModuleCompanyCodeSupplier".$this->name, '{s1}', '{s3}')."<br>\n";
		$texte = str_replace(array('{s1}', '{s2}', '{s3}', '{s4}'), array($s1, $s2, $s3, $s4), $texte);
		$texte .= "<br>\n";
		// Remove special char if COMPANY_DIGITARIA_REMOVE_SPECIAL is set to 1 or not set (default)
		if (!isset($conf->global->COMPANY_DIGITARIA_REMOVE_SPECIAL) || !empty($conf->global->COMPANY_DIGITARIA_REMOVE_SPECIAL)) {
			$texte .= $langs->trans('RemoveSpecialChars').' = '.yn(1)."<br>\n";
		}
		// Apply a regex replacement pattern on code if COMPANY_DIGITARIA_CLEAN_REGEX is set. Value must be a regex with parenthesis. The part into parenthesis is kept, the rest removed.
		if (getDolGlobalString('COMPANY_DIGITARIA_CLEAN_REGEX')) {
			$texte .= $langs->trans('COMPANY_DIGITARIA_CLEAN_REGEX').' = ' . getDolGlobalString('COMPANY_DIGITARIA_CLEAN_REGEX')."<br>\n";
		}
		// If value is not unique (if COMPANY_DIGITARIA_UNIQUE_CODE is set to 0), we show this
		if (!getDolGlobalString('COMPANY_DIGITARIA_UNIQUE_CODE', '1')) {
			$texte .= $langs->trans('DuplicateForbidden').' = '.yn(0)."<br>\n";
		}
		$texte .= '</td>';
		$texte .= '<td class="right"><input type="submit" class="button button-edit reposition smallpaddingimp" name="modify" value="'.$langs->trans("Modify").'"></td>';
		$texte .= '</tr>';

		$texte .= '<tr><td>';
		$texte .= "<br>\n";

		$texthelp  = $langs->trans("RemoveSpecialWordsHelp");
		$texttitle = $langs->trans("RemoveSpecialWords");

		$texte .= $form->textwithpicto($texttitle, $texthelp, 1, 'help', '', 1);
		$texte .= "<br>\n";
		$texte .= '<textarea class="flat textareafordir" spellcheck="false" cols="60" name="value5">';
		if (getDolGlobalString('COMPANY_DIGITARIA_CLEAN_WORDS')) {
			$texte .= $conf->global->COMPANY_DIGITARIA_CLEAN_WORDS;
		}
		$texte .= '</textarea>';
		$texte .= '</tr></table>';

		$texte .= '</form>';

		return $texte;
	}

	/**
	 * Return an example of result returned by getNextValue
	 *
	 * @param	?Translate		$langs		Object langs
	 * @param	Societe|string	$objsoc		Object thirdparty
	 * @param	int<-1,2>		$type		Type of third party (1:customer, 2:supplier, -1:autodetect)
	 * @return	string						Return string example
	 */
	public function getExample($langs = null, $objsoc = '', $type = -1)
	{
		global $conf, $mysoc;
		if (!$langs instanceof Translate) {
			$langs = $GLOBALS['langs'];
			'@phan-var-force Translate $langs';
		}

		$s = $langs->trans("ThirdPartyName").": ".$mysoc->name;
		$s .= "<br>\n";

		if (!isset($conf->global->COMPANY_DIGITARIA_REMOVE_SPECIAL)) {
			$thirdpartylabelexample = (string) preg_replace('/([^a-z0-9])/i', '', $mysoc->name);
		} else {
			$thirdpartylabelexample = '';
		}
		$s .= "<br>\n";
		$s .= $this->prefixcustomeraccountancycode.strtoupper(substr($thirdpartylabelexample, 0, (int) $this->customeraccountancycodecharacternumber));
		$s .= "<br>\n";
		$s .= $this->prefixsupplieraccountancycode.strtoupper(substr($thirdpartylabelexample, 0, (int) $this->supplieraccountancycodecharacternumber));
		return $s;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Set accountancy account code for a third party into this->code
	 *
	 *  @param	DoliDB		$db				Database handler
	 *  @param  ?Societe	$societe		Third party object
	 *  @param  'customer'|'supplier'|''	$type	'customer' or 'supplier'
	 *  @return	int							>=0 if OK, <0 if KO
	 */
	public function get_code($db, $societe, $type = '')
	{
		// phpcs:enable
		global $conf;
		$i = 0;
		$this->code = '';

		$disponibility = 0;

		if (is_object($societe)) {
			dol_syslog("mod_codecompta_digitaria::get_code search code for type=".$type." & company=".(!empty($societe->name) ? $societe->name : ''));

			if ($type == 'supplier') {
				$codetouse = (string) $societe->name;
				$prefix = $this->prefixsupplieraccountancycode;
				$width = $this->supplieraccountancycodecharacternumber;
			} elseif ($type == 'customer') {
				$codetouse = (string) $societe->name;
				$prefix = $this->prefixcustomeraccountancycode;
				$width = $this->customeraccountancycodecharacternumber;
			} else {
				$this->error = 'Bad value for parameter type';
				return -1;
			}

			// Clean declared words
			if (getDolGlobalString('COMPANY_DIGITARIA_CLEAN_WORDS')) {
				$cleanWords = explode(";", getDolGlobalString('COMPANY_DIGITARIA_CLEAN_WORDS'));
				$codetouse = str_replace($cleanWords, "", $codetouse);
			}
			// Remove special char if COMPANY_DIGITARIA_REMOVE_SPECIAL is set to 1 or not set (default)
			if (!isset($conf->global->COMPANY_DIGITARIA_REMOVE_SPECIAL) || getDolGlobalString('COMPANY_DIGITARIA_REMOVE_SPECIAL')) {
				$codetouse = (string) preg_replace('/([^a-z0-9])/i', '', $codetouse);
			}
			// Apply a regex replacement pattern on code if COMPANY_DIGITARIA_CLEAN_REGEX is set. Value must be a regex with parenthesis. The part into parenthesis is kept, the rest removed.
			if (getDolGlobalString('COMPANY_DIGITARIA_CLEAN_REGEX')) {	// Example: $conf->global->COMPANY_DIGITARIA_CLEAN_REGEX='^..(..)..';
				$codetouse = (string) preg_replace('/' . getDolGlobalString('COMPANY_DIGITARIA_CLEAN_REGEX').'/', '\1\2\3', $codetouse);
			}

			$this->code = $prefix.strtoupper(substr($codetouse, 0, (int) $width));
			dol_syslog("mod_codecompta_digitaria::get_code search code proposed=".$this->code, LOG_DEBUG);

			// Unique index on code if COMPANY_DIGITARIA_UNIQUE_CODE is set to 1 or not set (default)
			if (getDolGlobalString('COMPANY_DIGITARIA_UNIQUE_CODE', '1')) {
				$disponibility = $this->checkIfAccountancyCodeIsAlreadyUsed($db, $this->code, $type);

				while ($disponibility != 0 && $i < 1000) {
					$widthsupplier = $this->supplieraccountancycodecharacternumber;
					$widthcustomer = $this->customeraccountancycodecharacternumber;

					if ($i <= 9) {
						$a = 1;
					} elseif ($i <= 99) {  // Also >= 10
						$a = 2;
					} else {  // ($i >= 100 && $i <= 999) {
						$a = 3;
					}

					if ($type == 'supplier') {
						$this->code = $prefix.strtoupper(substr($codetouse, 0, (int) $widthsupplier - $a)).$i;
					} elseif ($type == 'customer') {
						$this->code = $prefix.strtoupper(substr($codetouse, 0, (int) $widthcustomer - $a)).$i;
					}
					$disponibility = $this->checkIfAccountancyCodeIsAlreadyUsed($db, $this->code, $type);

					$i++;
				}
			} // else { $disponibility = 0; /* Already set */ }
		}

		if ($disponibility == 0) {
			return 0; // return ok
		} else {
			return -1; // return ko
		}
	}

	/**
	 *  Check accountancy account code for a third party into this->code
	 *
	 *  @param	DoliDB	$db             Database handler
	 *  @param  string	$code           Code of third party
	 *  @param  string	$type			'customer' or 'supplier'
	 *  @return	int						>=0 if OK, <0 if KO
	 */
	public function checkIfAccountancyCodeIsAlreadyUsed($db, $code, $type = '')
	{
		if ($type == 'supplier') {
			if (getDolGlobalString('MAIN_COMPANY_PERENTITY_SHARED')) {
				$typethirdparty = 'accountancy_code_supplier';
			} else {
				$typethirdparty = 'code_compta_fournisseur';
			}
		} elseif ($type == 'customer') {
			if (getDolGlobalString('MAIN_COMPANY_PERENTITY_SHARED')) {
				$typethirdparty = 'accountancy_code_customer';
			} else {
				$typethirdparty = 'code_compta';
			}
		} else {
			$this->error = 'Bad value for parameter type';
			return -1;
		}

		if (getDolGlobalString('MAIN_COMPANY_PERENTITY_SHARED')) {
			$sql = "SELECT " . $typethirdparty . " FROM " . MAIN_DB_PREFIX . "societe_perentity";
			$sql .= " WHERE " . $typethirdparty . " = '" . $db->escape($code) . "'";
		} else {
			$sql = "SELECT " . $typethirdparty . " FROM " . MAIN_DB_PREFIX . "societe";
			$sql .= " WHERE " . $typethirdparty . " = '" . $db->escape($code) . "'";
		}
		$sql .= " AND entity IN (".getEntity('societe').")";

		$resql = $db->query($sql);
		if ($resql) {
			if ($db->num_rows($resql) == 0) {
				dol_syslog("mod_codecompta_digitaria::checkIfAccountancyCodeIsAlreadyUsed '".$code."' available");
				return 0; // Available
			} else {
				dol_syslog("mod_codecompta_digitaria::checkIfAccountancyCodeIsAlreadyUsed '".$code."' not available");
				return -1; // Not available
			}
		} else {
			$this->error = $db->error()." sql=".$sql;
			return -2; // Error
		}
	}
}
