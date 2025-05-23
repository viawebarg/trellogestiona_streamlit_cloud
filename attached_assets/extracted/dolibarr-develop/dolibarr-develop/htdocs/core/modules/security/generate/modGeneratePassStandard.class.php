<?php
/* Copyright (C) 2006-2011 Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2024		Frédéric France			<frederic.france@free.fr>
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
 *      \file       htdocs/core/modules/security/generate/modGeneratePassStandard.class.php
 *      \ingroup    core
 *		\brief      File to manage password generation according to standard rule
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/security/generate/modules_genpassword.php';


/**
 *	Class to generate a password according to a dolibarr standard rule (12 random chars)
 */
class modGeneratePassStandard extends ModeleGenPassword
{
	/**
	 * @var string ID
	 */
	public $id;

	public $picto = 'fa-shield-alt';

	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db			Database handler
	 *	@param		Conf		$conf		Handler de conf
	 *	@param		Translate	$langs		Handler de langue
	 *	@param		User		$user		Handler du user connected
	 */
	public function __construct($db, $conf, $langs, $user)
	{
		$this->id = "standard";
		$this->length = '12';
		$this->length2 = 12;

		$this->db = $db;
		$this->conf = $conf;
		$this->langs = $langs;
		$this->user = $user;
	}

	/**
	 *		Return description of module
	 *
	 *      @return     string      Description of module
	 */
	public function getDescription()
	{
		global $langs;
		return $langs->trans("PasswordGenerationStandard", $this->length);
	}

	/**
	 * 		Return an example of password generated by this module
	 *
	 *      @return     string      Example of password
	 */
	public function getExample()
	{
		return $this->getNewGeneratedPassword();
	}

	/**
	 * 		Build new password
	 *
	 *      @return     string      Return a new generated password
	 */
	public function getNewGeneratedPassword()
	{
		// start with a blank password
		$password = "";

		// define possible characters
		$possible = "0123456789qwertyuiopasdfghjklzxcvbnmASDFGHJKLZXCVBNMQWERTYUIOP";

		// set up a counter
		$i = 0;

		// add random characters to $password until $length is reached
		while ($i < $this->length) {
			// pick a random character from the possible ones
			if (function_exists('random_int')) {	// Cryptographic random
				$char = substr($possible, random_int(0, dol_strlen($possible) - 1), 1);
			} else {
				$char = substr($possible, mt_rand(0, dol_strlen($possible) - 1), 1);
			}

			if (substr_count($password, $char) <= 6) {	// we don't want this character if it's already 5 times in the password
				$password .= $char;
				$i++;
			}
		}

		// done!
		return $password;
	}

	/**
	 *  Validate a password
	 * 	This function is called by User->setPassword() and internally to validate that the password matches the constraints.
	 *
	 *  @param      string  $password   Password to check
	 *  @return     int                 0 if KO, >0 if OK
	 */
	public function validatePassword($password)
	{
		global $langs;

		dol_syslog("modGeneratePassStandard::validatePassword");

		if (dol_strlen($password) < $this->length2) {
			$langs->load("other");
			$this->error = $langs->trans("YourPasswordMustHaveAtLeastXChars", $this->length2);
			return 0;
		}

		return 1;
	}
}
