<?php
/**
 * Página de automatización de tareas
 */

// Carga del entorno Dolibarr
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once './lib/trellogestiona.lib.php';

// Control de acceso
if (!$user->rights->trellogestiona->automatizacion) {
    accessforbidden();
}

// Cargar la URL de la aplicación Streamlit
$streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;

// Título de la página
$title = "Automatización de Tareas";
llxHeader('', $title);

// Mostrar pestañas
print_trellogestiona_tabs('automatizacion');

// Mostrar contenido
print '<div class="automatizacion-container">';
print '<h1>Automatización de Tareas</h1>';

// Si hay una URL de Streamlit configurada, mostrarla en un iframe
if (!empty($streamlit_url)) {
    // Construir URL con parámetro para ir directamente a la pestaña de automatización
    $streamlit_url_with_params = get_streamlit_url_with_params(array('tab' => 'Automatización'));
    
    print '<div class="streamlit-container" style="margin-top: 20px;">';
    print '<h2>Panel de Automatización</h2>';
    print '<div style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; margin-bottom: 20px;">';
    print '<p>A continuación se muestra el panel de automatización de tareas:</p>';
    
    // Mostrar en iframe
    print '<div style="position: relative; overflow: hidden; padding-top: 56.25%;">';
    print '<iframe src="'.$streamlit_url_with_params.'" frameborder="0" style="position: absolute; top: 0; left: 0; width: 100%; height: 800px; border: 0;" allowfullscreen></iframe>';
    print '</div>';
    
    // Agregar un enlace para abrir en una nueva ventana
    print '<div style="margin-top: 10px; text-align: right;">';
    print '<a href="'.$streamlit_url_with_params.'" target="_blank" class="button buttonaction">Abrir en nueva ventana</a>';
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
print '<h2>Instrucciones para la Automatización</h2>';
print '<p>Esta sección permite automatizar tareas repetitivas basadas en los tableros de Trello:</p>';
print '<ol>';
print '<li><strong>Análisis de tareas:</strong> El sistema analiza las tareas para identificar cuáles pueden ser automatizadas.</li>';
print '<li><strong>Generación de scripts:</strong> Se generan scripts de automatización para tareas específicas.</li>';
print '<li><strong>Programación:</strong> Los scripts pueden programarse para ejecutarse automáticamente.</li>';
print '<li><strong>Monitoreo:</strong> Se puede hacer seguimiento a la ejecución de las automatizaciones.</li>';
print '</ol>';
print '</div>';

print '</div>';

// Pie de página
llxFooter();
$db->close();