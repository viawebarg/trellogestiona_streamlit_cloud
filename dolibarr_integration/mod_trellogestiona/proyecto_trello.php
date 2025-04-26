<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Página para vincular proyectos de Dolibarr con tableros de Trello
 */

// Cargar Dolibarr
$res = 0;
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Archivo de inclusión principal no encontrado");

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

// Control de acceso
if (!$user->rights->trellogestiona->read) accessforbidden();

// Cargar traducciones
$langs->loadLangs(array('trellogestiona@trellogestiona', 'projects'));

// Parámetros
$action = GETPOST('action', 'aZ09');
$project_id = GETPOST('project_id', 'int');
$tablero_id = GETPOST('tablero_id', 'alpha');

// Obtener URL de Streamlit y credenciales de Trello
$streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;
$trello_api_key = $conf->global->TRELLOGESTIONA_API_KEY;
$trello_token = $conf->global->TRELLOGESTIONA_TOKEN;

// Cargar el proyecto
$project = new Project($db);
if ($project_id > 0) {
    $result = $project->fetch($project_id);
    if ($result <= 0) {
        dol_print_error($db, $project->error);
        exit;
    }
}

/*
 * Acciones
 */

if ($action == 'link' && !empty($project_id) && !empty($tablero_id)) {
    // Guardar la relación en la base de datos
    $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
            WHERE fk_project = ".$project_id;
    $resql = $db->query($sql);
    
    if ($resql && $db->num_rows($resql) > 0) {
        // Actualizar relación existente
        $sql = "UPDATE ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
                SET tablero_id = '".$db->escape($tablero_id)."',
                    tms = '".$db->idate(dol_now())."',
                    fk_user_modif = ".$user->id."
                WHERE fk_project = ".$project_id;
    } else {
        // Crear nueva relación
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
                (fk_project, tablero_id, date_creation, fk_user_creat)
                VALUES (".$project_id.", '".$db->escape($tablero_id)."', 
                '".$db->idate(dol_now())."', ".$user->id.")";
    }
    
    $result = $db->query($sql);
    if ($result) {
        setEventMessages($langs->trans("ProjectLinkedWithBoard"), null, 'mesgs');
    } else {
        dol_print_error($db);
    }
}

if ($action == 'unlink' && !empty($project_id)) {
    // Eliminar la relación de la base de datos
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
            WHERE fk_project = ".$project_id;
    
    $result = $db->query($sql);
    if ($result) {
        setEventMessages($langs->trans("ProjectUnlinkedFromBoard"), null, 'mesgs');
    } else {
        dol_print_error($db);
    }
}

/*
 * Vista
 */

$title = $langs->trans("LinkProjectWithTrello");

llxHeader('', $title);

print load_fiche_titre($title, '', 'trellogestiona.png@trellogestiona');

// Verificar si está configurada la URL de Streamlit y credenciales de Trello
if (empty($streamlit_url)) {
    print '<div class="warning">'.$langs->trans("NoStreamlitURLConfigured").'</div>';
    print '<a href="'.DOL_URL_ROOT.'/custom/trellogestiona/setup.php" class="button">'.$langs->trans("GoToSetup").'</a>';
    llxFooter();
    exit;
}

if (empty($trello_api_key) || empty($trello_token)) {
    print '<div class="warning">'.$langs->trans("NoTrelloCredentialsConfigured").'</div>';
    print '<a href="'.DOL_URL_ROOT.'/custom/trellogestiona/setup.php" class="button">'.$langs->trans("GoToSetup").'</a>';
    llxFooter();
    exit;
}

// Si hay un proyecto seleccionado, mostrar información del proyecto
if ($project_id > 0) {
    // Información del proyecto
    print '<div class="fichecenter">';
    print '<div class="fichehalfleft">';
    print '<div class="div-table-responsive-no-min">';
    print '<table class="border centpercent">';
    print '<tr><td class="titlefield">'.$langs->trans("Ref").'</td><td>'.$project->ref.'</td></tr>';
    print '<tr><td>'.$langs->trans("Label").'</td><td>'.$project->title.'</td></tr>';
    print '<tr><td>'.$langs->trans("Description").'</td><td>'.$project->description.'</td></tr>';
    print '</table>';
    print '</div>';
    print '</div>';
    print '</div>';
    
    // Buscar si el proyecto ya está vinculado con un tablero
    $tablero_actual = '';
    $sql = "SELECT tablero_id FROM ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero 
            WHERE fk_project = ".$project_id;
    $resql = $db->query($sql);
    if ($resql && $db->num_rows($resql) > 0) {
        $obj = $db->fetch_object($resql);
        $tablero_actual = $obj->tablero_id;
    }
    
    // Si ya está vinculado, mostrar la información del tablero
    if (!empty($tablero_actual)) {
        print '<div class="fichecenter">';
        print '<div class="fichehalfleft">';
        print '<div class="div-table-responsive-no-min">';
        print '<table class="border centpercent">';
        print '<tr class="liste_titre">';
        print '<td colspan="2">'.$langs->trans("LinkedTrelloBoard").'</td>';
        print '</tr>';
        print '<tr><td>'.$langs->trans("BoardID").'</td><td>'.$tablero_actual.'</td></tr>';
        print '</table>';
        print '</div>';
        print '</div>';
        print '</div>';
        
        // Botón para desenlazar
        print '<div class="tabsAction">';
        print '<a class="butActionDelete" href="'.$_SERVER['PHP_SELF'].'?project_id='.$project_id.'&action=unlink">'.$langs->trans("UnlinkFromTrello").'</a>';
        print '</div>';
    } else {
        // Formulario para vincular con un tablero de Trello
        print '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
        print '<input type="hidden" name="token" value="'.newToken().'">';
        print '<input type="hidden" name="action" value="link">';
        print '<input type="hidden" name="project_id" value="'.$project_id.'">';
        
        print '<div class="fichecenter">';
        print '<div class="fichehalfleft">';
        print '<div class="div-table-responsive-no-min">';
        print '<table class="border centpercent">';
        print '<tr class="liste_titre">';
        print '<td colspan="2">'.$langs->trans("LinkWithTrelloBoard").'</td>';
        print '</tr>';
        print '<tr><td>'.$langs->trans("TrelloBoardID").'</td><td><input type="text" name="tablero_id" value="" size="40"></td></tr>';
        print '</table>';
        print '</div>';
        print '</div>';
        print '</div>';
        
        print '<div class="center">';
        print '<input type="submit" class="button" value="'.$langs->trans("LinkWithTrello").'">';
        print '</div>';
        
        print '</form>';
    }
} else {
    // Mostrar la lista de proyectos para seleccionar
    print '<div class="div-table-responsive-no-min">';
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans("Ref").'</td>';
    print '<td>'.$langs->trans("Label").'</td>';
    print '<td>'.$langs->trans("DateStart").'</td>';
    print '<td>'.$langs->trans("DateEnd").'</td>';
    print '<td>'.$langs->trans("Status").'</td>';
    print '<td>'.$langs->trans("TrelloStatus").'</td>';
    print '<td></td>';
    print '</tr>';
    
    $sql = "SELECT p.rowid, p.ref, p.title, p.dateo, p.datee, p.fk_statut, 
                  t.tablero_id
            FROM ".MAIN_DB_PREFIX."projet as p
            LEFT JOIN ".MAIN_DB_PREFIX."trellogestiona_proyecto_tablero as t ON p.rowid = t.fk_project
            ORDER BY p.ref DESC";
    
    $resql = $db->query($sql);
    if ($resql) {
        $num = $db->num_rows($resql);
        
        if ($num > 0) {
            $i = 0;
            while ($i < $num) {
                $obj = $db->fetch_object($resql);
                
                print '<tr class="oddeven">';
                print '<td><a href="'.$_SERVER['PHP_SELF'].'?project_id='.$obj->rowid.'">'.$obj->ref.'</a></td>';
                print '<td>'.$obj->title.'</td>';
                print '<td>'.dol_print_date($db->jdate($obj->dateo), 'day').'</td>';
                print '<td>'.dol_print_date($db->jdate($obj->datee), 'day').'</td>';
                print '<td>'.$langs->trans($project->LibStatut($obj->fk_statut)).'</td>';
                print '<td>';
                if (!empty($obj->tablero_id)) {
                    print '<span class="badge badge-status4">'.$langs->trans("Linked").'</span>';
                } else {
                    print '<span class="badge badge-status8">'.$langs->trans("NotLinked").'</span>';
                }
                print '</td>';
                print '<td>';
                if (!empty($obj->tablero_id)) {
                    print '<a href="'.$_SERVER['PHP_SELF'].'?project_id='.$obj->rowid.'&action=unlink" class="butActionDelete">'.$langs->trans("Unlink").'</a>';
                } else {
                    print '<a href="'.$_SERVER['PHP_SELF'].'?project_id='.$obj->rowid.'" class="butAction">'.$langs->trans("Link").'</a>';
                }
                print '</td>';
                print '</tr>';
                
                $i++;
            }
        } else {
            print '<tr><td colspan="7"><span class="opacitymedium">'.$langs->trans("NoProjects").'</span></td></tr>';
        }
    } else {
        dol_print_error($db);
    }
    
    print '</table>';
    print '</div>';
}

// Cerrar página
llxFooter();
$db->close();