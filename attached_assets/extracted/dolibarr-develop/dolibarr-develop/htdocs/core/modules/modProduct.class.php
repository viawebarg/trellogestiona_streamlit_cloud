<?php
/* Copyright (C) 2003		Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2004		Sebastien Di Cintio		<sdicintio@ressource-toi.org>
 * Copyright (C) 2004		Benoit Mortier			<benoit.mortier@opensides.be>
 * Copyright (C) 2005-2012	Regis Houssin			<regis.houssin@inodbox.com>
 * Copyright (C) 2012-2013	Juanjo Menent			<jmenent@2byte.es>
 * Copyright (C) 2014		Christophe Battarel		<contact@altairis.fr>
 * Copyright (C) 2014		Cedric Gross			<c.gross@kreiz-it.fr>
 * Copyright (C) 2020-2021	Alexandre Spangaro		<aspangaro@open-dsi.fr>
 * Copyright (C) 2024		MDW						<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2025       Frédéric France         <frederic.france@free.fr>
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
 *	\defgroup   produit     Module products
 *	\brief      Module to manage catalog of predefined products
 *	\file       htdocs/core/modules/modProduct.class.php
 *	\ingroup    produit
 *	\brief      Description and activation file for the module to manage catalog of predefined products
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';


/**
 *	Class descriptor of Product module
 */
class modProduct extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $conf, $mysoc;

		$this->db = $db;
		$this->numero = 50;

		$this->family = "products";
		$this->module_position = '26';
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "Product management";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'dolibarr';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto = 'product';

		// Data directories to create when module is enabled
		$this->dirs = array("/product/temp");

		// Dependencies
		$this->hidden = false; // A condition to hide module
		$this->depends = array(); // List of module class names as string that must be enabled if this module is enabled
		$this->requiredby = array("modStock", "modBarcode", "modProductBatch", "modVariants", "modBom"); // List of module ids to disable if this one is disabled
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with
		$this->phpmin = array(7, 0); // Minimum version of PHP required by module

		// Config pages
		$this->config_page_url = array("product.php@product");
		$this->langfiles = array("products", "companies", "stocks", "bills");

		// Constants
		$this->const = [
			[
				"PRODUCT_CODEPRODUCT_ADDON",
				"chaine",
				"mod_codeproduct_leopard",
				'Module to control product codes',
				0,
			],
			[

				"PRODUCT_PRICE_UNIQ",
				"chaine",
				"1",
				'pricing rule by default',
				0,
			],
			/*[
				"PRODUCT_ADDON_PDF",
				"chaine",
				"standard",
				'Default module for document generation',
				0,
			],*/
		];

		// Boxes
		$this->boxes = array(
			0 => array('file' => 'box_produits.php', 'enabledbydefaulton' => 'Home'),
			1 => array('file' => 'box_produits_alerte_stock.php', 'enabledbydefaulton' => 'Home'),
			2 => array('file' => 'box_graph_product_distribution.php', 'enabledbydefaulton' => 'Home')
		);

		// Permissions
		$this->rights = array();
		$this->rights_class = 'produit';
		$r = 0;

		$this->rights[$r][0] = 31; // id de la permission
		$this->rights[$r][1] = 'Read products'; // libelle de la permission
		$this->rights[$r][2] = 'r'; // type de la permission (deprecated)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par default
		$this->rights[$r][4] = 'lire';
		$r++;

		$this->rights[$r][0] = 32; // id de la permission
		$this->rights[$r][1] = 'Create/modify products'; // libelle de la permission
		$this->rights[$r][2] = 'w'; // type de la permission (deprecated)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par default
		$this->rights[$r][4] = 'creer';
		$r++;

		$this->rights[$r][0] = 33; // id de la permission
		$this->rights[$r][1] = 'Read prices products'; // libelle de la permission
		$this->rights[$r][2] = 'w'; // type de la permission (deprecated)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par default
		$this->rights[$r][4] = 'product_advance';
		$this->rights[$r][5] = 'read_prices';
		$r++;

		$this->rights[$r][0] = 35; // id de la permission
		$this->rights[$r][1] = 'Read supplier prices'; // libelle de la permission
		$this->rights[$r][2] = 'w'; // type de la permission (deprecated)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par default
		$this->rights[$r][4] = 'product_advance';
		$this->rights[$r][5] = 'read_supplier_prices';
		$r++;

		$this->rights[$r][0] = 34; // id de la permission
		$this->rights[$r][1] = 'Delete products'; // libelle de la permission
		$this->rights[$r][2] = 'd'; // type de la permission (deprecated)
		$this->rights[$r][3] = 0; // La permission est-elle une permission par default
		$this->rights[$r][4] = 'supprimer';
		$r++;

		$this->rights[$r][0] = 38; // Must be same permission than in service module
		$this->rights[$r][1] = 'Export products';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'export';
		$r++;

		$this->rights[$r][0] = 39;
		$this->rights[$r][1] = 'Ignore minimum price';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ignore_price_min_advance';
		$r++;

		// Menus
		//-------

		$this->menu = 1; // This module adds menu entries. They are coded into menu manager.
		/* We can't enable this here because it must be enabled in both product and service module and this creates duplicate inserts
		$r = 0;
		$this->menu[$r] = array(
			// Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'fk_menu' => 'fk_mainmenu=home,fk_leftmenu=admintools',
			// This is a Left menu entry
			'type' => 'left',
			'titre' => 'ProductVatMassChange',
			'url' => '/product/admin/product_tools.php?mainmenu=home&leftmenu=admintools',
			// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'langs' => 'products',
			'position' => 300,
			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'enabled' => 'isModEnabled("product") && preg_match(\'/^(admintools|all)/\',$leftmenu)',
			// Use 'perms'=>'$user->hasRight("mymodule","level1","level2")' if you want your menu with a permission rules
			'perms' => '1',
			'target' => '',
			// 0=Menu for internal users, 1=external users, 2=both
			'user' => 0
		);
		$r++;
		*/

		$usenpr = 0;
		if (is_object($mysoc)) {
			$usenpr = $mysoc->useNPR();
		}

		// Exports
		//--------
		$r = 0;

		$alias_product_perentity = !getDolGlobalString('MAIN_PRODUCT_PERENTITY_SHARED') ? "p" : "ppe";

		$r++;
		$this->export_code[$r] = $this->rights_class.'_'.$r;
		$this->export_label[$r] = "Products"; // Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_permission[$r] = array(array("produit", "export"));
		$this->export_fields_array[$r] = array(
			'p.rowid' => "Id", 'p.ref' => "Ref", 'p.label' => "Label",
			'p.fk_product_type' => 'Type', 'p.tosell' => "OnSell", 'p.tobuy' => "OnBuy",
			'p.description' => "Description", 'p.url' => "PublicUrl",
			'p.customcode' => 'CustomsCode', 'p.fk_country' => 'IDCountry',
			$alias_product_perentity . '.accountancy_code_sell' => "ProductAccountancySellCode", $alias_product_perentity . '.accountancy_code_sell_intra' => "ProductAccountancySellIntraCode",
			$alias_product_perentity . '.accountancy_code_sell_export' => "ProductAccountancySellExportCode", $alias_product_perentity . '.accountancy_code_buy' => "ProductAccountancyBuyCode",
			$alias_product_perentity . '.accountancy_code_buy_intra' => "ProductAccountancyBuyIntraCode", $alias_product_perentity . '.accountancy_code_buy_export' => "ProductAccountancyBuyExportCode",
			'p.note' => "NotePrivate", 'p.note_public' => 'NotePublic',
			'p.weight' => "Weight", 'p.weight_units' => "WeightUnits", 'p.length' => "Length", 'p.length_units' => "LengthUnits", 'p.width' => "Width", 'p.width_units' => "WidthUnits", 'p.height' => "Height", 'p.height_units' => "HeightUnits",
			'p.surface' => "Surface", 'p.surface_units' => "SurfaceUnits", 'p.volume' => "Volume", 'p.volume_units' => "VolumeUnits",
			'p.duration' => "Duration",
			'p.finished' => 'Nature',
			'p.price_base_type' => "PriceBase", 'p.price' => "UnitPriceHT", 'p.price_ttc' => "UnitPriceTTC",
			'p.price_min' => "MinPriceHT",'p.price_min_ttc' => "MinPriceTTC",
			'p.tva_tx' => 'VATRate',
			'p.datec' => 'DateCreation', 'p.tms' => 'DateModification'
		);
		if (is_object($mysoc) && $usenpr) {
			$this->export_fields_array[$r]['p.recuperableonly'] = 'NPR';
		}
		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice") || isModEnabled('margin')) {
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('p.cost_price' => 'CostPrice'));
		}
		if (isModEnabled('stock')) {
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('e.ref' => 'DefaultWarehouse', 'p.tobatch' => 'ManageLotSerial', 'p.stock' => 'Stock', 'p.seuil_stock_alerte' => 'StockLimit', 'p.desiredstock' => 'DesiredStock', 'p.pmp' => 'PMPValue'));
		}
		if (isModEnabled('barcode')) {
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('p.barcode' => 'BarCode'));
		}
		$keyforselect = 'product';
		$keyforelement = 'product';
		$keyforaliasextra = 'extra';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice")) {
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('s.nom' => 'Supplier', 'pf.ref_fourn' => 'SupplierRef', 'pf.quantity' => 'QtyMin', 'pf.remise_percent' => 'DiscountQtyMin', 'pf.unitprice' => 'BuyingPrice', 'pf.delivery_time_days' => 'NbDaysToDelivery'));
		}
		if (getDolGlobalString('EXPORTTOOL_CATEGORIES')) {
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('group_concat(cat.label)' => 'Categories'));
		}
		if (getDolGlobalInt('MAIN_MULTILANGS')) {
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('l.lang' => 'Language', 'l.label' => 'TranslatedLabel', 'l.description' => 'TranslatedDescription', 'l.note' => 'TranslatedNote'));
		}
		if (getDolGlobalInt('PRODUCT_USE_UNITS')) {
			$this->export_fields_array[$r]['p.fk_unit'] = 'Unit';
		}
		$this->export_TypeFields_array[$r] = array(
			'p.ref' => "Text", 'p.label' => "Text",
			'p.fk_product_type' => 'Numeric', 'p.tosell' => "Boolean", 'p.tobuy' => "Boolean",
			'p.description' => "Text", 'p.url' => "Text",
			$alias_product_perentity . '.accountancy_code_sell' => "Text", $alias_product_perentity . '.accountancy_code_sell_intra' => "Text", $alias_product_perentity . '.accountancy_code_sell_export' => "Text",
			$alias_product_perentity . '.accountancy_code_buy' => "Text", $alias_product_perentity . '.accountancy_code_buy_intra' => "Text", $alias_product_perentity . '.accountancy_code_buy_export' => "Text",
			'p.note' => "Text", 'p.note_public' => "Text",
			'p.weight' => "Numeric", 'p.length' => "Numeric", 'p.width' => "Numeric", 'p.height' => "Numeric", 'p.surface' => "Numeric", 'p.volume' => "Numeric",
			'p.customcode' => 'Text',
			'p.duration' => "Text",
			'p.finished' => 'Numeric',
			'p.price_base_type' => "Text", 'p.price' => "Numeric", 'p.price_ttc' => "Numeric",
			'p.price_min' => "Numeric", 'p.price_min_ttc' => "Numeric",
			'p.tva_tx' => 'Numeric',
			'p.datec' => 'Date', 'p.tms' => 'Date'
		);
		if (isModEnabled('stock')) {
			$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array('e.ref' => 'Text', 'p.tobatch' => 'Numeric', 'p.stock' => 'Numeric', 'p.seuil_stock_alerte' => 'Numeric', 'p.desiredstock' => 'Numeric', 'p.pmp' => 'Numeric', 'p.cost_price' => 'Numeric'));
		}
		if (isModEnabled('barcode')) {
			$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array('p.barcode' => 'Text'));
		}
		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice")) {
			$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array('s.nom' => 'Text', 'pf.ref_fourn' => 'Text', 'pf.unitprice' => 'Numeric', 'pf.quantity' => 'Numeric', 'pf.remise_percent' => 'Numeric', 'pf.delivery_time_days' => 'Numeric'));
		}
		if (getDolGlobalInt('MAIN_MULTILANGS')) {
			$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array('l.lang' => 'Text', 'l.label' => 'Text', 'l.description' => 'Text', 'l.note' => 'Text'));
		}
		if (getDolGlobalString('EXPORTTOOL_CATEGORIES')) {
			$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array("group_concat(cat.label)" => 'Text'));
		}
		$this->export_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
		if (getDolGlobalString('EXPORTTOOL_CATEGORIES')) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array("group_concat(cat.label)" => 'category'));
		}
		if (isModEnabled('stock')) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('p.stock' => 'product', 'p.pmp' => 'product'));
		}
		if (isModEnabled('barcode')) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('p.barcode' => 'product'));
		}
		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice")) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('s.nom' => 'product_supplier_ref', 'pf.ref_fourn' => 'product_supplier_ref', 'pf.unitprice' => 'product_supplier_ref', 'pf.quantity' => 'product_supplier_ref', 'pf.remise_percent' => 'product_supplier_ref', 'pf.delivery_time_days' => 'product_supplier_ref'));
		}
		if (getDolGlobalInt('MAIN_MULTILANGS')) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('l.lang' => 'translation', 'l.label' => 'translation', 'l.description' => 'translation', 'l.note' => 'translation'));
		}
		if (getDolGlobalString('EXPORTTOOL_CATEGORIES')) {
			$this->export_dependencies_array[$r] = array('category' => 'p.rowid');
		}
		if (isModEnabled('stock')) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('p.stock' => 'product', 'p.pmp' => 'product'));
		}
		if (isModEnabled('barcode')) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('p.barcode' => 'product'));
		}
		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice")) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('s.nom' => 'product_supplier_ref', 'pf.ref_fourn' => 'product_supplier_ref', 'pf.unitprice' => 'product_supplier_ref', 'pf.quantity' => 'product_supplier_ref', 'pf.remise_percent' => 'product_supplier_ref', 'pf.delivery_time_days' => 'product_supplier_ref'));
		}
		if (getDolGlobalInt('MAIN_MULTILANGS')) {
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('l.lang' => 'translation', 'l.label' => 'translation', 'l.description' => 'translation', 'l.note' => 'translation'));
		}
		if (getDolGlobalString('EXPORTTOOL_CATEGORIES')) {
			$this->export_dependencies_array[$r] = array('category' => 'p.rowid');
		}
		$this->export_sql_start[$r] = 'SELECT DISTINCT ';
		$this->export_sql_end[$r]  = ' FROM '.MAIN_DB_PREFIX.'product as p';
		if (getDolGlobalString('MAIN_PRODUCT_PERENTITY_SHARED')) {
			$this->export_sql_end[$r] .= " LEFT JOIN " . MAIN_DB_PREFIX . "product_perentity as ppe ON ppe.fk_product = p.rowid AND ppe.entity = " . ((int) $conf->entity);
		}
		if (getDolGlobalString('EXPORTTOOL_CATEGORIES')) {
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'categorie_product as cp ON cp.fk_product = p.rowid LEFT JOIN '.MAIN_DB_PREFIX.'categorie as cat ON cp.fk_categorie = cat.rowid';
		}
		if (getDolGlobalInt('MAIN_MULTILANGS')) {
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_lang as l ON l.fk_product = p.rowid';
		}
		$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_extrafields as extra ON p.rowid = extra.fk_object';
		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice")) {
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_fournisseur_price as pf ON pf.fk_product = p.rowid LEFT JOIN '.MAIN_DB_PREFIX.'societe s ON s.rowid = pf.fk_soc';
		}
		if (isModEnabled('stock')) {
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'entrepot as e ON e.rowid = p.fk_default_warehouse';
		}
		$this->export_sql_end[$r] .= ' WHERE p.fk_product_type = 0 AND p.entity IN ('.getEntity('product').')';
		if (getDolGlobalString('EXPORTTOOL_CATEGORIES')) {
			$this->export_sql_order[$r] = ' GROUP BY p.rowid'; // FIXME The group by used a generic value to say "all fields in select except function fields"
		}

		if (getDolGlobalString('PRODUIT_MULTIPRICES') || getDolGlobalString('PRODUIT_CUSTOMER_PRICES_AND_MULTIPRICES')) {
			// Exports product multiprice
			$r++;
			$this->export_code[$r] = $this->rights_class.'_'.$r;
			$this->export_label[$r] = "ProductsMultiPrice"; // Translation key (used only if key ExportDataset_xxx_z not found)
			$this->export_permission[$r] = array(array("produit", "export"));
			$this->export_fields_array[$r] = array('p.rowid' => "Id", 'p.ref' => "Ref", 'p.label' => "Label",
				'pr.price_base_type' => "PriceBase", 'pr.price_level' => "PriceLevel",
				'pr.price' => "PriceLevelUnitPriceHT", 'pr.price_ttc' => "PriceLevelUnitPriceTTC",
				'pr.price_min' => "MinPriceLevelUnitPriceHT", 'pr.price_min_ttc' => "MinPriceLevelUnitPriceTTC",
				'pr.tva_tx' => 'PriceLevelVATRate',
				'pr.date_price' => 'DateCreation');
			if (is_object($mysoc) && $usenpr) {
				$this->export_fields_array[$r]['pr.recuperableonly'] = 'NPR';
			}
			//$this->export_TypeFields_array[$r]=array(
			//	'p.ref'=>"Text",'p.label'=>"Text",'p.description'=>"Text",'p.url'=>"Text",'p.accountancy_code_sell'=>"Text",'p.accountancy_code_buy'=>"Text",
			//	'p.note'=>"Text",'p.length'=>"Numeric",'p.surface'=>"Numeric",'p.volume'=>"Numeric",'p.weight'=>"Numeric",'p.customcode'=>'Text',
			//	'p.price_base_type'=>"Text",'p.price'=>"Numeric",'p.price_ttc'=>"Numeric",'p.tva_tx'=>'Numeric','p.tosell'=>"Boolean",'p.tobuy'=>"Boolean",
			//	'p.datec'=>'Date','p.tms'=>'Date'
			//);
			$this->export_entities_array[$r] = array('p.rowid' => "product", 'p.ref' => "product", 'p.label' => "Label",
				'pr.price_base_type' => "product", 'pr.price_level' => "product", 'pr.price' => "product",
				'pr.price_ttc' => "product",
				'pr.price_min' => "product", 'pr.price_min_ttc' => "product",
				'pr.tva_tx' => 'product',
				'pr.recuperableonly' => 'product',
				'pr.date_price' => "product");
			$this->export_sql_start[$r] = 'SELECT DISTINCT ';
			$this->export_sql_end[$r]  = ' FROM '.MAIN_DB_PREFIX.'product as p';
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_price as pr ON p.rowid = pr.fk_product AND pr.entity = '.$conf->entity; // export prices only for the current entity
			$this->export_sql_end[$r] .= ' WHERE p.entity IN ('.getEntity('product').')'; // For product and service profile
			$this->export_sql_end[$r] .= ' AND pr.date_price = (SELECT MAX(pr2.date_price) FROM '.MAIN_DB_PREFIX.'product_price as pr2 WHERE pr2.fk_product = pr.fk_product AND pr2.price_level = pr.price_level AND pr2.entity IN ('.getEntity('product').'))'; // export only latest prices not full history
			$this->export_sql_end[$r] .= ' ORDER BY p.ref, pr.price_level';
		}

		if (getDolGlobalString('PRODUIT_CUSTOMER_PRICES') || getDolGlobalString('PRODUIT_CUSTOMER_PRICES_AND_MULTIPRICES')) {
			// Exports product multiprice
			$r++;
			$this->export_code[$r] = $this->rights_class.'_'.$r;
			$this->export_label[$r] = "ProductsPricePerCustomer"; // Translation key (used only if key ExportDataset_xxx_z not found)
			$this->export_permission[$r] = array(array("produit", "export"));
			$this->export_fields_array[$r] = array('p.rowid' => "Id", 'p.ref' => "Ref", 'p.label' => "Label",
				's.nom' => 'ThirdParty',
				's.code_client' => 'CodeClient',
				'pr.date_begin' => "AppliedPricesFrom",
				'pr.date_end' => "AppliedPricesTo",
				'pr.price_base_type' => "PriceBase",
				'pr.price' => "PriceUnitPriceHT", 'pr.price_ttc' => "PriceUnitPriceTTC",
				'pr.price_min' => "MinPriceUnitPriceHT", 'pr.price_min_ttc' => "MinPriceUnitPriceTTC",
				'pr.tva_tx' => 'PriceVATRate',
				'pr.default_vat_code' => 'PriceVATCode',
				'pr.discount_percent' => 'Discount',
				'pr.datec' => 'DateCreation');
			if (is_object($mysoc) && $usenpr) {
				$this->export_fields_array[$r]['pr.recuperableonly'] = 'NPR';
			}
			$this->export_entities_array[$r] = array('p.rowid' => "product", 'p.ref' => "product", 'p.label' => "Label",
				's.nom' => 'company',
				's.code_client' => 'company',
				'pr.date_begin' => "product",
				'pr.date_end' => "product",
				'pr.price_base_type' => "product", 'pr.price' => "product",
				'pr.price_ttc' => "product",
				'pr.price_min' => "product", 'pr.price_min_ttc' => "product",
				'pr.tva_tx' => 'product',
				'pr.default_vat_code' => 'product',
				'pr.discount_percent' => 'product',
				'pr.recuperableonly' => 'product',
				'pr.datec' => "product");
			$this->export_sql_start[$r] = 'SELECT DISTINCT ';
			$this->export_sql_end[$r]  = ' FROM '.MAIN_DB_PREFIX.'product as p';
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_customer_price as pr ON p.rowid = pr.fk_product AND pr.entity = '.$conf->entity; // export prices only for the current entity
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'societe as s ON pr.fk_soc = s.rowid';
			$this->export_sql_end[$r] .= ' WHERE p.entity IN ('.getEntity('product').')'; // For product and service profile
		}

		if (getDolGlobalString('PRODUIT_SOUSPRODUITS')) {
			// Exports virtual products
			$r++;
			$this->export_code[$r] = $this->rights_class.'_'.$r;
			$this->export_label[$r] = "AssociatedProducts"; // Translation key (used only if key ExportDataset_xxx_z not found)
			$this->export_permission[$r] = array(array("produit", "export"));
			$this->export_fields_array[$r] = array(
				'p.rowid' => "Id", 'p.ref' => "Ref", 'p.label' => "Label", 'p.description' => "Description", 'p.url' => "PublicUrl",
				$alias_product_perentity . '.accountancy_code_sell' => "ProductAccountancySellCode", $alias_product_perentity . '.accountancy_code_sell_intra' => "ProductAccountancySellIntraCode",
				$alias_product_perentity . '.accountancy_code_sell_export' => "ProductAccountancySellExportCode", $alias_product_perentity . '.accountancy_code_buy' => "ProductAccountancyBuyCode",
				$alias_product_perentity . '.accountancy_code_buy_intra' => "ProductAccountancyBuyIntraCode", $alias_product_perentity . '.accountancy_code_buy_export' => "ProductAccountancyBuyExportCode",
				'p.note' => "NotePrivate", 'p.note_public' => 'NotePublic',
				'p.weight' => "Weight", 'p.length' => "Length", 'p.surface' => "Surface", 'p.volume' => "Volume", 'p.customcode' => 'CustomsCode',
				'p.price_base_type' => "PriceBase", 'p.price' => "UnitPriceHT", 'p.price_ttc' => "UnitPriceTTC", 'p.tva_tx' => 'VATRate', 'p.tosell' => "OnSell",
				'p.tobuy' => "OnBuy", 'p.datec' => 'DateCreation', 'p.tms' => 'DateModification'
			);
			if (isModEnabled('stock')) {
				$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('p.stock' => 'Stock', 'p.seuil_stock_alerte' => 'StockLimit', 'p.desiredstock' => 'DesiredStock', 'p.pmp' => 'PMPValue'));
			}
			if (isModEnabled('barcode')) {
				$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('p.barcode' => 'BarCode'));
			}
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('pa.qty' => 'Qty', 'pa.incdec' => 'ComposedProductIncDecStock'));
			$this->export_TypeFields_array[$r] = array(
				'p.ref' => "Text", 'p.label' => "Text", 'p.description' => "Text", 'p.url' => "Text",
				$alias_product_perentity . '.accountancy_code_sell' => "Text", $alias_product_perentity . '.accountancy_code_sell_intra' => "Text", $alias_product_perentity . '.accountancy_code_sell_export' => "Text",
				$alias_product_perentity . '.accountancy_code_buy' => "Text", $alias_product_perentity . '.accountancy_code_buy_intra' => "Text", $alias_product_perentity . '.accountancy_code_buy_export' => "Text",
				'p.note' => "Text", 'p.note_public' => "Text",
				'p.weight' => "Numeric", 'p.length' => "Numeric", 'p.surface' => "Numeric", 'p.volume' => "Numeric", 'p.customcode' => 'Text',
				'p.price_base_type' => "Text", 'p.price' => "Numeric", 'p.price_ttc' => "Numeric", 'p.tva_tx' => 'Numeric', 'p.tosell' => "Boolean", 'p.tobuy' => "Boolean",
				'p.datec' => 'Date', 'p.tms' => 'Date'
			);
			if (isModEnabled('stock')) {
				$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array('p.stock' => 'Numeric', 'p.seuil_stock_alerte' => 'Numeric', 'p.desiredstock' => 'Numeric', 'p.pmp' => 'Numeric', 'p.cost_price' => 'Numeric'));
			}
			if (isModEnabled('barcode')) {
				$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array('p.barcode' => 'Text'));
			}
			$this->export_TypeFields_array[$r] = array_merge($this->export_TypeFields_array[$r], array('pa.qty' => 'Numeric'));
			$this->export_entities_array[$r] = array(
				'p.rowid' => "virtualproduct", 'p.ref' => "virtualproduct", 'p.label' => "virtualproduct", 'p.description' => "virtualproduct", 'p.url' => "virtualproduct",
				$alias_product_perentity . '.accountancy_code_sell' => 'virtualproduct', $alias_product_perentity . '.accountancy_code_sell_intra' => 'virtualproduct', $alias_product_perentity . '.accountancy_code_sell_export' => 'virtualproduct',
				$alias_product_perentity . '.accountancy_code_buy' => 'virtualproduct', $alias_product_perentity . '.accountancy_code_buy_intra' => 'virtualproduct', $alias_product_perentity . '.accountancy_code_buy_export' => 'virtualproduct',
				'p.note' => "virtualproduct", 'p.length' => "virtualproduct",
				'p.surface' => "virtualproduct", 'p.volume' => "virtualproduct", 'p.weight' => "virtualproduct", 'p.customcode' => 'virtualproduct',
				'p.price_base_type' => "virtualproduct", 'p.price' => "virtualproduct", 'p.price_ttc' => "virtualproduct", 'p.tva_tx' => "virtualproduct",
				'p.tosell' => "virtualproduct", 'p.tobuy' => "virtualproduct", 'p.datec' => "virtualproduct", 'p.tms' => "virtualproduct"
			);
			if (isModEnabled('stock')) {
				$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('p.stock' => 'virtualproduct', 'p.seuil_stock_alerte' => 'virtualproduct', 'p.desiredstock' => 'virtualproduct', 'p.pmp' => 'virtualproduct'));
			}
			if (isModEnabled('barcode')) {
				$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('p.barcode' => 'virtualproduct'));
			}
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('pa.qty' => "subproduct", 'pa.incdec' => 'subproduct'));
			$keyforselect = 'product';
			$keyforelement = 'product';
			$keyforaliasextra = 'extra';
			include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
			$this->export_fields_array[$r] = array_merge($this->export_fields_array[$r], array('p2.rowid' => "Id", 'p2.ref' => "Ref", 'p2.label' => "Label", 'p2.description' => "Description"));
			$this->export_entities_array[$r] = array_merge($this->export_entities_array[$r], array('p2.rowid' => "subproduct", 'p2.ref' => "subproduct", 'p2.label' => "subproduct", 'p2.description' => "subproduct"));
			$this->export_sql_start[$r] = 'SELECT DISTINCT ';
			$this->export_sql_end[$r]  = ' FROM '.MAIN_DB_PREFIX.'product as p';
			if (getDolGlobalString('MAIN_PRODUCT_PERENTITY_SHARED')) {
				$this->export_sql_end[$r] .= " LEFT JOIN " . MAIN_DB_PREFIX . "product_perentity as ppe ON ppe.fk_product = p.rowid AND ppe.entity = " . ((int) $conf->entity);
			}
			$this->export_sql_end[$r] .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_extrafields as extra ON p.rowid = extra.fk_object,';
			$this->export_sql_end[$r] .= ' '.MAIN_DB_PREFIX.'product_association as pa, '.MAIN_DB_PREFIX.'product as p2';
			$this->export_sql_end[$r] .= ' WHERE p.entity IN ('.getEntity('product').')'; // For product and service profile
			$this->export_sql_end[$r] .= ' AND p.rowid = pa.fk_product_pere AND p2.rowid = pa.fk_product_fils';
		}

		// Imports
		//--------
		$r = 0;

		// Import list of products

		$r++;
		$this->import_code[$r] = $this->rights_class.'_'.$r;
		$this->import_label[$r] = "Products"; // Translation key
		$this->import_icon[$r] = $this->picto;
		$this->import_entities_array[$r] = array(); // We define here only fields that use a different icon from the one defined in import_icon
		$this->import_tables_array[$r] = array('p' => MAIN_DB_PREFIX.'product', 'extra' => MAIN_DB_PREFIX.'product_extrafields');
		$this->import_tables_creator_array[$r] = array('p' => 'fk_user_author'); // Fields to store import user id
		$this->import_fields_array[$r] = array(
			'p.ref' => "Ref*",
			'p.label' => "Label*",
			'p.fk_product_type' => "Type*",
			'p.tosell' => "OnSell*",
			'p.tobuy' => "OnBuy*",
			'p.description' => "Description",
			'p.url' => "PublicUrl",
			'p.customcode' => 'CustomsCode',
			'p.fk_country' => 'CountryCode',
			'p.accountancy_code_sell' => "ProductAccountancySellCode",
			'p.accountancy_code_sell_intra' => "ProductAccountancySellIntraCode",
			'p.accountancy_code_sell_export' => "ProductAccountancySellExportCode",
			'p.accountancy_code_buy' => "ProductAccountancyBuyCode",
			'p.accountancy_code_buy_intra' => "ProductAccountancyBuyIntraCode",
			'p.accountancy_code_buy_export' => "ProductAccountancyBuyExportCode",
			'p.note_public' => "NotePublic",
			'p.note' => "NotePrivate",
			'p.weight' => "Weight",
			'p.weight_units' => "WeightUnits",
			'p.length' => "Length",
			'p.length_units' => "LengthUnits",
			'p.width' => "Width",
			'p.width_units' => "WidthUnits",
			'p.height' => "Height",
			'p.height_units' => "HeightUnits",
			'p.surface' => "Surface",
			'p.surface_units' => "SurfaceUnits",
			'p.volume' => "Volume",
			'p.volume_units' => "VolumeUnits",
			'p.duration' => "Duration", //duration of service
			'p.finished' => 'Nature',
			'p.price' => "SellingPriceHT", //without
			'p.price_min' => "MinPrice",
			'p.price_ttc' => "SellingPriceTTC", //with tax
			'p.price_min_ttc' => "SellingMinPriceTTC",
			'p.price_base_type' => "PriceBaseType", //price base: with-tax (TTC) or without (HT) tax. Displays accordingly in Product card
			'p.tva_tx' => 'VATRate',
			'p.datec' => 'DateCreation',
			'p.cost_price' => "CostPrice"
		);

		$this->import_convertvalue_array[$r] = array(
				'p.weight_units' => array(
						'rule' => 'fetchscalefromcodeunits', // Switch this to fetchidfromcodeunits when we will store id instead of scale in product table
						'classfile' => '/core/class/cunits.class.php',
						'class' => 'CUnits',
						'method' => 'fetch',
						'units' => 'weight',
						'dict' => 'DictionaryMeasuringUnits'
				),
				'p.length_units' => array(
					'rule' => 'fetchscalefromcodeunits', // Switch this to fetchidfromcodeunits when we will store id instead of scale in product table
						'classfile' => '/core/class/cunits.class.php',
						'class' => 'CUnits',
						'method' => 'fetch',
						'units' => 'size',
						'dict' => 'DictionaryMeasuringUnits'
				),
				'p.width_units' => array(
						'rule' => 'fetchscalefromcodeunits', // Switch this to fetchidfromcodeunits when we will store id instead of scale in product table
						'classfile' => '/core/class/cunits.class.php',
						'class' => 'CUnits',
						'method' => 'fetch',
						'units' => 'size',
						'dict' => 'DictionaryMeasuringUnits'
				),
				'p.height_units' => array(
						'rule' => 'fetchscalefromcodeunits', // Switch this to fetchidfromcodeunits when we will store id instead of scale in product table
						'classfile' => '/core/class/cunits.class.php',
						'class' => 'CUnits',
						'method' => 'fetch',
						'units' => 'size',
						'dict' => 'DictionaryMeasuringUnits'
				),
				'p.surface_units' => array(
						'rule' => 'fetchscalefromcodeunits', // Switch this to fetchidfromcodeunits when we will store id instead of scale in product table
						'classfile' => '/core/class/cunits.class.php',
						'class' => 'CUnits',
						'method' => 'fetch',
						'units' => 'surface',
						'dict' => 'DictionaryMeasuringUnits'
				),
				'p.volume_units' => array(
						'rule' => 'fetchscalefromcodeunits', // Switch this to fetchidfromcodeunits when we will store id instead of scale in product table
						'classfile' => '/core/class/cunits.class.php',
						'class' => 'CUnits',
						'method' => 'fetch',
						'units' => 'volume',
						'dict' => 'DictionaryMeasuringUnits'
				),
				'p.fk_country' => array(
					'rule' => 'fetchidfromcodeid',
					'classfile' => '/core/class/ccountry.class.php',
					'class' => 'Ccountry',
					'method' => 'fetch',
					'dict' => 'DictionaryCountry'
				),
				'p.finished' => array(
					'rule' => 'fetchidfromcodeorlabel',
					'classfile' => '/core/class/cproductnature.class.php',
					'class' => 'CProductNature',
					'method' => 'fetch',
					'dict' => 'DictionaryProductNature'
				),
				'p.accountancy_code_sell' => array('rule' => 'accountingaccount'),
				'p.accountancy_code_sell_intra' => array('rule' => 'accountingaccount'),
				'p.accountancy_code_sell_export' => array('rule' => 'accountingaccount'),
				'p.accountancy_code_buy' => array('rule' => 'accountingaccount'),
				'p.accountancy_code_buy_intra' => array('rule' => 'accountingaccount'),
				'p.accountancy_code_buy_export' => array('rule' => 'accountingaccount'),
		);

		$this->import_regex_array[$r] = array(
			'p.ref' => '[^ ]',
			'p.price_base_type' => '\AHT\z|\ATTC\z',
			'p.tosell' => '^[0|1]$',
			'p.tobuy' => '^[0|1]$',
			'p.fk_product_type' => '^[0|1]$',
			'p.datec' => '^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$',
			'p.recuperableonly' => '^[0|1]$',
		);

		if (isModEnabled('stock')) {//if Stock module enabled
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array(
				'p.fk_default_warehouse' => 'DefaultWarehouse',
				'p.tobatch' => 'ManageLotSerial',
				'p.seuil_stock_alerte' => 'StockLimit', //lower limit for warning
				'p.pmp' => 'PMPValue', //weighted average price
				'p.desiredstock' => 'DesiredStock'//desired stock for replenishment feature
			));

			$this->import_regex_array[$r] = array_merge($this->import_regex_array[$r], array(
				'p.tobatch' => '^[0|1|2]$'
			));

			$this->import_convertvalue_array[$r] = array_merge($this->import_convertvalue_array[$r], array(
					'p.fk_default_warehouse' => array(
					'rule' => 'fetchidfromref',
					'classfile' => '/product/stock/class/entrepot.class.php',
					'class' => 'Entrepot',
					'method' => 'fetch',
					'element' => 'Warehouse'
				)
			));
		}

		if (getDolGlobalString('PRODUCT_USE_CUSTOMER_PACKAGING')) {
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array(
				'p.packaging' => 'PackagingForThisProductSell',
			));
		}

		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice") || isModEnabled('margin')) {
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('p.cost_price' => 'CostPrice'));
		}
		if (is_object($mysoc) && $usenpr) {
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('p.recuperableonly' => 'NPR'));
		}
		if (is_object($mysoc) && $mysoc->useLocalTax(1)) {
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('p.localtax1_tx' => 'LT1', 'p.localtax1_type' => 'LT1Type'));
		}
		if (is_object($mysoc) && $mysoc->useLocalTax(2)) {
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('p.localtax2_tx' => 'LT2', 'p.localtax2_type' => 'LT2Type'));
		}
		if (isModEnabled('barcode')) {
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('p.barcode' => 'BarCode'));
		}
		if (getDolGlobalInt('PRODUCT_USE_UNITS')) {
			$this->import_fields_array[$r]['p.fk_unit'] = 'Unit';
		}

		// Add extra fields
		$import_extrafield_sample = array();
		$sql = "SELECT name, label, fieldrequired FROM ".MAIN_DB_PREFIX."extrafields WHERE type <> 'separate' AND elementtype = 'product' AND entity IN (0, ".$conf->entity.")";
		$resql = $this->db->query($sql);
		if ($resql) {    // This can fail when class is used on old database (during migration for example)
			while ($obj = $this->db->fetch_object($resql)) {
				$fieldname = 'extra.'.$obj->name;
				$fieldlabel = ucfirst($obj->label);
				$this->import_fields_array[$r][$fieldname] = $fieldlabel.($obj->fieldrequired ? '*' : '');
				$import_extrafield_sample[$fieldname] = $fieldlabel;
			}
		}
		// End add extra fields
		$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'product'); // aliastable.field => ('user->id' or 'lastrowid-'.tableparent)

		// field order as per structure of table llx_product
		$import_sample = array(
			'p.ref' => "ref:PREF123456",
			'p.datec' => dol_print_date(dol_now(), '%Y-%m-%d'),
			'p.label' => "Product name in default language",
			'p.description' => "Product description in default language",
			'p.note_public' => "a public note (free text)",
			'p.note' => "a private note (free text)",
			'p.customcode' => 'customs code',
			'p.fk_country' => 'FR',
			'p.price' => "100",
			'p.price_min' => "100",
			'p.price_ttc' => "110",
			'p.price_min_ttc' => "110",
			'p.price_base_type' => "HT (show/use price excl. tax) / TTC (show/use price incl. tax)",
			'p.tva_tx' => '10', // tax rate eg: 10. Must match numerically one of the tax rates defined for your country'
			'p.tosell' => "0 (not for sale to customer, eg. raw material) / 1 (for sale)",
			'p.tobuy' => "0 (not for purchase from supplier, eg. virtual product) / 1 (for purchase)",
			'p.fk_product_type' => "0 (product) / 1 (service)",
			'p.duration' => "eg. 365d/12m/1y",
			'p.url' => 'link to product (no https)',
			'p.accountancy_code_sell' => "",
			'p.accountancy_code_sell_intra' => "",
			'p.accountancy_code_sell_export' => "",
			'p.accountancy_code_buy' => "",
			'p.accountancy_code_buy_intra' => "",
			'p.accountancy_code_buy_export' => "",
			'p.weight' => "",
			'p.weight_units' => 'kg', // Use a unit of measure from the dictionary. g/Kg/T etc....matches field "Short label" for unit type "weight" in table "' . MAIN_DB_PREFIX . 'c_units',
			'p.length' => "",
			'p.length_units' => 'm', // Use a unit of measure from the dictionary. m/cm/mm etc....matches field "Short label" for unit type "size" in table "' . MAIN_DB_PREFIX . 'c_units',
			'p.width' => "",
			'p.width_units' => 'm', // Use a unit of measure from the dictionary. m/cm/mm etc....matches field "Short label" for unit type "size" in table "' . MAIN_DB_PREFIX . 'c_units',
			'p.height' => "",
			'p.height_units' => 'm', // Use a unit of measure from the dictionary. m/cm/mm etc....matches field "Short label" for unit type "size" in table "' . MAIN_DB_PREFIX . 'c_units',
			'p.surface' => "",
			'p.surface_units' => 'm2', // Use a unit of measure from the dictionary. m2/cm2/mm2 etc....matches field "Short label" for unit type "surface" in table "' . MAIN_DB_PREFIX . 'c_units',
			'p.volume' => "",
			'p.volume_units' => 'm3', //Use a unit of measure from the dictionary. m3/cm3/mm3 etc....matches field "Short label" for unit type "volume" in table "' . MAIN_DB_PREFIX . 'c_units',
			'p.finished' => '0 (raw material) / 1 (finished goods), matches field "code" in dictionary table "'.MAIN_DB_PREFIX.'c_product_nature"'
		);
		//clauses copied from import_fields_array
		if (isModEnabled('stock')) {
			$import_sample = array_merge($import_sample, array(
				'p.tobatch' => "0 (don't use) / 1 (use batch) / 2 (use serial number)",
				'p.seuil_stock_alerte' => '',
				'p.pmp' => '0',
				'p.desiredstock' => ''
			));
		}
		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice") || isModEnabled('margin')) {
			$import_sample = array_merge($import_sample, array('p.cost_price' => '90'));
		}
		if (is_object($mysoc) && $usenpr) {
			$import_sample = array_merge($import_sample, array('p.recuperableonly' => '0'));
		}
		if (is_object($mysoc) && $mysoc->useLocalTax(1)) {
			$import_sample = array_merge($import_sample, array('p.localtax1_tx' => '', 'p.localtax1_type' => ''));
		}
		if (is_object($mysoc) && $mysoc->useLocalTax(2)) {
			$import_sample = array_merge($import_sample, array('p.localtax2_tx' => '', 'p.localtax2_type' => ''));
		}
		if (isModEnabled('barcode')) {
			$import_sample = array_merge($import_sample, array('p.barcode' => ''));
		}
		if (getDolGlobalInt('PRODUCT_USE_UNITS')) {
			$import_sample = array_merge(
				$import_sample,
				array(
					'p.fk_unit' => 'use a unit of measure from the dictionary. G/KG/M2/M3 etc....matches field "code" in table "'.MAIN_DB_PREFIX.'c_units"'
				)
			);

			$this->import_convertvalue_array[$r] = array_merge($this->import_convertvalue_array[$r], array(
				'p.fk_unit' => array(
					'rule' => 'fetchidfromcodeorlabel',
					'classfile' => '/core/class/cunits.class.php',
					'class' => 'CUnits',
					'method' => 'fetch',
					'dict' => 'DictionaryUnits'
				)
			));
		}

		if (getDolGlobalString('PRODUCT_USE_CUSTOMER_PACKAGING')) {
			$import_sample = array_merge($import_sample, array(
				'p.packaging' => "2",
			));
		}

		$this->import_examplevalues_array[$r] = array_merge($import_sample, $import_extrafield_sample);
		$this->import_updatekeys_array[$r] = array('p.ref' => 'Ref');
		if (isModEnabled('barcode')) {
			$this->import_updatekeys_array[$r] = array_merge($this->import_updatekeys_array[$r], array('p.barcode' => 'BarCode')); //only show/allow barcode as update key if Barcode module enabled
		}

		if (getDolGlobalString('STOCK_ALLOW_ADD_LIMIT_STOCK_BY_WAREHOUSE')) {
			// Import products limit and desired stock by product and warehouse
			$r++;
			$this->import_code[$r] = $this->rights_class.'_stock_by_warehouse';
			$this->import_label[$r] = "ProductStockWarehouse"; // Translation key
			$this->import_icon[$r] = $this->picto;
			$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
			$this->import_tables_array[$r] = array('pwp' => MAIN_DB_PREFIX.'product_warehouse_properties');
			$this->import_fields_array[$r] = array('pwp.fk_product' => "Product*",
				'pwp.fk_entrepot' => "Warehouse*", 'pwp.seuil_stock_alerte' => "StockLimit",
				'pwp.desiredstock' => "DesiredStock");
			$this->import_regex_array[$r] = array(
				'pwp.fk_product' => 'rowid@'.MAIN_DB_PREFIX.'product',
				'pwp.fk_entrepot' => 'rowid@'.MAIN_DB_PREFIX.'entrepot',
			);
			$this->import_convertvalue_array[$r] = array(
				'pwp.fk_product' => array('rule' => 'fetchidfromref', 'classfile' => '/product/class/product.class.php', 'class' => 'Product', 'method' => 'fetch', 'element' => 'Product')
				,'pwp.fk_entrepot' => array('rule' => 'fetchidfromref', 'classfile' => '/product/stock/class/entrepot.class.php', 'class' => 'Entrepot', 'method' => 'fetch', 'element' => 'Entrepot')
			);
			$this->import_examplevalues_array[$r] = array('pwp.fk_product' => "ref:PRODUCT_REF or id:123456",
				'pwp.fk_entrepot' => "ref:WAREHOUSE_REF or id:123456",
				'pwp.seuil_stock_alerte' => "100",
				'pwp.desiredstock' => "110"
			);
			$this->import_updatekeys_array[$r] = array('pwp.fk_product' => 'Product', 'pwp.fk_entrepot' => 'Warehouse');
		}

		if (isModEnabled("supplier_order") || isModEnabled("supplier_invoice")) {
			// Import suppliers prices (note: this code is duplicated in module Service)
			$r++;
			$this->import_code[$r] = $this->rights_class.'_supplierprices';
			$this->import_label[$r] = "SuppliersPricesOfProductsOrServices"; // Translation key
			$this->import_icon[$r] = $this->picto;
			$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
			$this->import_tables_array[$r] = array('sp' => MAIN_DB_PREFIX.'product_fournisseur_price', 'extra' => MAIN_DB_PREFIX.'product_fournisseur_price_extrafields');
			$this->import_tables_creator_array[$r] = array('sp' => 'fk_user');
			$this->import_fields_array[$r] = array(//field order as per structure of table llx_product_fournisseur_price, without optional fields
				'sp.fk_product' => "ProductOrService*",
				'sp.fk_soc' => "Supplier*",
				'sp.ref_fourn' => 'SupplierRef*',
				'sp.quantity' => "QtyMin*",
				'sp.tva_tx' => 'VATRate',
				'sp.default_vat_code' => 'VATCode',
				'sp.delivery_time_days' => 'NbDaysToDelivery',
				'sp.supplier_reputation' => 'SupplierReputation',
				'sp.status' => 'Status'
			);
			if (is_object($mysoc) && $usenpr) {
				$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('sp.recuperableonly' => 'VATNPR'));
			}
			if (is_object($mysoc) && $mysoc->useLocalTax(1)) {
				$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('sp.localtax1_tx' => 'LT1', 'sp.localtax1_type' => 'LT1Type'));
			}
			if (is_object($mysoc) && $mysoc->useLocalTax(2)) {
				$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('sp.localtax2_tx' => 'LT2', 'sp.localtax2_type' => 'LT2Type'));
			}
			$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array(
					'sp.price' => "PriceQtyMinHT*",
					'sp.unitprice' => 'UnitPriceHT*', // TODO Make this field not required and calculate it from price and qty
					'sp.remise_percent' => 'DiscountQtyMin'
			));

			if (isModEnabled("multicurrency")) {
				$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array(
					'sp.fk_multicurrency' => 'CurrencyCodeId', //ideally this should be automatically obtained from the CurrencyCode on the next line
					'sp.multicurrency_code' => 'CurrencyCode',
					'sp.multicurrency_tx' => 'CurrencyRate',
					'sp.multicurrency_unitprice' => 'CurrencyUnitPrice',
					'sp.multicurrency_price' => 'CurrencyPrice',
				));
			}

			if (getDolGlobalString('PRODUCT_USE_SUPPLIER_PACKAGING')) {
				$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('sp.packaging' => 'PackagingForThisProduct'));
			}

			// Add extra fields
			$import_extrafield_sample = array();
			$sql = "SELECT name, label, fieldrequired FROM ".MAIN_DB_PREFIX."extrafields WHERE type <> 'separate' AND  elementtype = 'product_fournisseur_price' AND entity IN (0, ".$conf->entity.")";
			$resql = $this->db->query($sql);
			if ($resql) {    // This can fail when class is used on old database (during migration for example)
				while ($obj = $this->db->fetch_object($resql)) {
					$fieldname = 'extra.'.$obj->name;
					$fieldlabel = ucfirst($obj->label);
					$this->import_fields_array[$r][$fieldname] = $fieldlabel.($obj->fieldrequired ? '*' : '');
					$import_extrafield_sample[$fieldname] = $fieldlabel;
				}
			}
			// End add extra fields
			$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'product_fournisseur_price'); // aliastable.field => ('user->id' or 'lastrowid-'.tableparent)

			$this->import_convertvalue_array[$r] = array(
					'sp.fk_soc' => array('rule' => 'fetchidfromref', 'classfile' => '/societe/class/societe.class.php', 'class' => 'Societe', 'method' => 'fetch', 'element' => 'ThirdParty'),
					'sp.fk_product' => array('rule' => 'fetchidfromref', 'classfile' => '/product/class/product.class.php', 'class' => 'Product', 'method' => 'fetch', 'element' => 'Product')
			);

			$this->import_examplevalues_array[$r] = array(
				'sp.fk_product' => "ref:PRODUCT_REF or id:123456",
				'sp.fk_soc' => "My Supplier",
				'sp.ref_fourn' => "XYZ-F123456",
				'sp.quantity' => "5",
				'sp.tva_tx' => '10',
				'sp.price' => "50",
				'sp.unitprice' => '50',
				'sp.remise_percent' => '0',
				'sp.default_vat_code' => '',
				'sp.delivery_time_days' => '5',
				'sp.supplier_reputation' => 'FAVORITE / NOTTHGOOD / DONOTORDER',
				'sp.status' => '1'
			);
			if (is_object($mysoc) && $usenpr) {
				$this->import_examplevalues_array[$r] = array_merge($this->import_examplevalues_array[$r], array('sp.recuperableonly' => ''));
			}
			if (is_object($mysoc) && $mysoc->useLocalTax(1)) {
				$this->import_examplevalues_array[$r] = array_merge($this->import_examplevalues_array[$r], array('sp.localtax1_tx' => 'LT1', 'sp.localtax1_type' => 'LT1Type'));
			}
			if (is_object($mysoc) && $mysoc->useLocalTax(2)) {
				$this->import_examplevalues_array[$r] = array_merge($this->import_examplevalues_array[$r], array('sp.localtax2_tx' => 'LT2', 'sp.localtax2_type' => 'LT2Type'));
			}
			$this->import_examplevalues_array[$r] = array_merge($this->import_examplevalues_array[$r], array(
				'sp.price' => "50.00",
				'sp.unitprice' => '10',
				// TODO Make this field not required and calculate it from price and qty
				'sp.remise_percent' => '20'
			));
			if (isModEnabled("multicurrency")) {
				$this->import_examplevalues_array[$r] = array_merge($this->import_examplevalues_array[$r], array(
					'sp.fk_multicurrency' => 'eg: 2, rowid for code of multicurrency currency',
					'sp.multicurrency_code' => 'GBP',
					'sp.multicurrency_tx' => '1.12345',
					'sp.multicurrency_unitprice' => '',
					// TODO Make this field not required and calculate it from price and qty
					'sp.multicurrency_price' => ''
				));
			}
			if (getDolGlobalString('PRODUCT_USE_SUPPLIER_PACKAGING')) {
				$this->import_examplevalues_array[$r] = array_merge($this->import_examplevalues_array[$r], array(
					'sp.packaging' => '10',
				));
			}

			$this->import_updatekeys_array[$r] = array('sp.fk_product' => 'ProductOrService', 'sp.ref_fourn' => 'SupplierRef', 'sp.fk_soc' => 'Supplier', 'sp.quantity' => "QtyMin");
		}

		if (getDolGlobalString('PRODUIT_MULTIPRICES') || getDolGlobalString('PRODUIT_CUSTOMER_PRICES_AND_MULTIPRICES')) {
			// Import products multiprices
			$r++;
			$this->import_code[$r] = $this->rights_class.'_multiprice';
			$this->import_label[$r] = "ProductsOrServiceMultiPrice"; // Translation key
			$this->import_icon[$r] = $this->picto;
			$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
			$this->import_tables_array[$r] = array('pr' => MAIN_DB_PREFIX.'product_price', 'extra' => MAIN_DB_PREFIX.'product_price_extrafields');
			$this->import_tables_creator_array[$r] = array('pr' => 'fk_user_author'); // Fields to store import user id
			$this->import_fields_array[$r] = array('pr.fk_product' => "ProductOrService*",
				'pr.price_base_type' => "PriceBase", 'pr.price_level' => "PriceLevel",
				'pr.price' => "PriceLevelUnitPriceHT", 'pr.price_ttc' => "PriceLevelUnitPriceTTC",
				'pr.price_min' => "MinPriceLevelUnitPriceHT", 'pr.price_min_ttc' => "MinPriceLevelUnitPriceTTC",
				'pr.date_price' => 'DateCreation*');
			if (getDolGlobalString('PRODUIT_MULTIPRICES_USE_VAT_PER_LEVEL')) {
				$this->import_fields_array[$r]['pr.tva_tx'] = 'VATRate';
			}
			if (is_object($mysoc) && $usenpr) {
				$this->import_fields_array[$r] = array_merge($this->import_fields_array[$r], array('pr.recuperableonly' => 'NPR'));
			}

			// Add extra fields
			$import_extrafield_sample = array();
			$sql = "SELECT name, label, fieldrequired FROM ".MAIN_DB_PREFIX."extrafields WHERE type <> 'separate' AND elementtype = 'product_price' AND entity IN (0, ".$conf->entity.")";
			$resql = $this->db->query($sql);
			if ($resql) {    // This can fail when class is used on old database (during migration for example)
				while ($obj = $this->db->fetch_object($resql)) {
					$fieldname = 'extra.'.$obj->name;
					$fieldlabel = ucfirst($obj->label);
					$this->import_fields_array[$r][$fieldname] = $fieldlabel.($obj->fieldrequired ? '*' : '');
					$import_extrafield_sample[$fieldname] = $fieldlabel;
				}
			}
			// End add extra fields
			$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'product_price'); // aliastable.field => ('user->id' or 'lastrowid-'.tableparent)

			$this->import_regex_array[$r] = array('pr.datec' => '^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$', 'pr.recuperableonly' => '^[0|1]$');
			$this->import_convertvalue_array[$r] = array(
				'pr.fk_product' => array('rule' => 'fetchidfromref', 'classfile' => '/product/class/product.class.php', 'class' => 'Product', 'method' => 'fetch', 'element' => 'Product')
			);
			$this->import_examplevalues_array[$r] = array('pr.fk_product' => "ref:PRODUCT_REF or id:123456",
				'pr.price_base_type' => "HT (for excl tax) or TTC (for inc tax)", 'pr.price_level' => "1",
				'pr.price' => "100", 'pr.price_ttc' => "110",
				'pr.price_min' => "100", 'pr.price_min_ttc' => "110",
				'pr.tva_tx' => '20',
				'pr.recuperableonly' => '0',
				'pr.date_price' => '2020-12-31');
		}

		if (getDolGlobalInt('MAIN_MULTILANGS')) {
			// Import translations of product names and descriptions
			$r++;
			$this->import_code[$r] = $this->rights_class.'_languages';
			$this->import_label[$r] = "ProductsOrServicesTranslations";
			$this->import_icon[$r] = $this->picto;
			$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
			$this->import_tables_array[$r] = array('l' => MAIN_DB_PREFIX.'product_lang');
			// multiline translation, one line per translation
			$this->import_fields_array[$r] = array('l.fk_product' => 'ProductOrService*', 'l.lang' => 'Language*', 'l.label' => 'TranslatedLabel', 'l.description' => 'TranslatedDescription');
			//$this->import_fields_array[$r]['l.note']='TranslatedNote';
			$this->import_convertvalue_array[$r] = array(
					'l.fk_product' => array('rule' => 'fetchidfromref', 'classfile' => '/product/class/product.class.php', 'class' => 'Product', 'method' => 'fetch', 'element' => 'Product')
			);
			$this->import_examplevalues_array[$r] = array('l.fk_product' => 'ref:PRODUCT_REF or id:123456', 'l.lang' => 'en_US', 'l.label' => 'Label in en_US', 'l.description' => 'Desc in en_US');
			$this->import_updatekeys_array[$r] = array('l.fk_product' => 'ProductOrService', 'l.lang' => 'Language');
		}

		if (getDolGlobalInt('PRODUIT_SOUSPRODUITS')) {
			// Import products kit
			$r++;
			$this->import_code[$r] = $this->rights_class . '_' . $r;
			$this->import_label[$r] = "AssociatedProducts"; // Translation key
			$this->import_icon[$r] = $this->picto;
			$this->import_entities_array[$r] = array(); // We define here only fields that use another icon that the one defined into import_icon
			$this->import_tables_array[$r] = array('pa' => MAIN_DB_PREFIX . 'product_association');
			$this->import_fields_array[$r] = array('pa.fk_product_pere' => 'ParentProducts', 'pa.fk_product_fils' => 'ComposedProduct', 'pa.qty' => 'Qty', 'pa.incdec' => 'ComposedProductIncDecStock', 'pa.rang' => 'rang');

			$this->import_convertvalue_array[$r] = array(
				'pa.fk_product_pere' => array('rule' => 'fetchidfromref', 'classfile' => '/product/class/product.class.php', 'class' => 'Product', 'method' => 'fetch', 'element' => 'Product'),
				'pa.fk_product_fils' => array('rule' => 'fetchidfromref', 'classfile' => '/product/class/product.class.php', 'class' => 'Product', 'method' => 'fetch', 'element' => 'Product')
			);
			$this->import_examplevalues_array[$r] = array(
				'pa.fk_product_pere' => "ref:PREF123456",
				'pa.fk_product_fils' => "ref:PREF123456",
				'pa.qty' => "100",
				'pa.incdec' => "0",
				'pa.rang' => "1");
			$this->import_regex_array[$r] = array('pa.fk_product_pere' => 'rowid@'.MAIN_DB_PREFIX.'product', 'pa.fk_product_fils' => 'rowid@'.MAIN_DB_PREFIX.'product');
			$this->import_updatekeys_array[$r] = array('pa.fk_product_pere' => 'ref parent', 'pa.fk_product_fils' => "ref enfant");
		}
	}


	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string	$options    Options when enabling module ('', 'newboxdefonly', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$this->remove($options);

		$sql = array();

		return $this->_init($sql, $options);
	}
}
