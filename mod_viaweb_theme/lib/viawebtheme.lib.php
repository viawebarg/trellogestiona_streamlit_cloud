<?php
/* Copyright (C) 2024 VIAWEB S.A.S
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
a * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    mod_viaweb_theme/lib/viawebtheme.lib.php
 * \ingroup viawebtheme
 * \brief   Library files for ViawebTheme module
 */

/**
 * Prepare head array with tabs
 *
 * @return array
 */
function viawebthemeAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load("viawebtheme@mod_viaweb_theme");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/mod_viaweb_theme/admin/setup.php", 1);
    $head[$h][1] = $langs->trans("Settings");
    $head[$h][2] = 'settings';
    $h++;

    return $head;
}