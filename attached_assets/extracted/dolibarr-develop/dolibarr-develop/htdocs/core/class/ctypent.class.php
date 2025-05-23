<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *      \file       htdocs/core/class/ctypent.class.php
 *      \ingroup    core
 *      \brief      This file is CRUD class file (Create/Read/Update/Delete) for c_typent dictionary
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commondict.class.php';


/**
 *	Class of dictionary type of thirdparty (used by imports)
 */
class Ctypent extends CommonDict
{
	/**
	 * @var int ID of country
	 */
	public $country_id;

	/**
	 * @var string
	 */
	public $libelle;
	/**
	 * @var string
	 */
	public $module;

	/**
	 *  Constructor
	 *
	 *  @param      DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 *  Create object into database
	 *
	 *  @param      User	$user        User that create
	 *  @param      int		$notrigger   0=launch triggers after, 1=disable triggers
	 *  @return     int      		   	 Return integer <0 if KO, Id of created object if OK
	 */
	public function create($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		// Clean parameters

		if (isset($this->id)) {
			$this->id = (int) $this->id;
		}
		if (isset($this->code)) {
			$this->code = trim($this->code);
		}
		if (isset($this->libelle)) {
			$this->libelle = trim($this->libelle);
		}
		if (isset($this->active)) {
			$this->active = (int) $this->active;
		}
		if (isset($this->module)) {
			$this->module = trim($this->module);
		}

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".$this->db->prefix()."c_typent(";
		$sql .= "id,";
		$sql .= "code,";
		$sql .= "libelle,";
		$sql .= "active,";
		$sql .= "module";
		$sql .= ") VALUES (";
		$sql .= " ".(!isset($this->id) ? 'NULL' : "'".$this->db->escape((string) $this->id)."'").",";
		$sql .= " ".(!isset($this->code) ? 'NULL' : "'".$this->db->escape($this->code)."'").",";
		$sql .= " ".(!isset($this->libelle) ? 'NULL' : "'".$this->db->escape($this->libelle)."'").",";
		$sql .= " ".(!isset($this->active) ? 'NULL' : "'".$this->db->escape((string) $this->active)."'").",";
		$sql .= " ".(!isset($this->module) ? 'NULL' : "'".$this->db->escape($this->module)."'");
		$sql .= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = "Error ".$this->db->lasterror();
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id($this->db->prefix()."c_typent");
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
			}
			$this->db->rollback();
			return -1 * $error;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}


	/**
	 *  Load object in memory from database
	 *
	 *  @param      int		$id    	Id object
	 *  @param		string	$code	Code
	 *  @param		string	$label	Label
	 *  @return     int          	Return integer <0 if KO, >0 if OK
	 */
	public function fetch($id, $code = '', $label = '')
	{
		$sql = "SELECT";
		$sql .= " t.id,";
		$sql .= " t.code,";
		$sql .= " t.libelle as label,";
		$sql .= " t.fk_country as country_id,";
		$sql .= " t.active,";
		$sql .= " t.module";
		$sql .= " FROM ".$this->db->prefix()."c_typent as t";
		if ($id) {
			$sql .= " WHERE t.id = ".((int) $id);
		} elseif ($code) {
			$sql .= " WHERE t.code = '".$this->db->escape($code)."'";
		} elseif ($label) {
			$sql .= " WHERE t.libelle = '".$this->db->escape($label)."'";
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->id;
				$this->code = $obj->code;
				$this->libelle = $obj->label;
				$this->country_id = $obj->country_id;
				$this->active = $obj->active;
				$this->module = $obj->module;
			}
			$this->db->free($resql);

			return 1;
		} else {
			$this->error = "Error ".$this->db->lasterror();
			return -1;
		}
	}


	/**
	 *  Update object into database
	 *
	 *  @param      User	$user        User that modify
	 *  @param      int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return     int     		   	 Return integer <0 if KO, >0 if OK
	 */
	public function update($user = null, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		// Clean parameters
		if (isset($this->code)) {
			$this->code = trim($this->code);
		}
		if (isset($this->libelle)) {
			$this->libelle = trim($this->libelle);
		}
		if (isset($this->active)) {
			$this->active = (int) $this->active;
		}
		if (isset($this->module)) {
			$this->module = trim($this->module);
		}


		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".$this->db->prefix()."c_typent SET";
		$sql .= " code=".(isset($this->code) ? "'".$this->db->escape($this->code)."'" : "null").",";
		$sql .= " libelle=".(isset($this->libelle) ? "'".$this->db->escape($this->libelle)."'" : "null").",";
		$sql .= " active=".(isset($this->active) ? ((int) $this->active) : "null").",";
		$sql .= " module=".(isset($this->module) ? "'".$this->db->escape($this->module)."'" : "null");
		$sql .= " WHERE id=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = "Error ".$this->db->lasterror();
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
			}
			$this->db->rollback();
			return -1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}


	/**
	 *  Delete object in database
	 *
	 *	@param  User	$user        User that delete
	 *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 Return integer <0 if KO, >0 if OK
	 */
	public function delete($user, $notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;

		$sql = "DELETE FROM ".$this->db->prefix()."c_typent";
		$sql .= " WHERE id = ".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::delete", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = "Error ".$this->db->lasterror();
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
			}
			$this->db->rollback();
			return -1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}
}
