<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Página de documentación de la API
 */

// Cargar Dolibarr
$res = 0;
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Archivo de inclusión principal no encontrado");

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Control de acceso
if (!$user->rights->trellogestiona->config) accessforbidden();

// Cargar traducciones
$langs->loadLangs(array('trellogestiona@trellogestiona'));

/*
 * Vista
 */

$title = $langs->trans("APIUsage");

llxHeader('', $title);

print load_fiche_titre($title, '', 'object_trellogestiona@trellogestiona');

// Información general de la API
print '<div class="fichecenter">';
print '<div class="fichehalfleft">';

print '<table class="border centpercent tableforfield">';
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("APIInstructions").'</td>';
print '</tr>';

// Punto de acceso (endpoint)
print '<tr>';
print '<td>'.$langs->trans("APIEndpoint").'</td>';
print '<td><code>'.DOL_URL_ROOT.'/custom/trellogestiona/api/api.php</code></td>';
print '</tr>';

// Token API
print '<tr>';
print '<td>'.$langs->trans("APIToken").'</td>';
print '<td><code>'.$conf->global->TRELLOGESTIONA_API_TOKEN.'</code></td>';
print '</tr>';

print '</table>';

print '</div>';
print '<div class="fichehalfright">';

print '<div class="underbanner clearboth"></div>';
print '<table class="border centpercent tableforfield">';

print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("RequiredParameters").'</td>';
print '</tr>';

// Parámetros obligatorios
print '<tr>';
print '<td>api_key</td>';
print '<td>'.$langs->trans("APITokenDesc").'</td>';
print '</tr>';

print '<tr>';
print '<td>action</td>';
print '<td>'.$langs->trans("ActionToPerform").'</td>';
print '</tr>';

print '</table>';

print '</div>';
print '</div>'; // fin fichecenter

// Ejemplos de llamadas a la API
print '<div class="fichecenter">';
print '<div class="underbanner clearboth"></div>';
print '<table class="border centpercent tableforfield">';

print '<tr class="liste_titre">';
print '<td colspan="3">'.$langs->trans("APIExamples").'</td>';
print '</tr>';

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Action").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td>'.$langs->trans("Example").'</td>';
print '</tr>';

// Obtener proyectos
print '<tr class="oddeven">';
print '<td>get_projects</td>';
print '<td>'.$langs->trans("GetProjectsDesc").'</td>';
print '<td>';
print '<code>GET '.DOL_URL_ROOT.'/custom/trellogestiona/api/api.php?api_key='.$conf->global->TRELLOGESTIONA_API_TOKEN.'&action=get_projects</code>';
print '</td>';
print '</tr>';

// Obtener tableros vinculados
print '<tr class="oddeven">';
print '<td>get_linked_boards</td>';
print '<td>'.$langs->trans("GetLinkedBoardsDesc").'</td>';
print '<td>';
print '<code>GET '.DOL_URL_ROOT.'/custom/trellogestiona/api/api.php?api_key='.$conf->global->TRELLOGESTIONA_API_TOKEN.'&action=get_linked_boards</code>';
print '</td>';
print '</tr>';

// Vincular proyecto con tablero
print '<tr class="oddeven">';
print '<td>link_project_board</td>';
print '<td>'.$langs->trans("LinkProjectBoardDesc").'</td>';
print '<td>';
print '<code>POST '.DOL_URL_ROOT.'/custom/trellogestiona/api/api.php<br>api_key='.$conf->global->TRELLOGESTIONA_API_TOKEN.'&action=link_project_board&project_id=123&board_id=abc</code>';
print '</td>';
print '</tr>';

// Desvincular proyecto
print '<tr class="oddeven">';
print '<td>unlink_project_board</td>';
print '<td>'.$langs->trans("UnlinkProjectBoardDesc").'</td>';
print '<td>';
print '<code>POST '.DOL_URL_ROOT.'/custom/trellogestiona/api/api.php<br>api_key='.$conf->global->TRELLOGESTIONA_API_TOKEN.'&action=unlink_project_board&project_id=123</code>';
print '</td>';
print '</tr>';

// Sincronizar tareas
print '<tr class="oddeven">';
print '<td>sync_tasks</td>';
print '<td>'.$langs->trans("SyncTasksDesc").'</td>';
print '<td>';
print '<code>POST '.DOL_URL_ROOT.'/custom/trellogestiona/api/api.php<br>api_key='.$conf->global->TRELLOGESTIONA_API_TOKEN.'&action=sync_tasks&project_id=123&board_id=abc&tasks=[...]</code>';
print '</td>';
print '</tr>';

print '</table>';
print '</div>'; // fin fichecenter

// Ejemplo de código Python para Streamlit
print '<div class="fichecenter">';
print '<div class="underbanner clearboth"></div>';
print '<table class="border centpercent tableforfield">';

print '<tr class="liste_titre">';
print '<td>'.$langs->trans("PythonExample").'</td>';
print '</tr>';

print '<tr>';
print '<td>';
print '<pre style="padding: 15px; background-color: #f8f8f8; overflow-x: auto;">';
print 'import requests
import json
import streamlit as st

# Configuración de la API
DOLIBARR_URL = "'.$conf->global->TRELLOGESTIONA_STREAMLIT_URL.'"
API_ENDPOINT = "'.DOL_URL_ROOT.'/custom/trellogestiona/api/api.php"
API_KEY = "'.$conf->global->TRELLOGESTIONA_API_TOKEN.'"

def get_projects():
    """Obtiene la lista de proyectos desde Dolibarr"""
    url = f"{API_ENDPOINT}?api_key={API_KEY}&action=get_projects"
    response = requests.get(url)
    if response.status_code == 200:
        data = response.json()
        if data.get("success"):
            return data.get("data", [])
    return []

def get_linked_boards():
    """Obtiene los tableros vinculados a proyectos"""
    url = f"{API_ENDPOINT}?api_key={API_KEY}&action=get_linked_boards"
    response = requests.get(url)
    if response.status_code == 200:
        data = response.json()
        if data.get("success"):
            return data.get("data", [])
    return []

def link_project_board(project_id, board_id):
    """Vincula un proyecto con un tablero de Trello"""
    url = f"{API_ENDPOINT}"
    data = {
        "api_key": API_KEY,
        "action": "link_project_board",
        "project_id": project_id,
        "board_id": board_id
    }
    response = requests.post(url, data=data)
    return response.json()

def unlink_project_board(project_id):
    """Desvincula un proyecto de su tablero de Trello"""
    url = f"{API_ENDPOINT}"
    data = {
        "api_key": API_KEY,
        "action": "unlink_project_board",
        "project_id": project_id
    }
    response = requests.post(url, data=data)
    return response.json()

def sync_tasks(project_id, board_id, tasks):
    """Sincroniza tareas de Trello con un proyecto de Dolibarr"""
    url = f"{API_ENDPOINT}"
    data = {
        "api_key": API_KEY,
        "action": "sync_tasks",
        "project_id": project_id,
        "board_id": board_id,
        "tasks": json.dumps(tasks)
    }
    response = requests.post(url, data=data)
    return response.json()

# Ejemplo de uso en Streamlit
def show_projects_page():
    st.title("Proyectos Dolibarr")
    
    projects = get_projects()
    if not projects:
        st.warning("No se encontraron proyectos o hubo un error de comunicación")
        return
        
    for project in projects:
        st.subheader(f"{project[\'ref\']} - {project[\'title\']}")
        
        # Resto de la implementación...';
print '</pre>';
print '</td>';
print '</tr>';

print '</table>';
print '</div>'; // fin fichecenter

// Cerrar página
llxFooter();
$db->close();