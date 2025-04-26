<?php
/* Copyright (C) 2024 VIAWEB S.A.S
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    mod_viaweb_theme/admin/setup.php
 * \ingroup viawebtheme
 * \brief   ViawebTheme setup page.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
    $i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
    $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once '../lib/viawebtheme.lib.php';

// Translations
$langs->loadLangs(array("admin", "viawebtheme@mod_viaweb_theme"));

// Access control
if (!$user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

$form = new Form($db);

// Title and toolbar
$help_url = '';
$page_name = "ViawebThemeSetup";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration page
$head = viawebthemeAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans($page_name), -1, "viawebtheme@mod_viaweb_theme");

print '<div style="text-align: center; margin-bottom: 20px;">';
print '<img src="'.DOL_URL_ROOT.'/custom/mod_viaweb_theme/img/viaweb_logo.png" alt="VIAWEB Logo" style="max-width: 150px; height: auto;">';
print '</div>';

print '<div class="opacitymedium">';
print $langs->trans("ViawebThemeDescription").'<br><br>';
print '</div>';

print '<div class="info" style="margin-top: 20px;">';
print '<strong>'.$langs->trans("Information").':</strong> ';
print $langs->trans("NoConfigurationRequired");
print '</div>';

print '<div style="margin-top: 30px; text-align: center;">';
print '<p>'.$langs->trans("DevelopedBy").' <strong>VIAWEB S.A.S</strong></p>';
print '<p><a href="https://web.viaweb.net.ar" target="_blank">https://web.viaweb.net.ar</a></p>';
print '</div>';

print dol_get_fiche_end();

// Page end
llxFooter();