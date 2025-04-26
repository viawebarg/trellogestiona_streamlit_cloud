<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Clase para crear un widget de tareas de Trello en el escritorio de Dolibarr
 */

include_once DOL_DOCUMENT_ROOT.'/core/boxes/modules_boxes.php';

/**
 * Clase para gestionar el widget de tareas Trello
 */
class trellogestiona_box extends ModeleBoxes
{
    /**
     * @var string Caja habilitada por defecto al instalar
     */
    public $boxcode = "trellogestiona";

    /**
     * @var string Permiso necesario para ver la caja
     */
    public $boximg = "trellogestiona@trellogestiona";

    /**
     * @var string Texto de la caja
     */
    public $boxlabel = "BoxTrelloTasks";

    /**
     * @var string Texto descriptivo
     */
    public $boxlabelhelp = "BoxTrelloTasksDescription";

    /**
     * @var DoliDB Database handler
     */
    public $db;

    /**
     * @var int box_id Can be used to show only some boxes
     */
    public $box_id;

    /**
     * @var int Orden de la caja
     */
    public $box_order;

    /**
     * @var array Opciones de la caja
     */
    public $options;

    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     * @param string $param Más parámetros
     */
    public function __construct($db, $param = '')
    {
        global $user;

        $this->db = $db;

        $this->hidden = !($user->rights->trellogestiona->read);
    }

    /**
     * Cargar datos en memoria para mostrar la caja
     *
     * @param array $max Número máximo de registros a mostrar
     * @return void
     */
    public function loadBox($max = 5)
    {
        global $conf, $user, $langs;

        $this->max = $max;

        // Revisar si el módulo está activo
        if (empty($conf->trellogestiona->enabled)) {
            $this->info_box_contents = array();
            return;
        }

        // Revisar si hay permisos necesarios
        if (!$user->rights->trellogestiona->read) {
            $this->info_box_contents = array();
            return;
        }

        // URL de la aplicación Streamlit y credenciales de Trello
        $streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;
        $trello_api_key = $conf->global->TRELLOGESTIONA_API_KEY;
        $trello_token = $conf->global->TRELLOGESTIONA_TOKEN;

        // Si falta alguna configuración, mostrar mensaje
        if (empty($streamlit_url) || empty($trello_api_key) || empty($trello_token)) {
            $this->info_box_head = array(
                'text' => $langs->trans("BoxTrelloTasks"),
                'limit' => 0,
                'sublink' => '/custom/trellogestiona/setup.php',
                'subtext' => $langs->trans("GoToSetup"),
                'subpicto' => 'setup',
                'subclass' => 'center',
            );

            $this->info_box_contents[0][0] = array(
                'td' => 'class="nohover opacitymedium center"',
                'text' => $langs->trans("ModuleNotConfigured"),
            );
            return;
        }

        $this->info_box_head = array(
            'text' => $langs->trans("BoxTrelloTasks"),
            'limit' => 0,
            'sublink' => '/custom/trellogestiona/dashboard.php',
            'subtext' => $langs->trans("AccessDashboard"),
            'subpicto' => 'object_trellogestiona@trellogestiona',
            'subclass' => 'center',
        );

        // Obtener tareas de Trello
        $tareas = $this->getTareasTrello($max);

        if (empty($tareas) || !is_array($tareas)) {
            $this->info_box_contents[0][0] = array(
                'td' => 'class="nohover opacitymedium center"',
                'text' => $langs->trans("NoTasksFound"),
            );
            return;
        }

        // Encabezados de la tabla
        $line = 0;
        $this->info_box_contents[$line][0] = array(
            'td' => 'class="liste_titre"',
            'text' => $langs->trans("Task"),
        );
        $this->info_box_contents[$line][1] = array(
            'td' => 'class="liste_titre right"',
            'text' => $langs->trans("DueDate"),
        );
        $this->info_box_contents[$line][2] = array(
            'td' => 'class="liste_titre right"',
            'text' => $langs->trans("Priority"),
        );
        $line++;

        // Contenido de la tabla
        foreach ($tareas as $tarea) {
            $this->info_box_contents[$line][0] = array(
                'td' => 'class="tdoverflowmax200"',
                'text' => $tarea['nombre'],
                'url' => '/custom/trellogestiona/dashboard.php?tarea_id=' . $tarea['id'],
            );

            $this->info_box_contents[$line][1] = array(
                'td' => 'class="right"',
                'text' => $tarea['fecha_vencimiento'] ? dol_print_date($tarea['fecha_vencimiento'], 'day') : '',
            );

            $priority_class = '';
            if ($tarea['prioridad'] == 'Alta') {
                $priority_class = 'class="right error"';
            } elseif ($tarea['prioridad'] == 'Media') {
                $priority_class = 'class="right warning"';
            } else {
                $priority_class = 'class="right"';
            }

            $this->info_box_contents[$line][2] = array(
                'td' => $priority_class,
                'text' => $langs->trans($tarea['prioridad']),
            );
            $line++;
        }
    }

    /**
     * Obtener tareas de Trello
     * En una implementación real, esto obtendría datos de la API o de la base de datos
     *
     * @param int $max Número máximo de tareas a obtener
     * @return array Lista de tareas
     */
    private function getTareasTrello($max = 5)
    {
        global $db;

        // En una implementación real, obtendríamos las tareas de la base de datos
        // o haríamos una llamada a la API de Trello
        // Para pruebas, retornamos datos de ejemplo
        $tareas = array();

        // Intentar obtener tareas de la base de datos (tableros vinculados a proyectos)
        $sql = "SELECT t.tablero_id FROM " . MAIN_DB_PREFIX . "trellogestiona_proyecto_tablero as t";
        $sql .= " JOIN " . MAIN_DB_PREFIX . "projet as p ON t.fk_project = p.rowid";
        $sql .= " WHERE p.fk_statut = 1"; // Sólo proyectos abiertos
        $sql .= " ORDER BY p.dateo DESC";
        $sql .= " LIMIT " . (int) $max;
        
        $resql = $db->query($sql);
        if ($resql && $db->num_rows($resql) > 0) {
            // Hay tableros vinculados, podríamos usar esta información para obtener tareas
            // Por ahora, simplemente mostramos ejemplos
            $tableros = array();
            while ($obj = $db->fetch_object($resql)) {
                $tableros[] = $obj->tablero_id;
            }
        }

        // Ejemplo de tareas
        $tareas = array(
            array(
                'id' => '1',
                'nombre' => 'Ejemplo de tarea 1',
                'fecha_vencimiento' => dol_now() + (3600 * 24 * 2), // 2 días desde ahora
                'prioridad' => 'Alta',
            ),
            array(
                'id' => '2',
                'nombre' => 'Ejemplo de tarea 2',
                'fecha_vencimiento' => dol_now() + (3600 * 24 * 5), // 5 días desde ahora
                'prioridad' => 'Media',
            ),
            array(
                'id' => '3',
                'nombre' => 'Ejemplo de tarea 3',
                'fecha_vencimiento' => dol_now() + (3600 * 24 * 10), // 10 días desde ahora
                'prioridad' => 'Baja',
            ),
        );

        return $tareas;
    }
}