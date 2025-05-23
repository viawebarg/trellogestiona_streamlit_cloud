<?php
/* Copyright (C) 2003-2007  Rodolphe Quiedeville    <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009  Regis Houssin           <regis.houssin@inodbox.com>
 * Copyright (C) 2015-2024  Frédéric France         <frederic.france@free.fr>
 * Copyright (C) 2024       Charlene Benke          <charlene@patas-monkey.com>
 * Copyright (C) 2024		MDW						<mdeweerd@users.noreply.github.com>
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
 *	\file       htdocs/core/boxes/box_dolibarr_state_board.php
 *	\ingroup	core
 *	\brief      Module Dolibarr state base
 */

include_once DOL_DOCUMENT_ROOT . '/core/boxes/modules_boxes.php';


/**
 * Class to manage the box to show last thirdparties
 */
class box_dolibarr_state_board extends ModeleBoxes
{
	public $boxcode = "dolibarrstatebox";
	public $boximg = "generic";
	public $boxlabel = "BoxDolibarrStateBoard";
	public $depends = array("user");

	public $enabled = 1;

	/**
	 *  Constructor
	 *
	 *  @param  DoliDB	$db      	Database handler
	 *  @param	string	$param		More parameters
	 */
	public function __construct($db, $param = '')
	{
		$this->db = $db;
	}

	/**
	 *  Load data for box to show them later
	 *
	 *  @param	int		$max        Maximum number of records to load
	 *  @return	void
	 */
	public function loadBox($max = 5)
	{
		global $user, $langs;
		$langs->load("boxes");

		$this->max = $max;
		$this->info_box_head = array('text' => $langs->trans("DolibarrStateBoard"));

		if (empty($user->socid) && !getDolGlobalString('MAIN_DISABLE_GLOBAL_BOXSTATS')) {
			$hookmanager = new HookManager($this->db);
			$hookmanager->initHooks(array('index'));
			$object = new stdClass();
			$action = '';
			$parameters = array();
			$hookmanager->executeHooks('addStatisticLine', $parameters, $object, $action);
			$boxstatItems = array();
			$boxstatFromHook = '';
			$boxstatFromHook = $hookmanager->resPrint;
			$boxstat = '';

			$keys = array(
				'users',
				'members',
				'expensereports',
				'holidays',
				'customers',
				'prospects',
				'suppliers',
				'contacts',
				'products',
				'services',
				'projects',
				'proposals',
				'orders',
				'invoices',
				'donations',
				'supplier_proposals',
				'supplier_orders',
				'supplier_invoices',
				'contracts',
				'interventions',
				'ticket',
				'knowledgebase',
				'dolresource'
			);
			$conditions = array(
				'users' => $user->hasRight('user', 'user', 'lire'),
				'members' => isModEnabled('member') && $user->hasRight('adherent', 'lire'),
				'customers' => isModEnabled('societe') && $user->hasRight('societe', 'lire') && !getDolGlobalString('SOCIETE_DISABLE_CUSTOMERS') && !getDolGlobalString('SOCIETE_DISABLE_CUSTOMERS_STATS'),
				'prospects' => isModEnabled('societe') && $user->hasRight('societe', 'lire') && !getDolGlobalString('SOCIETE_DISABLE_PROSPECTS') && !getDolGlobalString('SOCIETE_DISABLE_PROSPECTS_STATS'),
				'suppliers' => (
					(isModEnabled("fournisseur") && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD') && $user->hasRight('fournisseur', 'lire'))
								 || (isModEnabled("supplier_order") && $user->hasRight('supplier_order', 'lire'))
								 || (isModEnabled("supplier_invoice") && $user->hasRight('supplier_invoice', 'lire'))
				)
								 && !getDolGlobalString('SOCIETE_DISABLE_SUPPLIERS_STATS'),
				'contacts' => isModEnabled('societe') && $user->hasRight('societe', 'contact', 'lire'),
				'products' => isModEnabled('product') && $user->hasRight('product', 'read'),
				'services' => isModEnabled('service') && $user->hasRight('service', 'read'),
				'proposals' => isModEnabled('propal') && $user->hasRight('propal', 'read'),
				'orders' => isModEnabled('order') && $user->hasRight('commande', 'lire'),
				'invoices' => isModEnabled('invoice') && $user->hasRight('facture', 'lire'),
				'donations' => isModEnabled('don') && $user->hasRight('don', 'lire'),
				'contracts' => isModEnabled('contract') && $user->hasRight('contrat', 'lire'),
				'interventions' => isModEnabled('intervention') && $user->hasRight('ficheinter', 'lire'),
				'supplier_orders' => isModEnabled('supplier_order') && $user->hasRight('fournisseur', 'commande', 'lire') && !getDolGlobalString('SOCIETE_DISABLE_SUPPLIERS_ORDERS_STATS'),
				'supplier_invoices' => isModEnabled('supplier_invoice') && $user->hasRight('fournisseur', 'facture', 'lire') && !getDolGlobalString('SOCIETE_DISABLE_SUPPLIERS_INVOICES_STATS'),
				'supplier_proposals' => isModEnabled('supplier_proposal') && $user->hasRight('supplier_proposal', 'lire') && !getDolGlobalString('SOCIETE_DISABLE_SUPPLIERS_PROPOSAL_STATS'),
				'projects' => isModEnabled('project') && $user->hasRight('projet', 'lire'),
				'expensereports' => isModEnabled('expensereport') && $user->hasRight('expensereport', 'lire'),
				'holidays' => isModEnabled('holiday') && $user->hasRight('holiday', 'read'),
				'ticket' => isModEnabled('ticket') && $user->hasRight('ticket', 'read'),
				'knowledgebase' => isModEnabled('knowledgemanagement') && $user->hasRight('knowledgemanagement', 'knowledgerecord', 'read'),
				'dolresource' => isModEnabled('resource') && $user->hasRight('resource', 'read')
			);
			$classes = array(
				'users' => 'User',
				'members' => 'Adherent',
				'customers' => 'Client',
				'prospects' => 'Client',
				'suppliers' => 'Fournisseur',
				'contacts' => 'Contact',
				'products' => 'Product',
				'services' => 'ProductService',
				'proposals' => 'Propal',
				'orders' => 'Commande',
				'invoices' => 'Facture',
				'donations' => 'Don',
				'contracts' => 'Contrat',
				'interventions' => 'Fichinter',
				'supplier_orders' => 'CommandeFournisseur',
				'supplier_invoices' => 'FactureFournisseur',
				'supplier_proposals' => 'SupplierProposal',
				'projects' => 'Project',
				'expensereports' => 'ExpenseReport',
				'holidays' => 'Holiday',
				'ticket' => 'Ticket',
				'knowledgebase' => 'KnowledgeRecord',
				'dolresource' => 'Dolresource'
			);
			$includes = array(
				'users' => DOL_DOCUMENT_ROOT . "/user/class/user.class.php",
				'members' => DOL_DOCUMENT_ROOT . "/adherents/class/adherent.class.php",
				'customers' => DOL_DOCUMENT_ROOT . "/societe/class/client.class.php",
				'prospects' => DOL_DOCUMENT_ROOT . "/societe/class/client.class.php",
				'suppliers' => DOL_DOCUMENT_ROOT . "/fourn/class/fournisseur.class.php",
				'contacts' => DOL_DOCUMENT_ROOT . "/contact/class/contact.class.php",
				'products' => DOL_DOCUMENT_ROOT . "/product/class/product.class.php",
				'services' => DOL_DOCUMENT_ROOT . "/product/class/product.class.php",
				'proposals' => DOL_DOCUMENT_ROOT . "/comm/propal/class/propal.class.php",
				'orders' => DOL_DOCUMENT_ROOT . "/commande/class/commande.class.php",
				'invoices' => DOL_DOCUMENT_ROOT . "/compta/facture/class/facture.class.php",
				'donations' => DOL_DOCUMENT_ROOT . "/don/class/don.class.php",
				'contracts' => DOL_DOCUMENT_ROOT . "/contrat/class/contrat.class.php",
				'interventions' => DOL_DOCUMENT_ROOT . "/fichinter/class/fichinter.class.php",
				'supplier_orders' => DOL_DOCUMENT_ROOT . "/fourn/class/fournisseur.commande.class.php",
				'supplier_invoices' => DOL_DOCUMENT_ROOT . "/fourn/class/fournisseur.facture.class.php",
				'supplier_proposals' => DOL_DOCUMENT_ROOT . "/supplier_proposal/class/supplier_proposal.class.php",
				'projects' => DOL_DOCUMENT_ROOT . "/projet/class/project.class.php",
				'expensereports' => DOL_DOCUMENT_ROOT . "/expensereport/class/expensereport.class.php",
				'holidays' => DOL_DOCUMENT_ROOT . "/holiday/class/holiday.class.php",
				'ticket' => DOL_DOCUMENT_ROOT . "/ticket/class/ticket.class.php",
				'knowledgebase' => DOL_DOCUMENT_ROOT . "/knowledgemanagement/class/knowledgerecord.class.php",
				'dolresource' => DOL_DOCUMENT_ROOT . "/resource/class/dolresource.class.php"
			);
			$links = array(
				'users' => DOL_URL_ROOT . '/user/list.php',
				'members' => DOL_URL_ROOT . '/adherents/list.php?statut=1&mainmenu=members',
				'customers' => DOL_URL_ROOT . '/societe/list.php?type=c&mainmenu=companies',
				'prospects' => DOL_URL_ROOT . '/societe/list.php?type=p&mainmenu=companies',
				'suppliers' => DOL_URL_ROOT . '/societe/list.php?type=f&mainmenu=companies',
				'contacts' => DOL_URL_ROOT . '/contact/list.php?mainmenu=companies',
				'products' => DOL_URL_ROOT . '/product/list.php?type=0&mainmenu=products',
				'services' => DOL_URL_ROOT . '/product/list.php?type=1&mainmenu=products',
				'proposals' => DOL_URL_ROOT . '/comm/propal/list.php?mainmenu=commercial&leftmenu=propals',
				'orders' => DOL_URL_ROOT . '/commande/list.php?mainmenu=commercial&leftmenu=orders',
				'invoices' => DOL_URL_ROOT . '/compta/facture/list.php?mainmenu=billing&leftmenu=customers_bills',
				'donations' => DOL_URL_ROOT . '/don/list.php?leftmenu=donations',
				'contracts' => DOL_URL_ROOT . '/contrat/list.php?mainmenu=commercial&leftmenu=contracts',
				'interventions' => DOL_URL_ROOT . '/fichinter/list.php?mainmenu=commercial&leftmenu=ficheinter',
				'supplier_orders' => DOL_URL_ROOT . '/fourn/commande/list.php?mainmenu=commercial&leftmenu=orders_suppliers',
				'supplier_invoices' => DOL_URL_ROOT . '/fourn/facture/list.php?mainmenu=billing&leftmenu=suppliers_bills',
				'supplier_proposals' => DOL_URL_ROOT . '/supplier_proposal/list.php?mainmenu=commercial&leftmenu=',
				'projects' => DOL_URL_ROOT . '/projet/list.php?mainmenu=project',
				'expensereports' => DOL_URL_ROOT . '/expensereport/list.php?mainmenu=hrm&leftmenu=expensereport',
				'holidays' => DOL_URL_ROOT . '/holiday/list.php?mainmenu=hrm&leftmenu=holiday',
				'ticket' => DOL_URL_ROOT . '/ticket/list.php?leftmenu=ticket',
				'knowledgebase' => DOL_URL_ROOT . '/knowledgemanagement/knowledgerecord_list.php?leftmenu=knowledgebase',
				'dolresource' => DOL_URL_ROOT . '/resource/list.php?mainmenu=agenda',
			);
			$titres = array(
				'users' => "Users",
				'members' => "Members",
				'customers' => "ThirdPartyCustomersStats",
				'prospects' => "ThirdPartyProspectsStats",
				'suppliers' => "Suppliers",
				'contacts' => "Contacts",
				'products' => "Products",
				'services' => "Services",
				'proposals' => "CommercialProposalsShort",
				'orders' => "CustomersOrders",
				'invoices' => "BillsCustomers",
				'donations' => "Donations",
				'contracts' => "Contracts",
				'interventions' => "Interventions",
				'supplier_orders' => "SuppliersOrders",
				'supplier_invoices' => "SuppliersInvoices",
				'supplier_proposals' => "SupplierProposalShort",
				'projects' => "Projects",
				'expensereports' => "ExpenseReports",
				'holidays' => "Holidays",
				'ticket' => "Ticket",
				'knowledgebase' => "KnowledgeRecord",
				'dolresource' => "Resources",
			);
			$langfile = array(
				'customers' => "companies",
				'contacts' => "companies",
				'services' => "products",
				'proposals' => "propal",
				'invoices' => "bills",
				'supplier_orders' => "orders",
				'supplier_invoices' => "bills",
				'supplier_proposals' => 'supplier_proposal',
				'expensereports' => "trips",
				'holidays' => "holiday",
			);
			$boardloaded = array();

			foreach ($keys as $val) {
				if ($conditions[$val]) {
					$boxstatItem = '';
					$class = $classes[$val];
					// Search in cache if load_state_board is already realized
					$classkeyforcache = $class;
					if ($classkeyforcache == 'ProductService') {
						$classkeyforcache = 'Product'; // ProductService use same load_state_board than Product
					}

					if (!isset($boardloaded[$classkeyforcache]) || !is_object($boardloaded[$classkeyforcache])) {
						include_once $includes[$val]; // Loading a class cost around 1Mb

						$board = new $class($this->db);
						if (method_exists($board, 'load_state_board')) {
							// @phan-suppress-next-line PhanUndeclaredMethod  (Legacy, not present in core).
							$board->load_state_board();
						} elseif (method_exists($board, 'loadStateBoard')) {	// @phpstan-ignore-line
							$board->loadStateBoard();
						} else {
							$board = -1;
						}
						$boardloaded[$class] = $board;
					} else {
						$board = $boardloaded[$classkeyforcache];
					}

					$langs->load(empty($langfile[$val]) ? $val : $langfile[$val]);

					$text = $langs->trans($titres[$val]);
					$boxstatItem .= '<a href="' . $links[$val] . '" class="boxstatsindicator thumbstat nobold nounderline">';
					$boxstatItem .= '<div class="boxstats">';
					$boxstatItem .= '<span class="boxstatstext" title="' . dol_escape_htmltag($text) . '">' . $text . '</span><br>';
					$boxstatItem .= '<span class="boxstatsindicator">' . img_object("", $board->picto, 'class="inline-block"') . ' ' . (!empty($board->nb[$val]) ? $board->nb[$val] : 0) . '</span>';
					$boxstatItem .= '</div>';
					$boxstatItem .= '</a>';

					$boxstatItems[$val] = $boxstatItem;
				}
			}

			if (!empty($boxstatFromHook) || !empty($boxstatItems)) {
				$boxstat .= $boxstatFromHook;

				if (!empty($boxstatItems)) {
					$boxstat .= implode('', $boxstatItems);
				}

				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';
				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';
				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';
				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';
				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';
				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';
				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';
				$boxstat .= '<a class="boxstatsindicator thumbstat nobold nounderline"><div class="boxstatsempty"></div></a>';

				$this->info_box_contents[0][0] = array(
					'tr' => 'class="nohover"',
					'td' => 'class="tdwidgetstate center"',
					'textnoformat' => $boxstat
				);
			}
		} else {
			$this->info_box_contents[0][0] = array(
				'td' => '',
				'text' => $langs->trans("ReadPermissionNotAllowed")
			);
		}
	}




	/**
	 *	Method to show box.  Called when the box needs to be displayed.
	 *
	 *	@param	?array<array{text?:string,sublink?:string,subtext?:string,subpicto?:?string,picto?:string,nbcol?:int,limit?:int,subclass?:string,graph?:int<0,1>,target?:string}>   $head       Array with properties of box title
	 *	@param	?array<array{tr?:string,td?:string,target?:string,text?:string,text2?:string,textnoformat?:string,tooltip?:string,logo?:string,url?:string,maxlength?:int,asis?:int<0,1>}>   $contents   Array with properties of box lines
	 *	@param	int<0,1>	$nooutput	No print, only return string
	 *	@return	string
	 */
	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
		return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
	}
}
