<?php
/* Copyright (C) 2003-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2024-2025	MDW					<mdeweerd@users.noreply.github.com>
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
 *	    \file       htdocs/core/modules/mailings/modules_mailings.php
 *		\ingroup    mailing
 *		\brief      File with parent class of emailing target selectors modules
 */
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';


/**
 *		Parent class of emailing target selectors modules
 */
class MailingTargets // This can't be abstract as it is used for some method
{
	/**
	 * @var DoliDB		Database handler (result of a new DoliDB)
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var string[] of errors
	 */
	public $errors;

	/**
	 * @var string	Condition to be enabled
	 */
	public $enabled;

	/**
	 * @var string Name of the module
	 */
	public $name;

	/**
	 * @var string Description of the module
	 */
	public $desc;

	/**
	 * @var string Tooltip to show after description of the module
	 */
	public $tooltip = '';

	/**
	 * @var string To store the SQL string used to find the recipients
	 */
	public $sql;


	/**
	 * @var int<0,1>	Set this to 1 if you want to flag you also want to include email in target that has opt-out.
	 */
	public $evenunsubscribe = 0;


	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Return description of email selector
	 *
	 * @return     string      Return translation of module label. Try translation of $this->name then translation of 'MailingModuleDesc'.$this->name, or $this->desc if not found
	 */
	public function getDesc()
	{
		global $langs, $form;

		$langs->load("mails");
		$transstring = "MailingModuleDesc".$this->name;
		$s = '';

		if ($langs->trans($this->name) != $this->name) {
			$s = $langs->trans($this->name);
		} elseif ($langs->trans($transstring) != $transstring) {
			$s = $langs->trans($transstring);
		} else {
			$s = $this->desc;
		}

		if ($this->tooltip && is_object($form)) {
			$s .= ' '.$form->textwithpicto('', $langs->trans($this->tooltip), 1, 'help');
		}
		return $s;
	}

	/**
	 *	Return number of records for email selector
	 *
	 *  @return     integer      Example
	 */
	public function getNbOfRecords()
	{
		return 0;
	}

	/**
	 * Retourne nombre de destinataires
	 *
	 * @param      string		$sql        Sql request to count
	 * @return     int|string      			Nb of recipient, or <0 if error, or '' if NA
	 */
	public function getNbOfRecipients($sql)
	{
		$result = $this->db->query($sql);
		if ($result) {
			$total = 0;
			while ($obj = $this->db->fetch_object($result)) {
				$total += $obj->nb;
			}
			return $total;
		} else {
			$this->error = $this->db->lasterror();
			return -1;
		}
	}

	/**
	 * Affiche formulaire de filtre qui apparait dans page de selection
	 * des destinataires de mailings
	 *
	 * @return     string      Retourne zone select
	 */
	public function formFilter()
	{
		return '';
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 * Met a jour nombre de destinataires
	 *
	 * @param	int		$mailing_id          Id of emailing
	 * @return  int			                 Return integer < 0 si erreur, nb destinataires si ok
	 */
	public function update_nb($mailing_id)
	{
		// phpcs:enable
		// Mise a jour nombre de destinataire dans table des mailings
		$sql = "SELECT COUNT(*) nb FROM ".$this->db->prefix()."mailing_cibles";
		$sql .= " WHERE fk_mailing = ".((int) $mailing_id);
		$result = $this->db->query($sql);
		if ($result) {
			$obj = $this->db->fetch_object($result);
			$nb = $obj->nb;

			$sql = "UPDATE ".$this->db->prefix()."mailing";
			$sql .= " SET nbemail = ".((int) $nb)." WHERE rowid = ".((int) $mailing_id);
			if (!$this->db->query($sql)) {
				dol_syslog($this->db->error());
				$this->error = $this->db->error();
				return -1;
			}
		} else {
			return -1;
		}
		return $nb;
	}

	/**
	 * Add a list of targets into the database
	 *
	 * @param	int		$mailing_id    Id of emailing
	 * @param	array<array{fk_contact?:int,lastname:string,firstname:string,email:string,other:string,source_url:string,source_id?:int,source_type:string,id?:int}>		$cibles		Array with targets
	 * @return  int      			   Return integer < 0 if error, nb added if OK
	 */
	public function addTargetsToDatabase($mailing_id, $cibles)
	{
		global $conf;

		$this->db->begin();


		// Insert emailing targets from array into database
		$j = 0;
		$num = count($cibles);
		foreach ($cibles as $targetarray) {
			if (!empty($targetarray['email'])) { // avoid empty email address
				$sql = "INSERT INTO ".$this->db->prefix()."mailing_cibles";
				$sql .= " (fk_mailing,";
				$sql .= " fk_contact,";
				$sql .= " lastname, firstname, email, other, source_url, source_id,";
				$sql .= " tag,";
				$sql .= " source_type)";
				$sql .= " VALUES (".((int) $mailing_id).",";
				$sql .= (empty($targetarray['fk_contact']) ? '0' : (int) $targetarray['fk_contact']).",";
				$sql .= "'".$this->db->escape($targetarray['lastname'])."',";
				$sql .= "'".$this->db->escape($targetarray['firstname'])."',";
				$sql .= "'".$this->db->escape($targetarray['email'])."',";
				$sql .= "'".$this->db->escape($targetarray['other'])."',";
				$sql .= "'".$this->db->escape($targetarray['source_url'])."',";
				$sql .= (empty($targetarray['source_id']) ? 'null' : (int) $targetarray['source_id']).",";
				$sql .= "'".$this->db->escape(dol_hash($conf->file->instance_unique_id.";".$targetarray['email'].";".$targetarray['lastname'].";".((int) $mailing_id).";".getDolGlobalString('MAILING_EMAIL_UNSUBSCRIBE_KEY'), 'md5'))."',";
				$sql .= "'".$this->db->escape($targetarray['source_type'])."')";
				dol_syslog(__METHOD__, LOG_DEBUG);
				$result = $this->db->query($sql);
				if ($result) {
					$j++;
				} else {
					if ($this->db->errno() != 'DB_ERROR_RECORD_ALREADY_EXISTS') {
						// Si erreur autre que doublon
						dol_syslog($this->db->error().' : '.$targetarray['email']);
						$this->error = $this->db->error().' : '.$targetarray['email'];
						$this->db->rollback();
						return -1;
					}
				}
			}
		}

		dol_syslog(__METHOD__.": mailing ".$j." targets added");

		/*
		//Update the status to show thirdparty mail that don't want to be contacted anymore'
		$sql = "UPDATE ".$this->db->prefix()."mailing_cibles";
		$sql .= " SET statut=3";
		$sql .= " WHERE fk_mailing = ".((int) $mailing_id)." AND email in (SELECT email FROM ".$this->db->prefix()."societe where fk_stcomm=-1)";
		$sql .= " AND source_type='thirdparty'";
		dol_syslog(__METHOD__.": mailing update status to display thirdparty mail that do not want to be contacted");
		$result=$this->db->query($sql);

		//Update the status to show contact mail that don't want to be contacted anymore'
		$sql = "UPDATE ".$this->db->prefix()."mailing_cibles";
		$sql .= " SET statut=3";
		$sql .= " WHERE fk_mailing = ".((int) $mailing_id)." AND source_type='contact' AND (email in (SELECT sc.email FROM ".$this->db->prefix()."socpeople AS sc ";
		$sql .= " INNER JOIN ".$this->db->prefix()."societe s ON s.rowid=sc.fk_soc WHERE s.fk_stcomm=-1 OR no_email=1))";
		dol_syslog(__METHOD__.": mailing update status to display contact mail that do not want to be contacted",LOG_DEBUG);
		$result=$this->db->query($sql);
		*/

		if (empty($this->evenunsubscribe)) {
			$sql = "UPDATE ".$this->db->prefix()."mailing_cibles as mc";
			$sql .= " SET statut = 3";
			$sql .= " WHERE fk_mailing = ".((int) $mailing_id);
			$sql .= " AND EXISTS (SELECT rowid FROM ".$this->db->prefix()."mailing_unsubscribe as mu WHERE mu.email = mc.email and mu.entity = ".((int) $conf->entity).")";

			dol_syslog(__METHOD__.":mailing update status to display emails that do not want to be contacted anymore", LOG_DEBUG);
			$result = $this->db->query($sql);
			if (!$result) {
				dol_print_error($this->db);
			}
		}

		// Update nb of recipient into emailing record
		$this->update_nb($mailing_id);

		$this->db->commit();

		return $j;
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Supprime tous les destinataires de la table des cibles
	 *
	 *  @param  int		$mailing_id        Id of emailing
	 *  @return	void
	 */
	public function clear_target($mailing_id)
	{
		// phpcs:enable
		$sql = "DELETE FROM ".$this->db->prefix()."mailing_cibles";
		$sql .= " WHERE fk_mailing = ".((int) $mailing_id);

		if (!$this->db->query($sql)) {
			dol_syslog($this->db->error());
		}

		$this->update_nb($mailing_id);
	}


	/**
	 *  Return list of widget. Function used by admin page htdoc/admin/widget.
	 *  List is sorted by widget filename so by priority to run.
	 *
	 *  @param	?array<string>	$forcedir	null=All default directories. This parameter is used by modulebuilder module only.
	 *  @return array<array{picto:string,file:string,fullpath:string,relpath:string,iscoreorexternal:'external'|'internal',version:string,status:string,info:string}>	Array list of widgets
	 */
	public static function getEmailingSelectorsList($forcedir = null)
	{
		global $langs, $db;

		$files = array();
		$fullpath = array();
		$relpath = array();
		$iscoreorexternal = array();
		$modules = array();
		$orders = array();
		$i = 0;

		$diremailselector = array('/core/modules/mailings/'); // $conf->modules_parts['emailings'] is not required
		if (is_array($forcedir)) {
			$diremailselector = $forcedir;
		}

		foreach ($diremailselector as $reldir) {
			$dir = dol_buildpath($reldir, 0);
			$newdir = dol_osencode($dir);

			// Check if directory exists (we do not use dol_is_dir to avoid loading files.lib.php at each call)
			if (!is_dir($newdir)) {
				continue;
			}

			$handle = opendir($newdir);
			if (is_resource($handle)) {
				while (($file = readdir($handle)) !== false) {
					$reg = array();
					if (is_readable($newdir.'/'.$file) && preg_match('/^(.+)\.modules.php/', $file, $reg)) {
						if (preg_match('/\.back$/', $file) || preg_match('/^(.+)\.disabled\.php/', $file)) {
							continue;
						}

						$part1 = $reg[1];

						//$modName = ucfirst($reg[1]);
						$modName = 'mailing_'.$reg[1];	// name of selector submodule
						//print "file=$file modName=$modName"; exit;
						if (in_array($modName, $modules)) {
							$langs->load("errors");
							print '<div class="error">'.$langs->trans("Error").' : '.$langs->trans("ErrorDuplicateEmalingSelector", $modName, "").'</div>';
						} else {
							try {
								//print $newdir.'/'.$file;
								include_once $newdir.'/'.$file;
							} catch (Exception $e) {
								print $e->getMessage();
							}
						}

						$files[$i] = $file;
						$fullpath[$i] = $dir.'/'.$file;
						$relpath[$i] = preg_replace('/^\//', '', $reldir).'/'.$file;
						$iscoreorexternal[$i] = ($reldir == '/core/modules/mailings/' ? 'internal' : 'external');
						$modules[$i] = $modName;
						$orders[$i] = $part1; // Set sort criteria value

						$i++;
					}
				}
				closedir($handle);
			}
		}
		//echo "<pre>";print_r($modules);echo "</pre>";

		asort($orders);

		$widget = array();
		$j = 0;

		// Loop on each emailing selector
		foreach ($orders as $key => $value) {
			$modName = $modules[$key];
			if (empty($modName)) {
				continue;
			}

			if (!class_exists($modName)) {
				print 'Error: An emailing selector file was found but its class "'.$modName.'" was not found.'."<br>\n";
				continue;
			}

			$objMod = new $modName($db);
			if (is_object($objMod)) {
				'@phan-var-force ModeleBoxes $objMod';
				// Define disabledbyname and disabledbymodule
				$disabledbyname = 0;
				$disabledbymodule = 0; // TODO Set to 2 if module is not enabled
				$module = '';

				// Check if widget file is disabled by name
				if (preg_match('/NORUN$/i', $files[$key])) {
					$disabledbyname = 1;
				}

				// We set info of modules @phan-suppress-next-line PhanUndeclaredProperty
				$widget[$j]['picto'] = (empty($objMod->picto) ? (empty($objMod->boximg) ? img_object('', 'generic') : $objMod->boximg) : img_object('', $objMod->picto));
				$widget[$j]['file'] = $files[$key];
				$widget[$j]['fullpath'] = $fullpath[$key];
				$widget[$j]['relpath'] = $relpath[$key];
				$widget[$j]['iscoreorexternal'] = $iscoreorexternal[$key];
				$widget[$j]['version'] = empty($objMod->version) ? '' : $objMod->version;
				$widget[$j]['status'] = img_picto($langs->trans("Active"), 'tick');
				if ($disabledbyname > 0 || $disabledbymodule > 1) {
					$widget[$j]['status'] = '';
				}

				$text = '<b>'.$langs->trans("Description").':</b><br>';
				$text .= $objMod->boxlabel.'<br>';
				$text .= '<br><b>'.$langs->trans("Status").':</b><br>';
				if ($disabledbymodule == 2) {
					$text .= $langs->trans("WidgetDisabledAsModuleDisabled", $module).'<br>';
				}

				$widget[$j]['info'] = $text;
			}
			$j++;
		}

		return $widget;
	}


	/**
	 *  On the main mailing area, there is a box with statistics.
	 *  If you want to add a line in this report you must provide an
	 *  array of SQL request that returns two field:
	 *  One called "label", One called "nb".
	 *
	 *	@return		string[]		Array with SQL requests
	 */
	public function getSqlArrayForStats()
	{
		// Needs to be implemented in child class
		$msg = get_class($this)."::".__FUNCTION__." not implemented";
		dol_syslog($msg, LOG_ERR);
		return array();
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Add destinations in the targets table
	 *
	 *  @param  int     $mailing_id     Id of emailing
	 *  @return int                     Return integer < 0 on error, count of added when ok
	 */
	public function add_to_target($mailing_id)
	{
		// phpcs:enable
		// Needs to be implemented in child class
		$msg = get_class($this)."::".__FUNCTION__." not implemented";
		dol_syslog($msg, LOG_ERR);
		return -1;
	}
}
