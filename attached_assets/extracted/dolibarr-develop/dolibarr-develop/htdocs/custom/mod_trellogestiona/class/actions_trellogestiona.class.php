<?php
/**
 * Class ActionsWemoJobs
 * Hooks del módulo TrelloGestiona
 */
class ActionsTrelloGestiona
{
    /**
     * Constructor
     *
     * @param DoliDB $db Base de datos
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Añade pestaña en la ficha de proyecto
     *
     * @param array $parameters Parámetros del hook
     * @param object $object Objeto proyecto
     * @param string $action Acción en curso
     * @param HookManager $hookmanager Gestor de hooks
     * @return int Status
     */
    public function addProjectTab($parameters, &$object, &$action, $hookmanager)
    {
        global $langs, $conf, $user;

        if (!isset($parameters['object']) || !is_object($parameters['object'])) {
            return 0;
        }

        if ($parameters['currentcontext'] != 'projecttab') {
            return 0;
        }

        // Comprobar permisos
        if (!$user->rights->trellogestiona->read) {
            return 0;
        }

        // Cargamos traducciones
        $langs->load("trellogestiona@trellogestiona");

        // Añadir la pestaña
        $tab = array(
            'title' => 'TrelloGestiona',
            'url' => dol_buildpath('/trellogestiona/project_tab.php', 1).'?id='.$object->id
        );
        
        // Insertar tab en la lista de pestañas
        $reshook = $hookmanager->executeHooks('insertExtraProjectTab', $tab);
        if (empty($reshook)) {
            $parameters['head']['trellogestiona'] = $tab;
        }

        return 1;
    }

    /**
     * Añade botón en la ficha de proyecto
     *
     * @param array $parameters Parámetros del hook
     * @param object $object Objeto proyecto
     * @param string $action Acción en curso
     * @param HookManager $hookmanager Gestor de hooks
     * @return int Status
     */
    public function addProjectActionButton($parameters, &$object, &$action, $hookmanager)
    {
        global $langs, $conf, $user;

        if (!isset($parameters['object']) || !is_object($parameters['object'])) {
            return 0;
        }

        if ($parameters['currentcontext'] != 'projectcard') {
            return 0;
        }

        // Comprobar permisos
        if (!$user->rights->trellogestiona->read) {
            return 0;
        }

        // Cargamos traducciones
        $langs->load("trellogestiona@trellogestiona");
        
        // Comprobar si ya está vinculado
        require_once DOL_DOCUMENT_ROOT.'/custom/trellogestiona/lib/trellogestiona.lib.php';
        $board = get_project_trello_board($object->id);
        
        // Preparar botón
        if ($board) {
            // Ya está vinculado, mostrar enlace al tablero
            $button = '<a class="butAction" href="'.get_streamlit_url_with_params(array('board_id' => $board['board_id'])).'" target="_blank">';
            $button.= 'Ver tablero Trello';
            $button.= '</a>';
        } else {
            // No está vinculado, mostrar botón para vincular
            $button = '<a class="butAction" href="'.dol_buildpath('/trellogestiona/proyecto_trello.php', 1).'?action=link&project_id='.$object->id.'">';
            $button.= 'Vincular con Trello';
            $button.= '</a>';
        }
        
        // Añadir botón a la barra de acciones
        $html = '
            <div class="inline-block divButAction">
                '.$button.'
            </div>
        ';

        $hookmanager->resPrint = $html;
        return 1;
    }
}