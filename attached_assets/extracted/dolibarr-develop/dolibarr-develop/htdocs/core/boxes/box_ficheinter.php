<?php
/* Copyright (C) 2013 Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2013 Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2015-2025  Frédéric France	<frederic.france@free.fr>
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
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
 * 		\file       htdocs/core/boxes/box_ficheinter.php
 * 		\ingroup    ficheinter
 * 		\brief      Box to show last interventions
 */

include_once DOL_DOCUMENT_ROOT.'/core/boxes/modules_boxes.php';


/**
 * Class to manage the box to show last interventions
 */
class box_ficheinter extends ModeleBoxes
{
	public $boxcode = "ficheinter";
	public $boximg = "object_intervention";
	public $boxlabel = "BoxFicheInter";
	public $depends = array("ficheinter"); // conf->contrat->enabled

	/**
	 *  Constructor
	 *
	 *  @param  DoliDB  $db         Database handler
	 *  @param  string  $param      More parameters
	 */
	public function __construct($db, $param)
	{
		global $user;

		$this->db = $db;

		$this->hidden = !($user->hasRight('ficheinter', 'lire'));
		$this->urltoaddentry = DOL_URL_ROOT.'/fichinter/card.php?action=create';
		$this->msgNoRecords = 'NoRecordedInterventions';
	}

	/**
	 *  Load data for box to show them later
	 *
	 *  @param	int		$max        Maximum number of records to load
	 *  @return	void
	 */
	public function loadBox($max = 10)
	{
		global $user, $langs, $conf;

		$this->max = $max;

		include_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
		$ficheinterstatic = new Fichinter($this->db);
		$thirdpartystatic = new Societe($this->db);

		$this->info_box_head = array(
			'text' => $langs->trans("BoxTitleLastFicheInter", $max).'<a class="paddingleft" href="'.DOL_URL_ROOT.'/fichinter/list.php?sortfield=f.tms&sortorder=DESC"><span class="badge">...</span></a>'
		);

		if ($user->hasRight('ficheinter', 'lire')) {
			$sql = "SELECT f.rowid, f.ref, f.fk_soc, f.fk_statut as status";
			$sql .= ", f.datec";
			$sql .= ", f.date_valid as datev";
			$sql .= ", f.tms as datem";
			$sql .= ", s.rowid as socid, s.nom as name, s.name_alias";
			$sql .= ", s.code_client, s.code_compta, s.client";
			$sql .= ", s.logo, s.email, s.entity";
			$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
			if (empty($user->socid) && !$user->hasRight('societe', 'client', 'voir')) {
				$sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
			}
			$sql .= ", ".MAIN_DB_PREFIX."fichinter as f";
			$sql .= " WHERE f.fk_soc = s.rowid ";
			$sql .= " AND f.entity = ".$conf->entity;
			if (empty($user->socid) && !$user->hasRight('societe', 'client', 'voir')) {
				$sql .= " AND s.rowid = sc.fk_soc AND sc.fk_user = ".((int) $user->id);
			}
			if ($user->socid) {
				$sql .= " AND s.rowid = ".((int) $user->socid);
			}
			$sql .= " ORDER BY f.tms DESC";
			$sql .= $this->db->plimit($max, 0);

			dol_syslog(get_class($this).'::loadBox', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql) {
				$num = $this->db->num_rows($resql);
				$now = dol_now();

				$i = 0;

				while ($i < $num) {
					$objp = $this->db->fetch_object($resql);
					$datec = $this->db->jdate($objp->datec);
					$datem = $this->db->jdate($objp->datem);

					$ficheinterstatic->statut = $objp->status;	// deprecated
					$ficheinterstatic->status = $objp->status;
					$ficheinterstatic->id = $objp->rowid;
					$ficheinterstatic->ref = $objp->ref;

					$thirdpartystatic->id = $objp->socid;
					$thirdpartystatic->name = $objp->name;
					//$thirdpartystatic->name_alias = $objp->name_alias;
					$thirdpartystatic->code_client = $objp->code_client;
					$thirdpartystatic->code_compta = $objp->code_compta;
					$thirdpartystatic->code_compta_client = $objp->code_compta;
					$thirdpartystatic->client = $objp->client;
					$thirdpartystatic->logo = $objp->logo;
					$thirdpartystatic->email = $objp->email;
					$thirdpartystatic->entity = $objp->entity;

					$this->info_box_contents[$i][] = array(
						'td' => 'class="nowraponall"',
						'text' => $ficheinterstatic->getNomUrl(1),
						'asis' => 1,
					);

					$this->info_box_contents[$i][] = array(
						'td' => 'class="tdoverflowmax150"',
						'text' => $thirdpartystatic->getNomUrl(1),
						'asis' => 1,
					);

					$this->info_box_contents[$i][] = array(
						'td' => 'class="center nowraponall" title="'.dol_escape_htmltag($langs->trans("DateModification").': '.dol_print_date($datem, 'dayhour', 'tzuserrel')).'"',
						'text' => dol_print_date($datem, 'day', 'tzuserrel'),
					);

					$this->info_box_contents[$i][] = array(
						'td' => 'class="nowrap right"',
						'text' => $ficheinterstatic->getLibStatut(3),
						'asis' => 1,
					);

					$i++;
				}

				// if ($num == 0) {
				// 	$this->info_box_contents[$i][0] = array(
				// 	'td' => 'class="center"',
				// 		'text' => '<span class="opacitymedium">'.$langs->trans("NoRecordedInterventions").'</span>'
				// 	);
				// }

				$this->db->free($resql);
			} else {
				$this->info_box_contents[0][0] = array(
					'td' => '',
					'maxlength' => 500,
					'text' => ($this->db->error().' sql='.$sql),
				);
			}
		} else {
			$this->info_box_contents[0][0] = array(
				'td' => 'class="nohover left"',
				'text' => '<span class="opacitymedium">'.$langs->trans("ReadPermissionNotAllowed").'</span>'
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
