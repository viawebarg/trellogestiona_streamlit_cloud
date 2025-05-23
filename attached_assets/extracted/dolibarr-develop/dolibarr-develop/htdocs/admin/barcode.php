<?php
/* Copyright (C) 2003-2004	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@inodbox.com>
 * Copyright (C) 2011-2013	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France         <frederic.france@free.fr>
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
 *	\file       htdocs/admin/barcode.php
 *	\ingroup    barcode
 *	\brief      Page to setup barcode module
 */

// Load Dolibarr environment
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbarcode.class.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// Load translation files required by the page
$langs->load("admin");

// Security Check Access
if (!$user->admin) {
	accessforbidden();
}

// Get Parameters
$action = GETPOST('action', 'aZ09');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php


/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';

if ($action == 'setbarcodeproducton') {
	$barcodenumberingmodule = GETPOST('value', 'alpha');
	$res = dolibarr_set_const($db, "BARCODE_PRODUCT_ADDON_NUM", $barcodenumberingmodule, 'chaine', 0, '', $conf->entity);
	if ($barcodenumberingmodule == 'mod_barcode_product_standard' && !getDolGlobalString('BARCODE_STANDARD_PRODUCT_MASK')) {
		$res = dolibarr_set_const($db, "BARCODE_STANDARD_PRODUCT_MASK", '04{0000000000}', 'chaine', 0, '', $conf->entity);
	}
} elseif ($action == 'setbarcodeproductoff') {
	$res = dolibarr_del_const($db, "BARCODE_PRODUCT_ADDON_NUM", $conf->entity);
}

if ($action == 'setbarcodethirdpartyon') {
	$barcodenumberingmodule = GETPOST('value', 'alpha');
	$res = dolibarr_set_const($db, "BARCODE_THIRDPARTY_ADDON_NUM", $barcodenumberingmodule, 'chaine', 0, '', $conf->entity);
	if ($barcodenumberingmodule == 'mod_barcode_thirdparty_standard' && !getDolGlobalString('BARCODE_STANDARD_THIRDPARTY_MASK')) {
		$res = dolibarr_set_const($db, "BARCODE_STANDARD_THIRDPARTY_MASK", '04{0000000000}', 'chaine', 0, '', $conf->entity);
	}
} elseif ($action == 'setbarcodethirdpartyoff') {
	$res = dolibarr_del_const($db, "BARCODE_THIRDPARTY_ADDON_NUM", $conf->entity);
}

if ($action == 'setcoder') {
	$coder = GETPOST('coder', 'alpha');
	$code_id = GETPOSTINT('code_id');
	$sqlp = "UPDATE ".MAIN_DB_PREFIX."c_barcode_type";
	$sqlp .= " SET coder = '".$db->escape($coder)."'";
	$sqlp .= " WHERE rowid = ".((int) $code_id);
	$sqlp .= " AND entity = ".$conf->entity;

	$resql = $db->query($sqlp);
	if (!$resql) {
		dol_print_error($db);
	}
} elseif ($action == 'update') {
	$location = GETPOST('GENBARCODE_LOCATION', 'alpha');
	$res = dolibarr_set_const($db, "GENBARCODE_LOCATION", $location, 'chaine', 0, '', $conf->entity);
	$coder_id = GETPOST('PRODUIT_DEFAULT_BARCODE_TYPE', 'alpha');
	$res = dolibarr_set_const($db, "PRODUIT_DEFAULT_BARCODE_TYPE", $coder_id, 'chaine', 0, '', $conf->entity);
	$coder_id = GETPOST('GENBARCODE_BARCODETYPE_THIRDPARTY', 'alpha');
	$res = dolibarr_set_const($db, "GENBARCODE_BARCODETYPE_THIRDPARTY", $coder_id, 'chaine', 0, '', $conf->entity);

	if ($res > 0) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
} elseif ($action == 'updateengine') {
	$sql = "SELECT rowid, coder";
	$sql .= " FROM ".MAIN_DB_PREFIX."c_barcode_type";
	$sql .= " WHERE entity = ".$conf->entity;
	$sql .= " ORDER BY code";

	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$i = 0;

		while ($i < $num) {
			$obj = $db->fetch_object($resql);

			if (GETPOST('coder'.$obj->rowid, 'alpha')) {
				$coder = GETPOST('coder'.$obj->rowid, 'alpha');
				$code_id = $obj->rowid;

				$sqlp = "UPDATE ".MAIN_DB_PREFIX."c_barcode_type";
				$sqlp .= " SET coder = '".$db->escape($coder)."'";
				$sqlp .= " WHERE rowid = ".((int) $code_id);
				$sqlp .= " AND entity = ".$conf->entity;

				$upsql = $db->query($sqlp);
				if (!$upsql) {
					dol_print_error($db);
				}
			}

			$i++;
		}
	}
}


/*
 * View
 */

$form = new Form($db);
$formbarcode = new FormBarCode($db);

$help_url = 'EN:Module_Barcode|FR:Module_Codes_Barre|ES:Módulo Código de barra|DE:Modul_Barcode';
llxHeader('', $langs->trans("BarcodeSetup"), $help_url, '', 0, 0, '', '', '', 'mod-admin page-barcode');

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("BarcodeSetup"), $linkback, 'title_setup');

// Detect bar codes modules
$barcodelist = array();

clearstatcache();


// Scan list of all barcode included provided by external modules
$dirbarcode = array_merge(array("/core/modules/barcode/doc/"), $conf->modules_parts['barcode']);

foreach ($dirbarcode as $reldir) {
	$dir = dol_buildpath($reldir);
	$newdir = dol_osencode($dir);

	// Check if directory exists (we do not use dol_is_dir to avoid loading files.lib.php)
	if (!is_dir($newdir)) {
		continue;
	}

	$handle = @opendir($newdir);
	if (is_resource($handle)) {
		while (($file = readdir($handle)) !== false) {
			if (substr($file, 0, 1) != '.' && substr($file, 0, 3) != 'CVS') {
				if (is_readable($newdir.$file)) {
					if (preg_match('/(.*)\.modules\.php$/i', $file, $reg)) {
						$filebis = $reg[1];

						// Loading encoding class
						require_once $newdir.$file;
						$classname = "mod".ucfirst($filebis);
						$module = new $classname($db);

						'@phan-var-force ModeleBarCode $module';

						// Show modules according to features level
						if ($module->version == 'development' && getDolGlobalInt('MAIN_FEATURES_LEVEL') < 2) {
							continue;
						}
						if ($module->version == 'experimental' && getDolGlobalInt('MAIN_FEATURES_LEVEL') < 1) {
							continue;
						}

						if ($module->isEnabled()) {
							$barcodelist[$filebis] = $module->info($langs);
						}
					}
				}
			}
		}
	}
}
'@phan-var-force array<string,string> $barcodelist';


// Select barcode numbering module
if (isModEnabled('product')) {
	print load_fiche_titre($langs->trans("BarCodeNumberManager")." (".$langs->trans("Product").")", '', '');

	print '<div class="div-table-responsive-no-min">';
	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre">';
	print '<td width="140">'.$langs->trans("Name").'</td>';
	print '<td>'.$langs->trans("Description").'</td>';
	print '<td>'.$langs->trans("Example").'</td>';
	print '<td class="center" width="80">'.$langs->trans("Status").'</td>';
	print '<td class="center" width="60">'.$langs->trans("ShortInfo").'</td>';
	print "</tr>\n";

	$dirbarcodenum = array_merge(array('/core/modules/barcode/'), $conf->modules_parts['barcode']);

	foreach ($dirbarcodenum as $dirroot) {
		$dir = dol_buildpath($dirroot, 0);

		$handle = @opendir($dir);
		if (is_resource($handle)) {
			while (($file = readdir($handle)) !== false) {
				if (preg_match('/^mod_barcode_product_.*php$/', $file)) {
					$file = substr($file, 0, dol_strlen($file) - 4);

					try {
						dol_include_once($dirroot.$file.'.php');
					} catch (Exception $e) {
						dol_syslog($e->getMessage(), LOG_ERR);
					}

					$modBarCode = new $file();
					'@phan-var-force ModeleNumRefBarCode $modBarCode';

					print '<tr class="oddeven">';
					print '<td>'.(isset($modBarCode->name) ? $modBarCode->name : $modBarCode->nom)."</td><td>\n";
					print $modBarCode->info($langs);
					print '</td>';
					print '<td class="nowrap">'.$modBarCode->getExample($langs)."</td>\n";

					if (getDolGlobalString('BARCODE_PRODUCT_ADDON_NUM') == "$file") {
						print '<td class="center"><a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=setbarcodeproductoff&token='.newToken().'&amp;value='.urlencode($file).'">';
						print img_picto($langs->trans("Activated"), 'switch_on');
						print '</a></td>';
					} else {
						print '<td class="center"><a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=setbarcodeproducton&token='.newToken().'&amp;value='.urlencode($file).'">';
						print img_picto($langs->trans("Disabled"), 'switch_off');
						print '</a></td>';
					}
					print '<td class="center">';
					$s = $modBarCode->getToolTip($langs, null, -1);
					print $form->textwithpicto('', $s, 1);
					print '</td>';
					print "</tr>\n";
				}
			}
			closedir($handle);
		}
	}
	print "</table>\n";
	print '</div>';
}

// Select barcode numbering module
if (isModEnabled('societe')) {
	print load_fiche_titre($langs->trans("BarCodeNumberManager")." (".$langs->trans("ThirdParty").")", '', '');

	print '<div class="div-table-responsive-no-min">';
	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre">';
	print '<td width="140">'.$langs->trans("Name").'</td>';
	print '<td>'.$langs->trans("Description").'</td>';
	print '<td>'.$langs->trans("Example").'</td>';
	print '<td class="center" width="80">'.$langs->trans("Status").'</td>';
	print '<td class="center" width="60">'.$langs->trans("ShortInfo").'</td>';
	print "</tr>\n";

	$dirbarcodenum = array_merge(array('/core/modules/barcode/'), $conf->modules_parts['barcode']);

	foreach ($dirbarcodenum as $dirroot) {
		$dir = dol_buildpath($dirroot, 0);

		$handle = @opendir($dir);
		if (is_resource($handle)) {
			while (($file = readdir($handle)) !== false) {
				if (preg_match('/^mod_barcode_thirdparty_.*php$/', $file)) {
					$file = substr($file, 0, dol_strlen($file) - 4);

					try {
						dol_include_once($dirroot.$file.'.php');
					} catch (Exception $e) {
						dol_syslog($e->getMessage(), LOG_ERR);
					}

					$modBarCode = new $file();

					'@phan-var-force ModeleNumRefBarCode $modBarCode';

					print '<tr class="oddeven">';
					print '<td>'.(isset($modBarCode->name) ? $modBarCode->name : $modBarCode->nom)."</td><td>\n";
					print $modBarCode->info($langs);
					print '</td>';
					print '<td class="nowrap">'.$modBarCode->getExample($langs)."</td>\n";

					if (getDolGlobalString('BARCODE_THIRDPARTY_ADDON_NUM') && $conf->global->BARCODE_THIRDPARTY_ADDON_NUM == "$file") {
						print '<td class="center"><a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=setbarcodethirdpartyoff&token='.newToken().'&amp;value='.urlencode($file).'">';
						print img_picto($langs->trans("Activated"), 'switch_on');
						print '</a></td>';
					} else {
						print '<td class="center"><a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=setbarcodethirdpartyon&token='.newToken().'&amp;value='.urlencode($file).'">';
						print img_picto($langs->trans("Disabled"), 'switch_off');
						print '</a></td>';
					}
					print '<td class="center">';
					$s = $modBarCode->getToolTip($langs, null, -1);
					print $form->textwithpicto('', $s, 1);
					print '</td>';
					print "</tr>\n";
				}
			}
			closedir($handle);
		}
	}
	print "</table>\n";
	print '</div>';
}

/*
 *  CHOIX ENCODAGE
 */

print '<br>';
print load_fiche_titre($langs->trans("BarcodeEncodeModule"), '', '');

if (empty($conf->use_javascript_ajax)) {
	print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST" id="form_engine">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="updateengine">';
}

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td width="200" class="center">'.$langs->trans("Example").'</td>';
print '<td class="center" width="60">'.$langs->trans("CodeBarGenerator").'</td>';
print "</tr>\n";

$sql = "SELECT rowid, code as encoding, libelle as label, coder, example";
$sql .= " FROM ".MAIN_DB_PREFIX."c_barcode_type";
$sql .= " WHERE entity = ".$conf->entity;
$sql .= " ORDER BY code";

dol_syslog("admin/barcode.php", LOG_DEBUG);
$resql = $db->query($sql);
if ($resql) {
	$num = $db->num_rows($resql);
	$i = 0;

	while ($i < $num) {
		$obj = $db->fetch_object($resql);

		print '<tr class="oddeven">';
		print '<td width="100">';
		print dol_escape_htmltag($obj->label);
		print "</td><td>\n";
		print $langs->trans('BarcodeDesc'.$obj->encoding);
		//print "L'EAN se compose de 8 characters, 7 chiffres plus une cle de verification.<br>";
		//print "L'utilisation des symbologies EAN8 impose la souscription et l'abonnement aupres d'organismes comme GENCOD.<br>";
		//print "Codes numeriques utilises exclusivement a l'identification des produits susceptibles d'etre vendus au grand public.";
		print '</td>';

		// Show example
		print '<td class="center">';
		if ($obj->coder && $obj->coder != -1) {
			$result = 0;

			foreach ($dirbarcode as $reldir) {
				$dir = dol_buildpath($reldir, 0);
				$newdir = dol_osencode($dir);

				// Check if directory exists (we do not use dol_is_dir to avoid loading files.lib.php)
				if (!is_dir($newdir)) {
					continue;
				}

				$result = @include_once $newdir.$obj->coder.'.modules.php';
				if ($result) {
					break;
				}
			}
			if ($result) {
				$classname = "mod".ucfirst($obj->coder);
				if (class_exists($classname)) {
					$module = new $classname($db);
					'@phan-var-force ModeleBarCode $module';
					if ($module->encodingIsSupported($obj->encoding)) {
						// Build barcode on disk (not used, this is done to make debug easier)
						$result = $module->writeBarCode($obj->example, $obj->encoding, 'Y');
						// Generate on the fly and output barcode with generator
						$url = DOL_URL_ROOT.'/viewimage.php?modulepart=barcode&amp;generator='.urlencode($obj->coder).'&amp;code='.urlencode($obj->example).'&amp;encoding='.urlencode($obj->encoding);
						//print $url;
						print '<img src="'.$url.'" title="'.$obj->example.'" border="0">';
					} else {
						print $langs->trans("FormatNotSupportedByGenerator");
					}
				} else {
					print 'ErrorClassNotFoundInModule '.$classname.' '.$obj->coder;
				}
			}
		} else {
			print '<span class="opacitymedium">'.$langs->trans("ChooseABarCode").'</span>';
		}
		print '</td>';

		print '<td class="center">';
		print $formbarcode->setBarcodeEncoder($obj->coder, $barcodelist, $obj->rowid, 'form'.$i);
		print "</td></tr>\n";

		$i++;
	}
}
print "</table>\n";
print '</div>';

if (empty($conf->use_javascript_ajax)) {
	print $form->buttonsSaveCancel("Save", '');
}

print "<br>";


/*
 * Other options
 */
print load_fiche_titre($langs->trans("OtherOptions"), '', '');

print "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
print '<input type="hidden" name="token" value="'.newToken().'">';
print "<input type=\"hidden\" name=\"action\" value=\"update\">";

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td width="60" class="center"></td>';
print '<td>&nbsp;</td>';
print '</tr>';

// Chemin du binaire genbarcode sous linux
if (!isset($_SERVER['WINDIR'])) {
	print '<tr class="oddeven">';
	print '<td>'.$langs->trans("GenbarcodeLocation").'</td>';
	print '<td width="60" class="center">';
	print '<input type="text" size="40" name="GENBARCODE_LOCATION" value="'.getDolGlobalString('GENBARCODE_LOCATION').'">';
	if (getDolGlobalString('GENBARCODE_LOCATION') && !@file_exists($conf->global->GENBARCODE_LOCATION)) {
		$langs->load("errors");
		print '<br><span class="error">'.$langs->trans("ErrorFileNotFound", getDolGlobalString('GENBARCODE_LOCATION')).'</span>';
	}
	print '</td>';
	print '<td>&nbsp;</td>';
	print '</tr>';
}

// Module products
if (isModEnabled('product')) {
	print '<tr class="oddeven">';
	print '<td>'.$langs->trans("SetDefaultBarcodeTypeProducts").'</td>';
	print '<td width="60" class="right">';
	print $formbarcode->selectBarcodeType(getDolGlobalInt('PRODUIT_DEFAULT_BARCODE_TYPE'), "PRODUIT_DEFAULT_BARCODE_TYPE", 1);
	print '</td>';
	print '<td>&nbsp;</td>';
	print '</tr>';
}

// Module thirdparty
if (isModEnabled('societe')) {
	print '<tr class="oddeven">';
	print '<td>'.$langs->trans("SetDefaultBarcodeTypeThirdParties").'</td>';
	print '<td width="60" class="right">';
	print $formbarcode->selectBarcodeType(getDolGlobalInt('GENBARCODE_BARCODETYPE_THIRDPARTY'), "GENBARCODE_BARCODETYPE_THIRDPARTY", 1);
	print '</td>';
	print '<td>&nbsp;</td>';
	print '</tr>';
}

print "</table>\n";
print '</div>';

print '<div class="tabsAction">';
print '<input type="submit" class="button" name="submit_GENBARCODE_BARCODETYPE_THIRDPARTY" value="'.$langs->trans("Modify").'">';
print "</div>";
print '</form>';

print '<br>';


// End of page
llxFooter();
$db->close();
