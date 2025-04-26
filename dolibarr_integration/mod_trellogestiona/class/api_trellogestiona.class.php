<?php
/* Copyright (C) 2023-2025 TrelloGestiona
 * Este programa es software libre: puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada por
 * la Free Software Foundation, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior.
 */

/**
 * API para interactuar con TrelloGestiona desde Dolibarr u otras aplicaciones
 */

use Luracast\Restler\RestException;

require_once DOL_DOCUMENT_ROOT.'/main.inc.php';

/**
 * Clase para la API del módulo TrelloGestiona
 */
class TrelloGestionaApi
{
    /**
     * Constructor
     */
    public function __construct()
    {
        global $db, $conf;
        $this->db = $db;
        $this->streamlit_url = $conf->global->TRELLOGESTIONA_STREAMLIT_URL;
        $this->trello_api_key = $conf->global->TRELLOGESTIONA_API_KEY;
        $this->trello_token = $conf->global->TRELLOGESTIONA_TOKEN;
    }
    
    /**
     * Obtener la configuración del módulo
     *
     * @return array     Datos de configuración
     * @throws RestException
     */
    public function getConfig()
    {
        global $user;
        
        if (!$user->rights->trellogestiona->read) {
            throw new RestException(401, 'Unauthorized');
        }
        
        return array(
            'streamlit_url' => $this->streamlit_url,
            'has_trello_credentials' => (!empty($this->trello_api_key) && !empty($this->trello_token))
        );
    }
    
    /**
     * Obtener tareas de Trello
     *
     * @return array     Lista de tareas
     * @throws RestException
     */
    public function getTasks()
    {
        global $user;
        
        if (!$user->rights->trellogestiona->read) {
            throw new RestException(401, 'Unauthorized');
        }
        
        // Esta es una implementación básica
        // En una implementación real, haríamos una solicitud a la API de Trello
        // o a nuestra aplicación Streamlit para obtener los datos
        
        // Por ahora devolvemos datos simulados
        $tasks = $this->fetchTasksFromStreamlit();
        
        if (empty($tasks)) {
            return array('error' => 'No se pudieron obtener las tareas');
        }
        
        return $tasks;
    }
    
    /**
     * Obtener scripts de automatización generados
     *
     * @return array     Lista de scripts
     * @throws RestException
     */
    public function getAutomationScripts()
    {
        global $user;
        
        if (!$user->rights->trellogestiona->read) {
            throw new RestException(401, 'Unauthorized');
        }
        
        // Esta es una implementación básica
        // En una implementación real, haríamos una solicitud a la API de Streamlit
        // para obtener los scripts generados
        
        $scripts = $this->fetchScriptsFromStreamlit();
        
        if (empty($scripts)) {
            return array('error' => 'No se pudieron obtener los scripts de automatización');
        }
        
        return $scripts;
    }
    
    /**
     * Función para hacer una petición HTTP a la aplicación Streamlit
     * 
     * @param string $endpoint     URL relativa del endpoint de la API
     * @param array $params        Parámetros adicionales
     * @return array               Respuesta en formato array
     */
    private function callStreamlitApi($endpoint, $params = array())
    {
        // Esta función simula la comunicación con una API REST en la aplicación Streamlit
        // En una implementación real, haríamos una solicitud HTTP a la API
        
        $url = $this->streamlit_url . '/api/' . $endpoint;
        
        // Simular una respuesta
        return array('status' => 'success', 'message' => 'Operación simulada');
    }
    
    /**
     * Función para obtener las tareas de la aplicación Streamlit
     * 
     * @return array     Lista de tareas o array vacío en caso de error
     */
    private function fetchTasksFromStreamlit()
    {
        // En una implementación real, esta función haría una solicitud HTTP
        // a la aplicación Streamlit para obtener las tareas
        
        // Por ahora devolvemos datos de ejemplo
        return array(
            array(
                'id' => '1',
                'nombre' => 'Ejemplo de tarea 1',
                'descripcion' => 'Descripción de la tarea 1',
                'tablero' => 'Tablero de ejemplo',
                'lista' => 'Por hacer',
                'fecha_vencimiento' => '2023-12-31',
                'prioridad' => 'Alta',
                'etiquetas' => array('Desarrollo', 'Urgente')
            ),
            array(
                'id' => '2',
                'nombre' => 'Ejemplo de tarea 2',
                'descripcion' => 'Descripción de la tarea 2',
                'tablero' => 'Tablero de ejemplo',
                'lista' => 'En progreso',
                'fecha_vencimiento' => '2023-12-15',
                'prioridad' => 'Media',
                'etiquetas' => array('Diseño')
            )
        );
    }
    
    /**
     * Función para obtener los scripts de automatización de la aplicación Streamlit
     * 
     * @return array     Lista de scripts o array vacío en caso de error
     */
    private function fetchScriptsFromStreamlit()
    {
        // En una implementación real, esta función haría una solicitud HTTP
        // a la aplicación Streamlit para obtener los scripts
        
        // Por ahora devolvemos datos de ejemplo
        return array(
            array(
                'id' => '1',
                'nombre' => 'script_correo_ejemplo_20231201_123456.py',
                'tipo' => 'Envío de correos',
                'fecha_creacion' => '2023-12-01 12:34:56',
                'tarea_id' => '1'
            ),
            array(
                'id' => '2',
                'nombre' => 'script_reporte_ejemplo_20231130_123456.py',
                'tipo' => 'Generación de reportes',
                'fecha_creacion' => '2023-11-30 12:34:56',
                'tarea_id' => '2'
            )
        );
    }
}