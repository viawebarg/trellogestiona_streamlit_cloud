<?php
/* Copyright (C) 2024 VIAWEB S.A.S
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
 * 	\defgroup   viawebtheme     Module ViawebTheme
 *  \brief      Módulo de tema visual para Dolibarr con estilo VIAWEB
 *
 *  \file       htdocs/custom/mod_viaweb_theme/core/modules/modViawebTheme.class.php
 *  \ingroup    viawebtheme
 *  \brief      Archivo de descripción y activación del módulo ViawebTheme
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *  Clase de descripción y activación del módulo ViawebTheme
 */
class modViawebTheme extends DolibarrModules
{
    /**
     *   Constructor. Define nombres, constantes, directorios, cajas, permisos
     *
     *   @param      DoliDB      $db      Base de datos
     */
    public function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;

        // Id for module (must be unique)
        $this->numero = 777001;
        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'viawebtheme';

        // Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
        // It is used to group modules by family in module setup page
        $this->family = "theme";
        // Module position in the family on 2 digits ('01', '10', '20', ...)
        $this->module_position = '01';
        // Gives the possibility for the module to provide his own family info and position (some core modules do that like workflow)
        $this->familyinfo = array('theme' => array('position' => '01', 'label' => $langs->trans("Theme")));

        // Module label (no space allowed), used if translation string 'ModuleViawebThemeName' not found (Viawebtheme is name of module).
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        // Module description, used if translation string 'ModuleViawebThemeDesc' not found
        $this->description = "Tema visual Dolibarr con la identidad de VIAWEB";
        // Used only if file README.md and README-LL.md not found.
        $this->descriptionlong = "Tema visual para Dolibarr que implementa la identidad visual de VIAWEB (colores, tipografía y estilo). Compatible con el módulo TrelloGestiona.";

        // Author
        $this->editor_name = 'VIAWEB S.A.S';
        $this->editor_url = 'https://web.viaweb.net.ar';

        // Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
        $this->version = '1.0.0';

        // Url to the file with your last numberversion of this module
        //$this->url_last_version = 'http://www.example.com/versionmodule.txt';

        // Key used in llx_const table to save module status enabled/disabled (where VIAWEBTHEME is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        // Name of image file used for this module.
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
        // If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
        $this->picto = 'generic';

        // Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
        $this->module_parts = array(
            // Set this to 1 if module has its own trigger directory (core/triggers)
            'triggers' => 0,
            // Set this to 1 if module has its own login method file (core/login)
            'login' => 0,
            // Set this to 1 if module has its own substitution function file (core/substitutions)
            'substitutions' => 0,
            // Set this to 1 if module has its own menus handler directory (core/menus)
            'menus' => 0,
            // Set this to 1 if module overwrite template dir (core/tpl)
            'tpl' => 0,
            // Set this to 1 if module has its own barcode directory (core/modules/barcode)
            'barcode' => 0,
            // Set this to 1 if module has its own models directory (core/modules/xxx)
            'models' => 0,
            // Set this to 1 if module has its own theme directory (theme)
            'theme' => 0,
            // Set this to relative path of css file if module has its own css file
            'css' => array('/mod_viaweb_theme/css/viaweb_theme.css'),
            // Set this to relative path of js file if module has its own js file
            'js' => array('/mod_viaweb_theme/js/viaweb_theme.js'),
            // Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
            'hooks' => array('all'),
            // Set this to 1 if features of module are opened to external users
            'moduleforexternal' => 0,
        );

        // Data directories to create when module is enabled.
        // Example: this->dirs = array("/viawebtheme/temp","/viawebtheme/subdir");
        $this->dirs = array();

        // Config pages. Put here list of php page, stored into viawebtheme/admin directory, to use to set up module.
        $this->config_page_url = array("setup.php@mod_viaweb_theme");

        // Dependencies
        // A condition to hide module
        $this->hidden = false;
        // List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR', 'ES1'=>'modModuleToEnableES')
        $this->depends = array();
        $this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
        $this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

        // The language file dedicated to your module
        $this->langfiles = array("viawebtheme@mod_viaweb_theme");

        // Constants
        // List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
        // Example: $this->const=array(
        //    1 => array('VIAWEBTHEME_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
        //    2 => array('VIAWEBTHEME_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
        // );
        $this->const = array(
            1 => array('VIAWEBTHEME_VERSION', 'chaine', $this->version, 'Versión del tema VIAWEB', 1, 'current', 1),
        );

        // Some keys to add into the overwriting translation tables
        /*$this->overwrite_translation = array(
            'en_US:ParentCompany'=>'Parent company or reseller',
            'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
        )*/

        if (!isset($conf->viawebtheme) || !isset($conf->viawebtheme->enabled)) {
            $conf->viawebtheme = new stdClass();
            $conf->viawebtheme->enabled = 0;
        }

        // Array to add new pages in new tabs
        /*$this->tabs = array(
            'entity:-tabname-:Title:@viawebtheme:/viawebtheme/MyPage.php?id=__ID__'
        );*/
        /*$this->tabs = array(
            'object:+tabname1:Title1:mylangfile@viawebtheme:$user->rights->viawebtheme->read:/viawebtheme/mynewtab1.php?id=__ID__',  // To add a new tab identified by code tabname1
            'object:-tabname2:Title2::$user->rights->othermodule->read:/viawebtheme/mynewtab2.php?id=__ID__',  // To remove an existing tab identified by code tabname2. Label will be displayed by calling function dol_print_string($idlang, 'Title2')
        );*/
        $this->tabs = array();

        // Dictionaries
        $this->dictionaries = array();

        // Boxes/Widgets
        // Add here list of php file(s) stored in viawebtheme/core/boxes that contains a class to show a widget.
        $this->boxes = array(
            //  0 => array(
            //      'file' => 'viawebthemewidget1.php@mod_viaweb_theme',
            //      'note' => 'Widget provided by ViawebTheme',
            //      'enabledbydefaulton' => 'Home',
            //  ),
        );

        // Cronjobs (List of cron jobs entries to add when module is enabled)
        $this->cronjobs = array();

        // Permissions provided by this module
        $this->rights = array();

        // Main menu entries to add
        $this->menu = array();
    }

    /**
     *  Function called when module is enabled.
     *  The init function adds tabs, constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     *  It also creates data directories
     *
     *  @param      string  $options    Options when enabling module ('', 'newboxdefonly', 'noboxes')
     *  @return     int                 1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        $sql = array();

        $result = $this->_load_tables('/mod_viaweb_theme/sql/');

        return $this->_init($sql, $options);
    }

    /**
     *  Function called when module is disabled.
     *  Remove from database constants, boxes and permissions from Dolibarr database.
     *  Data directories are not deleted
     *
     *  @param      string  $options    Options when enabling module ('', 'noboxes')
     *  @return     int                 1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();

        return $this->_remove($sql, $options);
    }
}