<?php
/* Copyright (C) 2005-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Marcos García       <marcosgdf@gmail.com>
 * Copyright (C) 2024-2025	MDW					<mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024		Rafael San José     <rsanjose@alxarafe.com>
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
 *  \file       htdocs/core/triggers/interface_50_modMailmanspip_Mailmanspipsynchro.class.php
 *  \ingroup    core
 *  \brief      File to manage triggers Mailman and Spip
 */
require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';


/**
 *  Class of triggers for MailmanSpip module
 */
class InterfaceMailmanSpipsynchro extends DolibarrTriggers
{
	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "mailmanspip";
		$this->description = "Triggers of this module allows to synchronize Mailman an Spip.";
		$this->version = self::VERSIONS['prod'];
		$this->picto = 'technic';
	}

	/**
	 * Function called when a Dolibarr business event is done.
	 * All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers or htdocs/module/code/triggers (and declared)
	 *
	 * @param string		$action		Event action code
	 * @param CommonObject	$object     Object
	 * @param User		    $user       Object user
	 * @param Translate 	$langs      Object langs
	 * @param Conf		    $conf       Object conf
	 * @return int         				Return integer <0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
		if (empty($conf->mailmanspip) || empty($conf->mailmanspip->enabled)) {
			return 0; // Module not active, we do nothing
		}

		require_once DOL_DOCUMENT_ROOT."/mailmanspip/class/mailmanspip.class.php";
		require_once DOL_DOCUMENT_ROOT."/user/class/usergroup.class.php";

		if ($action == 'CATEGORY_LINK') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

			// We add subscription if we change category (new category may means more mailing-list to subscribe)
			if (is_object($object->context['linkto']) && method_exists($object->context['linkto'], 'add_to_abo') && $object->context['linkto']->add_to_abo() < 0) {
				$this->error = $object->context['linkto']->error;
				$this->errors = $object->context['linkto']->errors;
				$return = -1;
			} else {
				$return = 1;
			}

			return $return;
		} elseif ($action == 'CATEGORY_UNLINK') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

			// We remove subscription if we change category (lessw category may means less mailing-list to subscribe)
			if (is_object($object->context['unlinkoff']) && method_exists($object->context['unlinkoff'], 'del_to_abo') && $object->context['unlinkoff']->del_to_abo() < 0) {
				$this->error = $object->context['unlinkoff']->error;
				$this->errors = $object->context['unlinkoff']->errors;
				$return = -1;
			} else {
				$return = 1;
			}

			return $return;
		} elseif ($action == 'MEMBER_VALIDATE') {
			'@phan-var-force Adherent $object';
			// Members
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

			$return = 0;
			if ($object->add_to_abo() < 0) {
				$this->errors = $object->errors;
				if (!empty($object->error)) {
					$this->errors[] = $object->error;
				}
				$return = -1;
			} else {
				$return = 1;
			}

			return $return;
		} elseif ($action == 'MEMBER_MODIFY') {
			'@phan-var-force Adherent $object';
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

			$return = 0;
			// Add user into some linked tools (mailman, spip, etc...)
			if (($object->oldcopy->email != $object->email) || ($object->oldcopy->typeid != $object->typeid)) {
				if (is_object($object->oldcopy) && (($object->oldcopy->email != $object->email) || ($object->oldcopy->typeid != $object->typeid))) {    // If email has changed or if list has changed we delete mailman subscription for old email
					// $object->oldcopy may be a stdClass and not original object depending on copy type, so we reload a new object to run the del_to_abo()
					$tmpmember = new Adherent($this->db);
					$tmpmember->fetch($object->oldcopy->id);
					if ($tmpmember->del_to_abo() < 0) {
						$this->errors = $tmpmember->errors;
						if (!empty($tmpmember->error)) {
							$this->errors[] = $tmpmember->error;
						}
						$return = -1;
					} else {
						$return = 1;
					}
				}
				// We add subscription if new email or new type (new type may means more mailing-list to subscribe)
				if ($object->add_to_abo() < 0) {
					$this->errors = $object->errors;
					if (!empty($object->error)) {
						$this->errors[] = $object->error;
					}
					$return = -1;
				} else {
					$return = 1;
				}
			}

			return $return;
		} elseif ($action == 'MEMBER_RESILIATE' || $action == 'MEMBER_DELETE') {
			dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

			$return = 0;
			// Remove from external tools (mailman, spip, etc...)
			if ($object->del_to_abo() < 0) {
				$this->errors = $object->errors;
				if (!empty($object->error)) {
					$this->errors[] = $object->error;
				}
				$return = -1;
			} else {
				$return = 1;
			}

			return $return;
		}

		return 0;
	}
}
