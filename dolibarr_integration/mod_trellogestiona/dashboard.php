<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Página de dashboard del módulo TrelloGestiona
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

// Parámetros
$action = GETPOST('action', 'aZ09');

/*
 * Vista
 */

$title = $langs->trans("Dashboard");

llxHeader('', $title);

print load_fiche_titre($title, '', 'trellogestiona.png@trellogestiona');

// Obtener URL de Streamlit
$streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;

// Si no hay URL configurada, redirigir a la página de configuración
if (empty($streamlit_url)) {
    print '<div class="warning">'.$langs->trans("NoStreamlitURLConfigured").'</div>';
    print '<a href="'.DOL_URL_ROOT.'/custom/trellogestiona/setup.php" class="button">'.$langs->trans("GoToSetup").'</a>';
} else {
    // Modificar la URL para usar la sección específica de dashboard si es necesario
    // Por ejemplo, agregando un parámetro de consulta para abrir una pestaña específica
    $dashboard_url = $streamlit_url;
    
    // Mostrar iframe con la aplicación Streamlit en modo dashboard
    print '<iframe src="'.$dashboard_url.'" style="width:100%; height:800px; border:none;"></iframe>';
    
    // Información adicional o controles específicos para el dashboard
    print '<div class="tabsAction">';
    print '<a class="butAction" href="javascript:document.getElementById(\'streamlit-iframe\').contentWindow.location.reload();">'.$langs->trans("RefreshData").'</a>';
    print '</div>';
}

// Cerrar página
llxFooter();
$db->close();