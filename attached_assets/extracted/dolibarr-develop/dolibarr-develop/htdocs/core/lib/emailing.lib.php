<?php
/* Copyright (C) 2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024		MDW						<mdeweerd@users.noreply.github.com>
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
 *	    \file       htdocs/core/lib/emailing.lib.php
 *		\brief      Library file with function for emailing module
 */

/**
 * Prepare array with list of tabs
 *
 * @param   Mailing	$object		Object related to tabs
 * @return	array<array{0:string,1:string,2:string}>	Array of tabs to show
 */
function emailing_prepare_head(Mailing $object)
{
	global $user, $langs, $conf;

	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT."/comm/mailing/card.php?id=".$object->id;
	$head[$h][1] = $langs->trans("MailCard");
	$head[$h][2] = 'card';
	$h++;

	if (!getDolGlobalString('MAIN_USE_ADVANCED_PERMS') || (getDolGlobalString('MAIN_USE_ADVANCED_PERMS') && $user->hasRight('mailing', 'mailing_advance', 'recipient'))) {
		$head[$h][0] = DOL_URL_ROOT."/comm/mailing/targetemailing.php?id=".$object->id;
		$head[$h][1] = $langs->trans("MailRecipients");
		if ($object->nbemail > 0) {
			$head[$h][1] .= '<span class="badge marginleftonlyshort">'.$object->nbemail.'</span>';
		}
		$head[$h][2] = 'targets';
		$h++;
	}

	if (getDolGlobalString('EMAILING_USE_ADVANCED_SELECTOR')) {
		$head[$h][0] = DOL_URL_ROOT."/comm/mailing/advtargetemailing.php?id=".$object->id;
		$head[$h][1] = $langs->trans("MailAdvTargetRecipients");
		$head[$h][2] = 'advtargets';
		$h++;
	}

	$head[$h][0] = DOL_URL_ROOT."/comm/mailing/info.php?id=".$object->id;
	$head[$h][1] = $langs->trans("Info");
	$head[$h][2] = 'info';
	$h++;

	$head[$h][0] = DOL_URL_ROOT."/comm/mailing/note.php?id=".$object->id;
	$head[$h][1] = $langs->trans("Note");
	$head[$h][2] = 'note';
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'emailing');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'emailing', 'remove');

	return $head;
}
