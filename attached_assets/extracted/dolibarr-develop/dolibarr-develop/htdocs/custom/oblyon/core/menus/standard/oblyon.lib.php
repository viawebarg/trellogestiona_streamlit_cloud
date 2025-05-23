<?php
/**
 * Copyright (C) 2013-2016  Nicolas Rivera      <nrivera.pro@gmail.com>
 * Copyright (C) 2015-2024  Alexandre Spangaro  <alexandre@inovea-conseil.com>
 *
 * Copyright (C) 2010-2013  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2010       Regis Houssin       <regis.houssin@capnetworks.com>
 * Copyright (C) 2012-2013  Juanjo Menent       <jmenent@2byte.es>
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
 * or see https://www.gnu.org/
 */

/**
 *	\file		htdocs/custom/oblyon/core/menus/standard/oblyon.lib.php
 *	\brief		Library for file Oblyon menus
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';
dol_include_once('/oblyon/backport/v21/core/lib/functions.lib.php');

// Translations
$langs->loadLangs(array('oblyon@oblyon'));

/**
 * Core function to output top menu oblyon
 *
 * @param 	DoliDB	$db				Database handler
 * @param 	string	$atarget		Target
 * @param 	int		$type_user	 	0=Menu for backoffice, 1=Menu for front office
 * @param	array	&$tabMenu		 If array with menu entries already loaded, we put this array here (in most cases, it's empty)
 * @param	array	&$menu			Object Menu to return back list of menu entries
 * @param	int		$noout			Disable output (Initialise &$menu only).
 * @return	void
 */
function print_oblyon_menu($db, $atarget, $type_user = 0, &$tabMenu, &$menu, $noout=0, $forcemainmenu='', $forceleftmenu='', $moredata=null)
{
	global $user, $conf, $langs, $mysoc;
	global $dolibarr_main_db_name;

	$mainmenu = (empty($_SESSION["mainmenu"]) ? '' : $_SESSION["mainmenu"]);
	$leftmenu = (empty($_SESSION["leftmenu"]) ? '' : $_SESSION["leftmenu"]);

	$landingpage = (!getDolUserString('MAIN_LANDING_PAGE') ? (!getDolGlobalString('MAIN_LANDING_PAGE') ? '' : getDolGlobalString('MAIN_LANDING_PAGE')) : getDolUserString('MAIN_LANDING_PAGE'));
	if (! empty($landingpage)) {
		$landingpage = dol_buildpath($landingpage, 1);
	} else {
		$landingpage = DOL_URL_ROOT . '/index.php?mainmenu=home&amp;leftmenu=home';
	}

	$id = 'mainmenu';
	$listofmodulesforexternal = explode(',', getDolGlobalString('MAIN_MODULES_FOR_EXTERNAL'));

    $classname = '';

    $menu_invert = getDolGlobalInt('MAIN_MENU_INVERT');

    if (empty($noout)) print_start_menu_array();

    // Show logo company
	if (empty($menu_invert) && empty($noout) && getDolGlobalString('MAIN_SHOW_LOGO')) {
        $mysoc->logo_squarred_mini = getDolGlobalString('MAIN_INFO_SOCIETE_LOGO_SQUARRED_MINI', '');

        if (! empty($mysoc->logo_squarred_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_squarred_mini)) {
            $urllogo = DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/thumbs/'.$mysoc->logo_squarred_mini);
        }
        /*elseif (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini))
        {
            $urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/thumbs/'.$mysoc->logo_mini);
        }*/
        else {
            $urllogo = DOL_URL_ROOT.'/theme/dolibarr_logo.png';
            $logoContainerAdditionalClass = '';
        }
        $title = $langs->trans("GoIntoSetupToChangeLogo");

        print "\n".'<!-- Show logo on menu -->'."\n";
        print_start_menu_entry('companylogo', 'class="tmenu tmenucompanylogo"', 1);

        print '<div class="center backgroundforcompanylogo menulogocontainer">';
        print '<a href="' . $landingpage . '" alt="'.dol_escape_htmltag($title).'" title="'.dol_escape_htmltag($title).'">';
        print '<img class="mycompany" title="'.dol_escape_htmltag($title).'" alt="" src="'.$urllogo.'" style="max-width: 100px; height: 32px;">';
        print '</a>'."\n";
        print '</div>'."\n";

        print_end_menu_entry(1);
	}

    if (is_array($moredata) && ! empty($moredata['searchform']) && empty($noout))	// searchform can contains select2 code or link to show old search form or link to switch on search page
    {
        print "\n";
        print "<!-- Begin SearchForm -->\n";
        print '<div id="blockvmenusearch" class="blockvmenusearch">'."\n";
        print $moredata['searchform'];
        print '</div>'."\n";
        print "<!-- End SearchForm -->\n";
    }

    if (is_array($moredata) && ! empty($moredata['bookmarks']) && empty($noout))
    {
        print "\n";
        print "<!-- Begin Bookmarks -->\n";
        print '<div id="blockvmenubookmarks" class="blockvmenubookmarks">'."\n";
        print $moredata['bookmarks'];
        print '</div>'."\n";
        print "<!-- End Bookmarks -->\n";
    }

	if ( empty($menu_invert) && (getDolGlobalInt('OBLYON_HIDE_LEFTMENU') || $conf->dol_optimize_smallscreen) && empty($noout) ) {
		print '<div class="pushy-btn" title="'.$langs->trans("ShowLeftMenu").'">&#8801;</div>';
	}

	// Home
	$showmode=1;
	if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "home") {
        $itemsel=TRUE;
        $_SESSION['idmenu']='';
    } else {
        $itemsel = FALSE;
    }
    $idsel='home';

    $chaine=$langs->trans("Home");

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($chaine, 1, $landingpage, $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add($landingpage, $chaine, 0, $showmode, $atarget, "home", '');

	// Members
	$tmpentry = array(
		'enabled' => (isModEnabled('adherent') || isModEnabled('member')),
		'perms' => ($user->hasRight('adherent', 'lire')),
		'module' => 'adherent'
	);
    $showmode = dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode)
	{
		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "members") {
            $itemsel = TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
		$idsel='members';

        $chaine=$langs->trans("MenuMembers");

        if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
        if (empty($noout)) print_text_menu_entry($chaine, 1, DOL_URL_ROOT.'/adherents/index.php?mainmenu=members&amp;leftmenu=', $id, $idsel, $atarget);
        if (empty($noout)) print_end_menu_entry($showmode);
        $menu->add('/adherents/index.php?mainmenu=members&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "members", '');
	}

	// Third parties
	$tmpentry = array(
		'enabled'=> ((isModEnabled('societe') &&
			(!getDolGlobalString('SOCIETE_DISABLE_PROSPECTS') || !getDolGlobalString('SOCIETE_DISABLE_CUSTOMERS'))
			)
			|| ((isModEnabled('fournisseur') && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD')) || isModEnabled('supplier_order') || isModEnabled('supplier_invoice'))
			),
		'perms'=> ($user->hasRight('societe', 'lire') || $user->hasRight('fournisseur', 'lire') || $user->hasRight('supplier_order', 'lire') || $user->hasRight('supplier_invoice', 'lire') || $user->hasRight('supplier_proposal', 'lire')),
		'module'=>'societe|fournisseur'
	);
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
        $langs->loadLangs(array("companies","suppliers"));

		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "companies") {
            $itemsel = TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
		$idsel='companies';

        $chaine=$langs->trans("ThirdParties");

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/societe/index.php?mainmenu=companies&amp;leftmenu=', $id, $idsel, $atarget);
		if (empty($noout)) print_end_menu_entry($showmode);
		$menu->add('/societe/index.php?mainmenu=companies&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "companies", '');
	}

	// Products-Services
	$tmpentry = array(
		'enabled'=> (isModEnabled('product') || isModEnabled('service') || isModEnabled('expedition')),
		'perms'=> ($user->hasRight('produit', 'lire') || $user->hasRight('service', 'lire') || $user->hasRight('expedition', 'lire')),
		'module'=>'product|service'
	);
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
        $langs->loadLangs(array("products"));

		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "products") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
		$idsel='products';

		$chaine="";
		$chaine.=$langs->trans("MenuCatalog");

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/product/index.php?mainmenu=products&amp;leftmenu=', $id, $idsel, $atarget);
		if (empty($noout)) print_end_menu_entry($showmode);
		$menu->add('/product/index.php?mainmenu=products&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "products", '');
	}

	// MRP - GPAO
	$tmpentry = array(
		'enabled'=>(isModEnabled('bom') || isModEnabled('mrp')),
		'perms'=>($user->hasRight('bom', 'read') || $user->hasRight('mrp', 'read')),
		'module'=>'bom|mrp'
	);
    $showmode = dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
    if ($showmode) {
        $langs->loadLangs(array("mrp"));

        if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "mrp") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel=FALSE;
        }
        $idsel='mrp';

        $chaine = $langs->trans("TMenuMRP");

        if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
        if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/mrp/index.php?mainmenu=mrp&amp;leftmenu=', $id, $idsel, $atarget);
        if (empty($noout)) print_end_menu_entry($showmode);
        $menu->add('/mrp/index.php?mainmenu=mrp&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "mrp", '');
    }

	// Projects
	$tmpentry=array('enabled'=>isModEnabled('projet'),
		'perms'=>($user->hasRight('projet', 'lire')),
		'module'=>'projet');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
        $langs->loadLangs(array("projects"));

		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "project") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
        $idsel='project';

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
        if (getDolGlobalString('PROJECT_FORCE_LIST_ACCESS')) {
            if (empty($noout)) print_text_menu_entry($langs->trans("MenuProjectsOpportunities"), $showmode, DOL_URL_ROOT . '/projet/list.php?mainmenu=project&amp;leftmenu=', $id, $idsel, $atarget);
        } else {
            if (empty($noout)) print_text_menu_entry($langs->trans("MenuProjectsOpportunities"), $showmode, DOL_URL_ROOT . '/projet/index.php?mainmenu=project&amp;leftmenu=', $id, $idsel, $atarget);
        }
        if (empty($noout)) print_end_menu_entry($showmode);
		$title = $langs->trans("LeadsOrProjects");	// Leads and opportunities by default
		$showmodel = $showmodep = $showmode;
		if (!getDolGlobalString('PROJECT_USE_OPPORTUNITIES'))
		{
			$title = $langs->trans("Projects");
			$showmodel = 0;
		}
		if (getDolGlobalString('PROJECT_USE_OPPORTUNITIES') == 2) {
			$title = $langs->trans("Leads");
			$showmodep = 0;
		}
        if (getDolGlobalString('PROJECT_FORCE_LIST_ACCESS')) {
            $menu->add('/projet/list.php?mainmenu=project&amp;leftmenu=projets', $title, 0, $showmode, $atarget, "project", '', 70, $id, $idsel, $classname);
        } else {
            $menu->add('/projet/index.php?mainmenu=project&amp;leftmenu=', $title, 0, $showmode, $atarget, "project", '', 70, $id, $idsel, $classname);
        }
    }

	// Commercial (propal, commande, supplier_proposal, supplier_order, contrat, ficheinter)
	$tmpentry = array(
		'enabled'=>(isModEnabled('propal')
			|| isModEnabled('commande')
			|| isModEnabled('fournisseur')
			|| isModEnabled('supplier_proposal')
			|| isModEnabled('supplier_order')
			|| isModEnabled('contrat')
			|| isModEnabled('ficheinter')
		) ? 1 : 0,
		'perms'=>($user->hasRight('propal', 'lire')
			|| $user->hasRight('commande', 'lire')
			|| $user->hasRight('supplier_proposal', 'lire')
			|| $user->hasRight('fournisseur', 'lire')
			|| $user->hasRight('fournisseur', 'commande', 'lire')
			|| $user->hasRight('supplier_order', 'lire')
			|| $user->hasRight('contrat', 'lire')
			|| $user->hasRight('ficheinter', 'lire')
		),
		'module'=>'propal|commande|fournisseur|supplier_proposal|supplier_order|contrat|ficheinter'
	);
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
        $langs->loadLangs(array("commercial"));

        if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "commercial") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel=FALSE;
        }
        $idsel='commercial';

        $chaine=$langs->trans("Commercial");

        if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
        if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/comm/index.php?mainmenu=commercial&amp;leftmenu=', $id, $idsel, $atarget);
        if (empty($noout)) print_end_menu_entry($showmode);
        $menu->add('/comm/index.php?mainmenu=commercial&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "commercial", "");
    }

	// Billing - Financial
	$tmpentry = array(
		'enabled'=>(isModEnabled('facture') ||
			isModEnabled('don') ||
			isModEnabled('tax') ||
			isModEnabled('salaries') ||
			isModEnabled('supplier_invoice') ||
			isModEnabled('loan') ||
			isModEnabled('margins') ||
			isModEnabled('banque')
			) ? 1 : 0,
		'perms'=>($user->hasRight('facture', 'lire') || $user->hasRight('don', 'contact', 'lire')
			|| $user->hasRight('tax', 'charges', 'lire') || $user->hasRight('salaries', 'read')
			|| $user->hasRight('fournisseur', 'facture', 'lire') || $user->hasRight('loan', 'read') || $user->hasRight('margins', 'liretous')
			|| $user->hasRight('banque', 'lire')
			),
		'module'=>'facture|supplier_invoice|don|tax|salaries|loan'
	);
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
        $langs->loadLangs(array("compta","accountancy"));

		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "billing") {
            $itemsel=TRUE; $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
		$idsel='billing';

        $chaine=$langs->trans("MenuFinancial");

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/compta/index.php?mainmenu=billing&amp;leftmenu=', $id, $idsel, $atarget);
		if (empty($noout)) print_end_menu_entry($showmode);
        $menu->add('/compta/index.php?mainmenu=billing&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "billing", '');
	}

	// Bank
	$tmpentry = array(
		'enabled'=>(isModEnabled('banque') || isModEnabled('prelevement')),
		'perms'=>($user->hasRight('banque', 'lire') || $user->hasRight('prelevement', 'lire') || $user->hasRight('paymentbybanktransfer', 'read')),
		'module'=>'banque|prelevement|paymentbybanktransfer'
	);
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
        $langs->loadLangs(array("compta","banks"));

		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "bank") {
            $itemsel=TRUE; $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
		$idsel='bank';

        $chaine=$langs->trans("MenuBankCash");

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/compta/bank/list.php?mainmenu=bank&amp;leftmenu=', $id, $idsel, $atarget);
		if (empty($noout)) print_end_menu_entry($showmode);
		$menu->add('/compta/bank/list.php?mainmenu=bank&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "bank", '');
	}

	// Accounting
	$tmpentry = array(
		'enabled'=>(isModEnabled('comptabilite') || isModEnabled('accounting') || isModEnabled('asset') || isModEnabled('intracommreport')),
		'perms'=>($user->hasRight('compta', 'resultat', 'lire') || $user->hasRight('accounting', 'comptarapport', 'lire') || $user->hasRight('accounting', 'mouvements', 'lire') || $user->hasRight('asset', 'read') || $user->hasRight('intracommreport', 'read')),
		'module'=>'comptabilite|accounting|asset|intracommreport'
	);
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);

	if ($showmode) {
        $langs->loadLangs(array("compta", "accountancy", "assets", "intracommreport"));

		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "accountancy") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
		$idsel='accountancy';

        $chaine=$langs->trans("MenuAccountancy");

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/accountancy/index.php?mainmenu=accountancy&amp;leftmenu=', $id, $idsel, $atarget);
		if (empty($noout)) print_end_menu_entry($showmode);
		$menu->add('/accountancy/index.php?mainmenu=accountancy&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "accountancy", '');
	}

	// HRM
	$tmpentry = array(
		'enabled'=>(isModEnabled('hrm') || isModEnabled('holiday') || isModEnabled('deplacement') || isModEnabled('expensereport') || isModEnabled('recruitment')),
		'perms'=>($user->hasRight('user', 'user', 'lire') || $user->hasRight('holiday', 'read') || $user->hasRight('deplacement', 'lire') || $user->hasRight('expensereport', 'lire') || $user->hasRight('recruitment', 'recruitmentjobposition', 'read')),
		'module'=>'hrm|holiday|deplacement|expensereport|recruitment'
	);

	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);

	if ($showmode) {
        $langs->loadLangs(array("holiday", "recruitment"));

        if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "hrm") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
        $idsel='hrm';

        $chaine=$langs->trans("HRM");

        if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
        if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/hrm/index.php?mainmenu=hrm&amp;leftmenu=', $id, $idsel, $atarget);
        if (empty($noout)) print_end_menu_entry($showmode);
        $menu->add('/hrm/index.php?mainmenu=hrm&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "hrm", '');
	}

	// Tickets and knowledge base
	$tmpentry = array(
		'enabled'=>(isModEnabled('ticket') || isModEnabled('knowledgemanagement')),
		'perms'=>($user->hasRight('ticket', 'read') || $user->hasRight('knowledgemanagement', 'knowledgerecord', 'read')),
		'module'=>'ticket|knowledgemanagement'
	);

	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);

	if ($showmode) {
		$langs->loadLangs(array("other", "ticket"));

		if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "ticket") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
		$idsel='ticket';

        $chaine=$langs->trans("Tickets");

        $link = '';
		if (isModEnabled('ticket')) {
			$link = '/ticket/index.php?mainmenu=ticket&amp;leftmenu=';
		} else {
			$link = '/knowledgemanagement/knowledgerecord_list.php?mainmenu=ticket&amp;leftmenu=';
		}

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.$link, $id, $idsel, $atarget);
		if (empty($noout)) print_end_menu_entry($showmode);
		$menu->add($link, $chaine, 0, $showmode, $atarget, "ticket", '');
	}

	// Show personalized menus
	$menuArbo = new Menubase($db,'oblyon');
	$newTabMenu = $menuArbo->menuTopCharger('','',$type_user,'oblyon',$tabMenu);	// Return tabMenu with only top entries

	$num = count($newTabMenu);
	for ($i = 0; $i < $num; $i++) {
		//var_dump($type_user.' '.$newTabMenu[$i]['url'].' '.$showmode.' '.$newTabMenu[$i]['perms']);
		$idsel = (empty($newTabMenu[$i]['mainmenu']) ? 'none' : $newTabMenu[$i]['mainmenu']);

		$showmode=dol_oblyon_showmenu($type_user,$newTabMenu[$i],$listofmodulesforexternal);
		if ($showmode == 1) {
			$url = $shorturl = $newTabMenu[$i]['url'];
			if (! preg_match("/^(http:\/\/|https:\/\/)/i",$url))
			{
				$param='';
				if (! preg_match('/mainmenu/i',$url) || ! preg_match('/leftmenu/i',$url)) {
				if (! preg_match('/\?/',$url)) $param.='?';
				else $param.='&';
				$param.='mainmenu='.$newTabMenu[$i]['mainmenu'].'&amp;leftmenu=';
				}
				//$url.="idmenu=".$newTabMenu[$i]['rowid'];	// Already done by menuLoad
				$url = dol_buildpath($url,1).$param;
				$shorturl = $newTabMenu[$i]['url'].$param;
			}
			$url=preg_replace('/__LOGIN__/',$user->login,$url);
			$shorturl=preg_replace('/__LOGIN__/',$user->login,$shorturl);
			$url=preg_replace('/__USERID__/',$user->id,$url);
			$shorturl=preg_replace('/__USERID__/',$user->id,$shorturl);

			// Define the class (top menu selected or not)
			if (! empty($_SESSION['idmenu']) && $newTabMenu[$i]['rowid'] == $_SESSION['idmenu']) $itemsel=TRUE;
			else if (! empty($_SESSION["mainmenu"]) && $newTabMenu[$i]['mainmenu'] == $_SESSION["mainmenu"]) $itemsel=TRUE;
			else $itemsel=FALSE;
		}
		else if ($showmode == 2) $itemsel=FALSE;

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($newTabMenu[$i]['titre'], $showmode, $url, $id, $idsel, ($newTabMenu[$i]['target']?$newTabMenu[$i]['target']:$atarget));
		if (empty($noout)) print_end_menu_entry($showmode);
		$menu->add(
			$shorturl,
			$newTabMenu[$i]['titre'],
			0, $showmode,
			$newTabMenu[$i]['target'] ? $newTabMenu[$i]['target'] : $atarget,
			$newTabMenu[$i]['mainmenu'] ? $newTabMenu[$i]['mainmenu'] : $newTabMenu[$i]['rowid'],
			''
		);
	}

    // Show menu tools in last position
    // Tools
    $tmpentry = array(
        'enabled'=>1,
        'perms'=>1,
        'module'=>''
    );
    $showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);

    if ($showmode) {
        $langs->loadLangs(array("other"));

        if (!empty($_SESSION["mainmenu"]) && $_SESSION["mainmenu"] == "tools") {
            $itemsel=TRUE;
            $_SESSION['idmenu']='';
        } else {
            $itemsel = FALSE;
        }
        $idsel='tools';

        $chaine=$langs->trans("Tools");

        if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
        if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/core/tools.php?mainmenu=tools&amp;leftmenu=', $id, $idsel, $atarget);
        if (empty($noout)) print_end_menu_entry($showmode);
        $menu->add('/core/tools.php?mainmenu=tools&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "tools", '');
    }

	if (empty($noout)) print_end_menu_array();

	return 0;
}


/**
 * Output start menu array
 *
 * @return	void
 */
function print_start_menu_array() {
	global $conf;

    $menu_invert = getDolGlobalInt('MAIN_MENU_INVERT');

    print '<nav class="tmenudiv db-nav main-nav'.(empty($menu_invert)?'':' is-inverted').'">';
	print '<ul role="navigation" class="tmenu main-nav__list">';
}

/**
 * Output start menu entry
 *
 * @param	string	$idsel		Text
 * @param	bool	$itemsel	Item Selected = TRUE
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @return	void
 */
function print_start_menu_entry($idsel,$itemsel,$showmode) {
	if ($showmode) {
		print '<li class="tmenu main-nav__item'.(($itemsel)?' tmenusel is-sel':'').'" id="mainmenutd_'.$idsel.'">';
	}
}

/**
 * Output menu entry
 *
 * @param	string	$text		Text
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @param	string	$url		Url
 * @param	string	$id			Id
 * @param	string	$idsel		Id sel
 * @param	string	$atarget	Target
 * @return	void
 */
function print_text_menu_entry($text, $showmode, $url, $id, $idsel, $atarget)
{
	global $langs;
	global $conf;

	if ($showmode == 1)
	{
        print '<div>'; // for myfield offset and uses div for background color
		print '<a class="tmenu main-nav__link main-nav__'.$idsel.'" href="'.$url.'"'.($atarget?' target="'.$atarget.'"':'').' title="'.dol_escape_htmltag($text).'">';
		print '<i class="tmenuimage icon icon--'.$idsel.'"></i> ';
        print '<span class="mainmenuaspan">'.$text.'</span>'; // for myfield label and link
		print '</a>';
        print '</div>';
	}
	if ($showmode == 2)
	{
        print '<div>'; // for myfield offset and uses div for background color
		print '<a class="tmenu main-nav__link is-disabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
		print '<i class="tmenuimage icon icon--'.$idsel.'"></i> ';
        print '<span class="mainmenuaspan">'.$text.'</span>'; // for myfield label and link
		print '</a>';
        print '</div>';
	}
}

/**
 * Output end menu entry
 *
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @return	void
 */
function print_end_menu_entry($showmode)
{
	if ($showmode)
	{
		print '</li>';
	}
	print "\n";
}

/**
 * Output menu array
 *
 * @return	void
 */
function print_end_menu_array() {
	print '</ul>';
	print '</nav>';
	print "\n";
}



/**
 * Core function to output left menu oblyon
 *
 * @param	DoliDB		$db                 Database handler
 * @param 	array		$menu_array_before  Table of menu entries to show before entries of menu handler (menu->liste filled with menu->add)
 * @param   array		$menu_array_after   Table of menu entries to show after entries of menu handler (menu->liste filled with menu->add)
 * @param	array		$tabMenu       		If array with menu entries already loaded, we put this array here (in most cases, it's empty)
 * @param	Menu		$menu				Object Menu to return back list of menu entries
 * @param	int			$noout				Disable output (Initialise &$menu only).
 * @param	string		$forcemainmenu		'x'=Force mainmenu to mainmenu='x'
 * @param	string		$forceleftmenu		'all'=Force leftmenu to '' (= all). If value come being '', we change it to value in session and 'none' if not defined in session.
 * @param	array		$moredata			An array with more data to output
 * @param 	int			$type_user     		0=Menu for backoffice, 1=Menu for front office
 * @return	int								Nb of menu entries
 */
function print_left_oblyon_menu($db, $menu_array_before, $menu_array_after, &$tabMenu, &$menu, $noout = 0, $forcemainmenu = '', $forceleftmenu = '', $moredata = null, $type_user = 0)
{
	global $user, $conf, $langs, $dolibarr_main_db_name, $mysoc, $hookmanager;

	//var_dump($tabMenu);

	$newmenu = $menu;

	$mainmenu = ($forcemainmenu ? $forcemainmenu : $_SESSION["mainmenu"]);
	$leftmenu = ($forceleftmenu ? '' : (empty($_SESSION["leftmenu"]) ? 'none' : $_SESSION["leftmenu"]));

    $menu_invert = getDolGlobalInt('MAIN_MENU_INVERT');

	$usemenuhider = !empty($menu_invert) && (getDolGlobalInt('OBLYON_HIDE_LEFTMENU') || $conf->dol_optimize_smallscreen);

	if ( $usemenuhider ) {
		print '<div class="pushy-btn" title="'.$langs->trans("ShowLeftMenu").'">&#8801;</div>';
	}

	// get landing page
	$landingpage = (empty($user->conf->MAIN_LANDING_PAGE) ? getDolGlobalString('MAIN_LANDING_PAGE', '') : $user->conf->MAIN_LANDING_PAGE);
	if (! empty($landingpage)) {
		$landingpage = str_replace(DOL_URL_ROOT, '', dol_buildpath($landingpage, 1));
	} else {
		$landingpage = '/index.php?mainmenu=home&amp;leftmenu=home';
	}

	// Show logo company
	if (! empty($menu_invert) && empty($noout) && getDolGlobalString('MAIN_SHOW_LOGO')) {
        $mysoc->logo_squarred_mini = getDolGlobalString('MAIN_INFO_SOCIETE_LOGO_SQUARRED_MINI', '');

        if (! empty($mysoc->logo_squarred_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_squarred_mini))
        {
            $urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/thumbs/'.$mysoc->logo_squarred_mini);
        }
        else
        {
            $urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
            $logoContainerAdditionalClass = '';
        }
        $title=$langs->trans("GoIntoSetupToChangeLogo");

		print "\n".'<!-- Show logo on menu -->'."\n";

        print '<div class="menu_contenu db-menu__society center backgroundforcompanylogo menulogocontainer">';
        print '<a href="' . DOL_URL_ROOT . $landingpage . '" alt="'.dol_escape_htmltag($title).'" title="'.dol_escape_htmltag($title).'">';
        print '<img class="mycompany" title="'.dol_escape_htmltag($title).'" alt="" src="'.$urllogo.'" style="max-width: 100px; height: 32px;">';
        print '</a>'."\n";
        print '</div>'."\n";
	}

    if (getDolGlobalInt('OBLYON_SHOW_COMPNAME') && getDolGlobalString('MAIN_INFO_SOCIETE_NOM')) {
        if (! $menu_invert) {
            print '<div class="blockvmenusocietyname">'."\n";
            print '<span>'. getDolGlobalString('MAIN_INFO_SOCIETE_NOM') .'</span>'."\n";
            print '</div>'."\n";
        }
    }

	if (is_array($moredata) && !empty($moredata['searchform'])) {	// searchform can contains select2 code or link to show old search form or link to switch on search page
		print "\n";
		print "<!-- Begin SearchForm -->\n";
		print '<div id="blockvmenusearch" class="blockvmenusearch">'."\n";
		print $moredata['searchform'];
		print '</div>'."\n";
		print "<!-- End SearchForm -->\n";
	}

	if (is_array($moredata) && !empty($moredata['bookmarks'])) {
		print "\n";
		print "<!-- Begin Bookmarks -->\n";
		print '<div id="blockvmenubookmarks" class="blockvmenubookmarks">'."\n";
		print $moredata['bookmarks'];
		print '</div>'."\n";
		print "<!-- End Bookmarks -->\n";
	}

	$substitarray = getCommonSubstitutionArray($langs, 0, null, null);

	$listofmodulesforexternal = explode(',', getDolGlobalString('MAIN_MODULES_FOR_EXTERNAL'));

	/**
	 * We update newmenu with entries found into database
	 * --------------------------------------------------
	 */
	if ($mainmenu) {	// If this is empty, loading hard coded menu and loading personalised menu will fail
		/*
		 * Menu HOME
		 */
		if ($mainmenu == 'home') {
			$langs->load("users");

			// Home - dashboard
			$newmenu->add($landingpage, $langs->trans("MyDashboard"), 0, 1, '', $mainmenu, 'home');

			// Setup
			$newmenu->add("/admin/index.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Setup"), 0, $user->admin, '', $mainmenu, 'setup');

            if (! empty($menu_invert)) $leftmenu= 'setup';

			if (!empty($user->admin) && ($usemenuhider || empty($leftmenu) || $leftmenu == "setup")) {
				// Load translation files required by the page
				$langs->loadLangs(array("admin", "help"));

				$warnpicto = '';
				if (!getDolGlobalString('MAIN_INFO_SOCIETE_NOM') || !getDolGlobalString('MAIN_INFO_SOCIETE_COUNTRY')) {
					$langs->load("errors");
					$warnpicto = img_warning($langs->trans("WarningMandatorySetupNotComplete"));
				}
				$newmenu->add("/admin/company.php?mainmenu=home", $langs->trans("MenuCompanySetup").$warnpicto, 1);

				$warnpicto = '';
				if (count($conf->modules) <= getDolGlobalInt('MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING', 1)) {	// If only user module enabled
					$langs->load("errors");
					$warnpicto = img_warning($langs->trans("WarningMandatorySetupNotComplete"));
				}
				$newmenu->add("/admin/modules.php?mainmenu=home", $langs->trans("Modules").$warnpicto, 1);
				$newmenu->add("/admin/ihm.php?mainmenu=home", $langs->trans("GUISetup"), 1);
				$newmenu->add("/admin/menus.php?mainmenu=home", $langs->trans("Menus"), 1);

				$newmenu->add("/admin/translation.php?mainmenu=home", $langs->trans("Translation"), 1);
				$newmenu->add("/admin/defaultvalues.php?mainmenu=home", $langs->trans("DefaultValues"), 1);
				$newmenu->add("/admin/boxes.php?mainmenu=home", $langs->trans("Boxes"), 1);
				$newmenu->add("/admin/delais.php?mainmenu=home", $langs->trans("MenuWarnings"), 1);
				$newmenu->add("/admin/security_other.php?mainmenu=home", $langs->trans("Security"), 1);
				$newmenu->add("/admin/limits.php?mainmenu=home", $langs->trans("MenuLimits"), 1);
				$newmenu->add("/admin/pdf.php?mainmenu=home", $langs->trans("PDF"), 1);

				$warnpicto = '';
				if (getDolGlobalString('MAIN_MAIL_SENDMODE') && in_array(getDolGlobalString('MAIN_MAIL_SENDMODE'), array('smtps', 'swiftmail')) && !getDolGlobalString('MAIN_MAIL_SMTP_SERVER')) {
					$langs->load("errors");
					$warnpicto = img_warning($langs->trans("ErrorSetupOfEmailsNotComplete"));
				}

				$newmenu->add("/admin/mails.php?mainmenu=home", $langs->trans("Emails").$warnpicto, 1);
				$newmenu->add("/admin/sms.php?mainmenu=home", $langs->trans("SMS"), 1);
				$newmenu->add("/admin/dict.php?mainmenu=home", $langs->trans("Dictionary"), 1);
				$newmenu->add("/admin/const.php?mainmenu=home", $langs->trans("OtherSetup"), 1);
			}

			// System tools
			$newmenu->add("/admin/tools/index.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("AdminTools"), 0, $user->admin, '', $mainmenu, 'admintools');

            if (! empty($menu_invert)) $leftmenu= 'admintools';

			if (!empty($user->admin) && ($usemenuhider || empty($leftmenu) || preg_match('/^admintools/', $leftmenu))) {
				// Load translation files required by the page
				$langs->loadLangs(array('admin', 'help', 'cron'));

				$newmenu->add('/admin/system/dolibarr.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('InfoDolibarr'), 1);

                if (! empty($menu_invert)) $leftmenu= 'admintools_info';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == 'admintools_info') {
					$newmenu->add('/admin/system/modules.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('Modules'), 2);
					$newmenu->add('/admin/triggers.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('Triggers'), 2);
					$newmenu->add('/admin/system/filecheck.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('FileCheck'), 2);
				}
				$newmenu->add('/admin/system/browser.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoBrowser'), 1);
				$newmenu->add('/admin/system/os.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoOS'), 1);
				$newmenu->add('/admin/system/web.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoWebServer'), 1);
				$newmenu->add('/admin/system/phpinfo.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoPHP'), 1);
				$newmenu->add('/admin/system/database.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoDatabase'), 1);
				$newmenu->add("/admin/system/perf.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("InfoPerf"), 1);
				$newmenu->add("/admin/system/security.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("InfoSecurity"), 1);
				$newmenu->add("/admin/tools/dolibarr_export.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Backup"), 1);
				$newmenu->add("/admin/tools/dolibarr_import.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Restore"), 1);
				$newmenu->add("/admin/tools/update.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("MenuUpgrade"), 1);
				$newmenu->add("/admin/tools/purge.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Purge"), 1);
				$newmenu->add("/admin/tools/listevents.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Audit"), 1);
				$newmenu->add("/admin/tools/listsessions.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Sessions"), 1);
				$newmenu->add('/admin/system/about.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('ExternalResources'), 1);
                $newmenu->add('/cron/list.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('CronList'), 1);

				if (isModEnabled('product') || isModEnabled('service')) {
					$langs->load("products");
					$newmenu->add("/product/admin/product_tools.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("ProductVatMassChange"), 1, $user->admin);
				}
			}

			// Users & Groups
			$newmenu->add("/user/home.php?leftmenu=users", $langs->trans("MenuUsersAndGroups"), 0, $user->hasRight('user', 'user', 'lire'), '', $mainmenu, 'users', 0, '', '', '', img_picto('', 'user', 'class="paddingright pictofixedwidth"'));
			if ($user->hasRight('user', 'user', 'lire')) {
				if (! empty($menu_invert)) $leftmenu= 'users';

			    if ($usemenuhider || empty($leftmenu) || $leftmenu=="users")
				{
					$newmenu->add("/user/list.php?leftmenu=users", $langs->trans("Users"), 1, $user->hasRight('user', 'user', 'lire') || $user->admin);
					$newmenu->add("/user/card.php?leftmenu=users&action=create", $langs->trans("NewUser"), 2, ($user->hasRight('user', 'user', 'creer') || $user->admin) && !(!empty($conf->multicompany->enabled) && !empty($user->entity) && getDolGlobalString('MULTICOMPANY_TRANSVERSE_MODE')), '', 'home');
					$newmenu->add("/user/list.php?leftmenu=users", $langs->trans("ListOfUsers"), 2, $user->hasRight('user', 'user', 'lire') || $user->admin);
					$newmenu->add("/user/hierarchy.php?leftmenu=users", $langs->trans("HierarchicView"), 2, $user->hasRight('user', 'user', 'lire') || $user->admin);
					if (isModEnabled('categorie')) {
						$langs->load("categories");
						$newmenu->add("/categories/index.php?leftmenu=users&type=7", $langs->trans("UsersCategoriesShort"), 2, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
					}
					$newmenu->add("/user/group/list.php?leftmenu=users", $langs->trans("Groups"), 1, ($user->hasRight('user', 'user', 'lire') || $user->admin) && !(!empty($conf->multicompany->enabled) && !empty($user->entity) && getDolGlobalString('MULTICOMPANY_TRANSVERSE_MODE')));
					$newmenu->add("/user/group/card.php?leftmenu=users&action=create", $langs->trans("NewGroup"), 2, ((getDolGlobalString('MAIN_USE_ADVANCED_PERMS') ? $user->hasRight('user', 'group_advance', 'write') : $user->hasRight('user', 'user', 'creer')) || $user->admin) && !(!empty($conf->multicompany->enabled) && !empty($user->entity) && getDolGlobalString('MULTICOMPANY_TRANSVERSE_MODE')));
					$newmenu->add("/user/group/list.php?leftmenu=users", $langs->trans("ListOfGroups"), 2, ((getDolGlobalString('MAIN_USE_ADVANCED_PERMS') ? $user->hasRight('user', 'group_advance', 'read'): $user->hasRight('user', 'user', 'lire')) || $user->admin));
				}
			}
		}


		/*
		 * Menu THIRDPARTIES
		 */
		if ($mainmenu == 'companies') {
			// Societes
			if (isModEnabled('societe')) {
				$langs->load("companies");
				$newmenu->add("/societe/index.php?leftmenu=thirdparties", $langs->trans("ThirdParty"), 0, $user->hasRight('societe', 'lire'), '', $mainmenu, 'thirdparties');

				if ($user->hasRight('societe', 'creer')) {
					$newmenu->add("/societe/card.php?action=create", $langs->trans("MenuNewThirdParty"), 1);
					if (!$conf->use_javascript_ajax) {
						$newmenu->add("/societe/card.php?action=create&amp;private=1", $langs->trans("MenuNewPrivateIndividual"), 1);
					}
				}
			}

			$newmenu->add("/societe/list.php?leftmenu=thirdparties", $langs->trans("List"), 1, $user->hasRight('societe', 'lire'), '', $mainmenu, 'thirdparties_list', 2);

			// Prospects
			if (isModEnabled('societe') && !getDolGlobalString('SOCIETE_DISABLE_PROSPECTS')) {
				$langs->load("commercial");
				$newmenu->add("/societe/list.php?type=p&amp;leftmenu=prospects", $langs->trans("Prospects"), 2, $user->hasRight('societe', 'lire'), '', $mainmenu, 'prospects');
				$newmenu->add("/societe/card.php?leftmenu=prospects&amp;action=create&amp;type=p", $langs->trans("MenuNewProspect"), 3, $user->hasRight('societe', 'creer'));
			}

			// Customers/Prospects
			if (isModEnabled('societe') && !getDolGlobalString('SOCIETE_DISABLE_CUSTOMERS')) {
				$langs->load("commercial");
				$newmenu->add("/societe/list.php?type=c&amp;leftmenu=customers", $langs->trans("Customers"), 2, $user->hasRight('societe', 'lire'), '', $mainmenu, 'customers');

				$newmenu->add("/societe/card.php?leftmenu=customers&amp;action=create&amp;type=c", $langs->trans("MenuNewCustomer"), 3, $user->hasRight('societe', 'creer'));
			}

			// Suppliers
			if (isModEnabled('societe') && (((isModEnabled('fournisseur') && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD')) || isModEnabled('supplier_order') || isModEnabled('supplier_invoice')) || isModEnabled('supplier_proposal'))) {
				$langs->load("suppliers");
				$newmenu->add("/societe/list.php?type=f&amp;leftmenu=suppliers", $langs->trans("Suppliers"), 2, ($user->hasRight('fournisseur', 'lire') || $user->hasRight('supplier_order', 'lire') || $user->hasRight('supplier_invoice', 'lire') || $user->hasRight('supplier_proposal', 'lire')), '', $mainmenu, 'suppliers');
				$newmenu->add("/societe/card.php?leftmenu=suppliers&amp;action=create&amp;type=f", $langs->trans("MenuNewSupplier"), 3, $user->hasRight('societe', 'creer') && ($user->hasRight('fournisseur', 'lire') || $user->hasRight('supplier_order', 'lire') || $user->hasRight('supplier_invoice', 'lire') || $user->hasRight('supplier_proposal', 'lire')));
			}

			// Categories
			if (isModEnabled('categorie')) {
				$langs->load("categories");
				if (!getDolGlobalString('SOCIETE_DISABLE_PROSPECTS') || !getDolGlobalString('SOCIETE_DISABLE_CUSTOMERS')) {
					// Categories prospects/customers
					$menutoshow = $langs->trans("CustomersProspectsCategoriesShort");
					if (getDolGlobalString('SOCIETE_DISABLE_PROSPECTS')) {
						$menutoshow = $langs->trans("CustomersCategoriesShort");
					}
					if (getDolGlobalString('SOCIETE_DISABLE_CUSTOMERS')) {
						$menutoshow = $langs->trans("ProspectsCategoriesShort");
					}
					$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=2", $menutoshow, 1, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
				}
				// Categories suppliers
				if (isModEnabled('fournisseur') && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD') || isModEnabled('supplier_order') || isModEnabled('supplier_invoice')) {
					$newmenu->add("/categories/index.php?leftmenu=catfournish&amp;type=1", $langs->trans("SuppliersCategoriesShort"), 1, $user->hasRight('categorie', 'lire'));
				}
			}

			// Contacts
			$newmenu->add("/societe/index.php?leftmenu=thirdparties", (getDolGlobalString('SOCIETE_ADDRESSES_MANAGEMENT') ? $langs->trans("Contacts") : $langs->trans("ContactsAddresses")), 0, $user->hasRight('societe', 'contact', 'lire'), '', $mainmenu, 'contacts');

			$newmenu->add("/contact/card.php?leftmenu=contacts&amp;action=create", (getDolGlobalString('SOCIETE_ADDRESSES_MANAGEMENT') ? $langs->trans("NewContact") : $langs->trans("NewContactAddress")), 1, $user->hasRight('societe', 'contact', 'creer'));
			$newmenu->add("/contact/list.php?leftmenu=contacts", $langs->trans("List"), 1, $user->hasRight('societe', 'contact', 'lire'));
			if (!getDolGlobalString('SOCIETE_DISABLE_PROSPECTS')) {
				$newmenu->add("/contact/list.php?leftmenu=contacts&type=p", $langs->trans("Prospects"), 2, $user->hasRight('societe', 'contact', 'lire'));
			}
			if (!getDolGlobalString('SOCIETE_DISABLE_CUSTOMERS')) {
				$newmenu->add("/contact/list.php?leftmenu=contacts&type=c", $langs->trans("Customers"), 2, $user->hasRight('societe', 'contact', 'lire'));
			}
			if (isModEnabled('fournisseur') && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD') || isModEnabled('supplier_order') || isModEnabled('supplier_invoice')) {
				$newmenu->add("/contact/list.php?leftmenu=contacts&type=f", $langs->trans("Suppliers"), 2, $user->hasRight('societe', 'contact', 'lire'));
			}
			$newmenu->add("/contact/list.php?leftmenu=contacts&type=o", $langs->trans("ContactOthers"), 2, $user->hasRight('societe', 'contact', 'lire'));
			//$newmenu->add("/contact/list.php?userid=$user->id", $langs->trans("MyContacts"), 1, $user->hasRight('societe', 'contact', 'lire'));

			// Categories
			if (isModEnabled('categorie')) {
				$langs->load("categories");
				// Categories Contact
				$newmenu->add("/categories/index.php?leftmenu=catcontact&amp;type=4", $langs->trans("ContactCategoriesShort"), 1, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
			}
		}

		/*
		 * Menu COMMERCIAL (propal, commande, supplier_proposal, supplier_order, contrat, ficheinter)
		 */
		if ($mainmenu == 'commercial') {
			$langs->load("companies");

			// Customer proposal
			if (isModEnabled('propal')) {
				$langs->load("propal");
				$newmenu->add("/comm/propal/index.php?leftmenu=propals", $langs->trans("Proposals"), 0, $user->hasRight('propale', 'lire'), '', $mainmenu, 'propals', 100, '', '', '', img_picto('', 'propal', 'class="paddingright pictofixedwidth"'));
				$newmenu->add("/comm/propal/card.php?action=create&amp;leftmenu=propals", $langs->trans("NewPropal"), 1, $user->hasRight('propale', 'creer'));
				$newmenu->add("/comm/propal/list.php?leftmenu=propals", $langs->trans("List"), 1, $user->hasRight('propale', 'lire'));

                if (! empty($menu_invert)) $leftmenu= 'propals';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "propals") {
					$newmenu->add("/comm/propal/list.php?leftmenu=propals&search_status=0", $langs->trans("PropalsDraft"), 2, $user->hasRight('propale', 'lire'));
					$newmenu->add("/comm/propal/list.php?leftmenu=propals&search_status=1", $langs->trans("PropalsOpened"), 2, $user->hasRight('propale', 'lire'));
					$newmenu->add("/comm/propal/list.php?leftmenu=propals&search_status=2", $langs->trans("PropalStatusSigned"), 2, $user->hasRight('propale', 'lire'));
					$newmenu->add("/comm/propal/list.php?leftmenu=propals&search_status=3", $langs->trans("PropalStatusNotSigned"), 2, $user->hasRight('propale', 'lire'));
					$newmenu->add("/comm/propal/list.php?leftmenu=propals&search_status=4", $langs->trans("PropalStatusBilled"), 2, $user->hasRight('propale', 'lire'));
					//$newmenu->add("/comm/propal/list.php?leftmenu=propals&search_status=2,3,4", $langs->trans("PropalStatusClosedShort"), 2, $user->hasRight('propale', 'lire'));
				}
				$newmenu->add("/comm/propal/stats/index.php?leftmenu=propals", $langs->trans("Statistics"), 1, $user->hasRight('propale', 'lire'));
			}

			// Customers orders
			if (isModEnabled('commande'))
			{
				$langs->load("orders");
				$newmenu->add("/commande/index.php?leftmenu=orders", $langs->trans("CustomersOrders"), 0, $user->hasRight('commande', 'lire'), '', $mainmenu, 'orders', 200);
				$newmenu->add("/commande/card.php?action=create&amp;leftmenu=orders", $langs->trans("NewOrder"), 1, $user->hasRight('commande', 'creer'));
				$newmenu->add("/commande/list.php?leftmenu=orders", $langs->trans("List"), 1, $user->hasRight('commande', 'lire'));

                if (! empty($menu_invert)) $leftmenu= 'orders';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "orders") {
					$newmenu->add("/commande/list.php?leftmenu=orders&search_status=0", $langs->trans("StatusOrderDraftShort"), 2, $user->hasRight('commande', 'lire'));
					$newmenu->add("/commande/list.php?leftmenu=orders&search_status=1", $langs->trans("StatusOrderValidated"), 2, $user->hasRight('commande', 'lire'));
					if (isModEnabled('expedition')) {
						$newmenu->add("/commande/list.php?leftmenu=orders&search_status=2", $langs->trans("StatusOrderSentShort"), 2, $user->hasRight('commande', 'lire'));
					}
					$newmenu->add("/commande/list.php?leftmenu=orders&search_status=3", $langs->trans("StatusOrderDelivered"), 2, $user->hasRight('commande', 'lire'));
					//$newmenu->add("/commande/list.php?leftmenu=orders&search_status=4", $langs->trans("StatusOrderProcessed"), 2, $user->hasRight('commande', 'lire'));
					$newmenu->add("/commande/list.php?leftmenu=orders&search_status=-1", $langs->trans("StatusOrderCanceledShort"), 2, $user->hasRight('commande', 'lire'));
				}
				$newmenu->add("/commande/stats/index.php?leftmenu=orders", $langs->trans("Statistics"), 1, $user->hasRight('commande', 'lire'));
			}

			// Supplier proposal
			if (isModEnabled('supplier_proposal')) {
				$langs->load("supplier_proposal");
				$newmenu->add("/supplier_proposal/index.php?leftmenu=propals_supplier", $langs->trans("SupplierProposalsShort"), 0, $user->hasRight('supplier_proposal', 'lire'), '', $mainmenu, 'propals_supplier', 300);
				$newmenu->add("/supplier_proposal/card.php?action=create&amp;leftmenu=supplier_proposals", $langs->trans("SupplierProposalNew"), 1, $user->hasRight('supplier_proposal', 'creer'));
				$newmenu->add("/supplier_proposal/list.php?leftmenu=supplier_proposals", $langs->trans("List"), 1, $user->hasRight('supplier_proposal', 'lire'));
				$newmenu->add("/comm/propal/stats/index.php?leftmenu=supplier_proposals&amp;mode=supplier", $langs->trans("Statistics"), 1, $user->hasRight('supplier_proposal', 'lire'));
			}

			// Suppliers orders
			if (isModEnabled('supplier_order')) {
				$langs->load("orders");
				$newmenu->add("/fourn/commande/index.php?leftmenu=orders_suppliers", $langs->trans("SuppliersOrders"), 0, $user->hasRight('fournisseur', 'commande', 'lire'), '', $mainmenu, 'orders_suppliers', 400);
				$newmenu->add("/fourn/commande/card.php?action=create&amp;leftmenu=orders_suppliers", $langs->trans("NewSupplierOrderShort"), 1, $user->hasRight('fournisseur', 'commande', 'creer'));
				$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers", $langs->trans("List"), 1, $user->hasRight('fournisseur', 'commande', 'lire'));

                if (! empty($menu_invert)) $leftmenu= 'orders_suppliers';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "orders_suppliers") {
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=0", $langs->trans("StatusSupplierOrderDraftShort"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
					if (!getDolGlobalString('SUPPLIER_ORDER_HIDE_VALIDATED')) {
						$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=1", $langs->trans("StatusSupplierOrderValidated"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
					}
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=2", $langs->trans("StatusSupplierOrderApprovedShort"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=3", $langs->trans("StatusSupplierOrderOnProcessShort"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=4", $langs->trans("StatusSupplierOrderReceivedPartiallyShort"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=5", $langs->trans("StatusSupplierOrderReceivedAll"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=6,7", $langs->trans("StatusSupplierOrderCanceled"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=9", $langs->trans("StatusSupplierOrderRefused"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));
				}
				// Billed is another field. We should add instead a dedicated filter on list. if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders_suppliers") $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&billed=1", $langs->trans("Billed"), 2, $user->hasRight('fournisseur', 'commande', 'lire'));


				$newmenu->add("/commande/stats/index.php?leftmenu=orders_suppliers&amp;mode=supplier", $langs->trans("Statistics"), 1, $user->hasRight('fournisseur', 'commande', 'lire'));
			}

			// Contract
			if (isModEnabled('contrat')) {
				$langs->load("contracts");
				$newmenu->add("/contrat/index.php?leftmenu=contracts", $langs->trans("ContractsSubscriptions"), 0, $user->hasRight('contrat', 'lire'), '', $mainmenu, 'contracts', 2000);
				$newmenu->add("/contrat/card.php?action=create&amp;leftmenu=contracts", $langs->trans("NewContractSubscription"), 1, $user->hasRight('contrat', 'creer'));
				$newmenu->add("/contrat/list.php?leftmenu=contracts", $langs->trans("List"), 1, $user->hasRight('contrat', 'lire'));
				$newmenu->add("/contrat/services_list.php?leftmenu=contracts", $langs->trans("MenuServices"), 1, $user->hasRight('contrat', 'lire'));

				if (!empty($menu_invert)) {
                    $leftmenu = 'contracts';
                }

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "contracts") {
                    if ((float) DOL_VERSION >= 18.0) {
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;search_status=0", $langs->trans("MenuInactiveServices"), 2, $user->hasRight('contrat', 'lire'));
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;search_status=4", $langs->trans("MenuRunningServices"), 2, $user->hasRight('contrat', 'lire'));
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;search_status=4%26filter=expired", $langs->trans("MenuExpiredServices"), 2, $user->hasRight('contrat', 'lire'));
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;search_status=5", $langs->trans("MenuClosedServices"), 2, $user->hasRight('contrat', 'lire'));
                    } else {
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=0", $langs->trans("MenuInactiveServices"), 2, $user->hasRight('contrat', 'lire'));
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=4", $langs->trans("MenuRunningServices"), 2, $user->hasRight('contrat', 'lire'));
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=4&amp;filter=expired", $langs->trans("MenuExpiredServices"), 2, $user->hasRight('contrat', 'lire'));
                        $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=5", $langs->trans("MenuClosedServices"), 2, $user->hasRight('contrat', 'lire'));
                    }
                }
			}

			// Interventions
			if (isModEnabled('ficheinter')) {
				$langs->load("interventions");
				$newmenu->add("/fichinter/index.php?leftmenu=ficheinter", $langs->trans("Interventions"), 0, $user->hasRight('ficheinter', 'lire'), '', $mainmenu, 'ficheinter', 2200);
				$newmenu->add("/fichinter/card.php?action=create&amp;leftmenu=ficheinter", $langs->trans("NewIntervention"), 1, $user->hasRight('ficheinter', 'creer'), '', '', '', 201);
				$newmenu->add("/fichinter/list.php?leftmenu=ficheinter", $langs->trans("List"), 1, $user->hasRight('ficheinter', 'lire'), '', '', '', 202);
				if (getDolGlobalString('MAIN_FEATURES_LEVEL') >= 2) {
					$newmenu->add("/fichinter/card-rec.php?leftmenu=ficheinter", $langs->trans("ListOfTemplates"), 1, $user->hasRight('ficheinter', 'lire'), '', '', '', 203);
				}
				$newmenu->add("/fichinter/stats/index.php?leftmenu=ficheinter", $langs->trans("Statistics"), 1, $user->hasRight('ficheinter', 'lire'));
			}
		}


		/*
		 * Menu COMPTA-FINANCIAL
		 */
		if ($mainmenu == 'billing') {
			$langs->load("companies");

			// Customers invoices
			if (isModEnabled('facture')) {
				$langs->load("bills");
				$newmenu->add("/compta/facture/index.php?leftmenu=customers_bills", $langs->trans("BillsCustomers"), 0, $user->hasRight('facture', 'lire'), '', $mainmenu, 'customers_bills', 0);
				$newmenu->add("/compta/facture/card.php?action=create", $langs->trans("NewBill"), 1, $user->hasRight('facture', 'creer'));
				$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills", $langs->trans("List"), 1, $user->hasRight('facture', 'lire'), '', $mainmenu, 'customers_bills_list');

                if (! empty($menu_invert)) $leftmenu= 'customers_bills';

				if ($usemenuhider || empty($leftmenu) || preg_match('/customers_bills(|_draft|_notpaid|_paid|_canceled)$/', $leftmenu)) {
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_draft&amp;search_status=0", $langs->trans("BillShortStatusDraft"), 2, $user->hasRight('facture', 'lire'));
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_notpaid&amp;search_status=1", $langs->trans("BillShortStatusNotPaid"), 2, $user->hasRight('facture', 'lire'));
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_paid&amp;search_status=2", $langs->trans("BillShortStatusPaid"), 2, $user->hasRight('facture', 'lire'));
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_canceled&amp;search_status=3", $langs->trans("BillShortStatusCanceled"), 2, $user->hasRight('facture', 'lire'));
				}
				$newmenu->add("/compta/facture/invoicetemplate_list.php?leftmenu=customers_bills_templates", $langs->trans("ListOfTemplates"), 1, $user->hasRight('facture', 'creer'), '', $mainmenu, 'customers_bills_templates'); // No need to see recurring invoices, if user has no permission to create invoice.

				$newmenu->add("/compta/paiement/list.php?leftmenu=customers_bills_payment", $langs->trans("Payments"), 1, $user->hasRight('facture', 'lire'), '', $mainmenu, 'customers_bills_payment');

				if (getDolGlobalString('BILL_ADD_PAYMENT_VALIDATION')) {
					$newmenu->add("/compta/paiement/tovalidate.php?leftmenu=customers_bills_tovalid", $langs->trans("MenuToValid"), 2, $user->hasRight('facture', 'lire'), '', $mainmenu, 'customer_bills_tovalid');
				}
                if ($usemenuhider || empty($leftmenu) || preg_match('/customers_bills/', $leftmenu)) {
                    $newmenu->add("/compta/paiement/rapport.php?leftmenu=customers_bills_payment_report", $langs->trans("Reportings"), 2, $user->hasRight('facture', 'lire'), '', $mainmenu, 'customers_bills_payment_report');
                }
				$newmenu->add("/compta/facture/stats/index.php?leftmenu=customers_bills_stats", $langs->trans("Statistics"), 1, $user->hasRight('facture', 'lire'), '', $mainmenu, 'customers_bills_stats');
			}

			// Suppliers invoices
			if (isModEnabled('societe') && isModEnabled('supplier_invoice')) {
				$langs->load("bills");
				$newmenu->add("/fourn/facture/index.php?leftmenu=suppliers_bills", $langs->trans("BillsSuppliers"), 0, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills', 0);
				$newmenu->add("/fourn/facture/card.php?leftmenu=suppliers_bills&amp;action=create", $langs->trans("NewBill"), 1, ($user->hasRight('fournisseur', 'facture', 'creer') || $user->hasRight('supplier_invoice', 'creer')), '', $mainmenu, 'suppliers_bills_create');
				$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills", $langs->trans("List"), 1, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills_list');

                if (! empty($menu_invert)) $leftmenu= 'suppliers_bills';

				if ($usemenuhider || empty($leftmenu) || preg_match('/suppliers_bills/', $leftmenu)) {
					$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills_draft&amp;search_status=0", $langs->trans("BillShortStatusDraft"), 2, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills_draft');
					$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills_notpaid&amp;search_status=1", $langs->trans("BillShortStatusNotPaid"), 2, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills_notpaid');
					$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills_paid&amp;search_status=2", $langs->trans("BillShortStatusPaid"), 2, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills_paid');
				}

                if ((float) DOL_VERSION >= 16.0) {
                    $newmenu->add("/fourn/facture/list-rec.php?leftmenu=supplierinvoicestemplate_list", $langs->trans("ListOfTemplates"), 1, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'supplierinvoicestemplate_list');
                }

                $newmenu->add("/fourn/paiement/list.php?leftmenu=suppliers_bills_payment", $langs->trans("Payments"), 1, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills_payment');

                if ($usemenuhider || empty($leftmenu) || preg_match('/suppliers_bills/', $leftmenu)) {
                    $newmenu->add("/fourn/facture/rapport.php?leftmenu=suppliers_bills_payment_report", $langs->trans("Reportings"), 2, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills_payment_report');
                }

				$newmenu->add("/compta/facture/stats/index.php?mode=supplier&amp;leftmenu=suppliers_bills_stats", $langs->trans("Statistics"), 1, $user->hasRight('fournisseur', 'facture', 'lire'), '', $mainmenu, 'suppliers_bills_stats');
			}

			// Orders
			if (isModEnabled('commande')) {
				$langs->load("orders");
				if (isModEnabled('facture')) {
					$newmenu->add("/commande/list.php?leftmenu=orders&amp;search_status=-3&amp;search_billed=0&amp;contextpage=billableorders", $langs->trans("MenuOrdersToBill2"), 0, $user->hasRight('commande', 'lire'), '', $mainmenu, 'orders', 0);
				}
				//if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders") $newmenu->add("/commande/", $langs->trans("StatusOrderToBill"), 1, $user->hasRight('commande', 'lire'));
			}

			// Supplier Orders to bill
			if (isModEnabled('supplier_invoice')) {
				if (getDolGlobalString('SUPPLIER_MENU_ORDER_RECEIVED_INTO_INVOICE')) {
					$langs->load("supplier");
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders&amp;search_status=5&amp;search_billed=0", $langs->trans("MenuOrdersSupplierToBill"), 0, $user->hasRight('commande', 'lire'), '', $mainmenu, 'orders', 0);
					//if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders") $newmenu->add("/commande/", $langs->trans("StatusOrderToBill"), 1, $user->hasRight('commande', 'lire'));
				}
			}


			// Donations
			if (isModEnabled('don')) {
				$langs->load("donations");
				$newmenu->add("/don/index.php?leftmenu=donations&amp;mainmenu=billing",$langs->trans("Donations"), 0, $user->hasRight('don', 'lire'), '', $mainmenu, 'donations');

                if (! empty($menu_invert)) $leftmenu= 'donations';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "donations") {
					$newmenu->add("/don/card.php?leftmenu=donations&amp;action=create", $langs->trans("NewDonation"), 1, $user->hasRight('don', 'creer'));
					$newmenu->add("/don/list.php?leftmenu=donations", $langs->trans("List"), 1, $user->hasRight('don', 'lire'));
				}
				// if ($leftmenu=="donations") $newmenu->add("/don/stats/index.php",$langs->trans("Statistics"), 1, $user->hasRight('don', 'lire'));
			}

			// Taxes and social contributions
			if (isModEnabled('tax') || isModEnabled('salaries') || isModEnabled('loan') || isModEnabled('banque'))
			{
                global $mysoc;
                $langs->load("compta");

				$permtoshowmenu=((isModEnabled('tax') && $user->hasRight('tax', 'charges', 'lire')) || (isModEnabled('salaries') && $user->hasRight('salaries', 'read')) || (isModEnabled('loan') && $user->hasRight('loan', 'read')) || (isModEnabled('banque') && $user->hasRight('banque', 'lire')));
				$newmenu->add("/compta/charges/index.php?leftmenu=tax&amp;mainmenu=billing",$langs->trans("MenuSpecialExpenses"), 0, $permtoshowmenu, '', $mainmenu, 'tax');

				// Social contributions
				if (isModEnabled('tax'))
				{
					$newmenu->add("/compta/sociales/list.php?leftmenu=tax_social",$langs->trans("MenuSocialContributions"),1,$user->hasRight('tax', 'charges', 'lire'));

                    if (! empty($menu_invert)) $leftmenu= 'tax_social';

					if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_social/i', $leftmenu)) {
						$newmenu->add("/compta/sociales/card.php?leftmenu=tax_social&action=create", $langs->trans("MenuNewSocialContribution"), 2, $user->hasRight('tax', 'charges', 'creer'));
						$newmenu->add("/compta/sociales/list.php?leftmenu=tax_social", $langs->trans("List"), 2, $user->hasRight('tax', 'charges', 'lire'));
						$newmenu->add("/compta/sociales/payments.php?leftmenu=tax_social&amp;mainmenu=billing", $langs->trans("Payments"), 2, $user->hasRight('tax', 'charges', 'lire'));
					}

					// VAT
					if (!getDolGlobalString('TAX_DISABLE_VAT_MENUS'))
					{
						$newmenu->add("/compta/tva/list.php?leftmenu=tax_vat&amp;mainmenu=billing",$langs->transcountry("MenuVAT", $mysoc->country_code),1,$user->hasRight('tax', 'charges', 'lire'), '', $mainmenu, 'tax_vat');

                        if (! empty($menu_invert)) $leftmenu= 'tax_vat';

						if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_vat/i', $leftmenu)) {
							$newmenu->add("/compta/tva/card.php?leftmenu=tax_vat&action=create", $langs->trans("New"), 2, $user->hasRight('tax', 'charges', 'creer'));
							$newmenu->add("/compta/tva/list.php?leftmenu=tax_vat", $langs->trans("List"), 2, $user->hasRight('tax', 'charges', 'lire'));
							$newmenu->add("/compta/tva/payments.php?mode=tvaonly&amp;leftmenu=tax_vat", $langs->trans("Payments"), 2, $user->hasRight('tax', 'charges', 'lire'));
							$newmenu->add("/compta/tva/index.php?leftmenu=tax_vat", $langs->trans("ReportByMonth"), 2, $user->hasRight('tax', 'charges', 'lire'));
							$newmenu->add("/compta/tva/clients.php?leftmenu=tax_vat", $langs->trans("ReportByThirdparties"), 2, $user->hasRight('tax', 'charges', 'lire'));
							$newmenu->add("/compta/tva/quadri_detail.php?leftmenu=tax_vat", $langs->trans("ReportByQuarter"), 2, $user->hasRight('tax', 'charges', 'lire'));
						}

						global $mysoc;

						// Local Taxes 1
						if($mysoc->useLocalTax(1) && (isset($mysoc->localtax1_assuj) && $mysoc->localtax1_assuj=="1"))
						{
							$newmenu->add("/compta/localtax/list.php?leftmenu=tax_1_vat&amp;mainmenu=billing&amp;localTaxType=1",$langs->transcountry("LT1",$mysoc->country_code),1,$user->hasRight('tax', 'charges', 'lire'));

                            if (! empty($menu_invert)) $leftmenu= 'tax_1_vat';

							if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_1_vat/i', $leftmenu)) {
								$newmenu->add("/compta/localtax/card.php?leftmenu=tax_1_vat&action=create&amp;localTaxType=1", $langs->trans("New"), 2, $user->hasRight('tax', 'charges', 'creer'));
								$newmenu->add("/compta/localtax/list.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("List"), 2, $user->hasRight('tax', 'charges', 'lire'));
								$newmenu->add("/compta/localtax/index.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByMonth"), 2, $user->hasRight('tax', 'charges', 'lire'));
								$newmenu->add("/compta/localtax/clients.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByThirdparties"), 2, $user->hasRight('tax', 'charges', 'lire'));
								$newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByQuarter"), 2, $user->hasRight('tax', 'charges', 'lire'));
							}
						}

						// Local Taxes 2
						if($mysoc->useLocalTax(2) && (isset($mysoc->localtax2_assuj) && $mysoc->localtax2_assuj=="1"))
						{
							$newmenu->add("/compta/localtax/list.php?leftmenu=tax_2_vat&amp;mainmenu=billing&amp;localTaxType=2",$langs->transcountry("LT2",$mysoc->country_code),1,$user->hasRight('tax', 'charges', 'lire'));

                            if (! empty($menu_invert)) $leftmenu= 'tax_2_vat';

							if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_2_vat/i', $leftmenu)) {
								$newmenu->add("/compta/localtax/card.php?leftmenu=tax_2_vat&action=create&amp;localTaxType=2", $langs->trans("New"), 2, $user->hasRight('tax', 'charges', 'creer'));
								$newmenu->add("/compta/localtax/list.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("List"), 2, $user->hasRight('tax', 'charges', 'lire'));
								$newmenu->add("/compta/localtax/index.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByMonth"), 2, $user->hasRight('tax', 'charges', 'lire'));
								$newmenu->add("/compta/localtax/clients.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByThirdparties"), 2, $user->hasRight('tax', 'charges', 'lire'));
								$newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByQuarter"), 2, $user->hasRight('tax', 'charges', 'lire'));
							}
						}
					}
				}

				// Salaries
				if (isModEnabled('salaries')) {
					$langs->load("salaries");
					$newmenu->add("/salaries/list.php?leftmenu=tax_salary&amp;mainmenu=billing",$langs->trans("Salaries"),1,$user->hasRight('salaries', 'read'), '', $mainmenu, 'tax_salary');

                    if (! empty($menu_invert)) $leftmenu= 'tax_salary';

					if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_salary/i', $leftmenu)) {
						$newmenu->add("/salaries/card.php?leftmenu=tax_salary&action=create", $langs->trans("New"), 2, $user->hasRight('salaries', 'write'));
						$newmenu->add("/salaries/list.php?leftmenu=tax_salary", $langs->trans("List"), 2, $user->hasRight('salaries', 'read'));
						$newmenu->add("/salaries/payments.php?leftmenu=tax_salary", $langs->trans("Payments"), 2, $user->hasRight('salaries', 'read'));
						$newmenu->add("/salaries/stats/index.php?leftmenu=tax_salary", $langs->trans("Statistics"), 2, $user->hasRight('salaries', 'read'));
					}
                }

				// Loan
				if (isModEnabled('loan')) {
					$langs->load("loan");
					$newmenu->add("/loan/list.php?leftmenu=tax_loan&amp;mainmenu=billing", $langs->trans("Loans"), 1, $user->hasRight('loan', 'read'), '', $mainmenu, 'tax_loan');

					if (!empty($menu_invert)) $leftmenu = 'tax_loan';

					if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_loan/i', $leftmenu)) {
						$newmenu->add("/loan/card.php?leftmenu=tax_loan&action=create", $langs->trans("NewLoan"), 2, $user->hasRight('loan', 'write'));
						//$newmenu->add("/loan/payment/list.php?leftmenu=tax_loan",$langs->trans("Payments"),2,$user->hasRight('loan', 'read'));
					}
				}

				// Various payment
				if (isModEnabled('banque') && !getDolGlobalString('BANK_USE_OLD_VARIOUS_PAYMENT')) {
					$langs->load("banks");
					$newmenu->add("/compta/bank/various_payment/list.php?leftmenu=tax_various&amp;mainmenu=billing",$langs->trans("MenuVariousPayment"),1,$user->hasRight('banque', 'lire'), '', $mainmenu, 'tax_various');

                    if (! empty($menu_invert)) $leftmenu= 'tax_various';

					if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_various/i', $leftmenu)) {
						$newmenu->add("/compta/bank/various_payment/card.php?leftmenu=tax_various&action=create", $langs->trans("New"), 2, $user->hasRight('banque', 'modifier'));
						$newmenu->add("/compta/bank/various_payment/list.php?leftmenu=tax_various", $langs->trans("List"), 2, $user->hasRight('banque', 'lire'));
					}
				}
			}
		}

		/*
		 * Menu BANK
		 */
		if ($mainmenu == 'bank') {
			// Load translation files required by the page
			$langs->loadLangs(array("withdrawals", "banks", "bills", "categories"));

			// Bank-Cash account
			if (isModEnabled('banque')) {
				$newmenu->add("/compta/bank/list.php?leftmenu=bank&amp;mainmenu=bank", $langs->trans("MenuBankCash"), 0, $user->hasRight('banque', 'lire'), '', $mainmenu, 'bank', 0);

				$newmenu->add("/compta/bank/card.php?action=create", $langs->trans("MenuNewFinancialAccount"), 1, $user->hasRight('banque', 'configurer'));
				$newmenu->add("/compta/bank/list.php?leftmenu=bank&amp;mainmenu=bank", $langs->trans("List"), 1, $user->hasRight('banque', 'lire'), '', $mainmenu, 'bank');
				$newmenu->add("/compta/bank/bankentries_list.php", $langs->trans("ListTransactions"), 1, $user->hasRight('banque', 'lire'));
				$newmenu->add("/compta/bank/budget.php", $langs->trans("ListTransactionsByCategory"), 1, $user->hasRight('banque', 'lire'));

				$newmenu->add("/compta/bank/transfer.php", $langs->trans("MenuBankInternalTransfer"), 1, $user->hasRight('banque', 'transfer'));
			}

			if (isModEnabled('categorie')) {
				$langs->load("categories");
				$newmenu->add("/categories/index.php?type=5", $langs->trans("Rubriques"), 1, $user->hasRight('categorie', 'creer'), '', $mainmenu, 'tags');
                if ((float) DOL_VERSION >= 21.0) {
                    $newmenu->add("/categories/index.php?type=8", $langs->trans("RubriquesTransactions"), 1, $user->hasRight('banque', 'configurer'), '', $mainmenu, 'tags');
                } else {
                    $newmenu->add("/compta/bank/categ.php", $langs->trans("RubriquesTransactions"), 1, $user->hasRight('banque', 'configurer'), '', $mainmenu, 'tags');
                }
            }

			// Direct debit order
			if (isModEnabled('prelevement')) {
				$newmenu->add("/compta/prelevement/index.php?leftmenu=withdraw&amp;mainmenu=bank", $langs->trans("PaymentByDirectDebit"), 0, $user->hasRight('prelevement', 'bons', 'lire'), '', $mainmenu, 'withdraw', 0, '', '', '', img_picto('', 'payment', 'class="paddingright pictofixedwidth"'));

				if (! empty($menu_invert)) $leftmenu= 'withdraw';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "withdraw") {
					$newmenu->add("/compta/prelevement/create.php?mainmenu=bank", $langs->trans("NewStandingOrder"), 1, $user->hasRight('prelevement', 'bons', 'creer'));

					$newmenu->add("/compta/prelevement/orders_list.php?mainmenu=bank", $langs->trans("List"), 1, $user->hasRight('prelevement', 'bons', 'lire'));
					$newmenu->add("/compta/prelevement/list.php?mainmenu=bank", $langs->trans("WithdrawalsLines"), 1, $user->hasRight('prelevement', 'bons', 'lire'));
					$newmenu->add("/compta/prelevement/rejets.php?mainmenu=bank", $langs->trans("Rejects"), 1, $user->hasRight('prelevement', 'bons', 'lire'));
					$newmenu->add("/compta/prelevement/stats.php?mainmenu=bank", $langs->trans("Statistics"), 1, $user->hasRight('prelevement', 'bons', 'lire'));
				}
			}

			// Bank transfer order
			if (isModEnabled('paymentbybanktransfer')) {
				$newmenu->add("/compta/paymentbybanktransfer/index.php?leftmenu=banktransfer&amp;mainmenu=bank", $langs->trans("PaymentByBankTransfer"), 0, $user->hasRight('paymentbybanktransfer', 'read'), '', $mainmenu, 'banktransfer', 0, '', '', '', img_picto('', 'payment', 'class="paddingright pictofixedwidth"'));

				if (! empty($menu_invert)) $leftmenu= 'banktransfer';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "banktransfer") {
					$newmenu->add("/compta/prelevement/create.php?type=bank-transfer&mainmenu=bank", $langs->trans("NewPaymentByBankTransfer"), 1, $user->hasRight('paymentbybanktransfer', 'create'));

					$newmenu->add("/compta/prelevement/orders_list.php?type=bank-transfer&mainmenu=bank", $langs->trans("List"), 1, $user->hasRight('paymentbybanktransfer', 'read'));
					$newmenu->add("/compta/prelevement/list.php?type=bank-transfer&mainmenu=bank", $langs->trans("PaymentByBankTransferLines"), 1, $user->hasRight('paymentbybanktransfer', 'read'));
					$newmenu->add("/compta/prelevement/rejets.php?type=bank-transfer&mainmenu=bank", $langs->trans("Rejects"), 1, $user->hasRight('paymentbybanktransfer', 'read'));
					$newmenu->add("/compta/prelevement/stats.php?type=bank-transfer&mainmenu=bank", $langs->trans("Statistics"), 1, $user->hasRight('paymentbybanktransfer', 'read'));
				}
			}

			// Management of checks
			if (!getDolGlobalString('BANK_DISABLE_CHECK_DEPOSIT') && isModEnabled('banque') && (isModEnabled('facture') || getDolGlobalString('MAIN_MENU_CHEQUE_DEPOSIT_ON'))) {
				$newmenu->add("/compta/paiement/cheque/index.php?leftmenu=checks&amp;mainmenu=bank", $langs->trans("MenuChequeDeposits"), 0, $user->hasRight('banque', 'cheque'), '', $mainmenu, 'checks', 0, '', '', '', img_picto('', 'payment', 'class="paddingright pictofixedwidth"'));

                if (! empty($menu_invert)) $leftmenu= 'checks';

                if ($usemenuhider || empty($leftmenu) || $leftmenu == "checks") {
					$newmenu->add("/compta/paiement/cheque/card.php?leftmenu=checks&amp;action=new&amp;mainmenu=bank", $langs->trans("NewChequeDeposit"), 1, $user->hasRight('banque', 'cheque'));
					$newmenu->add("/compta/paiement/cheque/list.php?leftmenu=checks&amp;mainmenu=bank", $langs->trans("List"), 1, $user->hasRight('banque', 'cheque'));
				}
			}

			// Cash Control
			if (isModEnabled('takepos') || isModEnabled('cashdesk')) {
				$permtomakecashfence = ($user->hasRight('cashdesk', 'run') || $user->hasRight('takepos', 'run'));
				$newmenu->add("/compta/cashcontrol/cashcontrol_list.php?mainmenu=bank", $langs->trans("POS"), 0, $permtomakecashfence, '', $mainmenu, 'cashcontrol', 0, '', '', '', img_picto('', 'pos', 'class="pictofixedwidth"'));
				$newmenu->add("/compta/cashcontrol/cashcontrol_card.php?mainmenu=bank&action=create", $langs->trans("NewCashFence"), 1, $permtomakecashfence);
				$newmenu->add("/compta/cashcontrol/cashcontrol_list.php?mainmenu=bank", $langs->trans("List"), 1, $permtomakecashfence);
			}
		}

		/*
		 * Menu PRODUCTS-SERVICES
		 */
		if ($mainmenu == 'products') {
			// Products
			if (isModEnabled('product')) {
				$newmenu->add("/product/index.php?leftmenu=product&amp;type=0", $langs->trans("Products"), 0, $user->hasRight('produit', 'lire'), '', $mainmenu, 'product', 0);
				$newmenu->add("/product/card.php?leftmenu=product&amp;action=create&amp;type=0", $langs->trans("NewProduct"), 1, $user->hasRight('produit', 'creer'));
				$newmenu->add("/product/list.php?leftmenu=product&amp;type=0", $langs->trans("List"), 1, $user->hasRight('produit', 'lire'));
				if (isModEnabled('stock')) {
					$newmenu->add("/product/reassort.php?type=0", $langs->trans("MenuStocks"), 1, $user->hasRight('produit', 'lire') && $user->hasRight('stock', 'lire'));
				}
				if (isModEnabled('productbatch')) {
					$langs->load("stocks");
					$newmenu->add("/product/reassortlot.php?type=0", $langs->trans("StocksByLotSerial"), 1, $user->hasRight('produit', 'lire') && $user->hasRight('stock', 'lire'));
					$newmenu->add("/product/stock/productlot_list.php", $langs->trans("LotSerial"), 1, $user->hasRight('produit', 'lire') && $user->hasRight('stock', 'lire'));
				}
				if (isModEnabled('variants')) {
					$newmenu->add("/variants/list.php", $langs->trans("VariantAttributes"), 1, $user->hasRight('produit', 'lire'));
				}
				if (isModEnabled('propal') || (isModEnabled('commande') && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD')) || isModEnabled('facture') || isModEnabled('fournisseur') || isModEnabled('supplier_proposal') || isModEnabled('supplier_order') || isModEnabled('supplier_invoice')) {
					$newmenu->add("/product/stats/card.php?id=all&leftmenu=stats&type=0", $langs->trans("Statistics"), 1, $user->hasRight('produit', 'lire') || $user->hasRight('product', 'lire'));
				}

				// Categories
				if (isModEnabled('categorie')) {
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=0", $langs->trans("Categories"), 1, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
					//if ($usemenuhider || empty($leftmenu) || $leftmenu=="cat") $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->hasRight('categorie', 'lire'));
				}
			}

			// Services
			if (isModEnabled('service')) {
				$newmenu->add("/product/index.php?leftmenu=service&amp;type=1", $langs->trans("Services"), 0, $user->hasRight('service', 'lire'), '', $mainmenu, 'service', 0);
				$newmenu->add("/product/card.php?leftmenu=service&amp;action=create&amp;type=1", $langs->trans("NewService"), 1, $user->hasRight('service', 'creer'));
				$newmenu->add("/product/list.php?leftmenu=service&amp;type=1", $langs->trans("List"), 1, $user->hasRight('service', 'lire'));
				if (isModEnabled('propal') || isModEnabled('commande') || isModEnabled('facture') || (isModEnabled('fournisseur') && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD')) || isModEnabled('supplier_proposal') || isModEnabled('supplier_order') || isModEnabled('supplier_invoice')) {
					$newmenu->add("/product/stats/card.php?id=all&leftmenu=stats&type=1", $langs->trans("Statistics"), 1, $user->hasRight('service', 'lire') || $user->hasRight('product', 'lire'));
				}
				// Categories
				if (isModEnabled('categorie')) {
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=0", $langs->trans("Categories"), 1, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
					//if ($usemenuhider || empty($leftmenu) || $leftmenu=="cat") $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->hasRight('categorie', 'lire'));
				}
			}

			// Warehouse
			if (isModEnabled('stock')) {
				$langs->load("stocks");
				$newmenu->add("/product/stock/index.php?leftmenu=stock", $langs->trans("Warehouses"), 0, $user->hasRight('stock', 'lire'), '', $mainmenu, 'stock', 0);
				$newmenu->add("/product/stock/card.php?action=create", $langs->trans("MenuNewWarehouse"), 1, $user->hasRight('stock', 'creer'));
				$newmenu->add("/product/stock/list.php", $langs->trans("List"), 1, $user->hasRight('stock', 'lire'));
				$newmenu->add("/product/stock/movement_list.php", $langs->trans("Movements"), 1, $user->hasRight('stock', 'mouvement', 'lire'));

				$newmenu->add("/product/stock/massstockmove.php", $langs->trans("MassStockTransferShort"), 1, $user->hasRight('stock', 'mouvement', 'creer'));
				if (isModEnabled('supplier_order')) {
					$newmenu->add("/product/stock/replenish.php", $langs->trans("Replenishment"), 1, $user->hasRight('stock', 'mouvement', 'creer') && $user->hasRight('fournisseur', 'lire'));
				}
				$newmenu->add("/product/stock/stockatdate.php", $langs->trans("StockAtDate"), 1, $user->hasRight('produit', 'lire') && $user->hasRight('stock', 'lire'));

				// Categories for warehouses
				if (isModEnabled('categorie')) {
					$newmenu->add("/categories/index.php?leftmenu=stock&amp;type=9", $langs->trans("Categories"), 1, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
				}
			}

			// Inventory
			if (isModEnabled('stock')) {
				$langs->load("stocks");

				if (! empty($menu_invert)) $leftmenu= 'stock_inventories';

				if (!getDolGlobalString('MAIN_USE_ADVANCED_PERMS')) {
					$newmenu->add("/product/inventory/list.php?leftmenu=stock_inventories", $langs->trans("inventoryTitle"), 0, $user->hasRight('stock', 'lire'), '', $mainmenu, 'inventory', 0);
					if ($usemenuhider || empty($leftmenu) || $leftmenu == "stock_inventories") {
						$newmenu->add("/product/inventory/card.php?action=create&leftmenu=stock_inventories", $langs->trans("NewInventory"), 1, $user->hasRight('stock', 'creer'));
						$newmenu->add("/product/inventory/list.php?leftmenu=stock_inventories", $langs->trans("List"), 1, $user->hasRight('stock', 'lire'));
					}
				} else {
					$newmenu->add("/product/inventory/list.php?leftmenu=stock_inventories", $langs->trans("inventoryTitle"), 0, $user->hasRight('stock', 'inventory_advance', 'read'), '', $mainmenu, 'inventory', 0);
					if ($usemenuhider || empty($leftmenu) || $leftmenu == "stock_inventories") {
						$newmenu->add("/product/inventory/card.php?action=create&leftmenu=stock_inventories", $langs->trans("NewInventory"), 1, $user->hasRight('stock', 'inventory_advance', 'write'));
						$newmenu->add("/product/inventory/list.php?leftmenu=stock_inventories", $langs->trans("List"), 1, $user->hasRight('stock', 'inventory_advance', 'read'));
					}
				}
			}

			// Shipments
			if (isModEnabled('expedition')) {
				$langs->load("sendings");
				$newmenu->add("/expedition/index.php?leftmenu=sendings", $langs->trans("Shipments"), 0, $user->hasRight('expedition', 'lire'), '', $mainmenu, 'sendings', 0);
				$newmenu->add("/expedition/card.php?action=create2&amp;leftmenu=sendings", $langs->trans("NewSending"), 1, $user->hasRight('expedition', 'creer'));
				$newmenu->add("/expedition/list.php?leftmenu=sendings", $langs->trans("List"), 1, $user->hasRight('expedition', 'lire'));

                if (! empty($menu_invert)) $leftmenu= 'sendings';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "sendings") {
					$newmenu->add("/expedition/list.php?leftmenu=sendings&search_status=0", $langs->trans("StatusSendingDraftShort"), 2, $user->hasRight('expedition', 'lire'));
					$newmenu->add("/expedition/list.php?leftmenu=sendings&search_status=1", $langs->trans("StatusSendingValidatedShort"), 2, $user->hasRight('expedition', 'lire'));
					$newmenu->add("/expedition/list.php?leftmenu=sendings&search_status=2", $langs->trans("StatusSendingProcessedShort"), 2, $user->hasRight('expedition', 'lire'));
				}
				$newmenu->add("/expedition/stats/index.php?leftmenu=sendings", $langs->trans("Statistics"), 1, $user->hasRight('expedition', 'lire'));
			}

			// Receptions
			if (isModEnabled('reception')) {
				$langs->load("receptions");
				$newmenu->add("/reception/index.php?leftmenu=receptions", $langs->trans("Receptions"), 0, $user->hasRight('reception', 'lire'), '', $mainmenu, 'receptions', 0, '', '', '', img_picto('', 'dollyrevert', 'class="pictofixedwidth"'));
				$newmenu->add("/reception/card.php?action=create2&amp;leftmenu=receptions", $langs->trans("NewReception"), 1, $user->hasRight('reception', 'creer'));
				$newmenu->add("/reception/list.php?leftmenu=receptions", $langs->trans("List"), 1, $user->hasRight('reception', 'lire'));

				if (! empty($menu_invert)) $leftmenu= 'receptions';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "receptions") {
					$newmenu->add("/reception/list.php?leftmenu=receptions&search_status=0", $langs->trans("StatusReceptionDraftShort"), 2, $user->hasRight('reception', 'lire'));
				}
				if ($usemenuhider || empty($leftmenu) || $leftmenu == "receptions") {
					$newmenu->add("/reception/list.php?leftmenu=receptions&search_status=1", $langs->trans("StatusReceptionValidatedShort"), 2, $user->hasRight('reception', 'lire'));
				}
				if ($usemenuhider || empty($leftmenu) || $leftmenu == "receptions") {
					$newmenu->add("/reception/list.php?leftmenu=receptions&search_status=2", $langs->trans("StatusReceptionProcessedShort"), 2, $user->hasRight('reception', 'lire'));
				}
				$newmenu->add("/reception/stats/index.php?leftmenu=receptions", $langs->trans("Statistics"), 1, $user->hasRight('reception', 'lire'));
			}
		}

		/*
		 * Menu PRODUCTS-SERVICES MRP - GPAO
		 */
		if ($mainmenu == 'mrp') {
			// BOM
			if (isModEnabled('bom') || isModEnabled('mrp')) {
				$langs->load("mrp");

				$newmenu->add("", $langs->trans("MenuBOM"), 0, $user->hasRight('bom', 'read'), '', $mainmenu, 'bom', 0);
				$newmenu->add("/bom/bom_card.php?leftmenu=bom&amp;action=create", $langs->trans("NewBOM"), 1, $user->hasRight('bom', 'write'), '', $mainmenu, 'bom');
				$newmenu->add("/bom/bom_list.php?leftmenu=bom", $langs->trans("List"), 1, $user->hasRight('bom', 'read'), '', $mainmenu, 'bom');
			}

			if (isModEnabled('mrp')) {
				$langs->load("mrp");

				$newmenu->add("", $langs->trans("MenuMRP"), 0, $user->hasRight('mrp', 'read'), '', $mainmenu, 'mo', 0);
				$newmenu->add("/mrp/mo_card.php?leftmenu=mo&amp;action=create", $langs->trans("NewMO"), 1, $user->hasRight('mrp', 'write'), '', $mainmenu, 'mo');
				$newmenu->add("/mrp/mo_list.php?leftmenu=mo", $langs->trans("List"), 1, $user->hasRight('mrp', 'read'), '', $mainmenu, 'mo');
			}
		}

		/*
		 * Menu PROJECTS
		 */
		if ($mainmenu == 'project') {
			if (isModEnabled('project')) {
				$langs->load("projects");

				$search_project_user = GETPOST('search_project_user', 'int');

				$tmpentry = array(
					'enabled'=>(isModEnabled('project')),
					'perms'=>(!empty($user->hasRight('projet', 'lire'))),
					'module'=>'projet'
				);
				$showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal);

				$titleboth = $langs->trans("LeadsOrProjects");
				$titlenew = $langs->trans("NewLeadOrProject"); // Leads and opportunities by default
                if (!getDolGlobalString('PROJECT_USE_OPPORTUNITIES')) {
                    $titleboth = $langs->trans("Projects");
                    $titlenew = $langs->trans("NewProject");
                }
                if (getDolGlobalInt('PROJECT_USE_OPPORTUNITIES') == 2) {	// 2 = leads only
                    $titleboth = $langs->trans("Leads");
                    $titlenew = $langs->trans("NewLead");
                }

				// Project assigned to user
				$newmenu->add("/projet/index.php?leftmenu=projects".($search_project_user ? '&search_project_user='.$search_project_user : ''), $titleboth, 0, $user->hasRight('projet', 'lire'), '', $mainmenu, 'projects', 0);
				$newmenu->add("/projet/card.php?leftmenu=projects&action=create".($search_project_user ? '&search_project_user='.$search_project_user : ''), $titlenew, 1, $user->hasRight('projet', 'creer'));

                if (!getDolGlobalString('PROJECT_USE_OPPORTUNITIES')) {
					$newmenu->add("/projet/list.php?leftmenu=projets".($search_project_user ? '&search_project_user='.$search_project_user : '').'&search_status=99', $langs->trans("List"), 1, $showmode, '', 'project', 'list');
                } elseif (getDolGlobalInt('PROJECT_USE_OPPORTUNITIES') == 1) {
					$newmenu->add("/projet/list.php?leftmenu=projets".($search_project_user ? '&search_project_user='.$search_project_user : ''), $langs->trans("List"), 1, $showmode, '', 'project', 'list');
					$newmenu->add('/projet/list.php?mainmenu=project&amp;leftmenu=list&search_usage_opportunity=1&search_status=99&search_opp_status=openedopp&contextpage=lead', $langs->trans("ListOpenLeads"), 2, $showmode);
					$newmenu->add('/projet/list.php?mainmenu=project&amp;leftmenu=list&search_opp_status=notopenedopp&search_status=99&contextpage=project', $langs->trans("ListOpenProjects"), 2, $showmode);
                } elseif (getDolGlobalInt('PROJECT_USE_OPPORTUNITIES') == 2) {	// 2 = leads only
					$newmenu->add('/projet/list.php?mainmenu=project&amp;leftmenu=list&search_usage_opportunity=1&search_status=99', $langs->trans("List"), 2, $showmode);
				}

				$newmenu->add("/projet/stats/index.php?leftmenu=projects", $langs->trans("Statistics"), 1, $user->hasRight('projet', 'lire'));

				// Categories
				if (isModEnabled('categorie')) {
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=6", $langs->trans("Categories"), 1, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
				}

				if (!getDolGlobalString('PROJECT_HIDE_TASKS')) {
                    // Project affected to user
                    $newmenu->add("/projet/activity/index.php?leftmenu=tasks" . ($search_project_user ? '&search_project_user=' . $search_project_user : ''), $langs->trans("Activities"), 0, $user->hasRight('projet', 'lire'), '', 'project', 'tasks', 0);
                    $newmenu->add("/projet/tasks.php?leftmenu=tasks&action=create", $langs->trans("NewTask"), 1, $user->hasRight('projet', 'creer'));
                    $newmenu->add("/projet/tasks/list.php?leftmenu=tasks" . ($search_project_user ? '&search_project_user=' . $search_project_user : ''), $langs->trans("List"), 1, $user->hasRight('projet', 'lire'));
                    $newmenu->add("/projet/tasks/stats/index.php?leftmenu=projects", $langs->trans("Statistics"), 1, $user->hasRight('projet', 'lire'));

                    if (!getDolGlobalString('PROJECT_HIDE_MENU_TASKS_ACTIVITY')) {
                        $newmenu->add("/projet/activity/perweek.php?leftmenu=tasks" . ($search_project_user ? '&search_project_user=' . $search_project_user : ''), $langs->trans("NewTimeSpent"), 0, $user->hasRight('projet', 'lire'), '', 'project', 'timespent', 0);
                    }
                }
			}
		}

		/*
		 * Menu HRM
		*/
		if ($mainmenu == 'hrm') {
			// HRM module
			if (isModEnabled('hrm')) {
				$langs->load("hrm");

				$newmenu->add("/user/list.php?mainmenu=hrm&leftmenu=hrm&contextpage=employeelist", $langs->trans("Employees"), 0, $user->hasRight('user', 'user', 'lire'), '', $mainmenu, 'hrm', 0, '', '', '', img_picto('', 'user', 'class="paddingright pictofixedwidth"'));
				$newmenu->add("/user/card.php?mainmenu=hrm&leftmenu=hrm&action=create&employee=1", $langs->trans("NewEmployee"), 1, $user->hasRight('user', 'user', 'creer'));
				$newmenu->add("/user/list.php?mainmenu=hrm&leftmenu=hrm&contextpage=employeelist", $langs->trans("List"), 1, $user->hasRight('user', 'user', 'lire'));

                if ((float) DOL_VERSION >= 15.0) {
                    $newmenu->add("/hrm/skill_list.php?mainmenu=hrm&leftmenu=hrm_sm", $langs->trans("SkillsManagement"), 0, $user->hasRight('hrm', 'all', 'read'), '', $mainmenu, 'hrm_sm', 0, '', '', '', img_picto('', 'shapes', 'class="paddingright pictofixedwidth"'));

                    // Skills
                    $newmenu->add("/hrm/skill_list.php?mainmenu=hrm&leftmenu=hrm_sm", $langs->trans("Skills"), 1, $user->hasRight('hrm', 'all', 'read'), '', $mainmenu, 'hrm_sm', 0, '', '', '', img_picto('', 'shapes', 'class="paddingright pictofixedwidth"'));
                    
                    // Job (Description of work to do and skills required)
                    $newmenu->add("/hrm/job_list.php?mainmenu=hrm&leftmenu=hrm_sm", $langs->trans("JobsProfiles"), 1, $user->hasRight('hrm', 'all', 'read'), '', $mainmenu, 'hrm_sm', 0, '', '', '', img_picto('', 'technic', 'class="paddingright pictofixedwidth"'));
                    
                    // Position = Link job - user
                    $newmenu->add("/hrm/position_list.php?mainmenu=hrm&leftmenu=hrm_sm", $langs->trans("EmployeePositions"), 1, $user->hasRight('hrm', 'all', 'read'), '', $mainmenu, 'hrm_sm', 0, '', '', '', img_picto('', 'user-cog', 'class="paddingright pictofixedwidth"'));
                    
                    // Evaluation
                    $newmenu->add("/hrm/evaluation_list.php?mainmenu=hrm&leftmenu=hrm_sm", $langs->trans("Evals"), 1, $user->hasRight('hrm', 'evaluation', 'read'), '', $mainmenu, 'hrm_sm', 0, '', '', '', img_picto('', 'user', 'class="paddingright pictofixedwidth"'));
                    $newmenu->add("/hrm/compare.php?mainmenu=hrm&leftmenu=hrm_sm", $langs->trans("SkillComparison"), 1, $user->hasRight('hrm', 'evaluation', 'read') || $user->hasRight('hrm', 'compare_advance', 'read'));
                }
            }

			// Leave/Holiday/Vacation module
			if (isModEnabled('holiday')) {
				// Load translation files required by the page
				$langs->loadLangs(array("holiday", "trips"));

				$newmenu->add("/holiday/list.php?mainmenu=hrm&leftmenu=holiday", $langs->trans("CPTitreMenu"), 0, $user->hasRight('holiday', 'read'), '', $mainmenu, 'holiday', 0, '', '', '', img_picto('', 'holiday', 'class="paddingright pictofixedwidth"'));
				$newmenu->add("/holiday/card.php?mainmenu=hrm&leftmenu=holiday&action=create", $langs->trans("New"), 1, $user->hasRight('holiday', 'write'), '',$mainmenu);
                $newmenu->add("/holiday/card_group.php?mainmenu=hrm&leftmenu=holiday&action=create", $langs->trans("NewHolidayForGroup"), 1, $user->hasRight('holiday', 'writeall') && $user->hasRight('holiday', 'readall'), '', $mainmenu, 'holiday_sm');
                $newmenu->add("/holiday/list.php?mainmenu=hrm&leftmenu=holiday", $langs->trans("List"), 1, $user->hasRight('holiday', 'read'), '',$mainmenu);

				if (! empty($menu_invert)) $leftmenu= 'holiday';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "holiday") {
					$newmenu->add("/holiday/list.php?search_status=1&mainmenu=hrm&leftmenu=holiday", $langs->trans("DraftCP"), 2, $user->hasRight('holiday', 'read'),'',$mainmenu,'holiday_sm');
					$newmenu->add("/holiday/list.php?search_status=2&mainmenu=hrm&leftmenu=holiday", $langs->trans("ToReviewCP"), 2, $user->hasRight('holiday', 'read'),'',$mainmenu,'holiday_sm');
					$newmenu->add("/holiday/list.php?search_status=3&mainmenu=hrm&leftmenu=holiday", $langs->trans("ApprovedCP"), 2, $user->hasRight('holiday', 'read'),'',$mainmenu,'holiday_sm');
					$newmenu->add("/holiday/list.php?search_status=4&mainmenu=hrm&leftmenu=holiday", $langs->trans("CancelCP"), 2, $user->hasRight('holiday', 'read'),'',$mainmenu,'holiday_sm');
					$newmenu->add("/holiday/list.php?search_status=5&mainmenu=hrm&leftmenu=holiday", $langs->trans("RefuseCP"), 2, $user->hasRight('holiday', 'read'),'',$mainmenu,'holiday_sm');
				}
				$newmenu->add("/holiday/define_holiday.php?mainmenu=hrm&action=request", $langs->trans("MenuConfCP"), 1, $user->hasRight('holiday', 'read'), '',$mainmenu,'holiday_sm');
				$newmenu->add("/holiday/month_report.php?mainmenu=hrm&leftmenu=holiday", $langs->trans("MenuReportMonth"), 1, $user->hasRight('holiday', 'readall'), '',$mainmenu,'holiday_sm');
				$newmenu->add("/holiday/view_log.php?mainmenu=hrm&leftmenu=holiday&action=request", $langs->trans("MenuLogCP"), 1, $user->hasRight('holiday', 'define_holiday'), '',$mainmenu,'holiday_sm');
			}

			// Trips and expenses (old module)
			if (isModEnabled('deplacement')) {
				$langs->load("trips");
				$newmenu->add("/compta/deplacement/index.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("TripsAndExpenses"), 0, $user->hasRight('deplacement', 'lire'), '', $mainmenu, 'tripsandexpenses', 0);
				$newmenu->add("/compta/deplacement/card.php?action=create&amp;leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("New"), 1, $user->hasRight('deplacement', 'creer'));
				$newmenu->add("/compta/deplacement/list.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("List"), 1, $user->hasRight('deplacement', 'lire'));
				$newmenu->add("/compta/deplacement/stats/index.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("Statistics"), 1, $user->hasRight('deplacement', 'lire'));
			}

			// Expense report
			if (isModEnabled('expensereport')) {
                $langs->loadLangs(array("trips", "bills"));
				$newmenu->add("/expensereport/index.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("TripsAndExpenses"), 0, $user->hasRight('expensereport', 'lire'), '', $mainmenu, 'expensereport', 0);
				$newmenu->add("/expensereport/card.php?action=create&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("New"), 1, $user->hasRight('expensereport', 'creer'));
				$newmenu->add("/expensereport/list.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("List"), 1, $user->hasRight('expensereport', 'lire'));

                if (! empty($menu_invert)) $leftmenu= 'expensereport';

				if ($usemenuhider || empty($leftmenu) || $leftmenu == "expensereport") {
					$newmenu->add("/expensereport/list.php?search_status=0&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Draft"), 2, $user->hasRight('expensereport', 'lire'));
					$newmenu->add("/expensereport/list.php?search_status=2&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Validated"), 2, $user->hasRight('expensereport', 'lire'));
					$newmenu->add("/expensereport/list.php?search_status=5&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Approved"), 2, $user->hasRight('expensereport', 'lire'));
					$newmenu->add("/expensereport/list.php?search_status=6&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Paid"), 2, $user->hasRight('expensereport', 'lire'));
					$newmenu->add("/expensereport/list.php?search_status=4&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Canceled"), 2, $user->hasRight('expensereport', 'lire'));
					$newmenu->add("/expensereport/list.php?search_status=99&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Refused"), 2, $user->hasRight('expensereport', 'lire'));
				}
                if ((float) DOL_VERSION >= 16.0) {
                    $newmenu->add("/expensereport/payment/list.php?leftmenu=expensereport_payments&amp;mainmenu=hrm", $langs->trans("Payments"), 1, $user->hasRight('expensereport', 'lire'));
                }
                $newmenu->add("/expensereport/stats/index.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Statistics"), 1, $user->hasRight('expensereport', 'lire'));
			}

			if (isModEnabled('projet')) {
				if (!getDolGlobalString('PROJECT_HIDE_TASKS')) {
					$langs->load("projects");

					$search_project_user = GETPOST('search_project_user', 'int');

					$newmenu->add("/projet/activity/perweek.php?leftmenu=tasks".($search_project_user ? '&search_project_user='.$search_project_user : ''), $langs->trans("NewTimeSpent"), 0, $user->hasRight('projet', 'lire'), '', $mainmenu, 'timespent', 0);
				}
			}
		}

		/*
		 * Menu ACCOUNTANCY
		 */
		if ($mainmenu == 'accountancy')
		{
			$langs->load("companies");

			// Accountancy (Double entries)
			if (isModEnabled('accounting'))
			{
				$permtoshowmenu=(isModEnabled('accounting') || $user->hasRight('accounting', 'bind', 'write') || $user->hasRight('compta', 'resultat', 'lire') || (!getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'read')) || (getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'model_advance', 'read')));

                // Transfer in accounting
                $newmenu->add("/accountancy/index.php?leftmenu=accountancy",$langs->trans("TransferInAccounting"), 0, $permtoshowmenu, '', $mainmenu, 'transfer');

                    // Binding
                    if (isModEnabled('facture') && !getDolGlobalString('ACCOUNTING_DISABLE_BINDING_ON_SALES'))
                    {
                        $newmenu->add("/accountancy/customer/index.php?leftmenu=accountancy_dispatch_customer&amp;mainmenu=accountancy",$langs->trans("CustomersVentilation"),1,$user->hasRight('accounting', 'bind', 'write'), '', $mainmenu, 'dispatch_customer');

                        if (! empty($menu_invert)) $leftmenu= 'accountancy_dispatch_customer';

                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_customer/',$leftmenu)) $newmenu->add("/accountancy/customer/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer",$langs->trans("ToBind"),2,$user->hasRight('accounting', 'bind', 'write'));
                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_customer/',$leftmenu)) $newmenu->add("/accountancy/customer/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer",$langs->trans("Binded"),2,$user->hasRight('accounting', 'bind', 'write'));
                    }
                    if (isModEnabled('supplier_invoice') && !getDolGlobalString('ACCOUNTING_DISABLE_BINDING_ON_PURCHASES'))
                    {
                        $newmenu->add("/accountancy/supplier/index.php?leftmenu=accountancy_dispatch_supplier&amp;mainmenu=accountancy",$langs->trans("SuppliersVentilation"),1,$user->hasRight('accounting', 'bind', 'write'), '', $mainmenu, 'dispatch_supplier');

                        if (! empty($menu_invert)) $leftmenu= 'accountancy_dispatch_supplier';

                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_supplier/',$leftmenu)) $newmenu->add("/accountancy/supplier/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier",$langs->trans("ToBind"),2,$user->hasRight('accounting', 'bind', 'write'));
                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_supplier/',$leftmenu)) $newmenu->add("/accountancy/supplier/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier",$langs->trans("Binded"),2,$user->hasRight('accounting', 'bind', 'write'));
                    }
                    if (isModEnabled('expensereport') && !getDolGlobalString('ACCOUNTING_DISABLE_BINDING_ON_EXPENSEREPORTS'))
                    {
                        $newmenu->add("/accountancy/expensereport/index.php?leftmenu=accountancy_dispatch_expensereport&amp;mainmenu=accountancy",$langs->trans("ExpenseReportsVentilation"),1,$user->hasRight('accounting', 'bind', 'write'), '', $mainmenu, 'dispatch_expensereport');

                        if (! empty($menu_invert)) $leftmenu= 'accountancy_dispatch_expensereport';

                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_expensereport/',$leftmenu)) $newmenu->add("/accountancy/expensereport/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_expensereport",$langs->trans("ToBind"),2,$user->hasRight('accounting', 'bind', 'write'));
                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_expensereport/',$leftmenu)) $newmenu->add("/accountancy/expensereport/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_expensereport",$langs->trans("Binded"),2,$user->hasRight('accounting', 'bind', 'write'));
                    }

                    // Journals
                    if(isModEnabled('accounting') && $user->hasRight('accounting', 'comptarapport', 'lire') && $mainmenu == 'accountancy') {
                        $newmenu->add('',$langs->trans("RegistrationInAccounting"),1,$user->hasRight('accounting', 'comptarapport', 'lire'), '', $mainmenu, 'accountancy_journal');

                        // Multi journal
                        $sql = "SELECT rowid, code, label, nature";
                        $sql.= " FROM ".MAIN_DB_PREFIX."accounting_journal";
                        $sql.= " WHERE entity = ".$conf->entity;
                        $sql.= " AND active = 1";
                        $sql.= " ORDER BY nature ASC, label DESC";

                        $resql = $db->query($sql);
                        if ($resql)
                        {
                            $numr = $db->num_rows($resql);
                            $i = 0;

                            if ($numr > 0) {
                                while ($i < $numr) {
                                    $objp = $db->fetch_object($resql);

                                    $nature='';

                                    // Must match array $sourceList defined into journals_list.php
                                    if ($objp->nature == 2 && isModEnabled('facture') && !getDolGlobalString('ACCOUNTING_DISABLE_BINDING_ON_SALES')) {
                                        $nature="sells";
                                    }
                                    if ($objp->nature == 3 && isModEnabled('fournisseur') && !getDolGlobalString('ACCOUNTING_DISABLE_BINDING_ON_PURCHASES')) {
                                        $nature="purchases";
                                    }
                                    if ($objp->nature == 4 && isModEnabled('banque')) {
                                        $nature="bank";
                                    }
                                    if ($objp->nature == 5 && isModEnabled('expensereport') && !getDolGlobalString('ACCOUNTING_DISABLE_BINDING_ON_EXPENSEREPORTS')) {
                                        $nature="expensereports";
                                    }
                                    if ($objp->nature == 1 && isModEnabled('asset')) {
                                        $nature = "various";
                                    }
                                    if ($objp->nature == 8) {
                                        $nature="inventory";
                                    }
                                    if ($objp->nature == 9) {
                                        $nature="hasnew";
                                    }

                                    // To enable when page exists
                                    if (!getDolGlobalString('ACCOUNTANCY_SHOW_DEVELOP_JOURNAL'))
                                    {
                                        if ($nature == 'hasnew' || $nature == 'inventory') $nature='';
                                    }

                                    // Remove all type when treasury accounting is on
                                    if (isModEnabled('treasuryaccounting')) {
                                        if ($nature == 'sells' || $nature == 'purchases' || $nature == 'bank' || $nature == 'expensereports') $nature='';
                                    }

                                    if ($nature) {
                                        $langs->load('accountancy');
                                        $journallabel = $langs->transnoentities($objp->label); // Label of bank account in llx_accounting_journal

                                        $key = $langs->trans("AccountingJournalType".strtoupper($objp->nature));
                                        $transferlabel = ($objp->nature && $key != "AccountingJournalType".strtoupper($langs->trans($objp->nature)) ? $key.($journallabel != $key ? ' '.$journallabel : ''): $journallabel);

                                        $newmenu->add('/accountancy/journal/'.$nature.'journal.php?mainmenu=accountancy&leftmenu=accountancy_journal&id_journal='.$objp->rowid, $transferlabel, 2, $user->hasRight('accounting', 'comptarapport', 'lire'));
                                    }
                                    $i++;
                                }
                            } else {
                                // Should not happened. Entries are added
                                $newmenu->add('',$langs->trans("NoJournalDefined"), 2, $user->hasRight('accounting', 'comptarapport', 'lire'));
                            }
                        } else {
                            dol_print_error($db);
                        }
                        $db->free($resql);
                    }

                    // Accountancy
                    $newmenu->add("/accountancy/index.php?leftmenu=accountancy",$langs->trans("MenuAccountancy"), 0, $permtoshowmenu, '', $mainmenu, 'accountancy');

                    // Balance
                    $newmenu->add("/accountancy/bookkeeping/balance.php?mainmenu=accountancy&amp;leftmenu=accountancy_balance",$langs->trans("AccountBalance"),1,$user->hasRight('accounting', 'mouvements', 'lire'));

                    // General Ledger
                    $newmenu->add("/accountancy/bookkeeping/listbyaccount.php?mainmenu=accountancy&amp;leftmenu=accountancy_ledger",$langs->trans("Bookkeeping"),1,$user->hasRight('accounting', 'mouvements', 'lire'));

                    // Journals
                    $newmenu->add("/accountancy/bookkeeping/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_journals",$langs->trans("Journals"),1,$user->hasRight('accounting', 'mouvements', 'lire'));

                    // Export accountancy
                    if (getDolGlobalFloat('EASYA_VERSION') >= 2022.5 || (float) DOL_VERSION >= 18.0) {
                        $newmenu->add("/accountancy/bookkeeping/export.php?mainmenu=accountancy&amp;leftmenu=accountancy_export",$langs->trans("MenuExportAccountancy"),1,$user->hasRight('accounting', 'mouvements', 'lire'));
                    }

                    // Files
                    if (!getDolGlobalString('ACCOUNTANCY_HIDE_EXPORT_FILES_MENU'))
                    {
                        $newmenu->add("/compta/accounting-files.php?mainmenu=accountancy&amp;leftmenu=accountancy_files", $langs->trans("AccountantFiles"), 1, $user->hasRight('accounting', 'mouvements', 'lire'));
                    }

                    // Closure
                    $newmenu->add("/accountancy/closure/index.php?mainmenu=accountancy&amp;leftmenu=accountancy_closure", $langs->trans("MenuAccountancyClosure"), 1, $user->hasRight('accounting', 'fiscalyear', 'write'), '', $mainmenu, 'closure');

                    // Reports
                    $newmenu->add("/compta/resultat/index.php?mainmenu=accountancy&amp;leftmenu=accountancy_report", $langs->trans("Reportings"), 1, $user->hasRight('accounting', 'comptarapport', 'lire'), '', $mainmenu, 'ca');

                    if (! empty($menu_invert)) $leftmenu= 'accountancy_report';

                    if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) {
                        $newmenu->add("/compta/resultat/index.php?leftmenu=accountancy_report", $langs->trans("MenuReportInOut"), 2, $user->hasRight('accounting', 'comptarapport', 'lire'));
                        $newmenu->add("/compta/resultat/clientfourn.php?leftmenu=accountancy_report", $langs->trans("ByPredefinedAccountGroups"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                        $newmenu->add("/compta/resultat/result.php?leftmenu=accountancy_report", $langs->trans("ByPersonalizedAccountGroups"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                    }

                    $modecompta='CREANCES-DETTES';
                    if(isModEnabled('accounting') && $user->hasRight('accounting', 'comptarapport', 'lire') && $mainmenu == 'accountancy') {
                        $modecompta='BOOKKEEPING';	// Not yet implemented. Should be BOOKKEEPINGCOLLECTED
                    }
                    if ($modecompta) {
                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) {
                            $newmenu->add("/compta/stats/index.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportTurnover"), 2, $user->hasRight('accounting', 'comptarapport', 'lire'));
                            $newmenu->add("/compta/stats/casoc.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                            $newmenu->add("/compta/stats/cabyuser.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByUsers"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                            $newmenu->add("/compta/stats/cabyprodserv.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByProductsAndServices"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                            $newmenu->add("/compta/stats/byratecountry.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByVatRate"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                        }
                    }

                    $modecompta='RECETTES-DEPENSES';
                    if ($modecompta) {
                        if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) {
                            $newmenu->add("/compta/stats/index.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportTurnoverCollected"), 2, $user->hasRight('accounting', 'comptarapport', 'lire'));
                            $newmenu->add("/compta/stats/casoc.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                            $newmenu->add("/compta/stats/cabyuser.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByUsers"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                        }
                    }

                $modecompta = 'CREANCES-DETTES';
                if (isModEnabled('accounting') && $user->hasRight('accounting', 'comptarapport', 'lire') && $mainmenu == 'accountancy') {
                    $modecompta = 'BOOKKEEPING'; // Not yet implemented.
                }
                if ($modecompta && ((!empty($conf->fournisseur->enabled) && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD')) || !empty($conf->supplier_invoice->enabled))) {
                    if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) {
                        $newmenu->add("/compta/stats/supplier_turnover.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportPurchaseTurnover"), 2, $user->hasRight('accounting', 'comptarapport', 'lire'));
                        $newmenu->add("/compta/stats/supplier_turnover_by_thirdparty.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                        $newmenu->add("/compta/stats/supplier_turnover_by_prodserv.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByProductsAndServices"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                    }
                }

                $modecompta = 'RECETTES-DEPENSES';
                if (isModEnabled('accounting') && $user->hasRight('accounting', 'comptarapport', 'lire') && $mainmenu == 'accountancy') {
                    $modecompta = 'BOOKKEEPINGCOLLECTED'; // Not yet implemented.
                }
                if ($modecompta && ((isModEnabled('fournisseur') && !getDolGlobalString('MAIN_USE_NEW_SUPPLIERMOD')) || isModEnabled('supplier_invoice'))) {
                    if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) {
                        $newmenu->add("/compta/stats/supplier_turnover.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportPurchaseTurnoverCollected"), 2, $user->hasRight('accounting', 'comptarapport', 'lire'));
                        $newmenu->add("/compta/stats/supplier_turnover_by_thirdparty.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 3, $user->hasRight('accounting', 'comptarapport', 'lire'));
                    }
                }

                // Configuration
                $newmenu->add("/accountancy/index.php?leftmenu=accountancy_admin", $langs->trans("Setup"), 0, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin', 1);

                if (! empty($menu_invert)) $leftmenu= 'accountancy_admin';

                if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_admin/', $leftmenu)) {
                    $newmenu->add("/accountancy/admin/index.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("General"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_general', 10);
                    $newmenu->add("/accountancy/admin/fiscalyear.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("FiscalPeriod"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'fiscalyear', 20);
                    $newmenu->add("/accountancy/admin/journals_list.php?id=35&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("AccountingJournals"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_journal', 30);
                    $newmenu->add("/accountancy/admin/accountmodel.php?id=31&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("Pcg_version"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_chartmodel', 40);
                    $newmenu->add("/accountancy/admin/account.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("Chartofaccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_chart', 41);
                    $newmenu->add("/accountancy/admin/subaccount.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("ChartOfSubaccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_chart', 42);
                    $newmenu->add("/accountancy/admin/defaultaccounts.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuDefaultAccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_default', 60);
                    if (isModEnabled('banque')) {
                        $newmenu->add("/compta/bank/list.php?mainmenu=accountancy&leftmenu=accountancy_admin&search_status=-1", $langs->trans("MenuBankAccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_bank', 70);
                    }
                    if (isModEnabled('facture') || isModEnabled('fournisseur')) {
                        $newmenu->add("/admin/dict.php?id=10&from=accountancy&search_country_id=".$mysoc->country_id."&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuVatAccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_default', 80);
                    }
                    if (isModEnabled('tax')) {
                        $newmenu->add("/admin/dict.php?id=7&from=accountancy&search_country_id=".$mysoc->country_id."&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuTaxAccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_default', 90);
                    }
                    if (isModEnabled('expensereport')) {
                        $newmenu->add("/admin/dict.php?id=17&from=accountancy&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuExpenseReportAccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_default', 100);
                    }
                    $newmenu->add("/accountancy/admin/productaccount.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuProductsAccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_product', 110);
                    $newmenu->add("/accountancy/admin/closure.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuClosureAccounts"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_closure', 120);
                    if (getDolGlobalInt('MAIN_FEATURES_LEVEL') >= 2 && (float) DOL_VERSION >= 21.0) {
                        $newmenu->add("/accountancy/admin/report_list.php?id=32&search_country_id=" . $mysoc->country_id . "&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("AccountingReport"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_report', 125);
                    }
                    $newmenu->add("/accountancy/admin/categories_list.php?id=32&search_country_id=".$mysoc->country_id."&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("AccountingCategory"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_chart', 130);

                    if ((float) DOL_VERSION < 21.0) {
                        $newmenu->add("/accountancy/admin/export.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("ExportOptions"), 1, $user->hasRight('accounting', 'chartofaccount'), '', $mainmenu, 'accountancy_admin_export', 130);
                    }
                }
            }

			// Accountancy (simple)
			if (isModEnabled('comptabilite')) {
                // Files
                if (!getDolGlobalString('ACCOUNTANCY_HIDE_EXPORT_FILES_MENU')) {
                    $newmenu->add("/compta/accounting-files.php?mainmenu=accountancy&amp;leftmenu=accountancy_files", $langs->trans("AccountantFiles"), 0, $user->hasRight('compta', 'resultat', 'lire'), '', $mainmenu, 'files');
                }

                // Bilan, resultats
                $newmenu->add("/compta/resultat/index.php?leftmenu=report&amp;mainmenu=accountancy", $langs->trans("Reportings"), 0, $user->hasRight('compta', 'resultat', 'lire'), '', $mainmenu, 'ca');

                if (! empty($menu_invert)) $leftmenu= 'report';

                if ($usemenuhider || empty($leftmenu) || preg_match('/report/', $leftmenu)) {
                    $newmenu->add("/compta/resultat/index.php?leftmenu=report", $langs->trans("MenuReportInOut"), 1, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/resultat/clientfourn.php?leftmenu=report", $langs->trans("ByCompanies"), 2, $user->hasRight('compta', 'resultat', 'lire'));
                    $modecompta = 'CREANCES-DETTES';
                    $newmenu->add("/compta/stats/index.php?leftmenu=report&modecompta=".$modecompta, $langs->trans("ReportTurnover"), 1, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/casoc.php?leftmenu=report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 2, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/cabyuser.php?leftmenu=report&modecompta=".$modecompta, $langs->trans("ByUsers"), 2, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/cabyprodserv.php?leftmenu=report&modecompta=".$modecompta, $langs->trans("ByProductsAndServices"), 2, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/byratecountry.php?leftmenu=report&modecompta=".$modecompta, $langs->trans("ByVatRate"), 2, $user->hasRight('compta', 'resultat', 'lire'));

                    $modecompta = 'RECETTES-DEPENSES';
                    $newmenu->add("/compta/stats/index.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportTurnoverCollected"), 1, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/casoc.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 2, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/cabyuser.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByUsers"), 2, $user->hasRight('compta', 'resultat', 'lire'));

                    //Achats
                    $modecompta = 'CREANCES-DETTES';
                    $newmenu->add("/compta/stats/supplier_turnover.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportPurchaseTurnover"), 1, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/supplier_turnover_by_thirdparty.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 2, $user->hasRight('compta', 'resultat', 'lire'));
                    $newmenu->add("/compta/stats/supplier_turnover_by_prodserv.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByProductsAndServices"), 2, $user->hasRight('compta', 'resultat', 'lire'));

                    // Journaux
                    $newmenu->add("/compta/journal/sellsjournal.php?leftmenu=report", $langs->trans("SellsJournal"), 1, $user->hasRight('compta', 'resultat', 'lire'), '', '', '', 50);
                    $newmenu->add("/compta/journal/purchasesjournal.php?leftmenu=report", $langs->trans("PurchasesJournal"), 1, $user->hasRight('compta', 'resultat', 'lire'), '', '', '', 51);
                }
            }

            // Intracomm report
            if (isModEnabled('intracommreport')) {
                $langs->load("intracommreport");

                $newmenu->add("/intracommreport/list.php?leftmenu=intracommreport", $langs->trans("MenuIntracommReport"), 0, $user->hasRight('intracommreport', 'read'), '', $mainmenu, 'intracommreport', 60, '', '', '', img_picto('', 'intracommreport', 'class="paddingright pictofixedwidth"'));

                if (! empty($menu_invert)) $leftmenu= 'intracommreport';

                if ($usemenuhider || empty($leftmenu) || preg_match('/intracommreport/', $leftmenu)) {
                    // DEB / DES
                    $newmenu->add("/intracommreport/card.php?action=create&leftmenu=intracommreport", $langs->trans("MenuIntracommReportNew"), 1, $user->hasRight('intracommreport', 'write'), '', $mainmenu, 'intracommreport', 1);
                    $newmenu->add("/intracommreport/list.php?leftmenu=intracommreport", $langs->trans("MenuIntracommReportList"), 1, $user->hasRight('intracommreport', 'read'), '', $mainmenu, 'intracommreport', 1);
                }
            }

			// Assets
			if (isModEnabled('asset'))
            {
                $langs->load("assets");

				if (getDolGlobalFloat('EASYA_VERSION') >= 2022.5 || (float) DOL_VERSION >= 16.0) {
                    $newmenu->add("/asset/list.php?leftmenu=asset&amp;mainmenu=accountancy", $langs->trans("MenuAssets"), 0, $user->hasRight('asset', 'read'), '', $mainmenu, 'asset', 100, '', '', '', img_picto('', 'payment', 'class="paddingright pictofixedwidth"'));
                    $newmenu->add("/asset/card.php?leftmenu=asset&amp;action=create", $langs->trans("MenuNewAsset"), 1, $user->hasRight('asset', 'write'));
                    $newmenu->add("/asset/list.php?leftmenu=asset&amp;mainmenu=accountancy", $langs->trans("MenuListAssets"), 1, $user->hasRight('asset', 'read'));
                    $newmenu->add("/asset/model/list.php?leftmenu=asset_model", $langs->trans("MenuAssetModels"), 1, (!getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'read')) || (getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'model_advance', 'read')), '', $mainmenu, 'asset_model');

                    if (!empty($menu_invert)) $leftmenu = 'asset_model';

                    if ($usemenuhider || empty($leftmenu) || preg_match('/asset_model/', $leftmenu)) {
                        $newmenu->add("/asset/model/card.php?leftmenu=asset_model&amp;action=create", $langs->trans("MenuNewAssetModel"), 2, (!getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'write')) || (getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'model_advance', 'write')));
                        $newmenu->add("/asset/model/list.php?leftmenu=asset_model", $langs->trans("MenuListAssetModels"), 2, (!getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'read')) || (getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('asset', 'model_advance', 'read')));
                    }
                } elseif ((float) DOL_VERSION >= 17.0) {
                    $newmenu->add("/asset/list.php?leftmenu=asset&amp;mainmenu=accountancy", $langs->trans("MenuAssets"), 0, $user->hasRight('asset', 'read'), '', $mainmenu, 'asset');
                    $newmenu->add("/asset/card.php?leftmenu=asset&amp;action=create", $langs->trans("MenuNewAsset"), 1, $user->hasRight('asset', 'write'));
                    $newmenu->add("/asset/list.php?leftmenu=asset&amp;mainmenu=accountancy", $langs->trans("MenuListAssets"), 1, $user->hasRight('asset', 'read'));
                    $newmenu->add("/asset/type.php?leftmenu=asset_type", $langs->trans("MenuTypeAssets"), 1, $user->hasRight('asset', 'read'), '', $mainmenu, 'asset_type');

                    if (!empty($menu_invert)) $leftmenu = 'asset_type';

                    if ($usemenuhider || empty($leftmenu) || preg_match('/asset_type/', $leftmenu)) {
                        $newmenu->add("/asset/type.php?leftmenu=asset_type&amp;action=create", $langs->trans("MenuNewTypeAssets"), 2, $user->hasRight('asset', 'configurer'));
                        $newmenu->add("/asset/type.php?leftmenu=asset_type", $langs->trans("MenuListTypeAssets"), 2, $user->hasRight('asset', 'read'));
                    }
                }
            }
		}

		/*
		 * Menu TOOLS
		 */
		if ($mainmenu == 'tools') {
            if (empty($user->socid)) { // limit to internal users
                $langs->load("mails");
                $newmenu->add("/admin/mails_templates.php?leftmenu=email_templates", $langs->trans("EMailTemplates"), 0, 1, '', $mainmenu, 'email_templates', 0);
            }

            if (isModEnabled('mailing')) {
                $newmenu->add("/comm/mailing/index.php?leftmenu=mailing", $langs->trans("EMailings"), 0, $user->hasRight('mailing', 'lire'), '', $mainmenu, 'mailing', 0);
                $newmenu->add("/comm/mailing/card.php?leftmenu=mailing&amp;action=create", $langs->trans("NewMailing"), 1, $user->hasRight('mailing', 'creer'));
                $newmenu->add("/comm/mailing/list.php?leftmenu=mailing", $langs->trans("List"), 1, $user->hasRight('mailing', 'lire'));
            }

            if (isModEnabled('import')) {
                $langs->load("exports");
                $newmenu->add("/imports/index.php?leftmenu=import", $langs->trans("FormatedImport"), 0, $user->hasRight('import', 'run'), '', $mainmenu, 'import', 0);
                $newmenu->add("/imports/import.php?leftmenu=import", $langs->trans("NewImport"), 1, $user->hasRight('import', 'run'));
            }

            if (isModEnabled('export')) {
                $langs->load("exports");
                $newmenu->add("/exports/index.php?leftmenu=export", $langs->trans("FormatedExport"), 0, $user->hasRight('export', 'lire'), '', $mainmenu, 'export', 0);
                $newmenu->add("/exports/export.php?leftmenu=export", $langs->trans("NewExport"), 1, $user->hasRight('export', 'lire'));
                //$newmenu->add("/exports/export.php?leftmenu=export",$langs->trans("List"),1, $user->hasRight('export', 'lire'));
            }

            if ((float) DOL_VERSION >= 21.0) {
                $newmenu->add("/core/customreports.php?leftmenu=customreports", $langs->trans("CustomReports"), 0, 1, '', $mainmenu, 'customreports', 0, '', '', '', img_picto('', 'graph', 'class="paddingright pictofixedwidth"'));
            }
        }

		/*
		 * Menu MEMBERS
		 */
		if ($mainmenu == 'members') {
			if (isModEnabled('adherent')) {
				// Load translation files required by the page
				$langs->loadLangs(array("members", "compta"));

				$newmenu->add("/adherents/index.php?leftmenu=members&amp;mainmenu=members", $langs->trans("Members"), 0, $user->hasRight('adherent', 'lire'), '', $mainmenu, 'members', 0);
				$newmenu->add("/adherents/card.php?leftmenu=members&amp;action=create", $langs->trans("NewMember"), 1, $user->hasRight('adherent', 'creer'));
				$newmenu->add("/adherents/list.php?leftmenu=members", $langs->trans("List"), 1, $user->hasRight('adherent', 'lire'));
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=-1", $langs->trans("MenuMembersToValidate"), 2, $user->hasRight('adherent', 'lire'));
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1", $langs->trans("MenuMembersValidated"), 2, $user->hasRight('adherent', 'lire'));
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1&amp;filter=withoutsubscription", $langs->trans("WithoutSubscription"), 3, $user->hasRight('adherent', 'lire'));
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1&amp;filter=uptodate", $langs->trans("UpToDate"), 3, $user->hasRight('adherent', 'lire'));
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1&amp;filter=outofdate", $langs->trans("OutOfDate"), 3, $user->hasRight('adherent', 'lire'));
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=0", $langs->trans("MenuMembersResiliated"), 2, $user->hasRight('adherent', 'lire'));
				$newmenu->add("/adherents/stats/index.php?leftmenu=members", $langs->trans("MenuMembersStats"), 1, $user->hasRight('adherent', 'lire'));

				$newmenu->add("/adherents/cartes/carte.php?leftmenu=export", $langs->trans("MembersCards"), 1, $user->hasRight('adherent', 'export'));

				if (! empty($menu_invert)) $leftmenu= '$leftmenu == "none"';

				if (getDolGlobalString('MEMBER_LINK_TO_HTPASSWDFILE') && ($usemenuhider || empty($leftmenu) || $leftmenu == 'none' || $leftmenu == "members" || $leftmenu == "export")) {
					$newmenu->add("/adherents/htpasswd.php?leftmenu=export", $langs->trans("Filehtpasswd"), 1, $user->hasRight('adherent', 'export'));
				}

				if (isModEnabled('categorie')) {
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=3", $langs->trans("Categories"), 1, $user->hasRight('categorie', 'lire'), '', $mainmenu, 'cat');
				}

				$newmenu->add("/adherents/index.php?leftmenu=members&amp;mainmenu=members", $langs->trans("Subscriptions"), 0, $user->hasRight('adherent', 'cotisation', 'lire'), '', $mainmenu, 'members', 0);
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=-1,1&amp;mainmenu=members", $langs->trans("NewSubscription"), 1, $user->hasRight('adherent', 'cotisation', 'creer'));
				$newmenu->add("/adherents/subscription/list.php?leftmenu=members", $langs->trans("List"), 1, $user->hasRight('adherent', 'cotisation', 'lire'));
				$newmenu->add("/adherents/stats/index.php?leftmenu=members", $langs->trans("MenuMembersStats"), 1, $user->hasRight('adherent', 'lire'));

				// Type
				$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members", $langs->trans("MembersTypes"), 0, $user->hasRight('adherent', 'configurer'), '', $mainmenu, 'setup', 0);
				$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members&amp;action=create", $langs->trans("New"), 1, $user->hasRight('adherent', 'configurer'));
				$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members", $langs->trans("List"), 1, $user->hasRight('adherent', 'configurer'));
			}
		}

		// Add personalized menus and modules menus
		$menuArbo = new Menubase($db,'oblyon');
		$newmenu = $menuArbo->menuLeftCharger($newmenu, $mainmenu, $leftmenu, (empty($user->socid) ? 0 : 1), 'oblyon', $tabMenu);

		if (isModEnabled('ftp') && $mainmenu == 'ftp') {	// Entry for FTP
			$MAXFTP = 20;
			$i = 1;
			while ($i <= $MAXFTP) {
				$paramkey = 'FTP_NAME_'.$i;
				//print $paramkey;
				if (getDolGlobalString($paramkey)) {
					$link = "/ftp/index.php?idmenu=".$_SESSION["idmenu"]."&numero_ftp=".$i;
					$newmenu->add($link, dol_trunc(getDolGlobalString($paramkey), 24));
				}
				$i++;
			}
		}

		// We update newmenu for special dynamic menus
		if ($user->hasRight('banque', 'lire') && $mainmenu == 'bank' && getDolGlobalString('OBLYON_ENABLE_MENU_BANK_RECONCILIATE'))	// Entry for each bank account
		{
			require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

			$sql = "SELECT rowid, label, courant, rappro, courant";
			$sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
			$sql.= " WHERE entity = ".$conf->entity;
			$sql.= " AND clos = 0";
			$sql.= " ORDER BY label";

			$resql = $db->query($sql);
			if ($resql) {
				$numr = $db->num_rows($resql);
				$i = 0;

				if ($numr > 0) 	$newmenu->add('/compta/bank/list.php',$langs->trans("BankAccounts"),0,$user->hasRight('banque', 'lire'), '', $mainmenu, 'bank');

				while ($i < $numr)
				{
					$objp = $db->fetch_object($resql);
					$newmenu->add('/compta/bank/card.php?id='.$objp->rowid,$objp->label,1,$user->hasRight('banque', 'lire'));
					if ($objp->rappro && $objp->courant != Account::TYPE_CASH && empty($objp->clos))  // If not cash account and not closed and can be reconciliate
					{
						$newmenu->add('/compta/bank/bankentries_list.php?action=reconcile&contextpage=banktransactionlist-'.$objp->rowid.'&account='.$objp->rowid.'&id='.$objp->rowid.'&search_conciliated=0',$langs->trans("Conciliate"),2,$user->hasRight('banque', 'consolidate'));
					}
					$i++;
				}
			}
			else dol_print_error($db);
			$db->free($resql);
		}

		// FTP
		if (isModEnabled('ftp') && $mainmenu == 'ftp') {
			$MAXFTP=20;
			$i=1;
			while ($i <= $MAXFTP)
			{
				$paramkey='FTP_NAME_'.$i;
				//print $paramkey;
				if (getDolGlobalString($paramkey))
				{
					$link="/ftp/index.php?idmenu=".$_SESSION["idmenu"]."&numero_ftp=".$i;

					$newmenu->add($link, dol_trunc(getDolGlobalString($paramkey), 24));
				}
				$i++;
			}
		} //end FTP

	} //end if ($mainmenu)


	// Build final $menu_array = $menu_array_before +$newmenu->liste + $menu_array_after
	//var_dump($menu_array_before);exit;
	//var_dump($menu_array_after);exit;
	$menu_array = $newmenu->liste;
	if (is_array($menu_array_before)) {
		$menu_array = array_merge($menu_array_before, $menu_array);
	}
	if (is_array($menu_array_after)) {
		$menu_array = array_merge($menu_array, $menu_array_after);
	}
	//var_dump($menu_array);exit;
	if (!is_array($menu_array)) {
		return 0;
	}

    // Allow the $menu_array of the menu to be manipulated by modules
    $parameters = array(
        'mainmenu' => $mainmenu,
    );
    $hook_items = $menu_array;
    $reshook = $hookmanager->executeHooks('menuLeftMenuItems', $parameters, $hook_items); // Note that $action and $object may have been modified by some hooks

    if (is_numeric($reshook)) {
        if ($reshook == 0 && !empty($hookmanager->results)) {
            $menu_array[] = $hookmanager->results; // add
        } elseif ($reshook == 1) {
            $menu_array = $hookmanager->results; // replace
        }
    }

	// TODO Use the position property in menu_array to reorder the $menu_array

	// Show menu
	$invert=empty($menu_invert)?"":"	is-inverted";
	if (empty($noout)) {
	$alt=0;
	$num=count($menu_array);

	print '<nav class="menu_contenu db-nav sec-nav'.$invert.'">'."\n";
	print '<ul class="blockvmenu sec-nav__list">'."\n";

	for ($i = 0; $i < $num; $i++) {
		$showmenu=true;
		$level= $menu_array[$i]['level'];

		if (getDolGlobalString('MAIN_MENU_HIDE_UNAUTHORIZED') && empty($menu_array[$i]['enabled'])) 	$showmenu=false;

		$alt++;

		// Place tabulation
		$tabstring='';
		$tabul=($menu_array[$i]['level'] - 1);
		if ($tabul > 0) {
			for ($j=0; $j < $tabul; $j++) {
				$tabstring.='<span class="caret	'.($langs->trans("DIRECTION")=='ltr'?'caret--left':'caret--right').'"></span> ';
			}
		}

		// For external modules
		$tmp=explode('?',$menu_array[$i]['url'],2);
		$url = $tmp[0];
		$param = (isset($tmp[1])?$tmp[1]:'');	// params in url of the menu link

		// Complete param to force leftmenu to '' to close open menu when we click on a link with no leftmenu defined.
		if ((! preg_match('/mainmenu/i',$param)) && (! preg_match('/leftmenu/i',$param)) && ! empty($menu_array[$i]['mainmenu']))
		{
			$param.=($param?'&':'').'mainmenu='.$menu_array[$i]['mainmenu'].'&leftmenu=';
		}
		if ((! preg_match('/mainmenu/i',$param)) && (! preg_match('/leftmenu/i',$param)) && empty($menu_array[$i]['mainmenu']))
		{
			$param.=($param?'&':'').'leftmenu=';
		}
		$url = dol_buildpath($url,1).($param?'?'.$param:'');

		$url=preg_replace('/__LOGIN__/',$user->login,$url);
		$url=preg_replace('/__USERID__/',$user->id,$url);

		// Level Menu = 0
		if ($level == 0){
			if ($menu_array[$i]['enabled']) {
				print '<li class="menu_titre menu_contenu sec-nav__item item-heading">';
				print '<a class="vmenu sec-nav__link" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>';
				print (!empty($menu_array[$i]['leftmenu'])?'<i class="icon icon--'.$menu_array[$i]['leftmenu'].'"></i>': '').$menu_array[$i]['titre'].' '.(!empty($menu_ivert) && !empty($menu_array[$i+1]['level'])?'<span class="caret	caret--top"></span>':'');
				print '</a>'."\n";
			} else if ($showmenu) {
				print '<li class="sec-nav__item is-disabled"><a class="tmenudisabled sec-nav__link	is-disabled" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$menu_array[$i]['titre'].'</a>'."\n";
			}

			if (!empty($menu_array[$i+1]['level'])) {
				print '<ul class="blockvmenu sec-nav__sub-list">';
			}
		}

		// Menu Level = 1 or 2
		if ($level > 0) {

			if ($menu_array[$i]['enabled']) {
				print '<li class="menu_contenu sec-nav__sub-item item-level'.$menu_array[$i]['level'].'">';
				if ($menu_array[$i]['url']) print '<a class="vsmenu sec-nav__link" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>'.$tabstring;
				print $menu_array[$i]['titre'];
				if ($menu_array[$i]['url']) print '</a>';
			} else if ($showmenu) {
				print '<li class="sec-nav__sub-item	is-disabled"><a class="vsmenu tmenudisabled sec-nav__link is-disabled" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$tabstring. '' .$menu_array[$i]['titre'].'</a>'."\n";
			}

			if ( empty($menu_array[$i+1]['level']) ) {
				print "</ul></li> \n ";
			} else {
			 print "</li> \n ";
			}
		}

	}//end for

	print '</nav>'."\n";

	}
	return count($menu_array);
}


/**
 * Function to test if an entry is enabled or not
 *
 * @param	string		$type_user					0=We need backoffice menu, 1=We need frontoffice menu
 * @param	array		$menuentry					Array for menu entry
 * @param	array		$listofmodulesforexternal	Array with list of modules allowed to external users
 * @return	int										0=Hide, 1=Show, 2=Show gray
 */
function dol_oblyon_showmenu($type_user, &$menuentry, &$listofmodulesforexternal) {
	global $conf;

	if (empty($menuentry['enabled'])) return 0;	// Entry disabled by condition
	if ($type_user && $menuentry['module'])
	{
		$tmploops=explode('|',$menuentry['module']);
		$found=0;
		foreach($tmploops as $tmploop)
		{
			if (in_array($tmploop, $listofmodulesforexternal)) {
				$found++; break;
			}
		}
		if (! $found) return 0;	// Entry is for menus all excluded to external users
	}
	if (! $menuentry['perms'] && $type_user) return 0; 												// No permissions and user is external
	if (! $menuentry['perms'] && getDolGlobalString('MAIN_MENU_HIDE_UNAUTHORIZED'))	return 0;	// No permissions and option to hide when not allowed, even for internal user, is on
	if (! $menuentry['perms']) return 2;															// No permissions and user is external
	return 1;
}
