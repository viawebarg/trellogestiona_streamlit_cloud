<?php
/* Copyright (C) 2003 		Steve Dillon
 * Copyright (C) 2003 		Laurent Passebecq
 * Copyright (C) 2001-2003 	Rodolphe Quiedeville 	<rodolphe@quiedeville.org>
 * Copyright (C) 2002-2003 	Jean-Louis Bergamo		<jlb@j1b.org>
 * Copyright (C) 2006-2013 	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2015 		Francis Appels  		<francis.appels@yahoo.com>
 * Copyright (C) 2024		Frédéric France			<frederic.france@free.fr>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file		htdocs/core/modules/member/doc/pdf_standard_member.class.php
 *	\ingroup	member
 *	\brief		File of class to generate PDF document of labels
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonstickergenerator.class.php';

/**
 *	Class to generate stick sheet with format Avery or other personalised
 */
class pdf_standard_member extends CommonStickerGenerator
{
	/**
	 * Dolibarr version of the loaded document
	 * @var string Version, possible values are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'''|'development'|'dolibarr'|'experimental'
	 */
	public $version = 'dolibarr';


	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $langs;

		// Translations
		$langs->loadLangs(array("main", "admin"));

		$this->db = $db;
		$this->name = "standard";
		$this->description = $langs->trans('TemplateforBusinessCards');
		//$this->update_main_doc_field = 1; // Save the name of generated file as the main doc when generating a doc with this template

		$this->type = 'pdf-various-sizes';
	}

	/**
	 * Output a sticker on page at position _COUNTX, _COUNTY (_COUNTX and _COUNTY start from 0)
	 *
	 * @param	TCPDF		$pdf			PDF reference
	 * @param	Translate	$outputlangs	Output langs
	 * @param	array<string,string>	$param		Associative array containing label content and optional parameters
	 * @return	void
	 */
	public function addSticker(&$pdf, $outputlangs, $param)
	{
		// use this method in future refactoring
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Output a sticker on page at position _COUNTX, _COUNTY (_COUNTX and _COUNTY start from 0)
	 * - __LOGO__ is replace with company logo
	 * - __PHOTO__ is replace with photo provided as parameter
	 *
	 * @param	 TCPDF		$pdf			PDF
	 * @param	 string		$textleft		Text left
	 * @param	 string		$header			Header
	 * @param	 string		$footer			Footer
	 * @param	 Translate	$outputlangs	Output langs
	 * @param	 string		$textright		Text right
	 * @param	 int		$idmember		Id member
	 * @param	 string		$photo			Photo (full path to image file used as replacement for key __PHOTOS__ into left, right, header or footer text)
	 * @return	 void
	 */
	public function Add_PDF_card(&$pdf, $textleft, $header, $footer, $outputlangs, $textright = '', $idmember = 0, $photo = '')
	{
		// phpcs:enable
		global $db, $mysoc, $conf, $langs;
		global $forceimgscalewidth, $forceimgscaleheight;

		$imgscalewidth = (empty($forceimgscalewidth) ? 0.3 : $forceimgscalewidth); // Scale of image for width (1=Full width of sticker)
		$imgscaleheight = (empty($forceimgscalewidth) ? 0.5 : $forceimgscalewidth); // Scale of image for height (1=Full height of sticker)

		// We are in a new page, then we must add a page
		if (($this->_COUNTX == 0) && ($this->_COUNTY == 0) and (!$this->_First == 1)) {
			$pdf->AddPage();
		}
		$this->_First = 0;
		$_PosX = $this->_Margin_Left + ($this->_COUNTX * ($this->_Width + $this->_X_Space));
		$_PosY = $this->_Margin_Top + ($this->_COUNTY * ($this->_Height + $this->_Y_Space));

		// Define logo
		$logo = $conf->mycompany->dir_output.'/logos/'.$mysoc->logo;
		if (!is_readable($logo)) {
			$logo = '';
			if (!empty($mysoc->logo_small) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small)) {
				$logo = $conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_small;
			} elseif (!empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo)) {
				$logo = $conf->mycompany->dir_output.'/logos/'.$mysoc->logo;
			}
		}

		$member = new Adherent($db);
		$member->id = $idmember;
		$member->ref = (string) $idmember;

		// Define photo
		$dir = $conf->adherent->dir_output;
		if (!empty($photo)) {
			$file = get_exdir(0, 0, 0, 0, $member, 'member').'photos/'.$photo;
			$photo = $dir.'/'.$file;
			if (!is_readable($photo)) {
				$photo = '';
			}
		}

		// Define background image
		$backgroundimage = '';
		if (getDolGlobalString('ADHERENT_CARD_BACKGROUND') && file_exists($conf->adherent->dir_output.'/' . getDolGlobalString('ADHERENT_CARD_BACKGROUND'))) {
			$backgroundimage = $conf->adherent->dir_output.'/' . getDolGlobalString('ADHERENT_CARD_BACKGROUND');
		}

		// Print lines
		if ($this->code == "CARD") {
			$this->Tformat = $this->_Avery_Labels["CARD"];
			//$this->_Pointille($pdf,$_PosX,$_PosY,$_PosX+$this->_Width,$_PosY+$this->_Height,0.3,25);
			$this->_Croix($pdf, $_PosX, $_PosY, $_PosX + $this->_Width, $_PosY + $this->_Height, 0.1, 10);
		}

		// Background
		if ($backgroundimage) {
			$pdf->image($backgroundimage, $_PosX, $_PosY, $this->_Width, $this->_Height);
		}

		$xleft = 2;
		$ytop = 2;

		// Top
		if ($header != '') {
			if ($this->code == "CARD") {
				$pdf->SetDrawColor(128, 128, 128);
				$pdf->Line($_PosX, $_PosY + $this->_Line_Height + 1, $_PosX + $this->_Width, $_PosY + $this->_Line_Height + 1); // Only 1 mm and not ytop for top text
				$pdf->SetDrawColor(0, 0, 0);
			}
			$pdf->SetXY($_PosX + $xleft, $_PosY + 1); // Only 1 mm and not ytop for top text
			$pdf->Cell($this->_Width - 2 * $xleft, $this->_Line_Height, $outputlangs->convToOutputCharset($header), 0, 1, 'C');
		}


		$ytop += (empty($header) ? 0 : (1 + $this->_Line_Height));

		// Define widthtouse and heighttouse
		$maxwidthtouse = round(($this->_Width - 2 * $xleft) * $imgscalewidth);
		$maxheighttouse = round(($this->_Height - 2 * $ytop) * $imgscaleheight);
		$defaultratio = ($maxwidthtouse / $maxheighttouse);
		$widthtouse = $maxwidthtouse;
		$heighttouse = 0; // old value for image
		$tmp = dol_getImageSize($photo, false);
		if (isset($tmp['height'])) {
			$imgratio = $tmp['width'] / $tmp['height'];
			if ($imgratio >= $defaultratio) {
				$widthtouse = $maxwidthtouse;
				$heighttouse = round($widthtouse / $imgratio);
			} else {
				$heighttouse = $maxheighttouse;
				$widthtouse = round($heighttouse * $imgratio);
			}
		}
		//var_dump($this->_Width.'x'.$this->_Height.' with border and scale '.$imgscale.' => max '.$maxwidthtouse.'x'.$maxheighttouse.' => We use '.$widthtouse.'x'.$heighttouse);exit;

		// Center
		if ($textright == '') {	// Only a left part
			// Output left area
			if ($textleft == '__LOGO__' && $logo) {
				$pdf->Image($logo, $_PosX + $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
			} elseif ($textleft == '__PHOTO__' && $photo) {
				$pdf->Image($photo, $_PosX + $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
			} else {
				$pdf->SetXY($_PosX + $xleft, $_PosY + $ytop);
				$pdf->MultiCell($this->_Width, $this->_Line_Height, $outputlangs->convToOutputCharset($textleft), 0, 'L');
			}
		} elseif ($textleft != '' && $textright != '') {	//
			if ($textleft == '__LOGO__' || $textleft == '__PHOTO__') {
				if ($textleft == '__LOGO__' && $logo) {
					$pdf->Image($logo, $_PosX + $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
				} elseif ($textleft == '__PHOTO__' && $photo) {
					$pdf->Image($photo, $_PosX + $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
				}
				$pdf->SetXY($_PosX + $xleft + $widthtouse + 1, $_PosY + $ytop);
				$pdf->MultiCell($this->_Width - $xleft - $xleft - $widthtouse - 1, $this->_Line_Height, $outputlangs->convToOutputCharset($textright), 0, 'R');
			} elseif ($textright == '__LOGO__' || $textright == '__PHOTO__') {
				if ($textright == '__LOGO__' && $logo) {
					$pdf->Image($logo, $_PosX + $this->_Width - $widthtouse - $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
				} elseif ($textright == '__PHOTO__' && $photo) {
					$pdf->Image($photo, $_PosX + $this->_Width - $widthtouse - $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
				}
				$pdf->SetXY($_PosX + $xleft, $_PosY + $ytop);
				$pdf->MultiCell($this->_Width - $widthtouse - $xleft - $xleft - 1, $this->_Line_Height, $outputlangs->convToOutputCharset($textleft), 0, 'L');
			} else { // text on halft left and text on half right
				$pdf->SetXY($_PosX + $xleft, $_PosY + $ytop);
				$pdf->MultiCell(round($this->_Width / 2), $this->_Line_Height, $outputlangs->convToOutputCharset($textleft), 0, 'L');
				$pdf->SetXY($_PosX + round($this->_Width / 2), $_PosY + $ytop);
				$pdf->MultiCell(round($this->_Width / 2) - 2, $this->_Line_Height, $outputlangs->convToOutputCharset($textright), 0, 'R');
			}
		} else { // Only a right part
			// Output right area
			if ($textright == '__LOGO__' && $logo) {
				$pdf->Image($logo, $_PosX + $this->_Width - $widthtouse - $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
			} elseif ($textright == '__PHOTO__' && $photo) {
				$pdf->Image($photo, $_PosX + $this->_Width - $widthtouse - $xleft, $_PosY + $ytop, $widthtouse, $heighttouse);
			} else {
				$pdf->SetXY($_PosX + $xleft, $_PosY + $ytop);
				$pdf->MultiCell($this->_Width - $xleft, $this->_Line_Height, $outputlangs->convToOutputCharset($textright), 0, 'R');
			}
		}

		// Bottom
		if ($footer != '') {
			if ($this->code == "CARD") {
				$pdf->SetDrawColor(128, 128, 128);
				$pdf->Line($_PosX, $_PosY + $this->_Height - $this->_Line_Height - 2, $_PosX + $this->_Width, $_PosY + $this->_Height - $this->_Line_Height - 2);
				$pdf->SetDrawColor(0, 0, 0);
			}
			$pdf->SetXY($_PosX, $_PosY + $this->_Height - $this->_Line_Height - 1);
			$pdf->Cell($this->_Width, $this->_Line_Height, $outputlangs->convToOutputCharset($footer), 0, 1, 'C');
		}
		//print "$_PosY+$this->_Height-$this->_Line_Height-1<br>\n";

		$this->_COUNTY++;

		if ($this->_COUNTY == $this->_Y_Number) {
			// Si on est en bas de page, on remonte le 'curseur' de position
			$this->_COUNTX++;
			$this->_COUNTY = 0;
		}

		if ($this->_COUNTX == $this->_X_Number) {
			// Si on est en bout de page, alors on repart sur une nouvelle page
			$this->_COUNTX = 0;
			$this->_COUNTY = 0;
		}
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Function to build PDF on disk, then output on HTTP stream.
	 *
	 *  @param  Adherent|array<array{textleft:string,textheader:string,textfooter:string,textright:string,id:string,photo:string}>   $object     Array of record information (array('textleft'=>,'textheader'=>, ..., 'id'=>,'photo'=>)
	 *  @param  Translate	$outputlangs		Lang object for output language
	 *  @param  string		$srctemplatepath	file. Example: '5161', 'AVERYC32010', 'CARD', ...
	 *  @param  string		$mode				Tell if doc module is called
	 *  @param  string		$nooutput			1=Generate only file on disk and do not return it on response // TODO: Fix not compatible parameter signature.
	 *  @param  string		$filename			Name of output file (without extension)
	 *  @return int<-1,1>                       1=OK, <=0=KO
	 */
	public function write_file($object, $outputlangs, $srctemplatepath, $mode = 'member', $nooutput = '', $filename = 'tmp_cards')
	{
		// phpcs:enable
		global $user, $conf, $langs, $mysoc, $_Avery_Labels;

		$this->code = $srctemplatepath;

		if (is_object($object)) {
			if ($object->country == '-') {
				$object->country = '';
			}

			$now = dol_now();
			$year = dol_print_date($now, '%Y');
			$month = dol_print_date($now, '%m');
			$day = dol_print_date($now, '%d');

			// List of values to scan for a replacement
			$substitutionarray = array(
				'__ID__' => (string) $object->id,
				'__REF__' => $object->ref,
				'__LOGIN__' => empty($object->login) ? '' : $object->login,
				'__FIRSTNAME__' => empty($object->firstname) ? '' : $object->firstname,
				'__LASTNAME__' => empty($object->lastname) ? '' : $object->lastname,
				'__FULLNAME__' => $object->getFullName($langs),
				'__COMPANY__' => empty($object->company) ? '' : $object->company,
				'__ADDRESS__' => empty($object->address) ? '' : $object->address,
				'__ZIP__' => empty($object->zip) ? '' : $object->zip,
				'__TOWN__' => empty($object->town) ? '' : $object->town,
				'__COUNTRY__' => empty($object->country) ? '' : $object->country,
				'__COUNTRY_CODE__' => empty($object->country_code) ? '' : $object->country_code,
				'__EMAIL__' => empty($object->email) ? '' : $object->email,
				'__BIRTH__' => dol_print_date($object->birth, 'day'),
				'__TYPE__' => empty($object->type) ? '' : $object->type,
				'__YEAR__' => $year,
				'__MONTH__' => $month,
				'__DAY__' => $day,
				'__DOL_MAIN_URL_ROOT__' => (string) DOL_MAIN_URL_ROOT,
				'__SERVER__' => "https://".$_SERVER["SERVER_NAME"]."/"
			);
			complete_substitutions_array($substitutionarray, $langs);

			// For business cards
			$textleft = make_substitutions(getDolGlobalString("ADHERENT_CARD_TEXT"), $substitutionarray);
			$textheader = make_substitutions(getDolGlobalString("ADHERENT_CARD_HEADER_TEXT"), $substitutionarray);
			$textfooter = make_substitutions(getDolGlobalString("ADHERENT_CARD_FOOTER_TEXT"), $substitutionarray);
			$textright = make_substitutions(getDolGlobalString("ADHERENT_CARD_TEXT_RIGHT"), $substitutionarray);

			$nb = $_Avery_Labels[$this->code]['NX'] * $_Avery_Labels[$this->code]['NY'];
			if ($nb <= 0) {
				$nb = 1; // Protection to avoid empty page
			}

			$arrayofmembers = array();
			for ($j = 0; $j < $nb; $j++) {
				$arrayofmembers[] = array(
					'textleft' => $textleft,
					'textheader' => $textheader,
					'textfooter' => $textfooter,
					'textright' => $textright,
					'id' => (isset($object->id) ? $object->id : ""),
					'photo' => (isset($object->photo) ? $object->photo : "")
				);
			}

			$arrayofrecords = $arrayofmembers;
		} else {
			// Old usage
			$arrayofrecords = $object;
		}

		$this->Tformat = $_Avery_Labels[$this->code];
		if (empty($this->Tformat)) {
			dol_print_error(null, 'ErrorBadTypeForCard'.$this->code);
			exit;
		}

		$this->type = 'pdf';
		$filename .= '.pdf';

		// standard format or custom
		$paper_size = $this->Tformat['paper-size'];
		if (!is_string($paper_size) || $paper_size != 'custom') {
			$this->format = $paper_size;
		} else {
			//custom
			$resolution = array($this->Tformat['custom_x'], $this->Tformat['custom_y']);
			$this->format = $resolution;
		}

		if (!is_object($outputlangs)) {
			$outputlangs = $langs;
		}
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (getDolGlobalString('MAIN_USE_FPDF')) {
			$outputlangs->charset_output = 'ISO-8859-1';
		}

		// Load traductions files required by page
		$outputlangs->loadLangs(array("main", "dict", "companies", "admin", "members"));

		if (empty($mode) || $mode == 'member') {
			$title = $outputlangs->transnoentities('MembersCards');
			$keywords = $outputlangs->transnoentities('MembersCards')." ".$outputlangs->transnoentities("Foundation")." ".$outputlangs->convToOutputCharset($mysoc->name);
		} else {
			dol_print_error(null, 'Bad value for $mode');
			return -1;
		}


		if (is_object($object)) {
			$outputdir = $conf->adherent->dir_output;
			$dir = $outputdir."/".get_exdir(0, 0, 0, 0, $object, 'member');
			$file = $dir.'/'.$filename;
		} else {
			$outputdir = $conf->adherent->dir_temp;
			$dir = $outputdir;
			$file = $dir.'/'.$filename;
		}

		//var_dump($file);exit;

		if (!file_exists($dir)) {
			if (dol_mkdir($dir) < 0) {
				$this->error = $langs->trans("ErrorCanNotCreateDir", $dir);
				return 0;
			}
		}

		$pdf = pdf_getInstance($this->format, $this->Tformat['metric'], $this->Tformat['orientation']);

		if (class_exists('TCPDF')) {
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
		}
		$pdf->SetFont(pdf_getPDFFont($outputlangs));

		$pdf->SetTitle($title);
		$pdf->SetSubject($title);
		$pdf->SetCreator("Dolibarr ".DOL_VERSION);
		$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
		$pdf->SetKeyWords($keywords);
		if (getDolGlobalString('MAIN_DISABLE_PDF_COMPRESSION')) {
			$pdf->SetCompression(false);
		}

		$pdf->SetMargins(0, 0);
		$pdf->setAutoPageBreak(false);

		$this->_Metric_Doc = $this->Tformat['metric'];
		// Permet de commencer l'impression de l'etiquette desiree dans le cas ou la page a deja service
		$posX = 1;
		$posY = 1;
		if ($posX > 0) {
			$posX--;
		} else {
			$posX = 0;
		}
		if ($posY > 0) {
			$posY--;
		} else {
			$posY = 0;
		}
		$this->_COUNTX = $posX;
		$this->_COUNTY = $posY;
		$this->_Set_Format($pdf, $this->Tformat);


		$pdf->Open();
		$pdf->AddPage();


		// Add each record
		foreach ($arrayofrecords as $val) {
			// imprime le texte specifique sur la carte
			$this->Add_PDF_card($pdf, $val['textleft'], $val['textheader'], $val['textfooter'], $langs, $val['textright'], $val['id'], $val['photo']);
		}

		//$pdf->SetXY(10, 295);
		//$pdf->Cell($this->_Width, $this->_Line_Height, 'XXX',0,1,'C');


		// Output to file
		$pdf->Output($file, 'F');

		dolChmod($file);

		$this->result = array('fullpath' => $file);

		// Output to http stream
		if (empty($nooutput)) {
			clearstatcache();

			$attachment = true;
			if (getDolGlobalString('MAIN_DISABLE_FORCE_SAVEAS')) {
				$attachment = false;
			}
			$type = dol_mimetype($filename);

			//if ($encoding)   header('Content-Encoding: '.$encoding);
			if ($type) {
				header('Content-Type: '.$type);
			}
			if ($attachment) {
				header('Content-Disposition: attachment; filename="'.$filename.'"');
			} else {
				header('Content-Disposition: inline; filename="'.$filename.'"');
			}

			// Ajout directives pour resoudre bug IE
			header('Cache-Control: Public, must-revalidate');
			header('Pragma: public');

			readfile($file);
		}

		return 1;
	}
}
