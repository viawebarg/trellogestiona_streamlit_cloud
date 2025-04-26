<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Clase de hooks para el módulo TrelloGestiona
 * Permite la integración con otros módulos de Dolibarr
 */

class TrelloGestionaHook
{
    private $db;

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
     * Acción ejecutada cuando se visualiza un proyecto
     * Permite agregar una sección de tareas de Trello en la ficha de proyecto
     *
     * @param   array           $parameters     Array de parámetros del hook
     * @param   CommonObject    $object         Objeto que llama al hook
     * @param   string          $action         Acción en curso
     * @param   HookManager     $hookmanager    Gestor de hooks
     * @return  int                             0=OK, >0=Error
     */
    public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $user, $langs;

        // Hook solo para proyectos
        if ($parameters['currentcontext'] == 'projectcard' && $user->rights->trellogestiona->read) {
            // Comprobar si es una ficha de proyecto
            if (is_object($object) && $object->element == 'project') {
                // Obtener URL de la aplicación Streamlit
                $streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;
                
                if (!empty($streamlit_url)) {
                    // Añadir un botón de acción para sincronizar con Trello
                    print '<a class="butAction" href="'.DOL_URL_ROOT.'/custom/trellogestiona/dashboard.php?project_id='.$object->id.'">'.$langs->trans("SyncWithTrello").'</a>';
                }
            }
        }
        
        return 0;
    }
    
    /**
     * Acción ejecutada en la barra de acciones de un proyecto
     * Permite agregar acciones de sincronización con Trello
     *
     * @param   array           $parameters     Array de parámetros del hook
     * @param   CommonObject    $object         Objeto que llama al hook
     * @param   string          $action         Acción en curso
     * @param   HookManager     $hookmanager    Gestor de hooks
     * @return  int                             0=OK, >0=Error
     */
    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $user, $langs;

        // Hook solo para proyectos
        if ($parameters['currentcontext'] == 'projecttab' && $user->rights->trellogestiona->read) {
            // Comprobar si es una pestaña de proyecto
            if (is_object($object) && $object->element == 'project') {
                // Obtener URL de la aplicación Streamlit
                $streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;
                
                if (!empty($streamlit_url)) {
                    // Añadir un bloque de contenido para mostrar tareas de Trello
                    print '<div class="fichecenter">';
                    print '<div class="fichehalfleft">';
                    print '<div class="div-table-responsive-no-min">';
                    print '<table class="noborder centpercent">';
                    print '<tr class="liste_titre">';
                    print '<td colspan="2">'.$langs->trans("TrelloTasks").'</td>';
                    print '</tr>';
                    
                    // Aquí iría la lógica para mostrar tareas de Trello asociadas a este proyecto
                    print '<tr class="oddeven">';
                    print '<td colspan="2">';
                    print '<a href="'.DOL_URL_ROOT.'/custom/trellogestiona/dashboard.php?project_id='.$object->id.'">'.$langs->trans("ViewTrelloTasks").'</a>';
                    print '</td>';
                    print '</tr>';
                    
                    print '</table>';
                    print '</div>';
                    print '</div>';
                    print '</div>';
                }
            }
        }
        
        return 0;
    }
}