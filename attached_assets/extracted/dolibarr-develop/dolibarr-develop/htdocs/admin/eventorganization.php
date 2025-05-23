<?php
/* Copyright (C) 2021		Florian Henry			<florian.henry@scopen.fr>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024       Frédéric France         <frederic.france@free.fr>
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
 * \file    htdocs/admin/eventorganization.php
 * \ingroup eventorganization
 * \brief   EventOrganization setup page.
 */

// Load Dolibarr environment
require '../main.inc.php';

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/lib/eventorganization.lib.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// Translations
$langs->loadLangs(array("admin", "eventorganization", "categories"));

// Parameters
$action = GETPOST('action', 'aZ09');
$cancel = GETPOST('cancel', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php

$scandir = GETPOST('scan_dir', 'alpha');
$type = 'myobject';

if (empty($action)) {
	$action = 'edit';
}

$arrayofparameters = array(
	'EVENTORGANIZATION_TASK_LABEL' => array('type' => 'textarea','enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_CATEG_THIRDPARTY_CONF' => array('type' => 'category:'.Categorie::TYPE_CUSTOMER, 'enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_CATEG_THIRDPARTY_BOOTH' => array('type' => 'category:'.Categorie::TYPE_CUSTOMER, 'enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_FILTERATTENDEES_CAT' => array('type' => 'category:'.Categorie::TYPE_CUSTOMER, 'enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_FILTERATTENDEES_TYPE' => array('type' => 'thirdparty_type:', 'enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_TEMPLATE_EMAIL_ASK_CONF' => array('type' => 'emailtemplate:conferenceorbooth', 'enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_TEMPLATE_EMAIL_ASK_BOOTH' => array('type' => 'emailtemplate:conferenceorbooth', 'enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_TEMPLATE_EMAIL_AFT_SUBS_BOOTH' => array('type' => 'emailtemplate:conferenceorbooth', 'enabled' => 1, 'css' => ''),
	'EVENTORGANIZATION_TEMPLATE_EMAIL_AFT_SUBS_EVENT' => array('type' => 'emailtemplate:conferenceorbooth', 'enabled' => 1, 'css' => ''),
	//'EVENTORGANIZATION_TEMPLATE_EMAIL_BULK_SPEAKER'=>array('type'=>'emailtemplate:conferenceorbooth', 'enabled'=>1, 'css' => ''),
	//'EVENTORGANIZATION_TEMPLATE_EMAIL_BULK_ATTENDES'=>array('type'=>'emailtemplate:conferenceorbooth', 'enabled'=>1, 'css' => ''),
	'SERVICE_BOOTH_LOCATION' => array('type' => 'product', 'enabled' => 1, 'css' => 'maxwidth500'),
	'SERVICE_CONFERENCE_ATTENDEE_SUBSCRIPTION' => array('type' => 'product', 'enabled' => 1, 'css' => 'maxwidth500'),
);

$error = 0;
$setupnotempty = 0;

$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);

// Access control
if (empty($user->admin)) {
	accessforbidden();
}



/*
 * Actions
 */

if ($cancel) {
	$action  = '';
}

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';

if ($action == 'updateMask') {
	$maskconstorder = GETPOST('maskconstorder', 'aZ09');
	$maskorder = GETPOST('maskorder', 'alpha');

	if ($maskconstorder && preg_match('/_MASK$/', $maskconstorder)) {
		$res = dolibarr_set_const($db, $maskconstorder, $maskorder, 'chaine', 0, '', $conf->entity);
		if (!($res > 0)) {
			$error++;
		}
	}

	if (!$error) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
} elseif ($action == 'setmod') {
	// TODO Check if numbering module chosen can be activated by calling method canBeActivated
	$tmpobjectkey = GETPOST('object', 'aZ09');
	if (!empty($tmpobjectkey)) {
		$constforval = 'EVENTORGANIZATION_'.strtoupper($tmpobjectkey)."_ADDON";
		dolibarr_set_const($db, $constforval, $value, 'chaine', 0, '', $conf->entity);
	}
} elseif ($action == 'set') {
	// Activate a model
	$ret = addDocumentModel($value, $type, $label, $scandir);
} elseif ($action == 'del') {
	$ret = delDocumentModel($value, $type);
	if ($ret > 0) {
		$tmpobjectkey = GETPOST('object', 'aZ09');
		if (!empty($tmpobjectkey)) {
			$constforval = 'EVENTORGANIZATION_'.strtoupper($tmpobjectkey).'_ADDON_PDF';
			if (getDolGlobalString($constforval) == "$value") {
				dolibarr_del_const($db, $constforval, $conf->entity);
			}
		}
	}
}


/*
 * View
 */

$form = new Form($db);

$page_name = "EventOrganizationSetup";

llxHeader('', $langs->trans($page_name), '', '', 0, 0, '', '', '', 'mod-admin page-eventorganization');

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = eventorganizationAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans($page_name), -1, 'eventorganization');

// Setup page goes here
//print '<span class="opacitymedium">'.$langs->trans("EventOrganizationSetupPage").'</span><br>';
print '<br>';


if ($action == 'edit') {
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';

	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre"><td class="titlefieldmiddle">'.$langs->trans("Parameter").'</td><td></td></tr>';

	foreach ($arrayofparameters as $constname => $val) {
		if ($val['enabled'] == 1) {
			$setupnotempty++;
			print '<tr class="oddeven"><td><!-- '.$constname.' -->';
			$tooltiphelp = (($langs->trans($constname . 'Tooltip') != $constname . 'Tooltip') ? $langs->trans($constname . 'Tooltip') : '');
			$tooltiphelp .= (($langs->trans($constname . 'Tooltip2') && $langs->trans($constname . 'Tooltip2') != $constname . 'Tooltip2') ? '<br><br>'."\n".$langs->trans($constname . 'Tooltip2') : '');
			print '<span id="helplink'.$constname.'" class="spanforparamtooltip">'.$form->textwithpicto($langs->trans($constname), $tooltiphelp, 1, 'info', '', 0, 3, 'tootips'.$constname).'</span>';
			print '</td><td>';

			if ($val['type'] == 'textarea') {
				print '<textarea class="flat" name="'.$constname.'" id="'.$constname.'" cols="50" rows="5" wrap="soft">' . "\n";
				print getDolGlobalString($constname);
				print "</textarea>\n";
			} elseif (preg_match('/emailtemplate:/', $val['type'])) {
				include_once DOL_DOCUMENT_ROOT . '/core/class/html.formmail.class.php';
				$formmail = new FormMail($db);

				$tmp = explode(':', $val['type']);
				$nboftemplates = $formmail->fetchAllEMailTemplate($tmp[1], $user, null, 1); // We set lang=null to get in priority record with no lang
				//$arraydefaultmessage = $formmail->getEMailTemplate($db, $tmp[1], $user, null, 0, 1, '');
				$arrayofmessagename = array();
				if (is_array($formmail->lines_model)) {
					foreach ($formmail->lines_model as $modelmail) {
						//var_dump($modelmail);
						$moreonlabel = '';
						if (!empty($arrayofmessagename[$modelmail->label])) {
							$moreonlabel = ' <span class="opacitymedium">(' . $langs->trans("SeveralLangugeVariatFound") . ')</span>';
						}
						// The 'label' is the key that is unique if we exclude the language
						$arrayofmessagename[$modelmail->id] = $langs->trans(preg_replace('/\(|\)/', '', $modelmail->label)) . $moreonlabel;
					}
				}
				print $form->selectarray($constname, $arrayofmessagename, getDolGlobalString($constname), 'None', 0, 0, '', 0, 0, 0, '', '', 1);
			} elseif (preg_match('/category:/', $val['type'])) {
				require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
				require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
				$formother = new FormOther($db);

				$tmp = explode(':', $val['type']);
				print img_picto('', 'category', 'class="pictofixedwidth"');
				print $formother->select_categories($tmp[1], getDolGlobalInt($constname), $constname, 0, $langs->trans('CustomersProspectsCategoriesShort'));
			} elseif (preg_match('/thirdparty_type/', $val['type'])) {
				require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
				$formcompany = new FormCompany($db);
				print $formcompany->selectProspectCustomerType(getDolGlobalString($constname), $constname, 'customerorprospect', 'form', '', '1');
			} elseif ($val['type'] == 'product') {
				if (isModEnabled("product") || isModEnabled("service")) {
					$selected = getDolGlobalInt($constname);
					print img_picto('', 'product', 'class="pictofixedwidth"');
					print $form->select_produits($selected, $constname, '', 0, 0, 1, 2, '', 0, array(), 0, '1', 0, 'maxwidth500 widthcentpercentminusx', 0, '', null, 1);
				}
			} else {
				print '<input name="' . $constname . '"  class="flat ' . (empty($val['css']) ? 'minwidth200' : $val['css']) . '" value="' . getDolGlobalString($constname) . '">';
			}
			print '</td></tr>';
		}
	}
	print '</table>';

	print $form->buttonsSaveCancel('Save', '');

	print '</form>';
	print '<br>';
} else {
	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre"><td class="titlefieldmiddle">'.$langs->trans("Parameter").'</td><td></td></tr>';

	foreach ($arrayofparameters as $constname => $val) {
		/*if ($val['enabled'] != 1) {
			continue;
		}*/
		$setupnotempty++;
		print '<tr class="oddeven">';
		print '<td><!-- '.$constname.' -->';
		$tooltiphelp = (($langs->trans($constname . 'Tooltip') != $constname . 'Tooltip') ? $langs->trans($constname . 'Tooltip') : '');
		$tooltiphelp .= (($langs->trans($constname . 'Tooltip2') && $langs->trans($constname . 'Tooltip2') != $constname . 'Tooltip2') ? '<br><br>'."\n".$langs->trans($constname . 'Tooltip2') : '');
		print $form->textwithpicto($langs->trans($constname), $tooltiphelp);
		print '</td><td>';

		if ($val['type'] == 'textarea') {
			print dol_nl2br(getDolGlobalString($constname));
		} elseif (preg_match('/emailtemplate:/', $val['type'])) {
			if (getDolGlobalString($constname)) {
				include_once DOL_DOCUMENT_ROOT . '/core/class/html.formmail.class.php';
				$formmail = new FormMail($db);

				$tmp = explode(':', $val['type']);
				$labelemailtemplate = getDolGlobalString($constname);
				if ($labelemailtemplate && $labelemailtemplate != '-1') {
					$template = $formmail->getEMailTemplate($db, $tmp[1], $user, $langs, getDolGlobalInt($constname));
					if (is_numeric($template) && $template < 0) {
						setEventMessages($formmail->error, $formmail->errors, 'errors');
					} else {
						if ($template->label != 'default') {
							print $langs->trans($template->label);
						}
					}
				}
			}
		} elseif (preg_match('/category:/', $val['type'])) {
			if (getDolGlobalInt($constname)) {
				$c = new Categorie($db);
				$result = $c->fetch(getDolGlobalInt($constname));
				if ($result < 0) {
					setEventMessages(null, $c->errors, 'errors');
				}
				$ways = $c->print_all_ways(' &gt;&gt; ', 'none', 0, 1); // $ways[0] = "ccc2 >> ccc2a >> ccc2a1" with html formatted text
				$toprint = array();
				foreach ($ways as $way) {
					$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories"' . ($c->color ? ' style="background: #' . $c->color . ';"' : ' style="background: #bbb"') . '>' . $way . '</li>';
				}
				print '<div class="select2-container-multi-dolibarr" style="width: 90%;"><ul class="select2-choices-dolibarr">' . implode(' ', $toprint) . '</ul></div>';
			}
		} elseif (preg_match('/thirdparty_type/', $val['type'])) {
			if (getDolGlobalString($constname) == 2) {
				print $langs->trans("Prospect");
			} elseif (getDolGlobalString($constname) == 3) {
				print $langs->trans("ProspectCustomer");
			} elseif (getDolGlobalString($constname) == 1) {
				print $langs->trans("Customer");
			} elseif (getDolGlobalString($constname) == 0) {
				print $langs->trans("NorProspectNorCustomer");
			}
		} elseif ($val['type'] == 'product') {
			$product = new Product($db);
			$idproduct = getDolGlobalInt($constname);
			if ($idproduct > 0) {
				$resprod = $product->fetch($idproduct);
				if ($resprod > 0) {
					print $product->getNomUrl(1);
				} elseif ($resprod < 0) {
					setEventMessages($product->error, $product->errors, "errors");
				}
			}
		} else {
			print getDolGlobalString($constname);
		}
		print '</td>';

		print '</tr>';
	}

	print '</table>';

	print '<div class="tabsAction">';
	print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&token='.newToken().'">'.$langs->trans("Modify").'</a>';
	print '</div>';
}


// Page end
print dol_get_fiche_end();

llxFooter();
$db->close();
