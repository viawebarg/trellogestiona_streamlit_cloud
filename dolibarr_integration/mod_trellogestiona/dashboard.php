<?php
/**
 * Dashboard del módulo TrelloGestiona
 */

// Carga del entorno Dolibarr
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once './lib/trellogestiona.lib.php';

// Control de acceso
if (!$user->rights->trellogestiona->read) {
    accessforbidden();
}

// Cargar la URL de la aplicación Streamlit
$streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;

// Título de la página
$title = "Dashboard TrelloGestiona";
llxHeader('', $title);

// Mostrar pestañas
print_trellogestiona_tabs('dashboard');

// Mostrar contenido del dashboard
print '<div class="dashboard-container">';
print '<h1>Dashboard de TrelloGestiona</h1>';

// Si hay una URL de Streamlit configurada, mostrarla en un iframe
if (!empty($streamlit_url)) {
    print '<div class="streamlit-container" style="margin-top: 20px;">';
    print '<h2>Aplicación de Gestión de Tareas</h2>';
    print '<div style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; margin-bottom: 20px;">';
    print '<p>La aplicación de gestión de tareas está embebida a continuación:</p>';
    
    // Mostrar en iframe
    print '<div style="position: relative; overflow: hidden; padding-top: 56.25%;">';
    print '<iframe src="'.$streamlit_url.'" frameborder="0" style="position: absolute; top: 0; left: 0; width: 100%; height: 800px; border: 0;" allowfullscreen></iframe>';
    print '</div>';
    
    // Agregar un enlace para abrir en una nueva ventana
    print '<div style="margin-top: 10px; text-align: right;">';
    print '<a href="'.$streamlit_url.'" target="_blank" class="button buttonaction">Abrir en nueva ventana</a>';
    print '</div>';
    
    print '</div>';
    print '</div>';
} else {
    print '<div class="info" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 5px solid #007bff;">';
    print '<p>No se ha configurado la URL de la aplicación Streamlit. Por favor, configúrala en la sección de configuración del módulo.</p>';
    print '<a href="./setup.php" class="button buttonaction">Ir a configuración</a>';
    print '</div>';
}

// Instrucciones de uso
print '<div class="info-section" style="margin-top: 30px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">';
print '<h2>Instrucciones de Uso</h2>';
print '<p>Este dashboard te permite acceder directamente a la aplicación de gestión de tareas de Trello sin necesidad de APIs ni configuraciones complejas. Simplemente:</p>';
print '<ol>';
print '<li>Configura la URL de la aplicación Streamlit en la sección de configuración</li>';
print '<li>Asegúrate que la aplicación Streamlit esté ejecutándose en la URL configurada</li>';
print '<li>Utiliza la interfaz embebida o ábrela en una nueva ventana para mayor comodidad</li>';
print '</ol>';
print '</div>';

print '</div>';

// Pie de página
llxFooter();
$db->close();