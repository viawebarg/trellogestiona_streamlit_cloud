<?php
/* Copyright (C) 2016       Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2020       Josep Lluís Amador   <joseplluis@lliuretic.cat>
 * Copyright (C) 2024-2025	MDW					 <mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024-2025  Frédéric France		 <frederic.france@free.fr>
 * Copyright (C) 2024	    Nick Fragoulis
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
 *	\file       htdocs/core/modules/bank/doc/pdf_sepamandate.modules.php
 *	\ingroup    project
 *	\brief      File of class to generate document with template sepamandate
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/bank/modules_bank.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/companybankaccount.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';


/**
 *	Class to generate SEPA mandate
 */

class pdf_sepamandate extends ModeleBankAccountDoc
{
	/**
	 * Dolibarr version of the loaded document
	 * @var string Version, possible values are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'''|'development'|'dolibarr'|'experimental'
	 */
	public $version = 'dolibarr';

	/**
	 * @var int Height reserved to output the info and total part
	 */
	public $heightforinfotot;

	/**
	 * @var int Height reserved to output the free text on last page
	 */
	public $heightforfreetext;

	/**
	 * @var int Height reserved to output the footer (value include bottom margin)
	 */
	public $heightforfooter;

	/**
	 * @var int x coordinate reserved to output the Signature area
	 */
	public $xPosSignArea;
	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs, $mysoc;

		// Translations
		$langs->loadLangs(array("main", "bank", "withdrawals", "companies"));

		$this->db = $db;
		$this->name = "sepamandate";
		$this->description = $langs->transnoentitiesnoconv("DocumentModelSepaMandate");

		// Page size for A4 format
		$this->type = 'pdf';
		$formatarray = pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = $formatarray['height'];
		$this->format = array($this->page_largeur, $this->page_hauteur);
		$this->marge_gauche = getDolGlobalInt('MAIN_PDF_MARGIN_LEFT', 10);
		$this->marge_droite = getDolGlobalInt('MAIN_PDF_MARGIN_RIGHT', 10);
		$this->marge_haute = getDolGlobalInt('MAIN_PDF_MARGIN_TOP', 10);
		$this->marge_basse = getDolGlobalInt('MAIN_PDF_MARGIN_BOTTOM', 10);
		$this->corner_radius = getDolGlobalInt('MAIN_PDF_FRAME_CORNER_RADIUS', 0);
		$this->option_logo = 1; // Display logo FAC_PDF_LOGO
		$this->option_tva = 1; // Manage the vat option FACTURE_TVAOPTION

		// Define column position
		$this->posxref = $this->marge_gauche;

		$this->update_main_doc_field = 1;

		$this->heightforinfotot = 50;

		$this->xPosSignArea = 120;

		$this->heightforfreetext = (getDolGlobalInt('MAIN_PDF_FREETEXT_HEIGHT') > 0 ? getDolGlobalInt('MAIN_PDF_FREETEXT_HEIGHT') : 5);

		$this->heightforfooter = $this->marge_basse + 8;

		if ($mysoc === null) {
			dol_syslog(get_class($this).'::__construct() Global $mysoc should not be null.'. getCallerInfoString(), LOG_ERR);
			return;
		}
		// Retrieves issuer
		$this->emetteur = $mysoc;
		if (!$this->emetteur->country_code) {
			$this->emetteur->country_code = substr($langs->defaultlang, -2); // By default if not defined
		}
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Function to create pdf of company bank account sepa mandate
	 *
	 *	@param	Account					$object				CompanyBankAccount bank account to generate document for
	 *	@param	Translate				$outputlangs		Lang output object
	 *  @param	string					$srctemplatepath	Full path of source filename for generator using a template file
	 *	@param	int<0,1>				$hidedetails		Do not show line details
	 *	@param	int<0,1>				$hidedesc			Do not show desc
	 *	@param	int<0,1>				$hideref			Do not show ref
	 *  @param  ?array<string,string>	$moreparams			More parameters
	 *	@return	int<-1,1>									1 if OK, <=0 if KO
	 */
	public function write_file($object, $outputlangs, $srctemplatepath = '', $hidedetails = 0, $hidedesc = 0, $hideref = 0, $moreparams = null)
	{
		// phpcs:enable
		global $conf, $hookmanager, $langs, $user, $mysoc;

		if (!$object instanceof CompanyBankAccount) {
			dol_syslog(get_class($this)."::write_file object is of type ".get_class($object)." which is not expected", LOG_ERR);
			return -1;
		}

		if (!is_object($outputlangs)) {
			$outputlangs = $langs;
		}
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (getDolGlobalString('MAIN_USE_FPDF')) {
			$outputlangs->charset_output = 'ISO-8859-1';
		}

		// Load translation files required by the page
		$outputlangs->loadLangs(array("main", "dict", "withdrawals", "companies", "projects", "bills"));

		if (!empty($conf->bank->dir_output)) {
			//$nblines = count($object->lines);  // This is set later with array of tasks

			// Definition of $dir and $file
			if ($object->specimen) {
				if (!empty($moreparams['force_dir_output'])) {
					$dir = $moreparams['force_dir_output'];
				} else {
					$dir = $conf->bank->dir_output;
				}
				$file = $dir."/SPECIMEN.pdf";
			} else {
				$objectref = dol_sanitizeFileName($object->ref);
				if (!empty($moreparams['force_dir_output'])) {
					$dir = $moreparams['force_dir_output'];
				} else {
					$dir = $conf->bank->dir_output."/".$objectref;
				}
				$file = $dir."/".$langs->transnoentitiesnoconv("SepaMandateShort").' '.$objectref."-".dol_sanitizeFileName($object->rum).".pdf";
			}

			if (!file_exists($dir)) {
				if (dol_mkdir($dir) < 0) {
					$this->error = $langs->transnoentities("ErrorCanNotCreateDir", $dir);
					return 0;
				}
			}

			if (file_exists($dir)) {
				// Add pdfgeneration hook
				if (!is_object($hookmanager)) {
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager = new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters = array('file' => $file, 'object' => $object, 'outputlangs' => $outputlangs);
				global $action;
				$reshook = $hookmanager->executeHooks('beforePDFCreation', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks

				$pdf = pdf_getInstance($this->format);
				$default_font_size = pdf_getPDFFontSize($outputlangs); // Must be after pdf_getInstance

				if (getDolGlobalString('MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS')) {
					$this->heightforfooter  += 6;
				}
				$pdf->setAutoPageBreak(true, 0);

				if (class_exists('TCPDF')) {
					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);
				}
				$pdf->SetFont(pdf_getPDFFont($outputlangs));

				$pdf->Open();
				$pagenb = 0;
				$pdf->SetDrawColor(128, 128, 128);

				$pdf->SetTitle($outputlangs->convToOutputCharset($object->ref));
				$pdf->SetSubject($outputlangs->transnoentities("SepaMandate"));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("SepaMandate"));
				if (getDolGlobalString('MAIN_DISABLE_PDF_COMPRESSION')) {
					$pdf->SetCompression(false);
				}

				// @phan-suppress-next-line PhanPluginSuspiciousParamOrder
				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite); // Left, Top, Right

				// New page
				$pdf->AddPage();
				$pagenb++;
				$this->_pagehead($pdf, $object, 1, $outputlangs);
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->MultiCell(0, 3, ''); // Set interline to 3
				$pdf->SetTextColor(0, 0, 0);

				$tab_top = 50;
				$tab_top_newpage = 40;

				$tab_height = $this->page_hauteur - $tab_top - $this->heightforfooter  - $this->heightforfreetext ;

				// Show notes
				if (!empty($object->note_public)) {
					$pdf->SetFont('', '', $default_font_size - 1);
					$pdf->writeHTMLCell(190, 3, $this->posxref, $tab_top - 2, dol_htmlentitiesbr($object->note_public), 0, 1);
					$nexY = $pdf->GetY();
					$height_note = $nexY - ($tab_top - 2);

					// Rect takes a length in 3rd parameter
					$pdf->SetDrawColor(192, 192, 192);
					$pdf->RoundedRect($this->marge_gauche, $tab_top - 3, $this->page_largeur - $this->marge_gauche - $this->marge_droite, $height_note + 2, $this->corner_radius, '1234', 'D');

					$tab_height -= $height_note;
					$tab_top = $nexY + 6;
				} else {
					$height_note = 0;
				}

				$iniY = $tab_top + 7;
				$curY = $tab_top + 7;
				$nexY = $tab_top + 7;

				$posY = $curY;

				$pdf->SetFont('', '', $default_font_size - 1);

				$pdf->line($this->marge_gauche, $posY, $this->page_largeur - $this->marge_droite, $posY);
				$posY += 2;

				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("RUMLong").' ('.$outputlangs->transnoentitiesnoconv("RUM").') : '.$object->rum, 0, 'L');

				$posY = $pdf->GetY();
				$posY += 2;
				$pdf->SetXY($this->marge_gauche, $posY);

				$ics = '';
				$idbankfordirectdebit = getDolGlobalInt('PRELEVEMENT_ID_BANKACCOUNT');
				if ($idbankfordirectdebit > 0) {
					$tmpbankfordirectdebit = new Account($this->db);
					$tmpbankfordirectdebit->fetch($idbankfordirectdebit);
					$ics = $tmpbankfordirectdebit->ics;	// ICS for direct debit
				}
				if (empty($ics) && getDolGlobalString('PRELEVEMENT_ICS')) {
					$ics = getDolGlobalString('PRELEVEMENT_ICS');
				}
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("CreditorIdentifier").' ('.$outputlangs->transnoentitiesnoconv("ICS").') : '.$ics, 0, 'L');

				$posY = $pdf->GetY();
				$posY += 1;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("CreditorName").' : '.$mysoc->name, 0, 'L');

				$posY = $pdf->GetY();
				$posY += 1;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("Address").' : ', 0, 'L');
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $mysoc->getFullAddress(1), 0, 'L');

				$posY = $pdf->GetY();
				$posY += 3;

				$pdf->line($this->marge_gauche, $posY, $this->page_largeur - $this->marge_droite, $posY);

				$pdf->SetFont('', '', $default_font_size - 1);

				$posY += 8;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 8, $outputlangs->transnoentitiesnoconv("SEPALegalText", $mysoc->name, $mysoc->name), 0, 'L');

				// Your data form
				$posY = $pdf->GetY();
				$posY += 8;
				$pdf->line($this->marge_gauche, $posY, $this->page_largeur - $this->marge_droite, $posY);
				$posY += 2;

				$pdf->SetFont('', '', $default_font_size - 2);

				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("SEPAFillForm"), 0, 'C');

				$thirdparty = new Societe($this->db);
				if ($object->socid > 0) {
					$thirdparty->fetch($object->socid);
				}

				$sepaname = '______________________________________________';
				if ($thirdparty->id > 0) {
					$sepaname = $thirdparty->name.($object->owner_name ? ' ('.$object->owner_name.')' : '');
				}
				$posY = $pdf->GetY();
				$posY += 3;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("SEPAFormYourName").' * : ', 0, 'L');
				$pdf->SetXY(80, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $sepaname, 0, 'L');

				$sepavatid = '__________________________________________________';
				if (!is_null($thirdparty->idprof1) && !empty($thirdparty->idprof1)) {
					$sepavatid = (string) $thirdparty->idprof1;
				}
				$posY = $pdf->GetY();
				$posY += 1;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv('ProfId1'.$thirdparty->country_code).' * : ', 0, 'L');
				$pdf->SetXY(80, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $sepavatid, 0, 'L');

				$address = '__________________________________________________';
				if (!empty($object->owner_address)) {
					$address = $object->owner_address;
				} elseif ($thirdparty->id > 0) {
					$tmpaddresswithoutcountry = $thirdparty->getFullAddress();	// we test on address without country
					if ($tmpaddresswithoutcountry) {
						$address = $thirdparty->getFullAddress(1);	// full address
					}
				}
				$posY = $pdf->GetY();
				$posY += 1;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("Address").' : ', 0, 'L');
				$pdf->SetXY(80, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $address, 0, 'L');
				if (preg_match('/_____/', $address)) {	// Second line ____ for address
					$posY += 5;
					$pdf->SetXY(80, $posY);
					$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $address, 0, 'L');
				}

				$ban = '__________________________________________________';
				if (!empty($object->iban)) {
					$ban = $object->iban;
				}
				$posY = $pdf->GetY();
				$posY += 1;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("SEPAFormYourBAN").' * : ', 0, 'L');
				$pdf->SetXY(80, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $ban, 0, 'L');

				$bic = '__________________________________________________';
				if (!empty($object->bic)) {
					$bic = $object->bic;
				}
				$posY = $pdf->GetY();
				$posY += 1;
				$pdf->SetXY($this->marge_gauche, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $outputlangs->transnoentitiesnoconv("SEPAFormYourBIC").' * : ', 0, 'L');
				$pdf->SetXY(80, $posY);
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $bic, 0, 'L');


				$posY = $pdf->GetY();
				$posY += 1;
				$pdf->SetXY($this->marge_gauche, $posY);
				$txt = $outputlangs->transnoentitiesnoconv("SEPAFrstOrRecur").' * : ';
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $txt, 0, 'L');
				$pdf->RoundedRect(80, $posY, 5, 5, $this->corner_radius, '1234', 'D');
				$pdf->SetXY(80, $posY);
				if ($object->frstrecur == 'RCUR') {
					$pdf->MultiCell(5, 3, 'X', 0, 'L');
				}
				$pdf->SetXY(86, $posY);
				$txt = $langs->transnoentitiesnoconv("ModeRECUR").'  '.$langs->transnoentitiesnoconv("or");
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $txt, 0, 'L');
				$posY += 6;
				$pdf->RoundedRect(80, $posY, 5, 5, $this->corner_radius, '1234', 'D');
				$pdf->SetXY(80, $posY);
				if ($object->frstrecur == 'FRST') {
					$pdf->MultiCell(5, 3, 'X', 0, 'L');
				}
				$pdf->SetXY(86, $posY);
				$txt = $langs->transnoentitiesnoconv("ModeFRST");
				$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $txt, 0, 'L');
				if (empty($object->frstrecur)) {
					$posY += 6;
					$pdf->SetXY(80, $posY);
					$txt = '('.$langs->transnoentitiesnoconv("PleaseCheckOne").')';
					$pdf->MultiCell($this->page_largeur - $this->marge_gauche - $this->marge_droite, 3, $txt, 0, 'L');
				}

				$posY = $pdf->GetY();
				$posY += 3;
				$pdf->line($this->marge_gauche, $posY, $this->page_largeur - $this->marge_droite, $posY);
				$posY += 3;


				// Show square
				if ($pagenb == 1) {
					$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $this->heightforinfotot  - $this->heightforfreetext  - $this->heightforfooter, 0, $outputlangs, 0, 0);
					$bottomlasttab = $this->page_hauteur - $this->heightforinfotot  - $this->heightforfreetext  - $this->heightforfooter  + 1;
				} else {
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $this->heightforinfotot  - $this->heightforfreetext  - $this->heightforfooter, 0, $outputlangs, 1, 0);
					$bottomlasttab = $this->page_hauteur - $this->heightforinfotot  - $this->heightforfreetext  - $this->heightforfooter  + 1;
				}

				//var_dump($tab_top);
				//var_dump($this->heightforinfotot );
				//var_dump($this->heightforfreetext );
				//var_dump($this->heightforfooter );
				//var_dump($bottomlasttab);

				// Affiche zone infos
				$posy = $this->_tableau_info($pdf, $object, $bottomlasttab, $outputlangs);

				/*
				 * Footer of the page
				 */
				$this->_pagefoot($pdf, $object, $outputlangs);
				if (method_exists($pdf, 'AliasNbPages')) {
					$pdf->AliasNbPages();  // @phan-suppress-current-line PhanUndeclaredMethod
				}

				$pdf->Close();

				$pdf->Output($file, 'F');

				// Add pdfgeneration hook
				if (!is_object($hookmanager)) {
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager = new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters = array('file' => $file, 'object' => $object, 'outputlangs' => $outputlangs);
				global $action;
				$reshook = $hookmanager->executeHooks('afterPDFCreation', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
				if ($reshook < 0) {
					$this->error = $hookmanager->error;
					$this->errors = $hookmanager->errors;
				}

				dolChmod($file);

				$this->result = array('fullpath' => $file);

				return 1; // No error
			} else {
				$this->error = $langs->transnoentities("ErrorCanNotCreateDir", $dir);
				return 0;
			}
		}

		$this->error = $langs->transnoentities("ErrorConstantNotDefined", "DELIVERY_OUTPUTDIR");
		return 0;
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 *   Show table for lines
	 *
	 *   @param		TCPDF		$pdf     		Object PDF
	 *   @param		float		$tab_top		Top position of table
	 *   @param		float		$tab_height		Height of table (rectangle)
	 *   @param		float		$nexY			Y
	 *   @param		Translate	$outputlangs	Langs object
	 *   @param		int			$hidetop		Hide top bar of array
	 *   @param		int			$hidebottom		Hide bottom bar of array
	 *   @return	void
	 */
	protected function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop = 0, $hidebottom = 0)
	{
		// phpcs:enable
		global $conf, $mysoc;

		$default_font_size = pdf_getPDFFontSize($outputlangs);
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *   Show miscellaneous information (payment mode, payment term, ...)
	 *
	 *   @param		TCPDF				$pdf     		Object PDF
	 *   @param		CompanyBankAccount	$object			Object to show
	 *   @param		float				$posy			Y
	 *   @param		Translate			$outputlangs	Langs object
	 *   @return	float
	 */
	protected function _tableau_info(&$pdf, $object, $posy, $outputlangs)
	{
		// phpcs:enable
		global $conf, $mysoc;

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		$diffsizetitle = (!getDolGlobalString('PDF_DIFFSIZE_TITLE') ? 1 : $conf->global->PDF_DIFFSIZE_TITLE);

		$posy += $this->_signature_area($pdf, $object, $posy, $outputlangs);

		$pdf->SetXY($this->marge_gauche, $posy);
		$pdf->SetFont('', '', $default_font_size);
		$pdf->MultiCell(100, 3, $outputlangs->transnoentitiesnoconv("PleaseReturnMandate", $mysoc->email).':', 0, 'L', false);
		$posy = $pdf->GetY() + 2;

		$pdf->SetXY($this->marge_gauche, $posy);
		$pdf->SetFont('', '', $default_font_size - $diffsizetitle);
		$pdf->MultiCell(100, 6, $mysoc->name, 0, 'L', false);
		$pdf->MultiCell(100, 6, $outputlangs->convToOutputCharset($mysoc->getFullAddress(1)), 0, 'L', false);
		$posy = $pdf->GetY() + 2;

		return $posy;
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *	Show area for the customer to sign
	 *
	 *	@param	TCPDF				$pdf           	Object PDF
	 *	@param  CompanyBankAccount	$object         Object invoice
	 *	@param	float				$posy			Position depart
	 *	@param	Translate			$outputlangs	Object langs
	 *	@return float								Position pour suite
	 */
	protected function _signature_area(&$pdf, $object, $posy, $outputlangs)
	{
		// phpcs:enable
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		$tab_top = $posy + 4;
		$tab_hl = 4;

		$posx = $this->marge_gauche;
		$pdf->SetXY($posx, $tab_top);

		$pdf->SetFont('', '', $default_font_size - 2);

		$pdf->MultiCell(100, 3, $outputlangs->transnoentitiesnoconv("DateSigning"), 0, 'L', false);
		$pdf->MultiCell(100, 3, ' ');
		$pdf->MultiCell(100, 3, '______________________', 0, 'L', false);

		$posx = $this->xPosSignArea;
		$largcol = ($this->page_largeur - $this->marge_droite - $posx);

		// Total HT
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetXY($posx, $tab_top);
		$pdf->MultiCell($largcol, $tab_hl, $outputlangs->transnoentitiesnoconv("Signature"), 0, 'L', true);

		$pdf->SetXY($posx, $tab_top + $tab_hl);
		//$pdf->MultiCell($largcol, $tab_hl * 3, '', 1, 'R');
		$pdf->RoundedRect($posx, $tab_top + $tab_hl + 3, $largcol, $tab_hl * 3, $this->corner_radius, '1234', 'D');

		return ($tab_hl * 7);
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 *  Show top header of page.
	 *
	 *  @param	TCPDF				$pdf     		Object PDF
	 *  @param  CompanyBankAccount	$object     	Object to show
	 *  @param  int	    			$showaddress    0=no, 1=yes
	 *  @param  Translate			$outputlangs	Object lang for output
	 *  @return	float|int                   		Return topshift value
	 */
	protected function _pagehead(&$pdf, $object, $showaddress, $outputlangs)
	{
		// phpcs:enable
		global $langs, $conf, $mysoc;

		$default_font_size = pdf_getPDFFontSize($outputlangs);

		pdf_pagehead($pdf, $outputlangs, $this->page_hauteur);

		$pdf->SetTextColor(0, 0, 60);
		$pdf->SetFont('', 'B', $default_font_size + 3);

		$posx = $this->page_largeur - $this->marge_droite - 100;
		$posy = $this->marge_haute;

		$pdf->SetXY($this->marge_gauche, $posy);

		// Logo
		$logo = $conf->mycompany->dir_output.'/logos/'.$mysoc->logo;
		if ($mysoc->logo) {
			if (is_readable($logo)) {
				$height = pdf_getHeightForLogo($logo);
				$pdf->Image($logo, $this->marge_gauche, $posy, 0, $height); // width=0 (auto)
			} else {
				$pdf->SetTextColor(200, 0, 0);
				$pdf->SetFont('', 'B', $default_font_size - 2);
				$pdf->MultiCell(100, 3, $langs->transnoentities("ErrorLogoFileNotFound", $logo), 0, 'L');
				$pdf->MultiCell(100, 3, $langs->transnoentities("ErrorGoToModuleSetup"), 0, 'L');
			}
		} else {
			$pdf->MultiCell(100, 4, $outputlangs->transnoentities($this->emetteur->name), 0, 'L');
		}

		$pdf->SetFont('', 'B', $default_font_size + 3);
		$pdf->SetXY($posx, $posy);
		$pdf->SetTextColor(0, 0, 60);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("SepaMandate"), '', 'R');
		$pdf->SetFont('', '', $default_font_size + 2);

		$posy += 6;
		$pdf->SetXY($posx, $posy);
		$pdf->SetTextColor(0, 0, 60);
		$daterum = '__________________';
		if (!empty($object->date_rum)) {
			$daterum = dol_print_date($object->date_rum, 'day', false, $outputlangs, true);
		} else {
			$daterum = dol_print_date($object->datec, 'day', false, $outputlangs, true); // For old record, the date_rum was not saved.
		}
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Date")." : ".$daterum, '', 'R');
		/*$posy+=6;
		$pdf->SetXY($posx,$posy);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("DateEnd")." : " . dol_print_date($object->date_end,'day',false,$outputlangs,true), '', 'R');
		*/

		$pdf->SetTextColor(0, 0, 60);

		// Add list of linked objects
		/* Removed: A project can have more than thousands linked objects (orders, invoices, proposals, etc....
		$object->fetchObjectLinked();

		foreach($object->linkedObjects as $objecttype => $objects)
		{
			var_dump($objects);exit;
			if ($objecttype == 'commande')
			{
				$outputlangs->load('orders');
				$num=count($objects);
				for ($i=0;$i<$num;$i++)
				{
					$posy+=4;
					$pdf->SetXY($posx,$posy);
					$pdf->SetFont('','', $default_font_size - 1);
					$text=$objects[$i]->ref;
					if ($objects[$i]->ref_client) $text.=' ('.$objects[$i]->ref_client.')';
					$pdf->MultiCell(100, 4, $outputlangs->transnoentities("RefOrder")." : ".$outputlangs->transnoentities($text), '', 'R');
				}
			}
		}
		*/

		return 0;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 *	Show footer of page. Need this->emetteur object
	 *
	 *	@param	TCPDF				$pdf     			PDF
	 * 	@param	CompanyBankAccount	$object				Object to show
	 * 	@param	Translate			$outputlangs		Object lang for output
	 *	@param	int					$hidefreetext		1=Hide free text
	 *	@return	integer
	 */
	protected function _pagefoot(&$pdf, $object, $outputlangs, $hidefreetext = 0)
	{
		// phpcs:enable
		$showdetails = getDolGlobalInt('MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS', 0);
		return pdf_pagefoot($pdf, $outputlangs, 'PAYMENTORDER_FREE_TEXT', null, $this->marge_basse, $this->marge_gauche, $this->page_hauteur, $object, $showdetails, $hidefreetext);
	}
}
