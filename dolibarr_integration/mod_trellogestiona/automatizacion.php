<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * Página de automatización del módulo TrelloGestiona
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

$title = $langs->trans("Automatizacion");

llxHeader('', $title);

print load_fiche_titre($title, '', 'trellogestiona.png@trellogestiona');

// Obtener URL de Streamlit
$streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;

// Si no hay URL configurada, redirigir a la página de configuración
if (empty($streamlit_url)) {
    print '<div class="warning">'.$langs->trans("NoStreamlitURLConfigured").'</div>';
    print '<a href="'.DOL_URL_ROOT.'/custom/trellogestiona/setup.php" class="button">'.$langs->trans("GoToSetup").'</a>';
} else {
    // Modificar la URL para usar la sección específica de automatización si es necesario
    // Por ejemplo, agregando un parámetro para abrir directamente la pestaña de automatización
    $automatizacion_url = $streamlit_url;
    
    // Mostrar iframe con la aplicación Streamlit enfocada en automatización
    print '<iframe src="'.$automatizacion_url.'" style="width:100%; height:800px; border:none;"></iframe>';
    
    // Información adicional sobre automatización
    print '<div class="opacitymedium">';
    print $langs->trans("AutomationInfo");
    print '</div>';
}

// Cerrar página
llxFooter();
$db->close();