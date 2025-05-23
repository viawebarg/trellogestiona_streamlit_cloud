<?php
/* Copyright (C) 2007-2011 Laurent Destailleur      <eldy@users.sourceforge.net>
 * Copyright (C) 2024-2025	MDW						<mdeweerd@users.noreply.github.com>
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
 *      \file       htdocs/core/class/cunits.class.php
 *      \ingroup    core
 *      \brief      This file is CRUD class file (Create/Read/Update/Delete) for c_units dictionary
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commondict.class.php';


/**
 *	Class of dictionary type of thirdparty (used by imports)
 */
class CUnits extends CommonDict
{
	/**
	 * @var CUnits[]
	 */
	public $records = array();

	// public $element = 'cunits';			//!< Id that identify managed objects
	// public $table_element = 'c_units';	//!< Name of table without prefix where object is stored

	/**
	 * @var string label
	 * @deprecated Use $label
	 * @see $label
	 */
	public $libelle;

	/**
	 * @var string
	 */
	public $sortorder;

	/**
	 * @var string
	 */
	public $short_label;

	/**
	 * @var string
	 */
	public $unit_type;

	/**
	 * @var ?int
	 */
	public $scale;


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
	 *  @param	User		$user        User that create
	 *  @param	int<0,1>	$notrigger   0=launch triggers after, 1=disable triggers
	 *  @return	int					   	 Return integer <0 if KO, Id of created object if OK
	 */
	public function create($user, $notrigger = 0)
	{
		$error = 0;

		// Clean parameters

		if (isset($this->id)) {
			$this->id = (int) $this->id;
		}
		if (isset($this->code)) {
			$this->code = trim($this->code);
		}
		if (isset($this->label)) {
			$this->libelle = trim($this->label);
		}
		if (isset($this->short_label)) {
			$this->libelle = trim($this->short_label);
		}
		if (isset($this->unit_type)) {
			$this->unit_type = trim($this->unit_type);
		}
		if (isset($this->active)) {
			$this->active = (int) $this->active;
		}
		if (isset($this->scale)) {
			$this->scale = (int) $this->scale;
		}

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".$this->db->prefix()."c_units(";
		$sql .= "rowid,";
		$sql .= "code,";
		$sql .= "label,";
		$sql .= "short_label,";
		$sql .= "unit_type,";
		$sql .= "scale";
		$sql .= ") VALUES (";
		$sql .= " ".(!isset($this->id) ? 'NULL' : "'".$this->db->escape((string) $this->id)."'").",";
		$sql .= " ".(!isset($this->code) ? 'NULL' : "'".$this->db->escape($this->code)."'").",";
		$sql .= " ".(!isset($this->label) ? 'NULL' : "'".$this->db->escape($this->label)."'").",";
		$sql .= " ".(!isset($this->short_label) ? 'NULL' : "'".$this->db->escape($this->short_label)."'").",";
		$sql .= " ".(!isset($this->unit_type) ? 'NULL' : "'".$this->db->escape($this->unit_type)."'").",";
		$sql .= " ".(!isset($this->scale) ? 'NULL' : (int) $this->scale);
		$sql .= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
			$this->errors[] = "Error ".$this->db->lasterror();
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id($this->db->prefix()."c_units");
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
	 *  @param      int		$id    			Id of CUnit object to fetch (rowid)
	 *  @param		string	$code			Code
	 *  @param		string	$short_label	Short Label ('g', 'kg', ...)
	 *  @param		string	$unit_type		Unit type ('size', 'surface', 'volume', 'weight', ...)
	 *  @return     int						Return integer <0 if KO, >0 if OK
	 */
	public function fetch($id, $code = '', $short_label = '', $unit_type = '')
	{
		$sql = "SELECT";
		$sql .= " t.rowid,";
		$sql .= " t.code,";
		$sql .= " t.label,";
		$sql .= " t.short_label,";
		$sql .= " t.scale,";
		$sql .= " t.unit_type,";
		$sql .= " t.active";
		$sql .= " FROM ".$this->db->prefix()."c_units as t";
		$sql_where = array();
		if ($id) {
			$sql_where[] = " t.rowid = ".((int) $id);
		}
		if ($unit_type) {
			$sql_where[] = " t.unit_type = '".$this->db->escape($unit_type)."'";
		}
		if ($code) {
			$sql_where[] = " t.code = '".$this->db->escape($code)."'";
		}
		if ($short_label) {
			$sql_where[] = " t.short_label = '".$this->db->escape($short_label)."'";
		}
		if (count($sql_where) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $sql_where);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id = (int) $obj->rowid;
				$this->code = $obj->code;
				$this->label = $obj->label;
				$this->short_label = $obj->short_label;
				$this->scale = $obj->scale ? (int) $obj->scale : null;
				$this->unit_type = $obj->unit_type;
				$this->active = (int) $obj->active;
			}
			$this->db->free($resql);

			return 1;
		} else {
			$this->error = "Error ".$this->db->lasterror();
			return -1;
		}
	}


	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      	$sortorder    	Sort Order
	 * @param  string      	$sortfield    	Sort field
	 * @param  int         	$limit        	Limit
	 * @param  int         	$offset       	Offset
	 * @param  string|array<string,mixed> $filter  	Filter USF
	 * @param  string      	$filtermode   	Filter mode (AND or OR)
	 * @return CUnits[]|int                 	int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT";
		$sql .= " t.rowid,";
		$sql .= " t.code,";
		$sql .= " t.sortorder,";
		$sql .= " t.label,";
		$sql .= " t.short_label,";
		$sql .= " t.unit_type,";
		$sql .= " t.scale,";
		$sql .= " t.active";
		$sql .= " FROM ".$this->db->prefix()."c_units as t";
		$sql .= " WHERE 1 = 1";

		// Manage filter
		if (is_array($filter)) {
			$sqlwhere = array();
			if (count($filter) > 0) {
				foreach ($filter as $key => $value) {
					if ($key == 't.rowid' || $key == 't.active' || $key == 't.scale') {
						$sqlwhere[] = $this->db->sanitize($key)." = ".((int) $value);
					} elseif (strpos($key, 'date') !== false) {
						$sqlwhere[] = $this->db->sanitize($key)." = '".$this->db->idate($value)."'";
					} elseif ($key == 't.unit_type' || $key == 't.code' || $key == 't.short_label') {
						$sqlwhere[] = $this->db->sanitize($key)." = '".$this->db->escape($value)."'";
					} else {
						$sqlwhere[] = $this->db->sanitize($key)." LIKE '%".$this->db->escape($this->db->escapeforlike($value))."%'";
					}
				}
			}
			if (count($sqlwhere) > 0) {
				$sql .= ' AND ('.implode(' '.$this->db->escape($filtermode).' ', $sqlwhere).')';
			}

			$filter = '';
		}

		// Manage filter
		$errormessage = '';
		$sql .= forgeSQLFromUniversalSearchCriteria($filter, $errormessage);
		if ($errormessage) {
			$this->errors[] = $errormessage;
			dol_syslog(__METHOD__.' '.implode(',', $this->errors), LOG_ERR);
			return -1;
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= $this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$this->records = array();
			$num = $this->db->num_rows($resql);
			if ($num > 0) {
				while ($obj = $this->db->fetch_object($resql)) {
					$record = new self($this->db);

					$record->id = (int) $obj->rowid;
					$record->code = $obj->code;
					$record->sortorder = $obj->sortorder;
					$record->label = $obj->label;
					$record->short_label = $obj->short_label;
					$record->unit_type = $obj->unit_type;
					$record->scale = $obj->scale ? (int) $obj->scale : null;
					$record->active = (int) $obj->active;

					$this->records[$record->id] = $record;
				}
			}
			$this->db->free($resql);

			return $this->records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.implode(',', $this->errors), LOG_ERR);

			return -1;
		}
	}


	/**
	 *  Update object into database
	 *
	 *  @param      User		$user        User that modify
	 *  @param      int<0,1>	$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return     int					   	 Return integer <0 if KO, >0 if OK
	 */
	public function update($user = null, $notrigger = 0)
	{
		$error = 0;

		// Clean parameters
		if (isset($this->code)) {
			$this->code = trim($this->code);
		}
		if (isset($this->sortorder)) {
			$this->sortorder = trim($this->sortorder);
		}
		if (isset($this->label)) {
			$this->libelle = trim($this->label);
		}
		if (isset($this->short_label)) {
			$this->libelle = trim($this->short_label);
		}
		if (isset($this->unit_type)) {
			$this->libelle = trim($this->unit_type);
		}
		if (isset($this->scale)) {
			$this->scale = (int) $this->scale;
		}
		if (isset($this->active)) {
			$this->active = (int) $this->active;
		}

		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".$this->db->prefix()."c_units SET";
		$sql .= " code=".(isset($this->code) ? "'".$this->db->escape($this->code)."'" : "null").",";
		$sql .= " sortorder=".(isset($this->sortorder) ? "'".$this->db->escape($this->sortorder)."'" : "null").",";
		$sql .= " label=".(isset($this->label) ? "'".$this->db->escape($this->label)."'" : "null").",";
		$sql .= " short_label=".(isset($this->short_label) ? "'".$this->db->escape($this->short_label)."'" : "null").",";
		$sql .= " unit_type=".(isset($this->unit_type) ? "'".$this->db->escape($this->unit_type)."'" : "null").",";
		$sql .= " scale=".(isset($this->scale) ? "'".$this->db->escape((string) $this->scale)."'" : "null").",";
		$sql .= " active=".(isset($this->active) ? $this->active : "null");
		$sql .= " WHERE rowid=".((int) $this->id);

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
	 *	@param  User		$user        User that delete
	 *  @param  int<0,1>	$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 Return integer <0 if KO, >0 if OK
	 */
	public function delete($user, $notrigger = 0)
	{
		$error = 0;

		$sql = "DELETE FROM ".$this->db->prefix()."c_units";
		$sql .= " WHERE rowid=".((int) $this->id);

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


	/**
	 * Get unit from code
	 * @param string $code code of unit
	 * @param string $mode 0= id , short_label=Use short label as value, code=use code
	 * @param string $unit_type weight,size,surface,volume,qty,time...
	 * @return int|string            Return integer <0 if KO, Id of code if OK (or $code if $mode is different from '', 'short_label' or 'code')
	 */
	public function getUnitFromCode($code, $mode = 'code', $unit_type = '')
	{
		if ($mode == 'short_label' || $mode == 'code') {
			return dol_getIdFromCode($this->db, $code, 'c_units', $mode, 'rowid', 0, " AND unit_type = '".$this->db->escape($unit_type)."'");
		}
		return $code;
	}

	/**
	 * Unit converter
	 * @param float $value value to convert
	 * @param int $fk_unit current unit id of value
	 * @param int $fk_new_unit the id of unit to convert in
	 * @return float
	 */
	public function unitConverter($value, $fk_unit, $fk_new_unit = 0)
	{
		$value = (float) price2num($value);
		$fk_unit = intval($fk_unit);

		// Calcul en unité de base
		$scaleUnitPow = $this->scaleOfUnitPow($fk_unit);

		// convert to standard unit
		$value *= $scaleUnitPow;
		if ($fk_new_unit != 0) {
			// Calcul en unité de base
			$scaleUnitPow = $this->scaleOfUnitPow($fk_new_unit);
			if (!empty($scaleUnitPow)) {
				// convert to new unit
				$value /= $scaleUnitPow;
			}
		}
		return round($value, 2);
	}

	/**
	 * Get scale of unit factor
	 *
	 * @param 	int 		$id 	Id of unit in dictionary
	 * @return 	float|int			Scale of unit
	 */
	public function scaleOfUnitPow($id)
	{
		$base = 10;

		$sql = "SELECT scale, unit_type FROM ".$this->db->prefix()."c_units WHERE rowid = ".((int) $id);

		$resql = $this->db->query($sql);
		if ($resql) {
			// TODO : add base col into unit dictionary table
			$unit = $this->db->fetch_object($resql);
			if ($unit) {
				// TODO : if base exists in unit dictionary table, remove this conversion exception and update conversion infos in database.
				// Example time hour currently scale 3600 will become scale 2 base 60
				if ($unit->unit_type == 'time') {
					return (float) $unit->scale;
				}

				return pow($base, (float) $unit->scale);
			}
		}

		return 0;
	}
}
