<?php
/* Copyright (C) 2022      Open-DSI             <support@open-dsi.fr>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	    \file       htdocs/ecommerceng/admin/setup.php
 *		\ingroup    ecommerceng
 *		\brief      Page to setup ecommerceng module
 */

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';		// to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once(DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php');
require_once(DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php');
require_once(DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php');
require_once(DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php');
dol_include_once('/ecommerceng/lib/eCommerce.lib.php');
dol_include_once('/ecommerceng/admin/class/data/eCommerceDict.class.php');

$langs->loadLangs(array("admin", "companies", "bills", "accountancy", "banks", "oauth", "ecommerce@ecommerceng", "opendsi@ecommerceng"));

if (!$user->admin && !$user->rights->ecommerceng->site && !empty($conf->societe->enabled)) accessforbidden();

$id = GETPOST('id', 'int');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'aZ09');

include dol_buildpath('/ecommerceng/admin/actions_selectsite.inc.php');

$object = new eCommerceSite($db);
if (!($id > 0)) {
	$sites = $object->listSites();
	if (!empty($sites)) {
		$id = array_values($sites)[0]['id'];
	}
	$action = '';
}
if ($id > 0) {
	$result = $object->fetch($id);
	if ($result < 0) {
		accessforbidden($object->errorsToString());
	} elseif ($result == 0) {
		$langs->load('errors');
		accessforbidden($langs->trans('ErrorRecordNotFound'));
	}
} else {
	accessforbidden($langs->trans('ErrorRecordNotFound'));
}

if (empty($conf->societe->enabled)) {
	accessforbidden($langs->trans('ModuleDisabled'));
}
if (empty($conf->categorie->enabled)) {
	accessforbidden($langs->trans('ModuleDisabled') . ' : ' . $langs->trans('Categories'));
}

$thirdparty_static = new Societe($db);
$contact_static = new Contact($db);
$extrafields = new ExtraFields($db);
$extrafields_thirdparty_labels = $extrafields->fetch_name_optionals_label($thirdparty_static->table_element);
$extrafields_thirdparty_labels_clean = array();
foreach ($extrafields_thirdparty_labels as $key => $label) {
	if (preg_match('/^ecommerceng_/', $key)) continue;
	$extrafields_thirdparty_labels_clean[$key] = $label;
}
$extrafields_contact_labels = $extrafields->fetch_name_optionals_label($contact_static->table_element);
$extrafields_contact_labels_clean = array();
foreach ($extrafields_contact_labels as $key => $label) {
	if (preg_match('/^ecommerceng_/', $key)) continue;
	$extrafields_contact_labels_clean[$key] = $label;
}

$extra_fields_list = array(
	$thirdparty_static->table_element => array('label' => 'ThirdParty', 'extra_fields' => $extrafields_thirdparty_labels_clean, 'default'=> true, 'metadata'=> true, 'attribute'=> []),
	$contact_static->table_element => array('label' => 'Contact', 'extra_fields' => $extrafields_contact_labels_clean, 'default'=> true, 'metadata'=> false, 'attribute'=> []),
);


/*
 *	Actions
 */
$error = 0;

include dol_buildpath('/ecommerceng/admin/actions_extrafields.inc.php');

if ($action == 'set_options') {
	$object->oldcopy = clone $object;

	$object->fk_cat_societe = GETPOST('fk_cat_societe', 'int');
	$object->fk_cat_societe = $object->fk_cat_societe > 0 ? $object->fk_cat_societe : 0;
	$object->parameters['realtime_dtoe']['thridparty'] = GETPOST('realtime_dtoe_thridparty', 'int') ? 1 : 0;
	$object->parameters['realtime_dtoe']['contact'] = GETPOST('realtime_dtoe_contact', 'int') ? 1 : 0;
	$object->fk_anonymous_thirdparty = GETPOST('fk_anonymous_thirdparty', 'int');
	$object->fk_anonymous_thirdparty = $object->fk_anonymous_thirdparty > 0 ? $object->fk_anonymous_thirdparty : 0;
	$object->parameters['customer_roles'] = GETPOST('customer_roles', 'alphanohtml');
	$object->parameters['dont_search_company_by_name_and_zip'] = GETPOST('dont_search_company_by_name_and_zip', 'int') ? 1 : 0;
	$object->parameters['dont_update_dolibarr_company'] = GETPOST('dont_update_dolibarr_company', 'int') ? 1 : 0;

	if(empty($object->fk_cat_societe)) {
		setEventMessage($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ECommerceCatSociete")), 'errors');
		$error++;
	}

	if (!$error) {
		$result = $object->update($user);

		if ($result < 0) {
			setEventMessages($object->error, $object->errors, 'errors');
		} else {
			setEventMessage($langs->trans("SetupSaved"));
			header("Location: " . $_SERVER["PHP_SELF"] . '?id=' . $object->id);
			exit;
		}
	}
}


/*
 *	View
 */

$form = new Form($db);
$category_static = new Categorie($db);

$wikihelp='EN:ECommerceNg_En|FR:ECommerceNg_Fr|ES:ECommerceNg_Es';
llxHeader('', $langs->trans("ECommerceSetup"), $wikihelp, '', 0, 0, array(
	'/ecommerceng/js/form.js',
));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("ECommerceSetup"),$linkback,'title_setup');

include dol_buildpath('/ecommerceng/admin/tpl/selectsite.tpl.php');

$head=ecommercengConfigSitePrepareHead($object);

print dol_get_fiche_head($head, 'thirdparty', $langs->trans("Module107100Name"), 0, 'opendsi@ecommerceng');

/**
 * Settings.
 */

print '<div id="options"></div>';
print load_fiche_titre($langs->trans("Parameters"), '', '');

print '<form method="post" action="'.$_SERVER["PHP_SELF"] . '?id=' . $object->id . '#options">';
print '<input type="hidden" name="token" value="'.ecommercengNewToken().'">';
print '<input type="hidden" name="action" value="set_options">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td class="20p">'.$langs->trans("Parameters").'</td>'."\n";
print '<td>'.$langs->trans("Description").'</td>'."\n";
print '<td class="right">'.$langs->trans("Value").'</td>'."\n";
print "</tr>\n";

// Third party category
print '<tr class="oddeven">' . "\n";
print '<td class="fieldrequired">'.$langs->trans("ECommerceCatSociete").'</td>'."\n";
print '<td>'.$langs->trans("ECommerceCatSocieteDescription").'</td>'."\n";
print '<td class="right">' . "\n";
$categories = $category_static->get_full_arbo(Categorie::TYPE_CUSTOMER);
$categories_list = array();
foreach ($categories as $category) {
	$categories_list[$category['id']] = $category['label'];
}
print $form->selectarray('fk_cat_societe', $categories_list, $object->fk_cat_societe, 1, 0, 0, '', 0, 0, 0, '', 'minwidth200 centpercent') . "\n";
print '</td></tr>' . "\n";

// Synchronize third party real time from dolibarr to site
print '<tr class="oddeven">' . "\n";
print '<td>'.$langs->trans("ECommerceRealTimeSynchroDolibarrToECommerceThirdParty").'</td>'."\n";
print '<td>'.$langs->trans("ECommerceRealTimeSynchroDolibarrToECommerceThirdPartyDescription").'</td>'."\n";
print '<td class="right">' . "\n";
print '<input type="checkbox" name="realtime_dtoe_thridparty" value="1"' . (!empty($object->parameters['realtime_dtoe']['thridparty']) ? ' checked' : '') . ' />' . "\n";
print '</td></tr>' . "\n";

// Synchronize contact real time from dolibarr to site
print '<tr class="oddeven">' . "\n";
print '<td>'.$langs->trans("ECommerceRealTimeSynchroDolibarrToECommerceContact").'</td>'."\n";
print '<td>'.$langs->trans("ECommerceRealTimeSynchroDolibarrToECommerceContactDescription").'</td>'."\n";
print '<td class="right">' . "\n";
print '<input type="checkbox" name="realtime_dtoe_contact" value="1"' . (!empty($object->parameters['realtime_dtoe']['contact']) ? ' checked' : '') . ' />' . "\n";
print '</td></tr>' . "\n";

// Third party anonymous
print '<tr class="oddeven">' . "\n";
print '<td>'.$langs->trans("ThirdPartyForNonLoggedUsers").'</td>'."\n";
print '<td>'.$langs->trans("SynchUnkownCustomersOnThirdParty").'</td>'."\n";
print '<td class="right">' . "\n";
print $form->select_company($object->fk_anonymous_thirdparty, 'fk_anonymous_thirdparty', '', 1) . "\n";
print '</td></tr>' . "\n";

// Supported customer roles
print '<tr class="oddeven">' . "\n";
print '<td>' . $langs->trans("ECommerceWoocommerceCustomerRolesSupported") . '</td>' . "\n";
print '<td>' . $langs->trans("ECommerceWoocommerceCustomerRolesSupportedDescription") . '</td>' . "\n";
print '<td class="right">' . "\n";
$value = isset($object->parameters['customer_roles']) ? $object->parameters['customer_roles'] : 'customer';
print '<input type="text" class="flat centpercent" name="customer_roles" value="' . dol_escape_htmltag($value) . '">' . "\n";
print '</td></tr>' . "\n";

// Don't search company by name and zip
print '<tr class="oddeven">' . "\n";
print '<td>' . $langs->trans("ECommerceDontSearchCompanyByNameAndZip") . '</td>' . "\n";
print '<td>' . $langs->trans("ECommerceDontSearchCompanyByNameAndZipDescription") . '</td>' . "\n";
print '<td class="right">' . "\n";
print '<input type="checkbox" name="dont_search_company_by_name_and_zip" value="1"' . (!empty($object->parameters['dont_search_company_by_name_and_zip']) ? ' checked' : '') . ' />' . "\n";
print '</td></tr>' . "\n";

// Don't update the third party
print '<tr class="oddeven">' . "\n";
print '<td>' . $langs->trans("ECommerceDontUpdateDolibarrCompany") . '</td>' . "\n";
print '<td>' . $langs->trans("ECommerceDontUpdateDolibarrCompanyDescription") . '</td>' . "\n";
print '<td class="right">' . "\n";
print '<input type="checkbox" name="dont_update_dolibarr_company" value="1"' . (!empty($object->parameters['dont_update_dolibarr_company']) ? ' checked' : '') . ' />' . "\n";
print '</td></tr>' . "\n";

print '</table>'."\n";

print '<br>';
print '<div align="center">';
print '<input type="submit" class="button" value="' . $langs->trans('Modify') . '" />';
print '</div>';

print '</form>';

/**
 * Extra fields.
 */
include dol_buildpath('/ecommerceng/admin/tpl/extrafields.tpl.php');

print dol_get_fiche_end();

llxFooter();

$db->close();
