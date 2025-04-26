<?php
/**
 * Configuración del módulo TrelloGestiona
 */

// Carga del entorno Dolibarr
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once './lib/trellogestiona.lib.php';

// Control de acceso
if (!$user->rights->trellogestiona->config) {
    accessforbidden();
}

// Parámetros de configuración
$streamlit_url = GETPOST('streamlit_url', 'alpha');
$trello_api_key = GETPOST('trello_api_key', 'alpha');
$trello_token = GETPOST('trello_token', 'alpha');

// Guardar configuración
$action = GETPOST('action', 'alpha');
if ($action == 'update') {
    // Actualizar URL de Streamlit
    dolibarr_set_const($db, 'TRELLOGESTIONA_STREAMLIT_URL', $streamlit_url, 'chaine', 0, '', $conf->entity);
    // Actualizar credenciales de Trello
    dolibarr_set_const($db, 'TRELLOGESTIONA_API_KEY', $trello_api_key, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, 'TRELLOGESTIONA_TOKEN', $trello_token, 'chaine', 0, '', $conf->entity);
    
    setEventMessage($langs->trans('SetupSaved'));
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

// Título de la página
$title = "Configuración TrelloGestiona";
llxHeader('', $title);

// Mostrar pestañas
print_trellogestiona_tabs('config');

// Mostrar formulario de configuración
print '<div class="config-container">';
print '<h1>Configuración del módulo TrelloGestiona</h1>';

// Iniciar formulario
print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="token" value="'.newToken().'">';

// Sección Configuración de Streamlit
print '<div class="config-section">';
print '<h2>Configuración de la aplicación Streamlit</h2>';
print '<div class="config-item">';
print '<label for="streamlit_url">URL de la aplicación Streamlit:</label>';
print '<input type="text" id="streamlit_url" name="streamlit_url" value="'.$conf->global->TRELLOGESTIONA_STREAMLIT_URL.'" size="50">';
print '<div class="help-text">URL completa donde se aloja la aplicación Streamlit (ej: http://localhost:5000)</div>';
print '</div>';
print '</div>';

// Sección Credenciales de Trello (opcional)
print '<div class="config-section">';
print '<h2>Credenciales de Trello (opcional)</h2>';
print '<div class="info-box" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
print '<p>Estas credenciales son opcionales y solo se utilizan si deseas que la aplicación Streamlit se conecte automáticamente a Trello.</p>';
print '</div>';
print '<div class="config-item">';
print '<label for="trello_api_key">Clave API de Trello:</label>';
print '<input type="text" id="trello_api_key" name="trello_api_key" value="'.$conf->global->TRELLOGESTIONA_API_KEY.'" size="50">';
print '</div>';
print '<div class="config-item">';
print '<label for="trello_token">Token de Trello:</label>';
print '<input type="password" id="trello_token" name="trello_token" value="'.$conf->global->TRELLOGESTIONA_TOKEN.'" size="50">';
print '</div>';
print '</div>';

// Botón de envío
print '<div class="config-submit">';
print '<input type="submit" class="button buttonaction" value="Guardar configuración">';
print '</div>';

print '</form>';

// Instrucciones de uso
print '<div class="info-section" style="margin-top: 30px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">';
print '<h2>Instrucciones para la integración simplificada</h2>';
print '<p>Esta versión del módulo utiliza una integración simple mediante iframe, sin necesidad de configurar API:</p>';
print '<ol>';
print '<li><strong>URL de la aplicación Streamlit:</strong> Es la única configuración obligatoria. Indica dónde está alojada la aplicación Streamlit.</li>';
print '<li><strong>Visualización:</strong> La aplicación se mostrará directamente dentro de Dolibarr en la sección Dashboard.</li>';
print '<li><strong>Credenciales de Trello:</strong> Son opcionales y solo se utilizan si deseas que la aplicación Streamlit se conecte automáticamente a Trello.</li>';
print '</ol>';
print '</div>';

// Test de conexión
print '<div class="test-section" style="margin-top: 30px;">';
print '<h2>Prueba de conexión</h2>';

if (!empty($conf->global->TRELLOGESTIONA_STREAMLIT_URL)) {
    // Comprobar si la URL es accesible
    $ch = curl_init($conf->global->TRELLOGESTIONA_STREAMLIT_URL);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($status_code >= 200 && $status_code < 300) {
        print '<div class="success-message" style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px;">';
        print '<p>✅ La aplicación Streamlit está accesible en la URL configurada.</p>';
        print '<p><a href="dashboard.php" class="button buttonaction">Ver en el Dashboard</a></p>';
        print '</div>';
    } else {
        print '<div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">';
        print '<p>❌ No se puede acceder a la aplicación Streamlit en la URL configurada.</p>';
        print '<p>Por favor, verifica que la aplicación esté en ejecución y que la URL sea correcta.</p>';
        print '</div>';
    }
} else {
    print '<div class="warning-message" style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px;">';
    print '<p>⚠️ No se ha configurado la URL de la aplicación Streamlit.</p>';
    print '</div>';
}

print '</div>';

print '</div>';

// Pie de página
llxFooter();
$db->close();