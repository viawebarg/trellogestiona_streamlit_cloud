# TrelloGestiona - Aplicación Streamlit Cloud

Esta es la aplicación TrelloGestiona para desplegar en Streamlit Cloud y conectar con Dolibarr en cPanel/Wiroos.

## Contenido del Paquete

Este paquete contiene todos los archivos necesarios para implementar la aplicación TrelloGestiona en Streamlit Cloud:

- `app.py`: Aplicación principal de Streamlit
- `db_manager.py`: Gestor de base de datos PostgreSQL
- `dolibarr_api_client.py`: Cliente de API para Dolibarr
- `trello_api.py`: Cliente de API para Trello
- `cargar_datos.py`: Funciones para cargar datos de tableros JSON
- `data_processor.py`: Procesador de datos para tareas
- `automatizacion_tareas.py`: Módulo de automatización de tareas
- `gestor_flujo_trabajo.py`: Gestor de flujos de trabajo
- `generador_scripts.py`: Generador de scripts de automatización
- `workflow_manager.py`: Administrador de flujos de trabajo
- `requirements.txt`: Dependencias necesarias para Streamlit Cloud
- `.streamlit/config.toml`: Configuración de tema VIAWEB para Streamlit
- `datos/`: Directorio con tableros JSON de ejemplo

## Instrucciones de Despliegue

Sigue las instrucciones detalladas en `INSTRUCCIONES_DESPLIEGUE_STREAMLIT_CLOUD.md` para:

1. Configurar un repositorio en GitHub con estos archivos
2. Desplegar la aplicación en Streamlit Cloud
3. Conectar con tu Dolibarr en cPanel/Wiroos

## Soporte

Para cualquier consulta o soporte, contacte a:

VIAWEB S.A.S
https://web.viaweb.net.ar/