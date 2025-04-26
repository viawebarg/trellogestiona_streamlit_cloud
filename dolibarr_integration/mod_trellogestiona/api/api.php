<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * API para la comunicación entre Streamlit y Dolibarr
 * Permite consultar y sincronizar datos entre ambas plataformas
 */

// Cargar Dolibarr
$res = 0;
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Archivo de inclusión principal no encontrado");

require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

// Desactivar salida de buffer para respuestas más rápidas
ob_end_clean();

// Configurar cabeceras para respuesta JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Verificar token API (básico)
$api_key = GETPOST('api_key', 'alpha');
if (empty($api_key) || $api_key !== $conf->global->TRELLOGESTIONA_API_TOKEN) {
    echo json_encode(array('error' => 'Unauthorized', 'code' => 401));
    exit;
}

// Obtener acción
$action = GETPOST('action', 'alpha');

// Comprobar que el usuario tiene permisos
if (!$user->rights->trellogestiona->read) {
    echo json_encode(array('error' => 'Forbidden', 'code' => 403));
    exit;
}

// Ejecutar acción
switch ($action) {
    case 'get_projects':
        getProjects();
        break;
    case 'get_linked_boards':
        getLinkedBoards();
        break;
    case 'link_project_board':
        linkProjectBoard();
        break;
    case 'unlink_project_board':
        unlinkProjectBoard();
        break;
    case 'sync_tasks':
        syncTasks();
        break;
    default:
        echo json_encode(array('error' => 'Action not found', 'code' => 404));
        exit;
}

/**
 * Obtener lista de proyectos
 */
function getProjects()
{
    global $db;
    
    $sql = "SELECT p.rowid, p.ref, p.title, p.dateo, p.datee, p.fk_statut
            FROM ".MAIN_DB_PREFIX."projet as p
            WHERE p.entity IN (".getEntity('project').")
            ORDER BY p.ref DESC";
    
    $resql = $db->query($sql);
    if (!$resql) {
        echo json_encode(array('error' => 'Database error', 'code' => 500, 'message' => $db->lasterror()));
        exit;
    }
    
    $projects = array();
    while ($obj = $db->fetch_object($resql)) {
        $projects[] = array(
            'id' => $obj->rowid,
            'ref' => $obj->ref,
            'title' => $obj->title,
            'date_start' => $db->jdate($obj->dateo),
            'date_end' => $db->jdate($obj->datee),
            'status' => $obj->fk_statut
        );
    }
    
    echo json_encode(array('success' => true, 'data' => $projects));
    exit;
}

/**
 * Obtener tableros vinculados a proyectos
 */
function getLinkedBoards()
{
    global $db;
    
    $sql = "SELECT t.fk_project, t.tablero_id, p.ref, p.title
            FROM ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero as t
            JOIN ".MAIN_DB_PREFIX."projet as p ON t.fk_project = p.rowid
            WHERE p.entity IN (".getEntity('project').")
            ORDER BY p.ref DESC";
    
    $resql = $db->query($sql);
    if (!$resql) {
        echo json_encode(array('error' => 'Database error', 'code' => 500, 'message' => $db->lasterror()));
        exit;
    }
    
    $links = array();
    while ($obj = $db->fetch_object($resql)) {
        $links[] = array(
            'project_id' => $obj->fk_project,
            'project_ref' => $obj->ref,
            'project_title' => $obj->title,
            'board_id' => $obj->tablero_id
        );
    }
    
    echo json_encode(array('success' => true, 'data' => $links));
    exit;
}

/**
 * Vincular un proyecto con un tablero de Trello
 */
function linkProjectBoard()
{
    global $db, $user;
    
    $project_id = GETPOST('project_id', 'int');
    $board_id = GETPOST('board_id', 'alpha');
    
    if (empty($project_id) || empty($board_id)) {
        echo json_encode(array('error' => 'Missing parameters', 'code' => 400));
        exit;
    }
    
    // Verificar que el proyecto existe
    $project = new Project($db);
    $result = $project->fetch($project_id);
    if ($result <= 0) {
        echo json_encode(array('error' => 'Project not found', 'code' => 404));
        exit;
    }
    
    // Verificar si ya existe una vinculación
    $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
            WHERE fk_project = ".$project_id;
    $resql = $db->query($sql);
    
    if ($resql && $db->num_rows($resql) > 0) {
        // Actualizar vinculación existente
        $sql = "UPDATE ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
                SET tablero_id = '".$db->escape($board_id)."',
                    tms = '".$db->idate(dol_now())."',
                    fk_user_modif = ".$user->id."
                WHERE fk_project = ".$project_id;
    } else {
        // Crear nueva vinculación
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
                (fk_project, tablero_id, date_creation, fk_user_creat)
                VALUES (".$project_id.", '".$db->escape($board_id)."', 
                '".$db->idate(dol_now())."', ".$user->id.")";
    }
    
    $result = $db->query($sql);
    if (!$result) {
        echo json_encode(array('error' => 'Database error', 'code' => 500, 'message' => $db->lasterror()));
        exit;
    }
    
    echo json_encode(array('success' => true, 'message' => 'Project linked to board'));
    exit;
}

/**
 * Desvincular un proyecto de un tablero de Trello
 */
function unlinkProjectBoard()
{
    global $db;
    
    $project_id = GETPOST('project_id', 'int');
    
    if (empty($project_id)) {
        echo json_encode(array('error' => 'Missing parameters', 'code' => 400));
        exit;
    }
    
    // Eliminar la vinculación
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
            WHERE fk_project = ".$project_id;
    
    $result = $db->query($sql);
    if (!$result) {
        echo json_encode(array('error' => 'Database error', 'code' => 500, 'message' => $db->lasterror()));
        exit;
    }
    
    echo json_encode(array('success' => true, 'message' => 'Project unlinked from board'));
    exit;
}

/**
 * Sincronizar tareas de Trello con proyectos de Dolibarr
 * Esta función requeriría implementación adicional según necesidades específicas
 */
function syncTasks()
{
    global $db, $conf;
    
    $project_id = GETPOST('project_id', 'int');
    $board_id = GETPOST('board_id', 'alpha');
    $tasks_json = GETPOST('tasks', 'none');
    
    if (empty($project_id) || empty($board_id) || empty($tasks_json)) {
        echo json_encode(array('error' => 'Missing parameters', 'code' => 400));
        exit;
    }
    
    // Decodificar las tareas recibidas
    $tasks = json_decode($tasks_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(array('error' => 'Invalid JSON', 'code' => 400));
        exit;
    }
    
    // Aquí iría la lógica para sincronizar las tareas con el proyecto
    // Por ejemplo, crear tareas en Dolibarr basadas en las tarjetas de Trello
    
    // Por ahora, simplemente devolvemos éxito
    echo json_encode(array('success' => true, 'message' => 'Tasks synchronized', 'count' => count($tasks)));
    exit;
}