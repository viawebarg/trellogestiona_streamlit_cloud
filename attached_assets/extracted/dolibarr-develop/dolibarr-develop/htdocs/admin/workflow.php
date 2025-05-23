<?php
/* Copyright (C) 2004		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004		Eric Seigne				<eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2021	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@inodbox.com>
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
 *	\file		htdocs/admin/workflow.php
 *	\ingroup	company
 *	\brief		Workflows setup page
 */

// Load Dolibarr environment
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

/**
 * @var Conf $conf
 * @var DoliDB $db
 * @var HookManager $hookmanager
 * @var Translate $langs
 * @var User $user
 */

// security check
if (!$user->admin) {
	accessforbidden();
}

// Load translation files required by the page
$langs->loadLangs(array("admin", "workflow", "propal", "workflow", "orders", "supplier_proposal", "receptions", "errors", 'sendings'));

$action = GETPOST('action', 'aZ09');


/*
 * Actions
 */

if (preg_match('/set(.*)/', $action, $reg)) {
	if (!dolibarr_set_const($db, $reg[1], '1', 'chaine', 0, '', $conf->entity) > 0) {
		dol_print_error($db);
	}
}

if (preg_match('/del(.*)/', $action, $reg)) {
	if (!dolibarr_set_const($db, $reg[1], '0', 'chaine', 0, '', $conf->entity) > 0) {
		dol_print_error($db);
	}
}

// List of workflow we can enable
clearstatcache();

$workflowcodes = array(
	// Automatic creation
	'WORKFLOW_PROPAL_AUTOCREATE_ORDER' => array(
		'family' => 'create',
		'position' => 10,
		'enabled' => (isModEnabled("propal") && isModEnabled('order')),
		'picto' => 'order'
	),
	'WORKFLOW_ORDER_AUTOCREATE_INVOICE' => array(
		'family' => 'create',
		'position' => 20,
		'enabled' => (isModEnabled('order') && isModEnabled('invoice')),
		'picto' => 'bill'
	),
	'WORKFLOW_TICKET_CREATE_INTERVENTION' => array(
		'family' => 'create',
		'position' => 25,
		'enabled' => (isModEnabled('ticket') && isModEnabled('intervention')),
		'picto' => 'ticket'
	),

	'separator1' => array('family' => 'separator', 'position' => 25, 'title' => '', 'enabled' => ((isModEnabled("propal") && isModEnabled('order')) || (isModEnabled('order') && isModEnabled('invoice')) || (isModEnabled('ticket') && isModEnabled('intervention')))),

	// Automatic classification of proposal
	'WORKFLOW_ORDER_CLASSIFY_BILLED_PROPAL' => array(
		'family' => 'classify_proposal',
		'position' => 30,
		'enabled' => (isModEnabled("propal") && isModEnabled('order')),
		'picto' => 'propal',
		'warning' => ''
	),
	'WORKFLOW_INVOICE_CLASSIFY_BILLED_PROPAL' => array(
		'family' => 'classify_proposal',
		'position' => 31,
		'enabled' => (isModEnabled("propal") && isModEnabled('invoice')),
		'picto' => 'propal',
		'warning' => ''
	),

	// Automatic classification of order
	'WORKFLOW_ORDER_CLASSIFY_SHIPPED_SHIPPING' => array(  // when shipping validated
		'family' => 'classify_order',
		'position' => 40,
		'enabled' => (isModEnabled("shipping") && isModEnabled('order')),
		'picto' => 'order'
	),
	'WORKFLOW_ORDER_CLASSIFY_SHIPPED_SHIPPING_CLOSED' => array( // when shipping closed
		'family' => 'classify_order',
		'position' => 41,
		'enabled' => (isModEnabled("shipping") && isModEnabled('order')),
		'picto' => 'order'
	),
	'WORKFLOW_INVOICE_AMOUNT_CLASSIFY_BILLED_ORDER' => array(
		'family' => 'classify_order',
		'position' => 42,
		'enabled' => (isModEnabled('invoice') && isModEnabled('order')),
		'picto' => 'order',
		'warning' => ''
	), // For this option, if module invoice is disabled, it does not exists, so "Classify billed" for order must be done manually from order card.

	'WORKFLOW_SUM_INVOICES_AMOUNT_CLASSIFY_BILLED_ORDER' => array(
		'family' => 'classify_order',
		'position' => 43,
		'enabled' => (isModEnabled('invoice') && isModEnabled('order')),
		'picto' => 'order',
		'warning' => ''
	), // For this option, if module invoice is disabled, it does not exists, so "Classify billed" for order must be done manually from order card.

	// Automatic classification supplier proposal
	'WORKFLOW_ORDER_CLASSIFY_BILLED_SUPPLIER_PROPOSAL' => array(
		'family' => 'classify_supplier_proposal',
		'position' => 60,
		'enabled' => (isModEnabled('supplier_proposal') && (isModEnabled("supplier_order") || isModEnabled("supplier_invoice"))),
		'picto' => 'supplier_proposal',
		'warning' => ''
	),

	// Automatic classification supplier order
	'WORKFLOW_ORDER_CLASSIFY_RECEIVED_RECEPTION' => array(
		'family' => 'classify_supplier_order',
		'position' => 63,
		'enabled' => (getDolGlobalString('MAIN_FEATURES_LEVEL') && isModEnabled("reception") && isModEnabled('supplier_order')),
		'picto' => 'supplier_order',
		'warning' => ''
	),

	'WORKFLOW_ORDER_CLASSIFY_RECEIVED_RECEPTION_CLOSED' => array(
		'family' => 'classify_supplier_order',
		'position' => 64,
		'enabled' => (getDolGlobalString('MAIN_FEATURES_LEVEL') && isModEnabled("reception") && isModEnabled('supplier_order')),
		'picto' => 'supplier_order',
		'warning' => ''
	),

	'WORKFLOW_INVOICE_AMOUNT_CLASSIFY_BILLED_SUPPLIER_ORDER' => array(
		'family' => 'classify_supplier_order',
		'position' => 65,
		'enabled' => (isModEnabled("supplier_order") || isModEnabled("supplier_invoice")),
		'picto' => 'supplier_order',
		'warning' => ''
	),

	// Automatic classification shipping
	/* Replaced by next option
	'WORKFLOW_SHIPPING_CLASSIFY_CLOSED_INVOICE' => array(
		'family' => 'classify_shipping',
		'position' => 90,
		'enabled' => isModEnabled("shipping") && isModEnabled("invoice"),
		'picto' => 'shipment',
		'deprecated' => 1
	),
	*/

	'WORKFLOW_SHIPPING_CLASSIFY_BILLED_INVOICE' => array(
		'family' => 'classify_shipping',
		'position' => 91,
		'enabled' => isModEnabled("shipping") && isModEnabled("invoice") && getDolGlobalString('WORKFLOW_BILL_ON_SHIPMENT') !== '0',
		'picto' => 'shipment'
	),

	// Automatic classification reception
	/*
	'WORKFLOW_RECEPTION_CLASSIFY_CLOSED_INVOICE'=>array(
		'family'=>'classify_reception',
		'position'=>95,
		'enabled'=>(isModEnabled("reception") && (isModEnabled("supplier_order") || isModEnabled("supplier_invoice"))),
		'picto'=>'reception'
	),
	*/

	'WORKFLOW_RECEPTION_CLASSIFY_BILLED_INVOICE' => array(
		'family' => 'classify_reception',
		'position' => 91,
		'enabled' => isModEnabled("reception") && isModEnabled("supplier_invoice") && getDolGlobalString('WORKFLOW_BILL_ON_RECEPTION') !== '0',
		'picto' => 'shipment'
	),


	'separator2' => array('family' => 'separator', 'position' => 400, 'enabled' => (isModEnabled('ticket') && isModEnabled('contract'))),

	// Automatic link ticket -> contract
	'WORKFLOW_TICKET_LINK_CONTRACT' => array(
		'family' => 'link_ticket',
		'position' => 500,
		'enabled' => (isModEnabled('ticket') && isModEnabled('contract')),
		'picto' => 'ticket',
		'reloadpage' => 1		// So next option can be shown
	),
	// This one depends on previous one WORKFLOW_TICKET_LINK_CONTRACT
	'WORKFLOW_TICKET_USE_PARENT_COMPANY_CONTRACTS' => array(
		'family' => 'link_ticket',
		'position' => 501,
		'enabled' => (isModEnabled('ticket') && isModEnabled('contract') && getDolGlobalString('WORKFLOW_TICKET_LINK_CONTRACT')),
		'picto' => 'ticket'
	),
);

if (!empty($conf->modules_parts['workflow']) && is_array($conf->modules_parts['workflow'])) {
	foreach ($conf->modules_parts['workflow'] as $workflow) {
		$workflowcodes = array_merge($workflowcodes, $workflow);
	}
}

// remove not available workflows (based on activated modules and global defined keys)
$workflowcodes = array_filter(
	$workflowcodes,
	/**
	 * @param array{enabled:int<0,1>} $var
	 * @return bool
	 */
	static function ($var) {
		return (bool) $var['enabled'];
	}
);

if ($action == 'setvarworkflow') {	// Test on permission already done
	if (GETPOSTISSET('product_category_id')) {
		$param_ticket_product_category = GETPOSTINT('product_category_id');
		$res = dolibarr_set_const($db, 'TICKET_PRODUCT_CATEGORY', $param_ticket_product_category, 'chaine', 0, '', $conf->entity);
	}
}


/*
 * View
 */

llxHeader('', $langs->trans("WorkflowSetup"), "EN:Module_Workflow_En|FR:Module_Workflow|ES:Módulo_Workflow", '', 0, 0, '', '', '', 'mod-admin page-workflow');

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("WorkflowSetup"), $linkback, 'title_setup');

print '<span class="opacitymedium">'.$langs->trans("WorkflowDesc").'</span>';
print '<br>';
print '<br>';

// current module setup don't support any automatic workflow of this module
if (count($workflowcodes) < 1) {
	print $langs->trans("ThereIsNoWorkflowToModify");

	llxFooter();
	$db->close();
	return;
}

// Sort on position
$workflowcodes = dol_sort_array($workflowcodes, 'position');

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data" >';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="setvarworkflow">';
print '<input type="hidden" name="page_y" value="">';

$oldfamily = '';
$tableopen = 0;
$atleastoneline = 0;

foreach ($workflowcodes as $key => $params) {
	if ($params['family'] == 'separator') {
		if ($atleastoneline) {
			print '</table>';
			print '<br>'."\n";

			$oldfamily = '';
			$atleastoneline = 0;
		}
		continue;
	}

	$reg = array();
	if ($oldfamily != $params['family']) {
		// New group
		if ($params['family'] == 'create') {
			$headerfamily = $langs->trans("AutomaticCreation");
			$header = $langs->trans("AutomaticCreation");
		} elseif (preg_match('/classify_(.*)/', $params['family'], $reg)) {
			$headerfamily = $langs->trans("AutomaticClassification");
			$header = $langs->trans("AutomaticClassification");
			if ($reg[1] == 'proposal') {
				$header .= ' - '.$langs->trans('Proposal');
			}
			if ($reg[1] == 'order') {
				$header .= ' - '.$langs->trans('Order');
			}
			if ($reg[1] == 'supplier_proposal') {
				$header .= ' - '.$langs->trans('SupplierProposal');
			}
			if ($reg[1] == 'supplier_order') {
				$header .= ' - '.$langs->trans('SupplierOrder');
			}
			if ($reg[1] == 'reception') {
				$header .= ' - '.$langs->trans('Reception');
			}
			if ($reg[1] == 'shipping') {
				$header .= ' - '.$langs->trans('Shipment');
			}
		} elseif (preg_match('/link_(.*)/', $params['family'], $reg)) {
			$headerfamily = $langs->trans("AutomaticLinking");
			$header = $langs->trans("AutomaticLinking");
			if ($reg[1] == 'ticket') {
				$header .= ' - '.$langs->trans('Ticket');
			}
		} else {
			$headerfamily = $langs->trans("Other");
			$header = $langs->trans("Description");
		}

		if ($tableopen) {
			print '</table><br>'."\n";
		}

		if ($oldfamily == '') {
			print load_fiche_titre($headerfamily);
		}

		print "\n";
		print '<table class="noborder centpercent">';
		$tableopen = 1;

		print '<tr class="liste_titre">';
		print '<th>'.$header.'</th>';
		print '<th class="right"></th>';
		print '</tr>';

		$oldfamily = $params['family'];
	}

	$atleastoneline = 1;

	print '<tr class="oddeven">';
	print '<td>';
	print img_object('', $params['picto'], 'class="pictofixedwidth"');
	print ' '.$langs->trans('desc'.$key);

	if (!empty($params['warning'])) {
		print ' '.img_warning($langs->transnoentitiesnoconv($params['warning']));
	}
	if (!empty($params['deprecated'])) {
		print ' '.img_warning($langs->transnoentitiesnoconv("Deprecated"));
	}

	if ($key == 'WORKFLOW_TICKET_LINK_CONTRACT' && getDolGlobalString('WORKFLOW_TICKET_LINK_CONTRACT')) {
		require_once DOL_DOCUMENT_ROOT."/core/class/html.formcategory.class.php";

		$formcategory = new FormCategory($db);

		$htmlname = "product_category_id";
		print '<br>';
		print $formcategory->textwithpicto($langs->trans("TicketChooseProductCategory"), $langs->trans("TicketChooseProductCategoryHelp"), 1, 'help');
		if (isModEnabled('category')) {
			print ' &nbsp; '.img_picto('', 'category', 'class="pictofixedwidth"');
			$formcategory->selectProductCategory(getDolGlobalInt('TICKET_PRODUCT_CATEGORY'), $htmlname, 1);
			if ($conf->use_javascript_ajax) {
				print ajax_combobox('select_'.$htmlname);
			}
			print '<input class="button smallpaddingimp" type="submit" value="'.$langs->trans("Save").'">';
		} else {
			print 'Module category must be enabled';
		}
	}

	print '</td>';

	print '<td class="right">';

	if (!empty($conf->use_javascript_ajax)) {
		if (!empty($params['reloadpage'])) {
			print ajax_constantonoff($key, array(), null, 0, 0, 1);
		} else {
			print ajax_constantonoff($key);
		}
	} else {
		if (getDolGlobalString($key)) {
			print '<a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=del'.$key.'&token='.newToken().'">';
			print img_picto($langs->trans("Activated"), 'switch_on');
			print '</a>';
		} else {
			print '<a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=set'.$key.'&token='.newToken().'">';
			print img_picto($langs->trans("Disabled"), 'switch_off');
			print '</a>';
		}
	}

	print '</td>';
	print '</tr>';
}

if ($tableopen) {
	print '</table>';
}

print '</form>';


// End of page
llxFooter();
$db->close();
