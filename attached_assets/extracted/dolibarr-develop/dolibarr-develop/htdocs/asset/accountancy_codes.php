<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018      Alexandre Spangaro   <aspangaro@open-dsi.fr>
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
 *  \file       htdocs/asset/accountancy_code.php
 *  \ingroup    asset
 *  \brief      Card with accountancy code on Asset
 */

// Load Dolibarr environment
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/asset.lib.php';
require_once DOL_DOCUMENT_ROOT . '/asset/class/asset.class.php';
require_once DOL_DOCUMENT_ROOT . '/asset/class/assetaccountancycodes.class.php';
require_once DOL_DOCUMENT_ROOT . '/asset/class/assetdepreciationoptions.class.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// Load translation files required by the page
$langs->loadLangs(array("assets", "companies"));

// Get parameters
$id = GETPOSTINT('id');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$cancel = GETPOST('cancel', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');

// Initialize a technical objects
$object = new Asset($db);
$assetaccountancycodes = new AssetAccountancyCodes($db);
$assetdepreciationoptions = new AssetDepreciationOptions($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->asset->dir_output . '/temp/massgeneration/' . $user->id;
$hookmanager->initHooks(array('assetaccountancycodes', 'globalcard')); // Note that conf->hooks_modules contains array
// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT . '/core/actions_fetchobject.inc.php'; // Must be 'include', not 'include_once'. Include fetch and fetch_thirdparty but not fetch_optionals
if ($id > 0 || !empty($ref)) {
	$upload_dir = $conf->asset->multidir_output[isset($object->entity) ? $object->entity : 1] . "/" . $object->id;
}

$permissiontoadd = $user->hasRight('asset', 'write'); // Used by the include of actions_addupdatedelete.inc.php

// Security check (enable the most restrictive one)
if ($user->socid > 0) {
	accessforbidden();
}
$isdraft = (($object->status == $object::STATUS_DRAFT) ? 1 : 0);
restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
if (!isModEnabled('asset')) {
	accessforbidden();
}

$object->asset_depreciation_options = &$assetdepreciationoptions;
$object->asset_accountancy_codes = &$assetaccountancycodes;
if (!empty($id)) {
	$depreciationoptionserrors = $assetdepreciationoptions->fetchDeprecationOptions($object->id, 0);
	$accountancycodeserrors = $assetaccountancycodes->fetchAccountancyCodes($object->id, 0);

	if ($depreciationoptionserrors < 0) {
		setEventMessages($assetdepreciationoptions->error, $assetdepreciationoptions->errors, 'errors');
	}
	if ($accountancycodeserrors < 0) {
		setEventMessages($assetaccountancycodes->error, $assetaccountancycodes->errors, 'errors');
	}
}


/*
 * Actions
 */

$reshook = $hookmanager->executeHooks('doActions', array(), $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}
if (empty($reshook)) {
	$backurlforlist = DOL_URL_ROOT.'/asset/list.php';

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
				$backtopage = $backurlforlist;
			} else {
				$backtopage = DOL_URL_ROOT.'/asset/accountancy_codes.php?id='.((!empty($id) && $id > 0) ? $id : '__ID__');
			}
		}
	}

	if ($cancel) {
		/*var_dump($cancel);var_dump($backtopage);var_dump($backtopageforcancel);exit;*/
		if (!empty($backtopageforcancel)) {
			header("Location: ".$backtopageforcancel);
			exit;
		} elseif (!empty($backtopage)) {
			header("Location: ".$backtopage);
			exit;
		}
		$action = '';
	}

	if ($action == "update" && $permissiontoadd) {
		$assetaccountancycodes->setAccountancyCodesFromPost();

		$result = $assetaccountancycodes->updateAccountancyCodes($user, $object->id);
		if ($result < 0) {
			setEventMessages($assetaccountancycodes->error, $assetaccountancycodes->errors, 'errors');
			$action = 'edit';
		} else {
			setEventMessage($langs->trans('RecordSaved'));
			header("Location: " . $_SERVER["PHP_SELF"] . '?id=' . $object->id);
			exit;
		}
	}
}


/*
 * View
 */

$form = new Form($db);

$help_url = '';
llxHeader('', $langs->trans('Asset'), $help_url, '', 0, 0, '', '', '', 'mod-asset page-card_accountancy');

if ($id > 0 || !empty($ref)) {
	$head = assetPrepareHead($object);
	print dol_get_fiche_head($head, 'accountancy_codes', $langs->trans("Asset"), -1, $object->picto);

	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="' . DOL_URL_ROOT . '/asset/list.php?restore_lastsearch_values=1">' . $langs->trans("BackToList") . '</a>';

	$morehtmlref = '<div class="refidno">';
	$morehtmlref .= '</div>';

	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

	print '<div class="fichecenter">';
	print '<div class="underbanner clearboth"></div>';
	print '</div>';

	print '<br>';

	if ($action == 'edit') {
		print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . '">';
		print '<input type="hidden" name="token" value="' . newToken() . '">';
		print '<input type="hidden" name="action" value="update">';
		if ($backtopage) {
			print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
		}
		if ($backtopageforcancel) {
			print '<input type="hidden" name="backtopageforcancel" value="' . $backtopageforcancel . '">';
		}

		include DOL_DOCUMENT_ROOT . '/asset/tpl/accountancy_codes_edit.tpl.php';

		print $form->buttonsSaveCancel();

		print '</form>';
	} else {
		include DOL_DOCUMENT_ROOT . '/asset/tpl/accountancy_codes_view.tpl.php';
	}

	print dol_get_fiche_end();

	if ($action != 'edit') {
		print '<div class="tabsAction">' . "\n";
		$parameters = array();
		$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if ($reshook < 0) {
			setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
		}

		if (empty($reshook)) {
			if ($object->status == $object::STATUS_DRAFT/* && !empty($object->enabled_modes)*/) {
				print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"] . '?id=' . $object->id . '&action=edit&token=' . newToken(), '', $permissiontoadd);
			}
		}
		print '</div>' . "\n";
	}
}

// End of page
llxFooter();
$db->close();
