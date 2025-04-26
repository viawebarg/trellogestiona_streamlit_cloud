<?php
/**
 * Librería de funciones para el módulo TrelloGestiona
 */

/**
 * Imprime las pestañas del módulo
 *
 * @param string $active Pestaña activa
 * @return void
 */
function print_trellogestiona_tabs($active = '')
{
    global $langs, $conf;

    $langs->load("trellogestiona@trellogestiona");

    // Definir las pestañas
    $tabs = array(
        'dashboard' => array(
            'title' => 'Dashboard',
            'url' => '/trellogestiona/dashboard.php',
            'perms' => '$user->rights->trellogestiona->read'
        ),
        'proyecto_trello' => array(
            'title' => 'Proyectos Trello',
            'url' => '/trellogestiona/proyecto_trello.php',
            'perms' => '$user->rights->trellogestiona->read'
        ),
        'automatizacion' => array(
            'title' => 'Automatización',
            'url' => '/trellogestiona/automatizacion.php',
            'perms' => '$user->rights->trellogestiona->automatizacion'
        ),
        'config' => array(
            'title' => 'Configuración',
            'url' => '/trellogestiona/setup.php',
            'perms' => '$user->rights->trellogestiona->config'
        )
    );

    // Imprimir las pestañas
    print '<div class="tabBar">';
    print '<div class="tabsAction">';

    foreach ($tabs as $code => $tab) {
        $isActive = ($active == $code) ? ' active' : '';
        
        // Comprobar permisos
        if (!empty($tab['perms'])) {
            $perm = eval('return '.$tab['perms'].';');
            if (!$perm) continue;
        }
        
        print '<a class="tabTitle'.$isActive.'" href="'.DOL_URL_ROOT.$tab['url'].'">';
        print $tab['title'];
        print '</a>';
    }

    print '</div>';
    print '</div>';
}

/**
 * Comprueba si un proyecto tiene tablero de Trello vinculado
 *
 * @param int $project_id ID del proyecto
 * @return array|bool Datos del tablero si existe, false si no
 */
function get_project_trello_board($project_id)
{
    global $db;
    
    $sql = "SELECT * FROM ".MAIN_DB_PREFIX."trellogestiona_project_board";
    $sql.= " WHERE fk_project = ".(int)$project_id;
    
    $result = $db->query($sql);
    if ($result && $db->num_rows($result) > 0) {
        $obj = $db->fetch_object($result);
        return array(
            'board_id' => $obj->board_id,
            'board_name' => $obj->board_name,
            'date_creation' => $obj->date_creation
        );
    }
    
    return false;
}

/**
 * Obtiene la URL de la aplicación Streamlit con parámetros adicionales
 *
 * @param array $params Parámetros adicionales para la URL
 * @return string URL completa
 */
function get_streamlit_url_with_params($params = array())
{
    global $conf;
    
    $base_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;
    
    // Si no hay parámetros, devolver la URL base
    if (empty($params)) {
        return $base_url;
    }
    
    // Añadir parámetros a la URL
    $url = $base_url;
    $separator = (strpos($base_url, '?') === false) ? '?' : '&';
    
    foreach ($params as $key => $value) {
        $url .= $separator . urlencode($key) . '=' . urlencode($value);
        $separator = '&';
    }
    
    return $url;
}

/**
 * Genera un enlace con parámetros para la aplicación Streamlit
 *
 * @param string $text Texto del enlace
 * @param array $params Parámetros para la URL
 * @param string $class Clase CSS adicional
 * @return string HTML del enlace
 */
function get_streamlit_link($text, $params = array(), $class = 'button buttonaction')
{
    $url = get_streamlit_url_with_params($params);
    return '<a href="'.$url.'" target="_blank" class="'.$class.'">'.$text.'</a>';
}