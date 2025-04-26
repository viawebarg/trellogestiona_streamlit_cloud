<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Descripción: Módulo para integrar el gestor de tareas Trello con Dolibarr
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Class modTrelloGestiona
 * Descripción del módulo TrelloGestiona
 */
class modTrelloGestiona extends DolibarrModules
{
    /**
     * Constructor. Define nombres, constantes, directorios, cajas, permisos
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;

        // ID del módulo (debe ser único)
        $this->numero = 500000; // TODO: verificar que no esté en uso

        // Familia del módulo
        $this->family = "crm";
        $this->module_position = 500;

        // Módulo habilitado por defecto (0=No, 1=Si)
        $this->always_enabled = 0;

        // Nombre del módulo
        $this->name = preg_replace('/^mod/i', '', get_class($this));

        // Descripción del módulo
        $this->description = "Integración con el gestor de tareas Trello";

        // Autor del módulo
        $this->editor_name = 'TrelloGestiona';
        $this->editor_url = '';

        // Versión del módulo
        $this->version = '1.0.0';

        // Requisito mínimo de versión de Dolibarr
        $this->need_dolibarr_version = array(11, 0);

        // Dependencias del módulo
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->phpmin = array(5, 6);

        // Constantes del módulo
        $this->const = array(
            0 => array(
                'TRELLOGESTIONA_STREAMLIT_URL',
                'chaine',
                'http://localhost:5000',
                'URL de la aplicación Streamlit',
                0,
                'current',
                1
            ),
            1 => array(
                'TRELLOGESTIONA_API_KEY',
                'chaine',
                '',
                'Clave API de Trello',
                0,
                'current',
                1
            ),
            2 => array(
                'TRELLOGESTIONA_TOKEN',
                'chaine',
                '',
                'Token de Trello',
                0,
                'current',
                1
            ),
            3 => array(
                'TRELLOGESTIONA_API_TOKEN',
                'chaine',
                md5(uniqid(mt_rand(), true)),
                'Token para la API de TrelloGestiona',
                0,
                'current',
                1
            )
        );

        // Array para agregar tabs
        $this->tabs = array();
        
        // Hooks
        $this->module_parts = array(
            'hooks' => array(
                'projectcard',         // Ficha de proyecto
                'projecttab'           // Pestaña de proyecto
            )
        );

        // Diccionarios
        $this->dictionaries = array();

        // Cajas/Widgets
        $this->boxes = array(
            array(
                'file' => 'trellogestiona_box.php@trellogestiona',
                'note' => '',
                'enabledbydefaulton' => 'Home'
            )
        );

        // Permisos
        $this->rights = array();
        $this->rights_class = 'trellogestiona';
        $r = 0;

        // Permiso principal - leer
        $this->rights[$r][0] = $this->numero + $r;
        $this->rights[$r][1] = 'Leer datos de Trello';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'read';
        $r++;
        
        // Permiso para automatizar
        $this->rights[$r][0] = $this->numero + $r;
        $this->rights[$r][1] = 'Administrar automatizaciones';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'automatizacion';
        $r++;
        
        // Permiso para configurar
        $this->rights[$r][0] = $this->numero + $r;
        $this->rights[$r][1] = 'Configurar módulo';
        $this->rights[$r][3] = 1; // Solo administradores
        $this->rights[$r][4] = 'config';
        $r++;

        // Menu entries
        $this->menu = array();
        $r = 0;

        // Menú principal
        $this->menu[$r] = array(
            'fk_menu' => 0,
            'type' => 'top',
            'titre' => 'TrelloGestiona',
            'mainmenu' => 'trellogestiona',
            'leftmenu' => '0',
            'url' => '/trellogestiona/index.php',
            'langs' => 'trellogestiona@trellogestiona',
            'position' => 100 + $r,
            'enabled' => '1',
            'perms' => '$user->rights->trellogestiona->read',
            'target' => '',
            'user' => 0
        );
        $r++;

        // Submenú - Dashboard
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=trellogestiona',
            'type' => 'left',
            'titre' => 'Dashboard',
            'mainmenu' => 'trellogestiona',
            'leftmenu' => 'dashboard',
            'url' => '/trellogestiona/dashboard.php',
            'langs' => 'trellogestiona@trellogestiona',
            'position' => 100 + $r,
            'enabled' => '1',
            'perms' => '$user->rights->trellogestiona->read',
            'target' => '',
            'user' => 0
        );
        $r++;

        // Submenú - Automatización
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=trellogestiona',
            'type' => 'left',
            'titre' => 'Automatización',
            'mainmenu' => 'trellogestiona',
            'leftmenu' => 'automatizacion',
            'url' => '/trellogestiona/automatizacion.php',
            'langs' => 'trellogestiona@trellogestiona',
            'position' => 100 + $r,
            'enabled' => '1',
            'perms' => '$user->rights->trellogestiona->automatizacion',
            'target' => '',
            'user' => 0
        );
        $r++;

        // Submenú - Proyectos Trello
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=trellogestiona',
            'type' => 'left',
            'titre' => 'ProyectosTrello',
            'mainmenu' => 'trellogestiona',
            'leftmenu' => 'proyectos',
            'url' => '/trellogestiona/proyecto_trello.php',
            'langs' => 'trellogestiona@trellogestiona',
            'position' => 100 + $r,
            'enabled' => '1',
            'perms' => '$user->rights->trellogestiona->read',
            'target' => '',
            'user' => 0
        );
        $r++;

        // Submenú - Configuración
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=trellogestiona',
            'type' => 'left',
            'titre' => 'Configuración',
            'mainmenu' => 'trellogestiona',
            'leftmenu' => 'configuracion',
            'url' => '/trellogestiona/setup.php',
            'langs' => 'trellogestiona@trellogestiona',
            'position' => 100 + $r,
            'enabled' => '1',
            'perms' => '$user->rights->trellogestiona->config',
            'target' => '',
            'user' => 0
        );
        $r++;
    }

    /**
     * Function called when module is enabled.
     * The init function add constants, boxes, permissions and menus
     * (defined in constructor) into Dolibarr database.
     * It also creates data directories
     *
     * @param   string  $options    Options when enabling module ('', 'noboxes')
     * @return  int                 1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        $sql = array();

        $result = $this->loadTables();

        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted
     *
     * @param   string  $options    Options when enabling module ('', 'noboxes')
     * @return  int                 1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();

        return $this->_remove($sql, $options);
    }

    /**
     * Create tables, keys and data required by module
     * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     * and create data commands must be stored in directory /trellogestiona/sql/
     * This function is called by this->init
     *
     * @return  int     <=0 if KO, >0 if OK
     */
    protected function loadTables()
    {
        return $this->_load_tables('/trellogestiona/sql/');
    }
}