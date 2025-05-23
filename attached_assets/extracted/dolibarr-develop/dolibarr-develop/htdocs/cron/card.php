<?php
/* Copyright (C) 2012       Nicolas Villa aka Boyquotes http://informetic.fr
 * Copyright (C) 2013       Florian Henry           <florian.henry@open-concpt.pro>
 * Copyright (C) 2013-2016  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2024	Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024		William Mead			<william.mead@manchenumerique.fr>
 * Copyright (C) 2025		MDW						<mdeweerd@users.noreply.github.com>
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
 *  \file       htdocs/cron/card.php
 *  \ingroup    cron
 *  \brief      Cron Jobs Card
 */

// Load Dolibarr environment
require '../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

// Cron job libraries
require_once DOL_DOCUMENT_ROOT."/cron/class/cronjob.class.php";
require_once DOL_DOCUMENT_ROOT."/core/class/html.formcron.class.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/cron.lib.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// Load translation files required by the page
$langs->loadLangs(array('admin', 'cron', 'members', 'bills'));

$id = GETPOSTINT('id');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');

$securitykey = GETPOST('securitykey', 'alpha');

if (!$user->hasRight('cron', 'create')) {
	accessforbidden();
}

$permissiontoadd = $user->hasRight('cron', 'create');
$permissiontoexecute = $user->hasRight('cron', 'execute');
$permissiontodelete = $user->hasRight('cron', 'delete');


/*
 * Actions
 */

$object = new Cronjob($db);
if (!empty($id)) {
	$result = $object->fetch($id);
	if ($result < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
	}
}

if (!empty($cancel)) {
	if (!empty($id) && empty($backtopage)) {
		$action = '';
	} else {
		if ($backtopage) {
			header("Location: ".$backtopage);
		} else {
			header("Location: ".DOL_URL_ROOT.'/cron/list.php');
		}
		exit;
	}
}

// Delete jobs
if ($action == 'confirm_delete' && $confirm == "yes" && $permissiontodelete) {
	$result = $object->delete($user);

	if ($result < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
		$action = 'edit';
	} else {
		header("Location: ".DOL_URL_ROOT.'/cron/list.php');
		exit;
	}
}

// Execute jobs
if ($action == 'confirm_execute' && $confirm == "yes" && $permissiontoexecute) {
	if (getDolGlobalString('CRON_KEY') && $conf->global->CRON_KEY != $securitykey) {
		setEventMessages('Security key '.$securitykey.' is wrong', null, 'errors');
	} else {
		$now = dol_now(); // Date we start

		$result = $object->run_jobs($user->login);

		if ($result < 0) {
			setEventMessages($object->error, $object->errors, 'errors');
		} else {
			$res = $object->reprogram_jobs($user->login, $now);
			if ($res > 0) {
				if ($object->lastresult > 0) {
					setEventMessages($langs->trans("JobFinished"), null, 'warnings');
				} else {
					setEventMessages($langs->trans("JobFinished"), null, 'mesgs');
				}
			} else {
				setEventMessages($object->error, $object->errors, 'errors');
			}
		}
	}
	$action = '';
}


if ($action == 'add' && $permissiontoadd) {
	$object->jobtype = GETPOST('jobtype');
	$object->label = GETPOST('label');
	$object->command = GETPOST('command');
	$object->classesname = GETPOST('classesname', 'alphanohtml');
	$object->objectname = GETPOST('objectname', 'aZ09');
	$object->methodename = GETPOST('methodename', 'aZ09');
	$object->params = GETPOST('params');
	$object->md5params = GETPOST('md5params');
	$object->module_name = GETPOST('module_name');
	$object->note_private = GETPOST('note', 'restricthtml');
	$object->datestart = dol_mktime(GETPOSTINT('datestarthour'), GETPOSTINT('datestartmin'), 0, GETPOSTINT('datestartmonth'), GETPOSTINT('datestartday'), GETPOSTINT('datestartyear'));
	$object->dateend = dol_mktime(GETPOSTINT('dateendhour'), GETPOSTINT('dateendmin'), 0, GETPOSTINT('dateendmonth'), GETPOSTINT('dateendday'), GETPOSTINT('dateendyear'));
	$object->priority = GETPOSTINT('priority');
	$object->datenextrun = dol_mktime(GETPOSTINT('datenextrunhour'), GETPOSTINT('datenextrunmin'), 0, GETPOSTINT('datenextrunmonth'), GETPOSTINT('datenextrunday'), GETPOSTINT('datenextrunyear'));
	$object->unitfrequency = GETPOST('unitfrequency', 'alpha');
	$object->frequency = GETPOSTINT('nbfrequency');
	$object->maxrun = GETPOSTINT('maxrun');
	$object->email_alert = GETPOST('email_alert');
	$object->status = 0;
	$object->processing = 0;
	$object->lastresult = '';
	// Add cron task
	$result = $object->create($user);

	// Test request result
	if ($result < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
		$action = 'create';
	} else {
		setEventMessages($langs->trans('CronSaveSucess'), null, 'mesgs');
		$action = '';
	}
}

// Save parameters
if ($action == 'update' && $permissiontoadd) {
	$object->id = $id;
	$object->jobtype = GETPOST('jobtype');
	$object->label = GETPOST('label');
	$object->command = GETPOST('command');
	$object->classesname = GETPOST('classesname', 'alphanohtml');
	$object->objectname = GETPOST('objectname', 'aZ09');
	$object->methodename = GETPOST('methodename', 'aZ09');
	$object->params = GETPOST('params');
	$object->md5params = GETPOST('md5params');
	$object->module_name = GETPOST('module_name');
	$object->note_private = GETPOST('note', 'restricthtml');
	$object->datestart = dol_mktime(GETPOSTINT('datestarthour'), GETPOSTINT('datestartmin'), 0, GETPOSTINT('datestartmonth'), GETPOSTINT('datestartday'), GETPOSTINT('datestartyear'));
	$object->dateend = dol_mktime(GETPOSTINT('dateendhour'), GETPOSTINT('dateendmin'), 0, GETPOSTINT('dateendmonth'), GETPOSTINT('dateendday'), GETPOSTINT('dateendyear'));
	$object->priority = GETPOSTINT('priority');
	$object->datenextrun = dol_mktime(GETPOSTINT('datenextrunhour'), GETPOSTINT('datenextrunmin'), 0, GETPOSTINT('datenextrunmonth'), GETPOSTINT('datenextrunday'), GETPOSTINT('datenextrunyear'));
	$object->unitfrequency = GETPOST('unitfrequency', 'alpha');
	$object->frequency = GETPOSTINT('nbfrequency');
	$object->maxrun = GETPOSTINT('maxrun');
	$object->email_alert = GETPOST('email_alert');

	// Add cron task
	$result = $object->update($user);

	// Test request result
	if ($result < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
		$action = 'edit';
	} else {
		setEventMessages($langs->trans('CronSaveSucess'), null, 'mesgs');
		$action = '';
	}
}

if ($action == 'activate' && $permissiontoadd) {
	$object->status = 1;

	// Add cron task
	$result = $object->update($user);

	// Test request result
	if ($result < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
		$action = 'edit';
	} else {
		setEventMessages($langs->trans('CronSaveSucess'), null, 'mesgs');
		$action = '';
	}
}

if ($action == 'inactive' && $permissiontoadd) {
	$object->status = 0;
	$object->processing = 0;

	// Add cron task
	$result = $object->update($user);

	// Test request result
	if ($result < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
		$action = 'edit';
	} else {
		setEventMessages($langs->trans('CronSaveSucess'), null, 'mesgs');
		$action = '';
	}
}

// Action clone object
if ($action == 'confirm_clone' && $confirm == 'yes' && $permissiontoadd) {
	if (1 == 0 && !GETPOST('clone_content') && !GETPOST('clone_receivers')) {  // @phan-suppress-current-line PhanPluginBothLiteralsBinaryOp
		setEventMessages($langs->trans("NoCloneOptionsSpecified"), null, 'errors');
	} else {
		$objectutil = dol_clone($object, 1); // We clone to avoid to denaturate loaded object when setting some properties for clone or if createFromClone modifies the object. We use the native clone to keep this->db valid.

		$result = $objectutil->createFromClone($user, (($object->id > 0) ? $object->id : $id));
		if (is_object($result) || $result > 0) {
			$newid = 0;
			if (is_object($result)) {
				$newid = $result->id;
			} else {
				$newid = $result;
			}
			header("Location: ".$_SERVER['PHP_SELF'].'?id='.$newid); // Open record of new object
			exit;
		} else {
			setEventMessages($objectutil->error, $objectutil->errors, 'errors');
			$action = '';
		}
	}
}


/*
 * View
 */

$form = new Form($db);
$formCron = new FormCron($db);

llxHeader('', $langs->trans("CronTask"));

$head = cron_prepare_head($object);

if ($action == 'create') {
	print load_fiche_titre($langs->trans("CronTask"), '', 'title_setup');
}

if ($conf->use_javascript_ajax) {
	print "\n".'<script type="text/javascript">';
	print 'jQuery(document).ready(function () {
                    function initfields()
                    {
                        if ($("#jobtype option:selected").val()==\'method\') {
							$(".blockmethod").show();
							$(".blockcommand").hide();
						}
						if ($("#jobtype option:selected").val()==\'command\') {
							$(".blockmethod").hide();
							$(".blockcommand").show();
						}
                    }
                    initfields();
                    jQuery("#jobtype").change(function() {
                        initfields();
                    });
               })';
	print '</script>'."\n";
}

$formconfirm = '';
if ($action == 'delete') {
	$formconfirm = $form->formconfirm($_SERVER['PHP_SELF']."?id=".$object->id, $langs->trans("CronDelete"), $langs->trans("CronConfirmDelete"), "confirm_delete", '', '', 1);

	$action = '';
}

if ($action == 'execute') {
	$formconfirm = $form->formconfirm($_SERVER['PHP_SELF']."?id=".$object->id.'&securitykey='.$securitykey, $langs->trans("CronExecute"), $langs->trans("CronConfirmExecute"), "confirm_execute", '', '', 1);

	$action = '';
}

// Clone confirmation
if ($action == 'clone') {
	// Create an array for form
	$formquestion = array();
	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
}

// Print form confirm
print $formconfirm;


/*
 * Create Template
 */

if (empty($object->status) && $action != 'create') {
	setEventMessages($langs->trans("CronTaskInactive"), null, 'warnings');
}

if (($action == "create") || ($action == "edit")) {
	print '<form name="cronform" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="token" value="'.newToken().'">'."\n";
	print '<input type="hidden" name="backtopage" value="'.GETPOST('backtopage').'">'."\n";
	if (!empty($object->id)) {
		print '<input type="hidden" name="action" value="update">'."\n";
		print '<input type="hidden" name="id" value="'.$object->id.'">'."\n";
	} else {
		print '<input type="hidden" name="action" value="add">'."\n";
	}

	if ($action == "edit") {
		print dol_get_fiche_head($head, 'card', $langs->trans("CronTask"), 0, 'cron');
	} else {
		print dol_get_fiche_head([]);
	}

	print '<table class="border centpercent">';

	print '<tr><td class="fieldrequired titlefieldcreate">';
	print $langs->trans('CronLabel')."</td>";
	print '<td><input type="text" class="width200" name="label" value="'.dol_escape_htmltag($object->label).'"> ';
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	print '<tr><td class="fieldrequired">';
	print $langs->trans('CronType')."</td><td>";
	print $formCron->select_typejob('jobtype', $object->jobtype);
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronModule')."</td><td>";
	print '<input type="text" class="width200" name="module_name" value="'.dol_escape_htmltag($object->module_name).'"> ';
	print "</td>";
	print "<td>";
	print $form->textwithpicto('', $langs->trans("CronModuleHelp"), 1, 'help');
	print "</td>";
	print "</tr>\n";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronClassFile')."</td><td>";
	print '<input type="text" class="minwidth300" name="classesname" value="'.dol_escape_htmltag($object->classesname).'"> ';
	print "</td>";
	print "<td>";
	print $form->textwithpicto('', $langs->trans("CronClassFileHelp"), 1, 'help');
	print "</td>";
	print "</tr>\n";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronObject')."</td><td>";
	print '<input type="text" class="width200" name="objectname" value="'.dol_escape_htmltag($object->objectname).'"> ';
	print "</td>";
	print "<td>";
	print $form->textwithpicto('', $langs->trans("CronObjectHelp"), 1, 'help');
	print "</td>";
	print "</tr>\n";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronMethod')."</td><td>";
	print '<input type="text" class="minwidth300" name="methodename" value="'.dol_escape_htmltag($object->methodename).'" /> ';
	print "</td>";
	print "<td>";
	print $form->textwithpicto('', $langs->trans("CronMethodHelp"), 1, 'help');
	print "</td>";
	print "</tr>\n";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronArgs')."</td><td>";
	print '<input type="text" class="quatrevingtpercent" name="params" value="'.$object->params.'" /> ';
	print "</td>";
	print "<td>";
	print $form->textwithpicto('', $langs->trans("CronArgsHelp"), 1, 'help');
	print "</td>";
	print "</tr>\n";

	print '<tr class="blockcommand"><td>';
	print $langs->trans('CronCommand')."</td><td>";
	print '<input type="text" class="minwidth150" name="command" value="'.$object->command.'" /> ';
	print "</td>";
	print "<td>";
	print $form->textwithpicto('', $langs->trans("CronCommandHelp"), 1, 'help');
	print "</td>";
	print "</tr>\n";

	print '<tr><td>';
	print $langs->trans('CronNote')."</td><td>";
	$doleditor = new DolEditor('note', $object->note_private, '', 160, 'dolibarr_notes', 'In', true, false, 0, ROWS_4, '90%');
	$doleditor->Create();
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	print '<tr class="blockemailalert"><td>';
	print $langs->trans('EmailIfError')."</td><td>";
	print '<input type="text" class="minwidth150" name="email_alert" value="'.dol_escape_htmltag($object->email_alert).'" /> ';
	print "</td>";
	print "<td>";
	//print $form->textwithpicto('', $langs->trans("CronCommandHelp"), 1, 'help');
	print "</td>";
	print "</tr>\n";

	print '<tr><td class="fieldrequired">';
	print $langs->trans('CronEvery')."</td>";
	print "<td>";
	print '<select name="nbfrequency">';
	for ($i = 1; $i <= 60; $i++) {
		if ($object->frequency == $i) {
			print "<option value='".$i."' selected>".$i."</option>";
		} else {
			print "<option value='".$i."'>".$i."</option>";
		}
	}
	print "</select>";
	$input = " <input type=\"radio\" name=\"unitfrequency\" value=\"60\" id=\"frequency_minute\" ";
	if ($object->unitfrequency == "60") {
		$input .= ' checked />';
	} else {
		$input .= ' />';
	}
	$input .= "<label for=\"frequency_minute\">".$langs->trans('Minutes')."</label>";
	print $input;

	$input = " <input type=\"radio\" name=\"unitfrequency\" value=\"3600\" id=\"frequency_heures\" ";
	if ($object->unitfrequency == "3600") {
		$input .= ' checked />';
	} else {
		$input .= ' />';
	}
	$input .= "<label for=\"frequency_heures\">".$langs->trans('Hours')."</label>";
	print $input;

	$input = " <input type=\"radio\" name=\"unitfrequency\" value=\"86400\" id=\"frequency_jours\" ";
	if ($object->unitfrequency == "86400") {
		$input .= ' checked />';
	} else {
		$input .= ' />';
	}
	$input .= "<label for=\"frequency_jours\">".$langs->trans('Days')."</label>";
	print $input;

	$input = " <input type=\"radio\" name=\"unitfrequency\" value=\"604800\" id=\"frequency_semaine\" ";
	if ($object->unitfrequency == "604800") {
		$input .= ' checked />';
	} else {
		$input .= ' />';
	}
	$input .= "<label for=\"frequency_semaine\">".$langs->trans('Weeks')."</label>";
	print $input;

	$input = " <input type=\"radio\" name=\"unitfrequency\" value=\"2678400\" id=\"frequency_month\" ";
	if ($object->unitfrequency == "2678400") {
		$input .= ' checked />';
	} else {
		$input .= ' />';
	}
	$input .= '<label for="frequency_month">'.$langs->trans('Months')."</label>";
	print $input;

	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	// Priority
	print "<tr><td>";
	print $langs->trans('CronPriority')."</td>";
	$priority = 0;
	if (!empty($object->priority)) {
		$priority = $object->priority;
	}
	print '<td><input type="text" class="width50" name="priority" value="'.$priority.'" /> ';
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	print "<tr><td>";
	print $langs->trans('CronDtStart')."</td><td>";
	if (!empty($object->datestart)) {
		print $form->selectDate($object->datestart, 'datestart', 1, 1, 0, "cronform");
	} else {
		print $form->selectDate(-1, 'datestart', 1, 1, 1, "cronform");
	}
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	print "<tr><td>";
	print $langs->trans('CronDtEnd')."</td><td>";
	if (!empty($object->dateend)) {
		print $form->selectDate($object->dateend, 'dateend', 1, 1, 0, "cronform");
	} else {
		print $form->selectDate(-1, 'dateend', 1, 1, 1, "cronform");
	}
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	print '<tr><td>';
	$maxrun = '';
	if (!empty($object->maxrun)) {
		$maxrun = $object->maxrun;
	}
	print $langs->trans('CronMaxRun')."</td>";
	print '<td><input type="text" class="width50" name="maxrun" value="'.$maxrun.'" /> ';
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>\n";

	print '<tr><td class="fieldrequired">';
	print $langs->trans('CronDtNextLaunch');
	//print ' ('.$langs->trans('CronFrom').')';
	print "</td><td>";
	if (!empty($object->datenextrun)) {
		print $form->selectDate($object->datenextrun, 'datenextrun', 1, 1, 0, "cronform");
	} else {
		print $form->selectDate(-1, 'datenextrun', 1, 1, 0, "cronform", 1, 1);
	}
	print "</td>";
	print "<td>";
	print "</td>";
	print "</tr>";

	print '</table>';

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel();

	print "</form>\n";
} else {
	// view card
	$now = dol_now();

	print dol_get_fiche_head($head, 'card', $langs->trans("CronTask"), -1, 'cron');

	$linkback = '<a href="'.DOL_URL_ROOT.'/cron/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

	$reg = array();
	if (preg_match('/:(.*)$/', $object->label, $reg)) {
		$langs->load($reg[1]);
	}

	$labeltoshow =  preg_replace('/:.*$/', '', $object->label);

	$morehtmlref = '<div class="refidno">';
	$morehtmlref .= $langs->trans($labeltoshow);
	$morehtmlref .= '</div>';

	dol_banner_tab($object, 'id', $linkback, 1, 'rowid', 'ref', $morehtmlref);

	// box add_jobs_box
	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';

	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">';

	/*print '<tr><td class="titlefield">';
	print $langs->trans('CronLabel')."</td>";
	print "<td>".$langs->trans($object->label);
	print "</td></tr>";*/

	print '<tr><td class="titlefieldmiddle">';
	print $langs->trans('CronType')."</td><td>";
	print $formCron->select_typejob('jobtype', $object->jobtype, 1);
	print "</td></tr>";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronModule')."</td><td>";
	print dol_escape_htmltag($object->module_name);
	print "</td></tr>";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronClassFile')."</td><td>";
	print dol_escape_htmltag($object->classesname);
	print "</td></tr>";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronObject')."</td><td>";
	print dol_escape_htmltag($object->objectname);
	print "</td></tr>";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronMethod')."</td><td>";
	print dol_escape_htmltag($object->methodename);
	print "</td></tr>";

	print '<tr class="blockmethod"><td>';
	print $langs->trans('CronArgs')."</td><td>";
	print dol_escape_htmltag($object->params);
	print "</td></tr>";

	print '<tr class="blockcommand"><td>';
	print $langs->trans('CronCommand')."</td><td>";
	print dol_escape_htmltag($object->command);
	print "</td></tr>";

	print '<tr><td>';
	print $langs->trans('CronNote')."</td><td>";
	if (!is_null($object->note_private) && $object->note_private != '') {
		print '<div class="small lineheightsmall">'.$langs->trans($object->note_private).'</div>';
	}
	print "</td></tr>";

	print '<tr class="blockemailalert"><td>';
	print $langs->trans('EmailIfError')."</td><td>";
	print dol_escape_htmltag($object->email_alert);
	print "</td></tr>";

	if (isModEnabled('multicompany')) {
		print '<tr><td>';
		print $langs->trans('Entity')."</td><td>";
		if (empty($object->entity)) {
			print img_picto($langs->trans("AllEntities"), 'entity', 'class="pictofixedwidth"').$langs->trans("AllEntities");
		} else {
			$mc->getInfo($object->entity);
			print img_picto($langs->trans("AllEntities"), 'entity', 'class="pictofixedwidth"').$mc->label;
		}
		print "</td></tr>";
	}

	print '</table>';
	print '</div>';

	print '<div class="fichehalfright">';

	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">';

	print '<tr><td class="titlefieldmiddle">';
	print $langs->trans('CronEvery')."</td>";
	print "<td>";
	if ($object->unitfrequency == "60") {
		print $langs->trans('CronEach')." ".($object->frequency)." ".$langs->trans('Minutes');
	}
	if ($object->unitfrequency == "3600") {
		print $langs->trans('CronEach')." ".($object->frequency)." ".$langs->trans('Hours');
	}
	if ($object->unitfrequency == "86400") {
		print $langs->trans('CronEach')." ".($object->frequency)." ".$langs->trans('Days');
	}
	if ($object->unitfrequency == "604800") {
		print $langs->trans('CronEach')." ".($object->frequency)." ".$langs->trans('Weeks');
	}
	if ($object->unitfrequency == "2678400") {
		print $langs->trans('CronEach')." ".($object->frequency)." ".$langs->trans('Months');
	}
	print "</td></tr>";

	// Priority
	print "<tr><td>";
	print $langs->trans('CronPriority')."</td>";
	print "<td>".$object->priority;
	print "</td></tr>";

	print '<tr><td>';
	print $langs->trans('CronDtStart')."</td><td>";
	if (!empty($object->datestart)) {
		print $form->textwithpicto(dol_print_date($object->datestart, 'dayhoursec'), $langs->trans("CurrentTimeZone"));
	}
	print "</td></tr>";

	print "<tr><td>";
	print $langs->trans('CronDtEnd')."</td><td>";
	if (!empty($object->dateend)) {
		print $form->textwithpicto(dol_print_date($object->dateend, 'dayhoursec'), $langs->trans("CurrentTimeZone"));
	}
	print "</td></tr>";

	print "<tr><td>";
	print $langs->trans('CronMaxRun')."</td>";
	print "<td>";
	print $object->maxrun > 0 ? $object->maxrun : '';
	print "</td></tr>";

	print "<tr><td>";
	print $langs->trans('CronNbRun')."</td>";
	print "<td>".$object->nbrun;
	print "</td></tr>";

	// Date next run (from)
	print '<tr><td>';
	print $langs->trans('CronDtNextLaunch');
	print ' ('.$langs->trans('CronFrom').')';
	print "</td><td>";
	if (!$object->status) {
		print img_picto('', 'object_calendarday').' <span class="opacitymedium strikefordisabled">'.$form->textwithpicto(dol_print_date($object->datenextrun, 'dayhoursec'), $langs->trans("CurrentTimeZone")).'</span> ';
		print $langs->trans("Disabled");
	} elseif (!empty($object->datenextrun)) {
		print img_picto('', 'object_calendarday').' '.$form->textwithpicto(dol_print_date($object->datenextrun, 'dayhoursec'), $langs->trans("CurrentTimeZone"));
	} else {
		print '<span class="opacitymedium">'.$langs->trans('CronNone').'</span>';
	}
	if ($object->status == Cronjob::STATUS_ENABLED) {
		if ($object->maxrun && $object->nbrun >= $object->maxrun) {
			print img_warning($langs->trans("MaxRunReached"));
		} elseif ($object->datenextrun && $object->datenextrun < $now) {
			print img_warning($langs->trans("Late"));
		}
	}
	print "</td></tr>";

	print '</table>';


	print '<br>';


	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent tableforfield">';

	print '<tr><td class="titlefieldmiddle">';
	print $langs->trans('CronDtLastLaunch')."</td><td>";
	if (!empty($object->datelastrun)) {
		print $form->textwithpicto(dol_print_date($object->datelastrun, 'dayhoursec'), $langs->trans("CurrentTimeZone"));
	} else {
		print '<span class="opacitymedium">'.$langs->trans('CronNotYetRan').'</span>';
	}
	print "</td></tr>";

	print '<tr><td>';
	print $langs->trans('CronDtLastResult')."</td><td>";
	if (!empty($object->datelastresult)) {
		print $form->textwithpicto(dol_print_date($object->datelastresult, 'dayhoursec'), $langs->trans("CurrentTimeZone"));
	} else {
		if (empty($object->datelastrun)) {
			print '<span class="opacitymedium">'.$langs->trans('CronNotYetRan').'</span>';
		} else {
			// In progress
		}
	}
	print "</td></tr>";

	print '<tr><td>';
	print $langs->trans('CronLastResult')."</td><td>";
	if ($object->lastresult) {
		print '<span class="error">';
	}
	print $object->lastresult;
	if ($object->lastresult) {
		print '</span>';
	}
	print "</td></tr>";

	print '<tr><td>';
	print $langs->trans('CronLastOutput')."</td><td>";
	print '<span class="small">'.(!empty($object->lastoutput) ? nl2br($object->lastoutput) : '').'</span>';
	print "</td></tr>";

	print '</table>';

	print '</div>';

	print '<div class="clearboth"></div>';


	print dol_get_fiche_end();


	print "\n\n".'<div class="tabsAction">'."\n";
	if (!$user->hasRight('cron', 'create')) {
		print '<a class="butActionRefused classfortooltip" href="#" title="'.dol_escape_htmltag($langs->transnoentitiesnoconv("NotEnoughPermissions")).'">'.$langs->trans("Edit").'</a>';
	} else {
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=edit&token='.newToken().'&id='.$object->id.'">'.$langs->trans("Edit").'</a>';
	}

	if ((!$user->hasRight('cron', 'execute'))) {
		print '<a class="butActionRefused classfortooltip" href="#" title="'.dol_escape_htmltag($langs->transnoentitiesnoconv("NotEnoughPermissions")).'">'.$langs->trans("CronExecute").'</a>';
	} elseif (empty($object->status)) {
		print '<a class="butActionRefused classfortooltip" href="#" title="'.dol_escape_htmltag($langs->transnoentitiesnoconv("JobDisabled")).'">'.$langs->trans("CronExecute").'</a>';
	} else {
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=execute&token='.newToken().'&id='.$object->id.(!getDolGlobalString('CRON_KEY') ? '' : '&securitykey='.urlencode(getDolGlobalString('CRON_KEY'))).'">'.$langs->trans("CronExecute").'</a>';
	}

	if (!$user->hasRight('cron', 'create')) {
		print '<a class="butActionRefused classfortooltip" href="#" title="'.dol_escape_htmltag($langs->transnoentitiesnoconv("NotEnoughPermissions")).'">'.$langs->trans("CronStatusActiveBtn").'/'.$langs->trans("CronStatusInactiveBtn").'</a>';
	} else {
		print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=clone&token='.newToken().'&id='.$object->id.'">'.$langs->trans("ToClone").'</a>';

		if (empty($object->status)) {
			print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=activate&token='.newToken().'&id='.$object->id.'">'.$langs->trans("CronStatusActiveBtn").'</a>';
		} else {
			print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=inactive&id='.$object->id.'">'.$langs->trans("CronStatusInactiveBtn").'</a>';
		}
	}

	if (!$user->hasRight('cron', 'delete')) {
		print '<a class="butActionDeleteRefused" href="#" title="'.dol_escape_htmltag($langs->transnoentitiesnoconv("NotEnoughPermissions")).'">'.$langs->trans("Delete").'</a>';
	} else {
		print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?action=delete&token='.newToken().'&id='.$object->id.'">'.$langs->trans("Delete").'</a>';
	}
	print '</div>';

	print '<br>';
}


llxFooter();

$db->close();
