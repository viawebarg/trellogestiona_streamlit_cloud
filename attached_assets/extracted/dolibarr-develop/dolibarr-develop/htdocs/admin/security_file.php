<?php
/* Copyright (C) 2004-2017	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2017	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2013		Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
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
 *	    \file       htdocs/admin/security_file.php
 *      \ingroup    core
 *      \brief      Security options setup
 */

// Load Dolibarr environment
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// Load translation files required by the page
$langs->loadLangs(array('users', 'admin', 'other'));

$action = GETPOST('action', 'aZ09');
$sortfield = GETPOST('sortfield', 'aZ09');
$sortorder = GETPOST('sortorder', 'aZ09');
if (empty($sortfield)) {
	$sortfield = 'date';
}
if (empty($sortorder)) {
	$sortorder = 'desc';
}

$upload_dir = $conf->admin->dir_temp;

if (!$user->admin) {
	accessforbidden();
}

$error = 0;


/*
 * Actions
 */

if (GETPOST('sendit') && getDolGlobalString('MAIN_UPLOAD_DOC')) {
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	dol_add_file_process($upload_dir, 1, 0, 'userfile');
}

if ($action == 'updateform') {
	$antivircommand = GETPOST('MAIN_ANTIVIRUS_COMMAND', 'restricthtml'); // Use GETPOST restricthtml because we must accept ". Example c:\Progra~1\ClamWin\bin\clamscan.exe
	$antivirparam = GETPOST('MAIN_ANTIVIRUS_PARAM', 'restricthtml'); // Use GETPOST restricthtml because we must accept ". Example --database="C:\Program Files (x86)\ClamWin\lib"
	$antivircommand = dol_string_nospecial($antivircommand, '', array("|", ";", "<", ">", "&", "+")); // Sanitize command
	$antivirparam = dol_string_nospecial($antivirparam, '', array("|", ";", "<", ">", "&", "+")); // Sanitize params

	if ($antivircommand && !empty($dolibarr_main_restrict_os_commands)) {
		$arrayofallowedcommand = explode(',', $dolibarr_main_restrict_os_commands);
		$arrayofallowedcommand = array_map('trim', $arrayofallowedcommand);
		dol_syslog("Command are restricted to ".$dolibarr_main_restrict_os_commands.". We check that one of this command is inside ".$antivircommand);
		$basenamecmddump = basename(str_replace('\\', '/', $antivircommand));
		if (!in_array($basenamecmddump, $arrayofallowedcommand)) {	// the provided command $cmddump must be an allowed command
			$errormsg = $langs->trans('CommandIsNotInsideAllowedCommands');
			setEventMessages($errormsg, null, 'errors');
			$error++;
		}
	}

	if (!$error) {
		$tmpumask = GETPOST('MAIN_UMASK', 'alpha');
		$tmpumask = (octdec($tmpumask) & 0666);
		$tmpumask = decoct($tmpumask);
		if (!preg_match('/^0/', $tmpumask)) {
			$tmpumask = '0'.$tmpumask;
		}
		if (empty($tmpumask)) {  // Also matches '0'
			$tmpumask = '0664';
		}

		$res3 = dolibarr_set_const($db, 'MAIN_UPLOAD_DOC', GETPOST('MAIN_UPLOAD_DOC', 'alpha'), 'chaine', 0, '', $conf->entity);
		$res4 = dolibarr_set_const($db, "MAIN_UMASK", $tmpumask, 'chaine', 0, '', $conf->entity);
		$res5 = dolibarr_set_const($db, "MAIN_ANTIVIRUS_COMMAND", trim($antivircommand), 'chaine', 0, '', $conf->entity);
		$res6 = dolibarr_set_const($db, "MAIN_ANTIVIRUS_PARAM", trim($antivirparam), 'chaine', 0, '', $conf->entity);
		$res7 = dolibarr_set_const($db, "MAIN_FILE_EXTENSION_UPLOAD_RESTRICTION", GETPOST('MAIN_FILE_EXTENSION_UPLOAD_RESTRICTION', 'alpha'), 'chaine', 0, '', $conf->entity);

		$res8 = dolibarr_set_const($db, "MAIN_SECURITY_MAXFILESIZE_DOWNLOADED", GETPOST('MAIN_SECURITY_MAXFILESIZE_DOWNLOADED', 'alpha'), 'chaine', 0, '', $conf->entity);

		if ($res3 && $res4 && $res5 && $res6 && $res7 && $res8) {
			setEventMessages($langs->trans("RecordModifiedSuccessfully"), null, 'mesgs');
		}
	}
} elseif ($action == 'deletefile') {
	// Delete file
	$langs->load("other");
	$file = $conf->admin->dir_temp.'/'.GETPOST('urlfile', 'alpha');
	$ret = dol_delete_file($file);
	if ($ret) {
		setEventMessages($langs->trans("FileWasRemoved", GETPOST('urlfile', 'alpha')), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile', 'alpha')), null, 'errors');
	}
}


/*
 * View
 */

$form = new Form($db);

$wikihelp = 'EN:Setup_Security|FR:Paramétrage_Sécurité|ES:Configuración_Seguridad';
llxHeader('', $langs->trans("Files"), $wikihelp, '', 0, 0, '', '', '', 'mod-admin page-security_file');

print load_fiche_titre($langs->trans("SecuritySetup"), '', 'title_setup');

print '<span class="opacitymedium">'.$langs->trans("SecurityFilesDesc")."</span><br>\n";
print "<br>\n";


print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="updateform">';

$head = security_prepare_head();

print dol_get_fiche_head($head, 'file', '', -1);

print '<br>';

// Upload options

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent nomarginbottom">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("UploadName").'</td>';
print '<td></td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("MaxSizeForUploadedFiles").'.';
$max = @ini_get('upload_max_filesize');
if (isset($max)) {
	print '<br><span class="opacitymedium">'.$langs->trans("MustBeLowerThanPHPLimit", ((int) $max) * 1024, $langs->trans("Kb")).'.</span>';
} else {
	print ' '.$langs->trans("NoMaxSizeByPHPLimit").'.';
}
print '</td>';
print '<td class="nowrap">';
print '<input class="flat width75 right" name="MAIN_UPLOAD_DOC" type="text" spellcheck="false" value="'.dol_escape_htmltag(getDolGlobalString('MAIN_UPLOAD_DOC')).'"> '.$langs->trans("Kb");
print '</td>';
print '</tr>';


print '<tr class="oddeven">';
print '<td>';
print $form->textwithpicto($langs->trans("UMask"), $langs->trans("UMaskExplanation"));
print '</td>';
print '<td class="nowrap">';
print '<input class="flat width75 right" name="MAIN_UMASK" type="text" spellcheck="false" value="'.dol_escape_htmltag(getDolGlobalString('MAIN_UMASK')).'">';
print '</td>';
print '</tr>';

// Use anti virus

print '<tr class="oddeven">';
print '<td>'.$langs->trans("AntiVirusCommand").'<br>';
print '<span class="opacitymedium">'.$langs->trans("AntiVirusCommandExample").'</span>';
// Check command in inside safe_mode
print '</td>';
print '<td>';
if (ini_get('safe_mode') && getDolGlobalString('MAIN_ANTIVIRUS_COMMAND')) {
	$langs->load("errors");
	$basedir = preg_replace('/"/', '', dirname($conf->global->MAIN_ANTIVIRUS_COMMAND));
	$listdir = explode(';', ini_get('safe_mode_exec_dir'));
	if (!in_array($basedir, $listdir)) {
		print img_warning($langs->trans('WarningSafeModeOnCheckExecDir'));
		dol_syslog("safe_mode is on, basedir is ".$basedir.", safe_mode_exec_dir is ".ini_get('safe_mode_exec_dir'), LOG_WARNING);
	}
}
print '<input type="text" '.((defined('MAIN_ANTIVIRUS_COMMAND') && !defined('MAIN_ANTIVIRUS_BYPASS_COMMAND_AND_PARAM')) ? 'disabled ' : '').'name="MAIN_ANTIVIRUS_COMMAND" class="minwidth500imp" spellcheck="false" value="'.dol_escape_htmltag(GETPOSTISSET('MAIN_ANTIVIRUS_COMMAND') ? GETPOST('MAIN_ANTIVIRUS_COMMAND') : getDolGlobalString('MAIN_ANTIVIRUS_COMMAND')).'">';
if (defined('MAIN_ANTIVIRUS_COMMAND') && !defined('MAIN_ANTIVIRUS_BYPASS_COMMAND_AND_PARAM')) {
	print '<br><span class="opacitymedium">'.$langs->trans("ValueIsForcedBySystem").'</span>';
}
print "</td>";
print '</tr>';

// Use anti virus

print '<tr class="oddeven">';
print '<td>'.$langs->trans("AntiVirusParam").'<br>';
print '<span class="opacitymedium">'.$langs->trans("AntiVirusParamExample").'</span>';
print '</td>';
print '<td>';
print '<input type="text" '.(defined('MAIN_ANTIVIRUS_PARAM') ? 'disabled ' : '').'name="MAIN_ANTIVIRUS_PARAM" class="minwidth500imp" spellcheck="false" value="'.(getDolGlobalString('MAIN_ANTIVIRUS_PARAM') ? dol_escape_htmltag(getDolGlobalString('MAIN_ANTIVIRUS_PARAM')) : '').'">';
if (defined('MAIN_ANTIVIRUS_PARAM')) {
	print '<br><span class="opacitymedium">'.$langs->trans("ValueIsForcedBySystem").'</span>';
}
print "</td>";
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("UploadExtensionRestriction").'<br>';
print '<span class="opacitymedium">'.$langs->trans("UploadExtensionRestrictionExemple").'</span>';
print '</td>';
print '<td>';
print '<input type="text" name="MAIN_FILE_EXTENSION_UPLOAD_RESTRICTION" class="minwidth500imp" spellcheck="false" value="'.getDolGlobalString('MAIN_FILE_EXTENSION_UPLOAD_RESTRICTION', implode(',', getExecutableContent())).'">';
print "</td>";
print '</tr>';

print '</table>';
print '</div>';


print '<br>';


// Download options

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent nomarginbottom">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Download").'</td>';
print '<td></td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("MAIN_SECURITY_MAXFILESIZE_DOWNLOADED").'<br>';
//print '<span class="opacitymedium">'.$langs->trans("MAIN_SECURITY_MAXFILESIZE_DOWNLOADED").'</span>';
print '</td>';
print '<td>';
print '<input type="text" name="MAIN_SECURITY_MAXFILESIZE_DOWNLOADED" class="width100 right" spellcheck="false" value="'.getDolGlobalString('MAIN_SECURITY_MAXFILESIZE_DOWNLOADED').'"> '.$langs->trans("Kb");
print "</td>";
print '</tr>';

print '</table>';
print '</div>';




print dol_get_fiche_end();

print $form->buttonsSaveCancel("Modify", '');

print '</form>';


// Form to test upload
print '<br>';
$formfile = new FormFile($db);
$formfile->form_attach_new_file($_SERVER['PHP_SELF'], $langs->trans("FormToTestFileUploadForm"), 0, 0, 1, 50, null, '', 1, '', 0);

// List of document
$filearray = dol_dir_list($upload_dir, "files", 0, '', '', $sortfield, $sortorder == 'desc' ? SORT_DESC : SORT_ASC, 1);
if (count($filearray) > 0) {
	$formfile->list_of_documents($filearray, null, 'admin_temp', '');
}

// End of page
llxFooter();
$db->close();
