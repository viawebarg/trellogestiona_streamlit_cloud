<?php
/* Copyright (C) 2013-2016 Olivier Geffroy		<jeff@jeffinfo.com>
 * Copyright (C) 2013-2021 Alexandre Spangaro	<aspangaro@open-dsi.fr>
 * Copyright (C) 2014-2015 Ari Elbaz (elarifr)	<github@accedinfo.com>
 * Copyright (C) 2014-2016 Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2014	   Juanjo Menent		<jmenent@2byte.es>
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
 * \file 		htdocs/accountancy/customer/lines.php
 * \ingroup 	Accountancy (Double entries)
 * \brief 		Page of detail of the lines of ventilation of invoices customers
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formaccounting.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/accountancy/class/accountingaccount.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/accounting.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Societe $mysoc
 * @var Translate $langs
 * @var User $user
 */

// Load translation files required by the page
$langs->loadLangs(array("bills", "compta", "accountancy", "productbatch", "products"));

$optioncss = GETPOST('optioncss', 'aZ'); // Option for the css output (always '' except when 'print')

$account_parent = GETPOST('account_parent');
$changeaccount = GETPOST('changeaccount', 'array');
// Search Getpost
$search_societe = GETPOST('search_societe', 'alpha');
$search_lineid = GETPOST('search_lineid', 'alpha');
$search_ref = GETPOST('search_ref', 'alpha');
$search_invoice = GETPOST('search_invoice', 'alpha');
$search_label = GETPOST('search_label', 'alpha');
$search_desc = GETPOST('search_desc', 'alpha');
$search_amount = GETPOST('search_amount', 'alpha');
$search_account = GETPOST('search_account', 'alpha');
$search_vat = GETPOST('search_vat', 'alpha');
$search_date_startday = GETPOSTINT('search_date_startday');
$search_date_startmonth = GETPOSTINT('search_date_startmonth');
$search_date_startyear = GETPOSTINT('search_date_startyear');
$search_date_endday = GETPOSTINT('search_date_endday');
$search_date_endmonth = GETPOSTINT('search_date_endmonth');
$search_date_endyear = GETPOSTINT('search_date_endyear');
$search_date_start = dol_mktime(0, 0, 0, $search_date_startmonth, $search_date_startday, $search_date_startyear);	// Use tzserver
$search_date_end = dol_mktime(23, 59, 59, $search_date_endmonth, $search_date_endday, $search_date_endyear);
$search_country = GETPOST('search_country', 'aZ09');
$search_tvaintra = GETPOST('search_tvaintra', 'alpha');

// Load variable for pagination
$limit = GETPOSTINT('limit') ? GETPOSTINT('limit') : getDolGlobalString('ACCOUNTING_LIMIT_LIST_VENTILATION', $conf->liste_limit);
$sortfield = GETPOST('sortfield', 'aZ09comma');
$sortorder = GETPOST('sortorder', 'aZ09comma');
$page = GETPOSTISSET('pageplusone') ? (GETPOSTINT('pageplusone') - 1) : GETPOSTINT("page");
if (empty($page) || $page < 0) {
	$page = 0;
}
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (!$sortfield) {
	$sortfield = "f.datef, f.ref, fd.rowid";
}
if (!$sortorder) {
	if (getDolGlobalInt('ACCOUNTING_LIST_SORT_VENTILATION_DONE') > 0) {
		$sortorder = "DESC";
	} else {
		$sortorder = "ASC";
	}
}

// Security check
if (!isModEnabled('accounting')) {
	accessforbidden();
}
if ($user->socid > 0) {
	accessforbidden();
}
if (!$user->hasRight('accounting', 'bind', 'write')) {
	accessforbidden();
}

// Initialize technical objects
$contextpage = 'accountancycustomerlines';
$hookmanager->initHooks([$contextpage ]);
$formaccounting = new FormAccounting($db);


$arrayfields = array(
	'fd.rowid' 				=> array('label' => "LineId", 				'position' => 1, 'checked' => '1', 'enabled' => '1'),
	'f.ref' 				=> array('label' => "Invoice", 				'position' => 1, 'checked' => '1', 'enabled' => '1'),
	'f.datef' 				=> array('label' => "Date", 				'position' => 1, 'checked' => '1', 'enabled' => '1'), // f.datef, f.ref, fd.rowid
	'p.ref' 				=> array('label' => "ProductRef", 			'position' => 1, 'checked' => '1', 'enabled' => '1'),
	'fd.description' 		=> array('label' => "ProductDescription", 	'position' => 1, 'checked' => '1', 'enabled' => '1'),
	'fd.total_ht' 			=> array('label' => "Amount", 				'position' => 1, 'checked' => '1', 'enabled' => '1'),
	'fd.tva_tx'				=> array('label' => "VATRate", 				'position' => 1, 'checked' => '1', 'enabled' => '1'),
	's.nom' 				=> array('label' => "ThirdParty", 			'position' => 1, 'checked' => '1', 'enabled' => '1'),
	'co.label' 				=> array('label' => "Country", 				'position' => 1, 'checked' => '1', 'enabled' => '1'),
	's.tva_intra' 			=> array('label' => "VATIntra", 			'position' => 1, 'checked' => '1', 'enabled' => '1'),
	'aa.account_number' 	=> array('label' => "AccountAccounting", 	'position' => 1, 'checked' => '1', 'enabled' => '1'),
);
// @phpstan-ignore-next-line
$arrayfields = dol_sort_array($arrayfields, 'position');

/*
 * Actions
 */

$parameters = array('arrayfields' => &$arrayfields);
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
	// Selection of new fields
	include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

	// Purge search criteria
	if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')) { // All tests are required to be compatible with all browsers
		$search_societe = '';
		$search_lineid = '';
		$search_ref = '';
		$search_invoice = '';
		$search_label = '';
		$search_desc = '';
		$search_amount = '';
		$search_account = '';
		$search_vat = '';
		$search_date_startday = '';
		$search_date_startmonth = '';
		$search_date_startyear = '';
		$search_date_endday = '';
		$search_date_endmonth = '';
		$search_date_endyear = '';
		$search_date_start = '';
		$search_date_end = '';
		$search_country = '';
		$search_tvaintra = '';
	}

	if (is_array($changeaccount) && count($changeaccount) > 0 && $user->hasRight('accounting', 'bind', 'write')) {
		$error = 0;

		if (!(GETPOSTINT('account_parent') >= 0)) {
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Account")), null, 'errors');
		}

		if (!$error) {
			$db->begin();

			$sql1 = "UPDATE ".MAIN_DB_PREFIX."facturedet";
			$sql1 .= " SET fk_code_ventilation = ".(GETPOSTINT('account_parent') > 0 ? GETPOSTINT('account_parent') : 0);
			$sql1 .= ' WHERE rowid IN ('.$db->sanitize(implode(',', $changeaccount)).')';

			dol_syslog('accountancy/customer/lines.php::changeaccount sql= '.$sql1);
			$resql1 = $db->query($sql1);
			if (!$resql1) {
				$error++;
				setEventMessages($db->lasterror(), null, 'errors');
			}
			if (!$error) {
				$db->commit();
				setEventMessages($langs->trans("Save"), null, 'mesgs');
			} else {
				$db->rollback();
				setEventMessages($db->lasterror(), null, 'errors');
			}

			$account_parent = ''; // Protection to avoid to mass apply it a second time
		}
	}
}

if (GETPOST('sortfield') == 'f.datef, f.ref, fd.rowid') {
	$value = (GETPOST('sortorder') == 'asc,asc,asc' ? 0 : 1);
	require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
	$res = dolibarr_set_const($db, "ACCOUNTING_LIST_SORT_VENTILATION_DONE", $value, 'yesno', 0, '', $conf->entity);
}

/*
 * View
 */

$form = new Form($db);
$formother = new FormOther($db);

$help_url = 'EN:Module_Double_Entry_Accounting|FR:Module_Comptabilit&eacute;_en_Partie_Double#Liaisons_comptables';

llxHeader('', $langs->trans("CustomersVentilation").' - '.$langs->trans("Dispatched"), $help_url, '', 0, 0, '', '', '', 'mod-accountancy accountancy-customer page-lines');

print '<script type="text/javascript">
			$(function () {
				$(\'#select-all\').click(function(event) {
				    // Iterate each checkbox
				    $(\':checkbox\').each(function() {
				    	this.checked = true;
				    });
			    });
			    $(\'#unselect-all\').click(function(event) {
				    // Iterate each checkbox
				    $(\':checkbox\').each(function() {
				    	this.checked = false;
				    });
			    });
			});
			 </script>';

/*
 * Customer Invoice lines
 */
$sql = "SELECT f.rowid as facid, f.ref as ref, f.type as ftype, f.situation_cycle_ref, f.datef, f.ref_client,";
$sql .= " fd.rowid, fd.description, fd.product_type as line_type, fd.total_ht, fd.total_tva, fd.tva_tx, fd.vat_src_code, fd.total_ttc, fd.situation_percent,";
$sql .= " s.rowid as socid, s.nom as name, s.code_client,";
if (getDolGlobalString('MAIN_COMPANY_PERENTITY_SHARED')) {
	$sql .= " spe.accountancy_code_customer as code_compta_client,";
	$sql .= " spe.accountancy_code_supplier as code_compta_fournisseur,";
} else {
	$sql .= " s.code_compta as code_compta_client,";
	$sql .= " s.code_compta_fournisseur,";
}
$sql .= " p.rowid as product_id, p.fk_product_type as product_type, p.ref as product_ref, p.label as product_label, p.tobuy, p.tosell,";
if (getDolGlobalString('MAIN_PRODUCT_PERENTITY_SHARED')) {
	$sql .= " ppe.accountancy_code_sell, ppe.accountancy_code_sell_intra, ppe.accountancy_code_sell_export,";
} else {
	$sql .= " p.accountancy_code_sell, p.accountancy_code_sell_intra, p.accountancy_code_sell_export,";
}
$sql .= " aa.rowid as fk_compte, aa.account_number, aa.label as label_account, aa.labelshort as labelshort_account,";
$sql .= " fd.situation_percent,";
$sql .= " co.code as country_code, co.label as country,";
$sql .= " s.rowid as socid, s.nom as name, s.tva_intra, s.email, s.town, s.zip, s.fk_pays, s.client, s.fournisseur, s.code_client, s.code_fournisseur";
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListSelect', $parameters); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;
$sql .= " FROM ".MAIN_DB_PREFIX."facturedet as fd";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON p.rowid = fd.fk_product";
if (getDolGlobalString('MAIN_PRODUCT_PERENTITY_SHARED')) {
	$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "product_perentity as ppe ON ppe.fk_product = p.rowid AND ppe.entity = " . ((int) $conf->entity);
}
$sql .= " INNER JOIN ".MAIN_DB_PREFIX."accounting_account as aa ON aa.rowid = fd.fk_code_ventilation";
$sql .= " INNER JOIN ".MAIN_DB_PREFIX."facture as f ON f.rowid = fd.fk_facture";
$sql .= " INNER JOIN ".MAIN_DB_PREFIX."societe as s ON s.rowid = f.fk_soc";
if (getDolGlobalString('MAIN_COMPANY_PERENTITY_SHARED')) {
	$sql .= " LEFT JOIN " . MAIN_DB_PREFIX . "societe_perentity as spe ON spe.fk_soc = s.rowid AND spe.entity = " . ((int) $conf->entity);
}
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as co ON co.rowid = s.fk_pays ";
// Add table from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListFrom', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;
$sql .= " WHERE fd.fk_code_ventilation > 0";
$sql .= " AND f.entity IN (".getEntity('invoice', 0).")"; // We don't share object for accountancy
$sql .= " AND f.fk_statut > 0";
if (getDolGlobalString('FACTURE_DEPOSITS_ARE_JUST_PAYMENTS')) {
	$sql .= " AND f.type IN (".Facture::TYPE_STANDARD.",".Facture::TYPE_REPLACEMENT.",".Facture::TYPE_CREDIT_NOTE.",".Facture::TYPE_SITUATION.")";
} else {
	$sql .= " AND f.type IN (".Facture::TYPE_STANDARD.",".Facture::TYPE_REPLACEMENT.",".Facture::TYPE_CREDIT_NOTE.",".Facture::TYPE_DEPOSIT.",".Facture::TYPE_SITUATION.")";
}
// Add search filter like
if ($search_societe) {
	$sql .= natural_search('s.nom', $search_societe);
}
if ($search_lineid) {
	$sql .= natural_search("fd.rowid", $search_lineid, 1);
}
if (strlen(trim($search_invoice))) {
	$sql .= natural_search("f.ref", $search_invoice);
}
if (strlen(trim($search_ref))) {
	$sql .= natural_search("p.ref", $search_ref);
}
if (strlen(trim($search_label))) {
	$sql .= natural_search("p.label", $search_label);
}
if (strlen(trim($search_desc))) {
	$sql .= natural_search("fd.description", $search_desc);
}
if (strlen(trim($search_amount))) {
	$sql .= natural_search("fd.total_ht", $search_amount, 1);
}
if (strlen(trim($search_account))) {
	$sql .= natural_search("aa.account_number", $search_account);
}
if (strlen(trim($search_vat))) {
	$sql .= natural_search("fd.tva_tx", price2num($search_vat), 1);
}
if ($search_date_start) {
	$sql .= " AND f.datef >= '".$db->idate($search_date_start)."'";
}
if ($search_date_end) {
	$sql .= " AND f.datef <= '".$db->idate($search_date_end)."'";
}
if (strlen(trim($search_country))) {
	$arrayofcode = getCountriesInEEC();
	$country_code_in_EEC = $country_code_in_EEC_without_me = '';
	foreach ($arrayofcode as $key => $value) {
		$country_code_in_EEC .= ($country_code_in_EEC ? "," : "")."'".$value."'";
		if ($value != $mysoc->country_code) {
			$country_code_in_EEC_without_me .= ($country_code_in_EEC_without_me ? "," : "")."'".$value."'";
		}
	}
	if ($search_country == 'special_allnotme') {
		$sql .= " AND co.code <> '".$db->escape($mysoc->country_code)."'";
	} elseif ($search_country == 'special_eec') {
		$sql .= " AND co.code IN (".$db->sanitize($country_code_in_EEC, 1).")";
	} elseif ($search_country == 'special_eecnotme') {
		$sql .= " AND co.code IN (".$db->sanitize($country_code_in_EEC_without_me, 1).")";
	} elseif ($search_country == 'special_noteec') {
		$sql .= " AND co.code NOT IN (".$db->sanitize($country_code_in_EEC, 1).")";
	} else {
		$sql .= natural_search("co.code", $search_country);
	}
}
if (strlen(trim($search_tvaintra))) {
	$sql .= natural_search("s.tva_intra", $search_tvaintra);
}
$sql .= " AND f.entity IN (".getEntity('invoice', 0).")"; // We don't share object for accountancy
// Add where from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListWhere', $parameters); // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;

$sql .= $db->order($sortfield, $sortorder);

// Count total nb of records
$nbtotalofrecords = '';
if (!getDolGlobalInt('MAIN_DISABLE_FULL_SCANLIST')) {
	$result = $db->query($sql);
	$nbtotalofrecords = $db->num_rows($result);
	if (($page * $limit) > $nbtotalofrecords) {	// if total resultset is smaller then paging size (filtering), goto and load page 0
		$page = 0;
		$offset = 0;
	}
}

$sql .= $db->plimit($limit + 1, $offset);

dol_syslog("/accountancy/customer/lines.php", LOG_DEBUG);
$result = $db->query($sql);
if ($result) {
	$num_lines = $db->num_rows($result);
	$i = 0;

	$param = '';
	if ($contextpage != $_SERVER["PHP_SELF"]) {
		$param .= '&contextpage='.urlencode($contextpage);
	}
	if ($limit > 0 && $limit != $conf->liste_limit) {
		$param .= '&limit='.((int) $limit);
	}
	if ($search_societe) {
		$param .= "&search_societe=".urlencode($search_societe);
	}
	if ($search_invoice) {
		$param .= "&search_invoice=".urlencode($search_invoice);
	}
	if ($search_ref) {
		$param .= "&search_ref=".urlencode($search_ref);
	}
	if ($search_label) {
		$param .= "&search_label=".urlencode($search_label);
	}
	if ($search_desc) {
		$param .= "&search_desc=".urlencode($search_desc);
	}
	if ($search_account) {
		$param .= "&search_account=".urlencode($search_account);
	}
	if ($search_vat) {
		$param .= "&search_vat=".urlencode($search_vat);
	}
	if ($search_date_startday) {
		$param .= '&search_date_startday='.urlencode((string) ($search_date_startday));
	}
	if ($search_date_startmonth) {
		$param .= '&search_date_startmonth='.urlencode((string) ($search_date_startmonth));
	}
	if ($search_date_startyear) {
		$param .= '&search_date_startyear='.urlencode((string) ($search_date_startyear));
	}
	if ($search_date_endday) {
		$param .= '&search_date_endday='.urlencode((string) ($search_date_endday));
	}
	if ($search_date_endmonth) {
		$param .= '&search_date_endmonth='.urlencode((string) ($search_date_endmonth));
	}
	if ($search_date_endyear) {
		$param .= '&search_date_endyear='.urlencode((string) ($search_date_endyear));
	}
	if ($search_country) {
		$param .= "&search_country=".urlencode($search_country);
	}
	if ($search_tvaintra) {
		$param .= "&search_tvaintra=".urlencode($search_tvaintra);
	}
	// Add $param from hooks
	$parameters = array('param' => &$param);
	$reshook = $hookmanager->executeHooks('printFieldListSearchParam', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	$param .= $hookmanager->resPrint;

	print '<form action="'.$_SERVER["PHP_SELF"].'" method="post">'."\n";
	print '<input type="hidden" name="action" value="ventil">';
	if ($optioncss != '') {
		print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
	}
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
	print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
	print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
	print '<input type="hidden" name="page" value="'.$page.'">';

	// @phan-suppress-next-line PhanPluginSuspiciousParamOrder
	print_barre_liste($langs->trans("InvoiceLinesDone").'<br><span class="opacitymedium small">'.$langs->trans("DescVentilDoneCustomer").'</span>', $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num_lines, $nbtotalofrecords, 'title_accountancy', 0, '', '', $limit, 0, 0, 1);

	print '<br>'.$langs->trans("ChangeAccount").' <div class="inline-block paddingbottom marginbottomonly">';
	print $formaccounting->select_account($account_parent, 'account_parent', 2, array(), 0, 0, 'maxwidth300 maxwidthonsmartphone valignmiddle');
	print '<input type="submit" class="button small smallpaddingimp valignmiddle" value="'.$langs->trans("ChangeBinding").'"/></div>';

	$moreforfilter = '';

	$varpage = $contextpage;
	$htmlofselectarray = $form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage, $conf->main_checkbox_left_column);  // This also change content of $arrayfields with user setup
	$selectedfields = $htmlofselectarray;
	$selectedfields .= $form->showCheckAddButtons('checkforselect', 1);

	print '<div class="div-table-responsive">';
	print '<table class="tagtable liste'.($moreforfilter ? " listwithfilterbefore" : "").'">'."\n";

	print '<tr class="liste_titre_filter">';
	// Action column
	if ($conf->main_checkbox_left_column) {
		print '<td class="liste_titre maxwidthsearch center actioncolumn">';
		$searchpicto = $form->showFilterButtons('left');
		print $searchpicto;
		print '</td>';
	}
	// Line ID
	if (!empty($arrayfields['fd.rowid']['checked'])) {
		print '<td class="liste_titre" data-key="lineid">';
		print '<input type="text" class="flat maxwidth40" name="search_lineid" value="'.dol_escape_htmltag($search_lineid).'">';
		print '</td>';
	}
	// Ref invoice
	if (!empty($arrayfields['f.ref']['checked'])) {
		print '<td class="liste_titre" data-key="invoice">';
		print '<input type="text" class="flat maxwidth50" name="search_invoice" value="'.dol_escape_htmltag($search_invoice).'">';
		print '</td>';
	}
	// date
	if (!empty($arrayfields['f.datef']['checked'])) {
		print '<td class="liste_titre center nowraponall">';
		print '<div class="nowrapfordate">';
		print $form->selectDate($search_date_start ? $search_date_start : -1, 'search_date_start', 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('From'));
		print '</div>';
		print '<div class="nowrapfordate">';
		print $form->selectDate($search_date_end ? $search_date_end : -1, 'search_date_end', 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('to'));
		print '</div>';
		print '</td>';
	}
	// Product ref
	if (!empty($arrayfields['p.ref']['checked'])) {
		print '<td class="liste_titre" data-key="ref">';
		print '<input type="text" class="flat maxwidth50" name="search_ref" value="'.dol_escape_htmltag($search_ref).'">';
		print '</td>';
	}
	//print '<td class="liste_titre"><input type="text" class="flat maxwidth50" name="search_label" value="' . dol_escape_htmltag($search_label) . '"></td>';
	// description
	if (!empty($arrayfields['fd.description']['checked'])) {
		print '<td class="liste_titre" data-key="desc">';
		print '<input type="text" class="flat maxwidth50" name="search_desc" value="'.dol_escape_htmltag($search_desc).'">';
		print '</td>';
	}
	// amount
	if (!empty($arrayfields['fd.total_ht']['checked'])) {
		print '<td class="liste_titre" data-key="amount">';
		print '<input type="text" class="right flat maxwidth50" name="search_amount" value="'.dol_escape_htmltag($search_amount).'">';
		print '</td>';
	}
	// VAT
	if (!empty($arrayfields['fd.tva_tx']['checked'])) {
		print '<td class="liste_titre" data-key="vat">';
		print '<input type="text" class="right flat maxwidth50" placeholder="%" name="search_vat" size="1" value="'.dol_escape_htmltag($search_vat).'">';
		print '</td>';
	}
	// Thirdparty
	if (!empty($arrayfields['s.nom']['checked'])) {
		print '<td class="liste_titre" data-key="societe">';
		print '<input type="text" class="flat maxwidth75imp" name="search_societe" value="'.dol_escape_htmltag($search_societe).'">';
		print '</td>';
	}
	// Country
	if (!empty($arrayfields['co.label']['checked'])) {
		print '<td class="liste_titre" data-key="country">';
		print $form->select_country($search_country, 'search_country', '', 0, 'maxwidth150', 'code2', 1, 0, 1);
		//print '<input type="text" class="flat maxwidth50" name="search_country" value="' . dol_escape_htmltag($search_country) . '">';
		print '</td>';
	}
	// TVA Intracom
	if (!empty($arrayfields['s.tva_intra']['checked'])) {
		print '<td class="liste_titre">';
		print '<input type="text" class="flat maxwidth50" name="search_tvaintra" value="'.dol_escape_htmltag($search_tvaintra).'">';
		print '</td>';
	}
	// Account
	if (!empty($arrayfields['aa.account_number']['checked'])) {
		print '<td class="liste_titre">';
		print '<input type="text" class="flat maxwidth50" name="search_account" value="'.dol_escape_htmltag($search_account).'">';
		print '</td>';
	}
	// Fields from hook
	$parameters = array('arrayfields' => $arrayfields);
	$reshook = $hookmanager->executeHooks('printFieldListOption', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;
	// Action column
	if (!$conf->main_checkbox_left_column) {
		print '<td class="liste_titre center maxwidthsearch actioncolumn">';
		$searchpicto = $form->showFilterButtons();
		print $searchpicto;
		print '</td>';
	}
	print "</tr>\n";

	// Fields title label
	// --------------------------------------------------------------------

	$totalarray = array();
	$totalarray['nbfield'] = 0;

	print '<tr class="liste_titre">';
	// Action column
	if ($conf->main_checkbox_left_column) {
		print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";
		$totalarray['nbfield']++;
	}
	// Line ID
	if (!empty($arrayfields['fd.rowid']['checked'])) {
		print_liste_field_titre($arrayfields['fd.rowid']['label'], $_SERVER["PHP_SELF"], "fd.rowid", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	// Ref invoice
	if (!empty($arrayfields['f.ref']['checked'])) {
		print_liste_field_titre($arrayfields['f.ref']['label'], $_SERVER["PHP_SELF"], "f.ref", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	// date
	if (!empty($arrayfields['f.datef']['checked'])) {
		print_liste_field_titre($arrayfields['f.datef']['label'], $_SERVER["PHP_SELF"], "f.datef, f.ref, fd.rowid", "", $param, '', $sortfield, $sortorder, 'center ');
		$totalarray['nbfield']++;
	}
	// Product ref
	if (!empty($arrayfields['p.ref']['checked'])) {
		print_liste_field_titre($arrayfields['p.ref']['label'], $_SERVER["PHP_SELF"], "p.ref", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	//print_liste_field_titre("ProductLabel", $_SERVER["PHP_SELF"], "p.label", "", $param, '', $sortfield, $sortorder);
	// description
	if (!empty($arrayfields['fd.description']['checked'])) {
		print_liste_field_titre($arrayfields['fd.description']['label'], $_SERVER["PHP_SELF"], "fd.description", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	// Amount
	if (!empty($arrayfields['fd.total_ht']['checked'])) {
		print_liste_field_titre($arrayfields['fd.total_ht']['label'], $_SERVER["PHP_SELF"], "fd.total_ht", "", $param, '', $sortfield, $sortorder, 'right ');
		$totalarray['nbfield']++;
	}
	// VAT
	if (!empty($arrayfields['fd.tva_tx']['checked'])) {
		print_liste_field_titre($arrayfields['fd.tva_tx']['label'], $_SERVER["PHP_SELF"], "fd.tva_tx", "", $param, '', $sortfield, $sortorder, 'right ');
		$totalarray['nbfield']++;
	}
	// Thirdparty
	if (!empty($arrayfields['s.nom']['checked'])) {
		print_liste_field_titre($arrayfields['s.nom']['label'], $_SERVER["PHP_SELF"], "s.nom", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	// Country
	if (!empty($arrayfields['co.label']['checked'])) {
		print_liste_field_titre($arrayfields['co.label']['label'], $_SERVER["PHP_SELF"], "co.label", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	// TVA Intracom
	if (!empty($arrayfields['s.tva_intra']['checked'])) {
		print_liste_field_titre($arrayfields['s.tva_intra']['label'], $_SERVER["PHP_SELF"], "s.tva_intra", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	// Account
	if (!empty($arrayfields['aa.account_number']['checked'])) {
		print_liste_field_titre($arrayfields['aa.account_number']['label'], $_SERVER["PHP_SELF"], "aa.account_number", "", $param, '', $sortfield, $sortorder);
		$totalarray['nbfield']++;
	}
	// Hook fields
	$parameters = array('arrayfields' => $arrayfields, 'param' => $param, 'sortfield' => $sortfield, 'sortorder' => $sortorder);
	$reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;
	// Action column
	if (!$conf->main_checkbox_left_column) {
		print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";
		$totalarray['nbfield']++;
	}
	print "</tr>\n";

	$thirdpartystatic = new Societe($db);
	$facturestatic = new Facture($db);
	$productstatic = new Product($db);
	$accountingaccountstatic = new AccountingAccount($db);
	$totalarray = array();
	$totalarray['nbfield'] = 0;

	$i = 0;
	while ($i < min($num_lines, $limit)) {
		$objp = $db->fetch_object($result);

		$facturestatic->ref = $objp->ref;
		$facturestatic->id = $objp->facid;
		$facturestatic->type = $objp->ftype;

		$thirdpartystatic->id = $objp->socid;
		$thirdpartystatic->name = $objp->name;
		$thirdpartystatic->client = $objp->client;
		$thirdpartystatic->fournisseur = $objp->fournisseur;
		$thirdpartystatic->code_client = $objp->code_client;
		$thirdpartystatic->code_compta_client = $objp->code_compta_client;
		$thirdpartystatic->code_fournisseur = $objp->code_fournisseur;
		$thirdpartystatic->code_compta_fournisseur = $objp->code_compta_fournisseur;
		$thirdpartystatic->email = $objp->email;
		$thirdpartystatic->country_code = $objp->country_code;

		$productstatic->ref = $objp->product_ref;
		$productstatic->id = $objp->product_id;
		$productstatic->label = $objp->product_label;
		$productstatic->type = $objp->line_type;
		$productstatic->status = $objp->tosell;
		$productstatic->status_buy = $objp->tobuy;
		$productstatic->accountancy_code_sell = $objp->accountancy_code_sell;
		$productstatic->accountancy_code_sell_intra = $objp->accountancy_code_sell_intra;
		$productstatic->accountancy_code_sell_export = $objp->accountancy_code_sell_export;

		$accountingaccountstatic->rowid = $objp->fk_compte;
		$accountingaccountstatic->label = $objp->label_account;
		$accountingaccountstatic->labelshort = $objp->labelshort_account;
		$accountingaccountstatic->account_number = $objp->account_number;

		print '<tr class="oddeven">';

		// Action column
		if ($conf->main_checkbox_left_column) {
			print '<td class="nowrap center actioncolumn">';
			$selected = in_array($objp->rowid, $changeaccount);
			print '<input id="cb'.$objp->rowid.'" class="flat checkforselect checkforaction" type="checkbox" name="changeaccount[]" value="'.$objp->rowid.'"'.($selected ? ' checked="checked"' : '').'>';
			print '</td>';
			if (!$i) {
				$totalarray['nbfield']++;
			}
		}
		// Line id
		if (!empty($arrayfields['fd.rowid']['checked'])) {
			print '<td>'.$objp->rowid.'</td>';
			$totalarray['nbfield']++;
		}
		// Ref Invoice
		if (!empty($arrayfields['f.ref']['checked'])) {
			print '<td class="nowraponall tdoverflowmax125">'.$facturestatic->getNomUrl(1).'</td>';
			$totalarray['nbfield']++;
		}
		// Date invoice
		if (!empty($arrayfields['f.datef']['checked'])) {
			print '<td class="center">'.dol_print_date($db->jdate($objp->datef), 'day').'</td>';
			$totalarray['nbfield']++;
		}
		// Ref Product
		if (!empty($arrayfields['p.ref']['checked'])) {
			print '<td class="tdoverflowmax100">';
			if ($productstatic->id > 0) {
				print $productstatic->getNomUrl(1);
			} else {
				print '&nbsp;';
			}
			//if ($productstatic->id > 0 && $objp->product_label) {
				print '<br>';
			//}
			if ($objp->product_label) {
				print '<span class="opacitymedium">'.$objp->product_label.'</span>';
			} else {
				print '&nbsp;';
			}
			print '</td>';
			$totalarray['nbfield']++;
		}
		// Description
		if (!empty($arrayfields['fd.description']['checked'])) {
			$text = dolGetFirstLineOfText(dol_string_nohtmltag($objp->description, 1));
			print '<td class="tdoverflowmax200 small" title="'.dol_escape_htmltag($text).'">';
			$trunclength = getDolGlobalInt('ACCOUNTING_LENGTH_DESCRIPTION', 32);
			print $form->textwithtooltip(dol_trunc($text, $trunclength), $objp->description);
			print '</td>';
			$totalarray['nbfield']++;
		}
		// Amount
		if (!empty($arrayfields['fd.total_ht']['checked'])) {
			print '<td class="right nowraponall amount">';

			// Create a compensation rate for old situation invoice feature.
			$situation_ratio = 1;
			if (getDolGlobalInt('INVOICE_USE_SITUATION') == 1) {
				if ($objp->situation_cycle_ref) {
					// Avoid divide by 0
					if ($objp->situation_percent == 0) {
						$situation_ratio = 0;
					} else {
						$line = new FactureLigne($db);
						$line->fetch($objp->rowid);

						// Situation invoices handling
						$prev_progress = $line->get_prev_progress($objp->facid);

						$situation_ratio = ($objp->situation_percent - $prev_progress) / $objp->situation_percent;
					}
				}
				print price($objp->total_ht * $situation_ratio);
			} else {
				print price($objp->total_ht);
			}
			print '</td>';
			$totalarray['nbfield']++;
		}
		// Vat rate
		if (!empty($arrayfields['fd.tva_tx']['checked'])) {
			print '<td class="right">'.vatrate($objp->tva_tx.($objp->vat_src_code ? ' ('.$objp->vat_src_code.')' : '')).'</td>';
			$totalarray['nbfield']++;
		}
		// Thirdparty
		if (!empty($arrayfields['s.nom']['checked'])) {
			print '<td class="tdoverflowmax100">'.$thirdpartystatic->getNomUrl(1, 'customer').'</td>';
			$totalarray['nbfield']++;
		}
		// Country
		if (!empty($arrayfields['co.label']['checked'])) {
			print '<td>';
			if ($objp->country_code) {
				print $langs->trans("Country".$objp->country_code).' ('.$objp->country_code.')';
			}
			print '</td>';
			$totalarray['nbfield']++;
		}
		// TVA Intracom
		if (!empty($arrayfields['s.tva_intra']['checked'])) {
			print '<td class="tdoverflowmax80" title="'.dol_escape_htmltag($objp->tva_intra).'">'.dol_escape_htmltag($objp->tva_intra).'</td>';
			$totalarray['nbfield']++;
		}
		// Account
		if (!empty($arrayfields['aa.account_number']['checked'])) {
			print '<td class="tdoverflowmax200" title="'.dol_escape_htmltag($accountingaccountstatic->label).'">';
			print '<a class="editfielda" href="./card.php?id='.$objp->rowid.'&backtopage='.urlencode($_SERVER["PHP_SELF"].($param ? '?'.$param : '')).'">';
			print img_edit();
			print '</a> ';
			print $accountingaccountstatic->getNomUrl(0, 1, 1, '', 1);
			print '</td>';
			$totalarray['nbfield']++;
		}

		// Fields from hook
		$parameters = array('arrayfields' => $arrayfields, 'obj' => $objp, 'i' => $i, 'totalarray' => &$totalarray);
		$reshook = $hookmanager->executeHooks('printFieldListValue', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		print $hookmanager->resPrint;

		// Action column
		if (!$conf->main_checkbox_left_column) {
			print '<td class="nowrap center actioncolumn">';
			$selected = in_array($objp->rowid, $changeaccount);
			print '<input id="cb'.$objp->rowid.'" class="flat checkforselect checkforaction" type="checkbox" name="changeaccount[]" value="'.$objp->rowid.'"'.($selected ? ' checked="checked"' : '').'>';
			print '</td>';
			if (!$i) {
				$totalarray['nbfield']++;
			}
		}

		print '</tr>';
		$i++;
	}
	if ($num_lines == 0) {
		print '<tr><td colspan="'.$totalarray['nbfield'].'"><span class="opacitymedium">'.$langs->trans("NoRecordFound").'</span></td></tr>';
	}

	$parameters = array('arrayfields' => $arrayfields, 'sql' => $sql);
	$reshook = $hookmanager->executeHooks('printFieldListFooter', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	print $hookmanager->resPrint;

	print '</table>';
	print "</div>";

	if ($nbtotalofrecords > $limit) {
		print_barre_liste('', $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num_lines, $nbtotalofrecords, '', 0, '', '', $limit, 1);
	}

	print '</form>';
} else {
	print $db->lasterror();
}

// End of page
llxFooter();
$db->close();
