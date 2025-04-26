<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Página principal del módulo TrelloGestiona
 */

// Cargar Dolibarr
$res = 0;
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Archivo de inclusión principal no encontrado");

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Control de acceso
if (!$user->rights->trellogestiona->read) accessforbidden();

// Cargar traducciones
$langs->loadLangs(array('trellogestiona@trellogestiona'));

/*
 * Vista
 */

$title = $langs->trans("TrelloGestiona");

llxHeader('', $title);

print load_fiche_titre($title, '', 'trellogestiona.png@trellogestiona');

// Verificar si está configurada la URL de Streamlit
$streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;
$trello_api_key = $conf->global->TRELLOGESTIONA_API_KEY;
$trello_token = $conf->global->TRELLOGESTIONA_TOKEN;

// Mostrar estado de la configuración
print '<div class="fichecenter">';
print '<div class="fichethirdleft">';

// Widget de estado del módulo
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("ConfigurationStatus").'</td>';
print '<td class="right">'.$langs->trans("Value").'</td>';
print '</tr>';

// Estado de la URL de Streamlit
print '<tr class="oddeven">';
print '<td>'.$langs->trans("StreamlitURL").'</td>';
print '<td class="right">';
if (!empty($streamlit_url)) {
    print '<span class="badge badge-status4">'.$langs->trans("ConfiguredValue").': '.$streamlit_url.'</span>';
} else {
    print '<span class="badge badge-status8">'.$langs->trans("NotConfigured").'</span>';
}
print '</td>';
print '</tr>';

// Estado de las credenciales de Trello
print '<tr class="oddeven">';
print '<td>'.$langs->trans("TrelloCredentials").'</td>';
print '<td class="right">';
if (!empty($trello_api_key) && !empty($trello_token)) {
    print '<span class="badge badge-status4">'.$langs->trans("ConfiguredValue").'</span>';
} else {
    print '<span class="badge badge-status8">'.$langs->trans("NotConfigured").'</span>';
}
print '</td>';
print '</tr>';

print '</table>';
print '</div>';

print '</div>';
print '<div class="fichetwothirdright">';

// Descripción general del módulo
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("ModuleDescription").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td colspan="2">';
print $langs->trans("ModuleDescriptionLong");
print '</td>';
print '</tr>';

print '</table>';
print '</div>';

print '</div>';
print '</div>'; // Fin fichecenter

// Links rápidos a funciones principales
print '<div class="clearboth"></div>';
print '<div class="fichecenter">';
print '<div class="fichehalfleft">';

// Acciones disponibles
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("AvailableActions").'</td>';
print '</tr>';

// Dashboard
print '<tr class="oddeven">';
print '<td><a href="'.DOL_URL_ROOT.'/custom/trellogestiona/dashboard.php">'.$langs->trans("AccessDashboard").'</a></td>';
print '<td>'.$langs->trans("DashboardDescription").'</td>';
print '</tr>';

// Automatización
if ($user->rights->trellogestiona->automatizacion) {
    print '<tr class="oddeven">';
    print '<td><a href="'.DOL_URL_ROOT.'/custom/trellogestiona/automatizacion.php">'.$langs->trans("AccessAutomatizacion").'</a></td>';
    print '<td>'.$langs->trans("AutomatizacionDescription").'</td>';
    print '</tr>';
}

// Configuración
if ($user->rights->trellogestiona->config) {
    print '<tr class="oddeven">';
    print '<td><a href="'.DOL_URL_ROOT.'/custom/trellogestiona/setup.php">'.$langs->trans("AccessSetup").'</a></td>';
    print '<td>'.$langs->trans("SetupDescription").'</td>';
    print '</tr>';
}

print '</table>';
print '</div>';

print '</div>';
print '</div>'; // Fin fichecenter

// Cerrar página
llxFooter();
$db->close();