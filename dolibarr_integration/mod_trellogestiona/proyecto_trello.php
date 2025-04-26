<?php
/**
 * Página de vinculación de proyectos con tableros de Trello
 */

// Carga del entorno Dolibarr
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once './lib/trellogestiona.lib.php';

// Control de acceso
if (!$user->rights->trellogestiona->read) {
    accessforbidden();
}

// Parámetros
$action = GETPOST('action', 'alpha');
$project_id = GETPOST('project_id', 'int');
$board_id = GETPOST('board_id', 'alpha');
$board_name = GETPOST('board_name', 'alpha');

// Título de la página
$title = "Proyectos - Tableros Trello";
llxHeader('', $title);

// Mostrar pestañas
print_trellogestiona_tabs('proyecto_trello');

// Acciones
if ($action == 'link' && !empty($project_id)) {
    // Formulario para vincular proyecto con tablero
    $project = new Project($db);
    $project->fetch($project_id);
    
    print '<div class="tabBar">';
    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="link_confirm">';
    print '<input type="hidden" name="project_id" value="'.$project_id.'">';
    
    print '<h1>Vincular proyecto con tablero de Trello</h1>';
    
    print '<table class="border centpercent">';
    
    // Proyecto
    print '<tr>';
    print '<td class="titlefieldcreate">Proyecto:</td>';
    print '<td>'.$project->ref.' - '.$project->title.'</td>';
    print '</tr>';
    
    // Tablero Trello (versión simplificada sin API)
    print '<tr>';
    print '<td class="titlefieldcreate">ID del tablero Trello:</td>';
    print '<td><input type="text" name="board_id" value="" required size="40"></td>';
    print '</tr>';
    
    print '<tr>';
    print '<td class="titlefieldcreate">Nombre del tablero:</td>';
    print '<td><input type="text" name="board_name" value="" required size="40"></td>';
    print '</tr>';
    
    print '<tr>';
    print '<td colspan="2" class="center">';
    print '<div class="info-box" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0px;">';
    print '<p>Para vincular un proyecto con un tablero de Trello, necesitas el ID del tablero. Puedes encontrarlo en la URL del tablero:</p>';
    print '<p><code>https://trello.com/b/<strong>ESTE-ES-EL-ID</strong>/nombre-del-tablero</code></p>';
    print '<p>No necesitas credenciales de API para esta vinculación, ya que usamos la aplicación Streamlit embebida.</p>';
    print '</div>';
    print '</td>';
    print '</tr>';
    
    print '</table>';
    
    print '<div class="center">';
    print '<input type="submit" class="button buttonaction" value="Vincular">';
    print ' &nbsp; <a href="'.dol_buildpath('/trellogestiona/proyecto_trello.php', 1).'" class="button buttoncancel">Cancelar</a>';
    print '</div>';
    
    print '</form>';
    print '</div>';
}
elseif ($action == 'link_confirm' && !empty($project_id) && !empty($board_id)) {
    // Guardar vinculación en la base de datos
    $sql = "INSERT INTO ".MAIN_DB_PREFIX."trellogestiona_project_board";
    $sql.= " (fk_project, board_id, board_name, date_creation)";
    $sql.= " VALUES (".(int)$project_id.", '".$db->escape($board_id)."', '".$db->escape($board_name)."', '".$db->idate(dol_now())."')";
    $sql.= " ON DUPLICATE KEY UPDATE";
    $sql.= " board_id = '".$db->escape($board_id)."',";
    $sql.= " board_name = '".$db->escape($board_name)."'";
    
    $result = $db->query($sql);
    
    if ($result) {
        setEventMessage($langs->trans('LinkSaved'));
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    } else {
        dol_print_error($db);
    }
}
elseif ($action == 'unlink' && !empty($project_id)) {
    // Eliminar vinculación
    $sql = "DELETE FROM ".MAIN_DB_PREFIX."trellogestiona_project_board";
    $sql.= " WHERE fk_project = ".(int)$project_id;
    
    $result = $db->query($sql);
    
    if ($result) {
        setEventMessage($langs->trans('LinkRemoved'));
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    } else {
        dol_print_error($db);
    }
}
else {
    // Mostrar lista de proyectos vinculados
    print '<div class="div-table-responsive">';
    
    // Mostrar información
    print '<div class="info-box" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
    print '<h2>Proyectos vinculados con tableros de Trello</h2>';
    print '<p>Esta página muestra los proyectos que están vinculados con tableros de Trello.</p>';
    print '<p>Para vincular un nuevo proyecto, utiliza el menú contextual del proyecto.</p>';
    print '</div>';
    
    // Tabla de proyectos vinculados
    print '<table class="tagtable liste">';
    
    print '<tr class="liste_titre">';
    print '<th>Ref. Proyecto</th>';
    print '<th>Título</th>';
    print '<th>Tablero Trello</th>';
    print '<th>Fecha vinculación</th>';
    print '<th>Acciones</th>';
    print '</tr>';
    
    // Consulta para obtener proyectos vinculados
    $sql = "SELECT p.rowid, p.ref, p.title, t.board_id, t.board_name, t.date_creation";
    $sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
    $sql.= " INNER JOIN ".MAIN_DB_PREFIX."trellogestiona_project_board as t ON p.rowid = t.fk_project";
    $sql.= " ORDER BY p.ref ASC";
    
    $result = $db->query($sql);
    
    if ($result && $db->num_rows($result) > 0) {
        $i = 0;
        while ($obj = $db->fetch_object($result)) {
            $i++;
            $trclass = ($i % 2 == 0) ? 'pair' : 'impair';
            
            print '<tr class="'.$trclass.'">';
            
            // Ref. proyecto
            print '<td><a href="'.DOL_URL_ROOT.'/projet/card.php?id='.$obj->rowid.'">'.$obj->ref.'</a></td>';
            
            // Título
            print '<td>'.$obj->title.'</td>';
            
            // Tablero Trello
            print '<td>';
            print $obj->board_name . ' <span class="opacitymedium">('.$obj->board_id.')</span>';
            print '</td>';
            
            // Fecha vinculación
            print '<td>'.dol_print_date($db->jdate($obj->date_creation), 'dayhour').'</td>';
            
            // Acciones
            print '<td class="center nowraponall">';
            print '<a class="reposition" href="'.get_streamlit_url_with_params(array('board_id' => $obj->board_id)).'" target="_blank">';
            print img_picto($langs->trans('ViewBoard'), 'object_globe.png');
            print '</a> &nbsp; ';
            print '<a class="reposition" href="'.$_SERVER['PHP_SELF'].'?action=unlink&project_id='.$obj->rowid.'&token='.newToken().'" onclick="return confirm(\''.$langs->trans('ConfirmUnlink').'\');">';
            print img_picto($langs->trans('Unlink'), 'unlink');
            print '</a>';
            print '</td>';
            
            print '</tr>';
        }
    } else {
        print '<tr><td colspan="5" class="opacitymedium">'.$langs->trans('NoRecords').'</td></tr>';
    }
    
    print '</table>';
    print '</div>';
    
    // Instrucciones de uso
    print '<div class="info-section" style="margin-top: 30px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">';
    print '<h2>Instrucciones de uso</h2>';
    print '<p>Para vincular un proyecto con un tablero de Trello:</p>';
    print '<ol>';
    print '<li>Ve a la ficha del proyecto en Dolibarr</li>';
    print '<li>Haz clic en el botón "Vincular con Trello" en la barra de acciones</li>';
    print '<li>Introduce el ID y nombre del tablero de Trello</li>';
    print '<li>Una vez vinculado, podrás acceder al tablero desde la ficha del proyecto o desde esta página</li>';
    print '</ol>';
    print '</div>';
}

// Pie de página
llxFooter();
$db->close();