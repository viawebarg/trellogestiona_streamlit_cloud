<?php
/* Copyright (C) 2004-2014 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2007      Patrick Raguin 		<patrick.raguin@gmail.com>
 * Copyright (C) 2024       Frédéric France             <frederic.france@free.fr>
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
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
 *      \file       htdocs/core/class/html.formadmin.class.php
 *      \ingroup    core
 *      \brief      File of class for html functions for admin pages
 */


/**
 *      Class to generate html code for admin pages
 */
class FormAdmin
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string error message
	 */
	public $error;


	/**
	 *  Constructor
	 *
	 *  @param      DoliDB|null      $db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return html select list with available languages (key='en_US', value='United States' for example)
	 *
	 *  @param      string|string[]	$selected       Language pre-selected. Can be an array if $multiselect is 1.
	 *  @param      string			$htmlname       Name of HTML select
	 *  @param      int<0,1>		$showauto       Show 'auto' choice
	 *  @param      string[]		$filter         Array of keys to exclude in list (opposite of $onlykeys)
	 *  @param		int<1,1>|string	$showempty		'1'=Add empty value or 'string to show'
	 *  @param      int<0,1>		$showwarning    Show a warning if language is not complete
	 *  @param		int<0,1>		$disabled		Disable edit of select
	 *  @param		string			$morecss		Add more css styles
	 *  @param      int<0,2>       	$showcode       1=Add language code into label at beginning, 2=Add language code into label at end
	 *  @param		int<0,1>		$forcecombo		Force to use combo box (so no ajax beautify effect)
	 *  @param		int<0,1>		$multiselect	Make the combo a multiselect
	 *  @param		string[]		$onlykeys		Array of language keys to restrict list with the following keys (opposite of $filter). Example array('fr', 'es', ...)
	 *  @param		int<0,1>		$mainlangonly	1=Show only main languages ('fr_FR' no' fr_BE', 'es_ES' not 'es_MX', ...)
	 *  @return		string							Return HTML select string with list of languages
	 */
	public function select_language($selected = '', $htmlname = 'lang_id', $showauto = 0, $filter = array(), $showempty = '', $showwarning = 0, $disabled = 0, $morecss = 'minwidth100', $showcode = 0, $forcecombo = 0, $multiselect = 0, $onlykeys = array(), $mainlangonly = 0)
	{
		// phpcs:enable
		global $langs;

		if (getDolGlobalString('MAIN_DEFAULT_LANGUAGE_FILTER')) {
			if (!is_array($filter)) {
				$filter = array();
			}
			$filter[getDolGlobalString('MAIN_DEFAULT_LANGUAGE_FILTER')] = 1;
		}

		$langs_available = $langs->get_available_languages(DOL_DOCUMENT_ROOT, 12, 0, $mainlangonly);

		// If empty value is not allowed and the language to select is not inside the list of available language and we must find
		// an alternative of the language code to pre-select (to avoid to have first element in list pre-selected).
		if ($selected && empty($showempty)) {
			if (!is_array($selected) && !array_key_exists($selected, $langs_available)) {
				$tmparray = explode('_', $selected);
				if (!empty($tmparray[1])) {
					$selected = getLanguageCodeFromCountryCode($tmparray[1]);
				}
				if (empty($selected)) {
					$selected = $langs->defaultlang;
				}
			} else {
				// If the preselected value is an array, we do not try to find alternative to preselect
			}
		}

		$out = '';

		$out .= '<select '.($multiselect ? 'multiple="multiple" ' : '').'class="flat'.($morecss ? ' '.$morecss : '').'" id="'.$htmlname.'" name="'.$htmlname.($multiselect ? '[]' : '').'"'.($disabled ? ' disabled' : '').'>';
		if ($showempty && !$multiselect) {
			if (is_numeric($showempty)) {
				$out .= '<option value="0"';
			} else {
				$out .= '<option value="-1"';
			}
			if ($selected === '') {
				$out .= ' selected';
			}
			$out .= '>';
			if ($showempty != '1') {
				$out .= $showempty;
			} else {
				$out .= '&nbsp;';
			}
			$out .= '</option>';
		}
		if ($showauto) {
			$out .= '<option value="auto"';
			if ($selected === 'auto') {
				$out .= ' selected';
			}
			$out .= '>'.$langs->trans("AutoDetectLang").'</option>';
		}

		asort($langs_available);	// array('XX' => 'Language (Country)', ...)

		foreach ($langs_available as $key => $value) {
			$valuetoshow = $value;
			if ($showcode == 1) {
				if ($mainlangonly) {
					$valuetoshow = '<span class="opacitymedium">'.preg_replace('/[_-].*$/', '', $key).'</span> - '.$value;
				} else {
					$valuetoshow = '<span class="opacitymedium">'.$key.'</span> - '.$value;
				}
			}
			if ($showcode == 2) {
				if ($mainlangonly) {
					$valuetoshow = $value.' <span class="opacitymedium">('.preg_replace('/[_-].*$/', '', $key).')</span>';
				} else {
					$valuetoshow = $value.' <span class="opacitymedium">('.$key.')</span>';
				}
			}

			$keytouse = $key;
			if ($mainlangonly) {
				$keytouse = preg_replace('/[_-].*$/', '', $key);
			}

			if ($filter && is_array($filter) && array_key_exists($keytouse, $filter)) {
				continue;
			}
			if ($onlykeys && is_array($onlykeys) && !array_key_exists($keytouse, $onlykeys)) {
				continue;
			}

			$valuetoshow = picto_from_langcode($key, 'class="saturatemedium"').' '.$valuetoshow;
			if ((is_string($selected) && (string) $selected == (string) $keytouse) || (is_array($selected) && in_array($keytouse, $selected))) {
				$out .= '<option value="'.$keytouse.'" selected data-html="'.dol_escape_htmltag($valuetoshow).'">'.$valuetoshow.'</option>';
			} else {
				$out .= '<option value="'.$keytouse.'" data-html="'.dol_escape_htmltag($valuetoshow).'">'.$valuetoshow.'</option>';
			}
		}
		$out .= '</select>';

		// Make select dynamic
		if (!$forcecombo) {
			include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
			$out .= ajax_combobox($htmlname);
		}

		return $out;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *    Return list of available menus (eldy_backoffice, ...)
	 *
	 *    @param	string		$selected        Preselected menu value
	 *    @param    string		$htmlname        Name of html select
	 *    @param    string[]	$dirmenuarray    Array of directories to scan
	 *    @param    string		$moreattrib      More attributes on html select tag
	 *    @return	integer|void
	 */
	public function select_menu($selected, $htmlname, $dirmenuarray, $moreattrib = '')
	{
		// phpcs:enable
		global $langs, $conf;

		// Clean parameters


		// Check parameters
		if (!is_array($dirmenuarray)) {
			return -1;
		}

		$menuarray = array();
		foreach ($conf->file->dol_document_root as $dirroot) {
			foreach ($dirmenuarray as $dirtoscan) {
				$dir = $dirroot.$dirtoscan;
				//print $dir.'<br>';
				if (is_dir($dir)) {
					$handle = opendir($dir);
					if (is_resource($handle)) {
						while (($file = readdir($handle)) !== false) {
							if (is_file($dir."/".$file) && substr($file, 0, 1) != '.' && substr($file, 0, 3) != 'CVS' && substr($file, 0, 5) != 'index') {
								if (preg_match('/lib\.php$/i', $file)) {
									continue; // We exclude library files
								}
								if (preg_match('/eldy_(backoffice|frontoffice)\.php$/i', $file)) {
									continue; // We exclude all menu manager files
								}
								if (preg_match('/auguria_(backoffice|frontoffice)\.php$/i', $file)) {
									continue; // We exclude all menu manager files
								}
								if (preg_match('/smartphone_(backoffice|frontoffice)\.php$/i', $file)) {
									continue; // We exclude all menu manager files
								}

								$filetoshow = preg_replace('/\.php$/i', '', $file);
								$filetoshow = ucfirst(preg_replace('/_menu$/i', '', $filetoshow));
								$prefix = '';
								// 0=Recommended, 1=Experimental, 2=Development, 3=Other
								if (preg_match('/^eldy/i', $file)) {
									$prefix = '0';
								} elseif (preg_match('/^smartphone/i', $file)) {
									$prefix = '2';
								} else {
									$prefix = '3';
								}

								$morelabel = '';
								if (preg_match('/^auguria/i', $file)) {
									$morelabel .= ' <span class="opacitymedium">('.$langs->trans("Unstable").')</span>';
								}
								if ($file == $selected) {
									$menuarray[$prefix.'_'.$file] = '<option value="'.$file.'" selected data-html="'.dol_escape_htmltag($filetoshow.$morelabel).'">';
									$menuarray[$prefix.'_'.$file] .= $filetoshow.$morelabel;
									$menuarray[$prefix.'_'.$file] .= '</option>';
								} else {
									$menuarray[$prefix.'_'.$file] = '<option value="'.$file.'" data-html="'.dol_escape_htmltag($filetoshow.$morelabel).'">';
									$menuarray[$prefix.'_'.$file] .= $filetoshow.$morelabel;
									$menuarray[$prefix.'_'.$file] .= '</option>';
								}
							}
						}
						closedir($handle);
					}
				}
			}
		}
		ksort($menuarray);

		// Output combo list of menus
		print '<select class="flat minwidth150" id="'.$htmlname.'" name="'.$htmlname.'"'.($moreattrib ? ' '.$moreattrib : '').'>';
		$oldprefix = '';
		foreach ($menuarray as $key => $val) {
			$tab = explode('_', $key);
			$newprefix = $tab[0];

			if ($newprefix == '1' && (getDolGlobalInt('MAIN_FEATURES_LEVEL') < 1)) {
				continue;
			}
			if ($newprefix == '2' && (getDolGlobalInt('MAIN_FEATURES_LEVEL') < 2)) {
				continue;
			}
			if ($newprefix != $oldprefix) {	// Add separators
				// Affiche titre
				print '<option value="-2" disabled>';
				if ($newprefix == '0') {
					print '-- '.$langs->trans("VersionRecommanded").' --';
				}
				if ($newprefix == '1') {
					print '-- '.$langs->trans("VersionExperimental").' --';
				}
				if ($newprefix == '2') {
					print '-- '.$langs->trans("VersionDevelopment").' --';
				}
				if ($newprefix == '3') {
					print '-- '.$langs->trans("Other").' --';
				}
				print '</option>';
				$oldprefix = $newprefix;
			}

			print $val."\n"; // Show menu entry ($val contains the <option> tags)
		}
		print '</select>';

		print ajax_combobox($htmlname);

		return;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return combo list of available menu families
	 *
	 *  @param	string		$selected        Menu pre-selected
	 *  @param	string		$htmlname        Name of html select
	 *  @param	string[]	$dirmenuarray    Directories to scan
	 *  @return	void
	 */
	public function select_menu_families($selected, $htmlname, $dirmenuarray)
	{
		// phpcs:enable
		global $langs, $conf;

		//$expdevmenu=array('smartphone_backoffice.php','smartphone_frontoffice.php');  // Menu to disable if $conf->global->MAIN_FEATURES_LEVEL is not set
		$expdevmenu = array();

		$menuarray = array();

		foreach ($dirmenuarray as $dirmenu) {
			foreach ($conf->file->dol_document_root as $dirroot) {
				$dir = $dirroot.$dirmenu;
				if (is_dir($dir)) {
					$handle = opendir($dir);
					if (is_resource($handle)) {
						while (($file = readdir($handle)) !== false) {
							if (is_file($dir."/".$file) && substr($file, 0, 1) != '.' && substr($file, 0, 3) != 'CVS') {
								$filelib = preg_replace('/(_backoffice|_frontoffice)?\.php$/i', '', $file);
								if (preg_match('/^index/i', $filelib)) {
									continue;
								}
								if (preg_match('/^default/i', $filelib)) {
									continue;
								}
								if (preg_match('/^empty/i', $filelib)) {
									continue;
								}
								if (preg_match('/\.lib/i', $filelib)) {
									continue;
								}
								if (getDolGlobalInt('MAIN_FEATURES_LEVEL') == 0 && in_array($file, $expdevmenu)) {
									continue;
								}

								$menuarray[$filelib] = 1;
							}
							$menuarray['all'] = 1;
						}
						closedir($handle);
					}
				}
			}
		}

		ksort($menuarray);

		// Show combo list of menu handlers
		print '<select class="flat width150" id="'.$htmlname.'" name="'.$htmlname.'">';
		foreach ($menuarray as $key => $val) {
			$tab = explode('_', $key);
			print '<option value="'.$key.'"';
			if ($key == $selected) {
				print '	selected';
			}
			print '>';
			if ($key == 'all') {
				print $langs->trans("AllMenus");
			} else {
				print $key;
			}
			print '</option>'."\n";
		}
		print '</select>';

		print ajax_combobox($htmlname);
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return a HTML select list of timezones
	 *
	 *  @param	string		$selected        Menu pre-selectionnee
	 *  @param  string		$htmlname        Nom de la zone select
	 *  @return	void
	 */
	public function select_timezone($selected, $htmlname)
	{
		// phpcs:enable
		print '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'">';
		print '<option value="-1">&nbsp;</option>';

		$arraytz = array(
			"Pacific/Midway" => "GMT-11:00",
			"Pacific/Fakaofo" => "GMT-10:00",
			"America/Anchorage" => "GMT-09:00",
			"America/Los_Angeles" => "GMT-08:00",
			"America/Dawson_Creek" => "GMT-07:00",
			"America/Chicago" => "GMT-06:00",
			"America/Bogota" => "GMT-05:00",
			"America/Anguilla" => "GMT-04:00",
			"America/Araguaina" => "GMT-03:00",
			"America/Noronha" => "GMT-02:00",
			"Atlantic/Azores" => "GMT-01:00",
			"Africa/Abidjan" => "GMT+00:00",
			"Europe/Paris" => "GMT+01:00",
			"Europe/Helsinki" => "GMT+02:00",
			"Europe/Moscow" => "GMT+03:00",
			"Asia/Dubai" => "GMT+04:00",
			"Asia/Karachi" => "GMT+05:00",
			"Indian/Chagos" => "GMT+06:00",
			"Asia/Jakarta" => "GMT+07:00",
			"Asia/Hong_Kong" => "GMT+08:00",
			"Asia/Tokyo" => "GMT+09:00",
			"Australia/Sydney" => "GMT+10:00",
			"Pacific/Noumea" => "GMT+11:00",
			"Pacific/Auckland" => "GMT+12:00",
			"Pacific/Enderbury" => "GMT+13:00"
		);
		foreach ($arraytz as $lib => $gmt) {
			print '<option value="'.$lib.'"';
			if ($selected == $lib || $selected == $gmt) {
				print ' selected';
			}
			print '>'.$gmt.'</option>'."\n";
		}
		print '</select>';
	}



	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return html select list with available languages (key='en_US', value='United States' for example)
	 *
	 *  @param      string	$selected       Paper format pre-selected
	 *  @param      string	$htmlname       Name of HTML select field
	 *  @param		string	$filter			Value to filter on code
	 *  @param		int		$showempty		Add empty value
	 * 	@param		int		$forcecombo		Force to load all values and output a standard combobox (with no beautification)
	 *  @return		string					Return HTML output
	 */
	public function select_paper_format($selected = '', $htmlname = 'paperformat_id', $filter = '', $showempty = 0, $forcecombo = 0)
	{
		// phpcs:enable
		global $langs;

		$langs->load("dict");

		$sql = "SELECT code, label, width, height, unit";
		$sql .= " FROM ".$this->db->prefix()."c_paper_format";
		$sql .= " WHERE active=1";
		if ($filter) {
			$sql .= " AND code LIKE '%".$this->db->escape($filter)."%'";
		}

		$paperformat = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				$unitKey = $langs->trans('SizeUnit'.$obj->unit);

				$paperformat[$obj->code] = $langs->trans('PaperFormat'.strtoupper($obj->code)).' - '.round($obj->width).'x'.round($obj->height).' '.($unitKey == 'SizeUnit'.$obj->unit ? $obj->unit : $unitKey);

				$i++;
			}
		} else {
			dol_print_error($this->db);
			return '';
		}
		$out = '';

		$out .= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'">';
		if ($showempty) {
			$out .= '<option value=""';
			if ($selected == '') {
				$out .= ' selected';
			}
			$out .= '>&nbsp;</option>';
		}
		foreach ($paperformat as $key => $value) {
			if ($selected == $key) {
				$out .= '<option value="'.$key.'" selected>'.$value.'</option>';
			} else {
				$out .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}
		$out .= '</select>';

		if (!$forcecombo) {
			include_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
			$out .= ajax_combobox($htmlname);
		}

		return $out;
	}


	/**
	 * Function to show the combo select to chose a type of field (varchar, int, email, ...)
	 *
	 * @param	string		$htmlname				Name of HTML select component
	 * @param	string		$type					Type preselected
	 * @param	array<string,string[]>	$typewecanchangeinto	Array of possible switch combination from 1 type to another one. This will grey not possible combinations.
	 * @return 	string							The combo HTML select component
	 */
	public function selectTypeOfFields($htmlname, $type, $typewecanchangeinto = array())
	{
		$type2label = ExtraFields::getListOfTypesLabels();

		$out = '';

		$out .= '<!-- combo with type of extrafields -->'."\n";
		$out .= '<select class="flat type" id="'.$htmlname.'" name="'.$htmlname.'">';
		foreach ($type2label as $key => $val) {
			$selected = '';
			if ($key == $type) {
				$selected = ' selected="selected"';
			}

			// Set $valhtml with the picto for the type
			$valhtml = ($key ? getPictoForType($key) : '').$val;

			if (empty($typewecanchangeinto) || in_array($key, $typewecanchangeinto[$type])) {
				$out .= '<option value="'.$key.'"'.$selected.' data-html="'.dol_escape_htmltag($valhtml).'">'.($val ? $val : '&nbsp;').'</option>';
			} else {
				$out .= '<option value="'.$key.'" disabled="disabled"'.$selected.' data-html="'.dol_escape_htmltag($valhtml).'">'.($val ? $val : '&nbsp;').'</option>';
			}
		}
		$out .= '</select>';
		$out .= ajax_combobox('type');

		return $out;
	}
}
