<?php
/**
 * Pestaña de proyectos para TrelloGestiona
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
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'alpha');

// Inicializar objeto proyecto
$project = new Project($db);

// Cargar proyecto
if (!empty($id) || !empty($ref)) {
    $result = $project->fetch($id, $ref);
    if ($result <= 0) {
        dol_print_error($db, $project->error);
        exit;
    }
}

// Inicialización de la página
$title = $langs->trans('Project').' - '.$langs->trans('TrelloGestiona');
$help_url = '';

// Cargar traducciones
$langs->loadLangs(array("projects", "companies"));

// Header
llxHeader('', $title, $help_url);

// Mostrar cabecera del proyecto
$head = project_prepare_head($project);
print dol_get_fiche_head($head, 'trellogestiona', $langs->trans("Project"), -1, 'project');

// Información básica del proyecto
$linkback = '<a href="'.DOL_URL_ROOT.'/projet/list.php?restore_lastsearch_values=1">'.$langs->trans("BackToList").'</a>';

$morehtmlref = '<div class="refidno">';
// Tercero
$morehtmlref .= $langs->trans('ThirdParty').' : ';
if ($project->thirdparty->id > 0) {
    $morehtmlref .= $project->thirdparty->getNomUrl(1, 'project');
} else {
    $morehtmlref .= $langs->trans("NoThirdParty");
}
$morehtmlref .= '</div>';

// Título del proyecto
dol_banner_tab($project, 'id', $linkback, 1, 'ref', 'ref', $morehtmlref);

print '<div class="underbanner clearboth"></div>';

// Obtener datos del tablero Trello
$board = get_project_trello_board($project->id);

// Mostrar la información de Trello
print '<div class="div-table-responsive">';
print '<table class="border centpercent">';

// Proyecto
print '<tr>';
print '<td class="titlefield">'.$langs->trans("Reference").'</td>';
print '<td>'.$project->ref.'</td>';
print '</tr>';

// Título
print '<tr>';
print '<td>'.$langs->trans("Label").'</td>';
print '<td>'.$project->title.'</td>';
print '</tr>';

if ($board) {
    // Tablero vinculado
    print '<tr>';
    print '<td>'.$langs->trans("TrelloBoard").'</td>';
    print '<td>'.$board['board_name'].' <span class="opacitymedium">('.$board['board_id'].')</span></td>';
    print '</tr>';
    
    // Fecha de vinculación
    print '<tr>';
    print '<td>'.$langs->trans("LinkDate").'</td>';
    print '<td>'.dol_print_date($board['date_creation'], 'dayhour').'</td>';
    print '</tr>';
    
    // Botones de acción
    print '<tr>';
    print '<td>'.$langs->trans("Actions").'</td>';
    print '<td>';
    print '<a href="'.get_streamlit_url_with_params(array('board_id' => $board['board_id'])).'" target="_blank" class="button buttonaction">'.$langs->trans("ViewBoard").'</a>';
    print ' &nbsp; <a href="proyecto_trello.php?action=unlink&project_id='.$project->id.'&token='.newToken().'" class="button buttoncancel" onclick="return confirm(\''.$langs->trans('ConfirmUnlink').'\');">'.$langs->trans("Unlink").'</a>';
    print '</td>';
    print '</tr>';
    
    // Integración con iframe
    print '<tr>';
    print '<td colspan="2">';
    
    // Mostrar Streamlit embebido
    $streamlit_url = get_streamlit_url_with_params(array('board_id' => $board['board_id']));
    
    print '<div class="trello-container" style="margin-top: 20px;">';
    print '<h2>'.$langs->trans("TrelloBoardView").'</h2>';
    print '<div style="position: relative; overflow: hidden; padding-top: 56.25%;">';
    print '<iframe src="'.$streamlit_url.'" frameborder="0" style="position: absolute; top: 0; left: 0; width: 100%; height: 800px; border: 0;" allowfullscreen></iframe>';
    print '</div>';
    print '</div>';
    
    print '</td>';
    print '</tr>';
} else {
    // No hay tablero vinculado
    print '<tr>';
    print '<td>'.$langs->trans("TrelloBoard").'</td>';
    print '<td class="opacitymedium">'.$langs->trans("NoTrelloBoardLinked").'</td>';
    print '</tr>';
    
    // Botón para vincular
    print '<tr>';
    print '<td>'.$langs->trans("Actions").'</td>';
    print '<td>';
    print '<a href="proyecto_trello.php?action=link&project_id='.$project->id.'&token='.newToken().'" class="button buttonaction">'.$langs->trans("LinkToTrello").'</a>';
    print '</td>';
    print '</tr>';
}

print '</table>';
print '</div>';

// Fin de la ficha
print dol_get_fiche_end();

// Pie de página
llxFooter();
$db->close();