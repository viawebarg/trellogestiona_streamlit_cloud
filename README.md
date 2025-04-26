# Gestor de Tareas Trello con Integración Dolibarr

## Descripción

Este proyecto proporciona una solución completa para la gestión de tareas de Trello con capacidades de análisis, automatización e integración con el ERP Dolibarr. Desarrollado con Streamlit, permite procesar tableros de Trello exportados en formato JSON, organizar las tareas por prioridad y categoría, definir flujos de trabajo personalizados, y sincronizar la información con Dolibarr.

## Características Principales

- **Importación desde Trello**: Carga y procesa datos desde tableros de Trello exportados en formato JSON.
- **Panel de Tareas**: Visualiza, filtra y organiza tareas de manera intuitiva.
- **Gestión de Flujo de Trabajo**: Define etapas personalizadas y mueve tareas a través del flujo.
- **Análisis de Datos**: Visualiza la distribución de tareas por categoría, prioridad y vencimiento.
- **Detección de Automatización**: Identifica tareas que pueden ser automatizadas.
- **Generación de Scripts**: Crea scripts de automatización para tareas repetitivas.
- **Integración con Dolibarr**: Sincroniza tareas con proyectos en el ERP Dolibarr.

## Componentes del Sistema

### Aplicación Streamlit

Interfaz principal para la gestión de tareas, desarrollada en Python con Streamlit.

### Módulo Dolibarr

Módulo personalizado para Dolibarr (`trellogestiona`) que permite la comunicación bidireccional entre Dolibarr y la aplicación Streamlit.

## Requisitos

- Python 3.7 o superior
- PostgreSQL
- Dolibarr ERP (opcional, para integración completa)
- Tableros de Trello exportados en formato JSON

## Instalación

### 1. Configuración de la Aplicación Streamlit

1. Clona el repositorio
2. Instala las dependencias:
```bash
pip install -r requirements.txt
```
3. Configura la base de datos PostgreSQL:
```bash
export DATABASE_URL="postgresql://usuario:contraseña@host:puerto/nombre_db"
```
4. Ejecuta la aplicación:
```bash
streamlit run app.py
```

### 2. Instalación del Módulo Dolibarr

1. Copia la carpeta `dolibarr_integration/mod_trellogestiona` al directorio `custom` de tu instalación de Dolibarr
2. Accede a Dolibarr como administrador
3. Ve a Inicio > Configuración > Módulos
4. Busca y activa el módulo "TrelloGestiona"
5. Configura el módulo en Inicio > TrelloGestiona > Configuración

## Uso de la Integración Dolibarr

### En Dolibarr:

1. **Configuración del Módulo**:
   - Establece la URL de la aplicación Streamlit
   - Copia el Token de API generado automáticamente

2. **Vinculación con Proyectos**:
   - En la ficha de un proyecto, ve a la pestaña "Trello"
   - Establece la vinculación con un tablero de Trello

3. **Visualización de Tareas**:
   - Las tareas sincronizadas aparecerán en el proyecto

### En la Aplicación Streamlit:

1. **Configuración de la Integración**:
   - Ve a la pestaña "Integración Dolibarr"
   - Ingresa la URL de Dolibarr y el token de API

2. **Sincronización**:
   - Obtén proyectos de Dolibarr
   - Vincula proyectos con tableros de Trello
   - Sincroniza tareas entre Trello y Dolibarr

## Uso de la API

El módulo Dolibarr proporciona una API REST para la comunicación con la aplicación Streamlit. La documentación completa está disponible en Dolibarr en TrelloGestiona > Configuración > Ver documentación de la API.

Ejemplo de uso en Python:

```python
import requests

# Configuración
DOLIBARR_URL = "https://tu-dolibarr.com"
API_ENDPOINT = f"{DOLIBARR_URL}/custom/trellogestiona/api/api.php"
API_KEY = "tu-token-api"

# Obtener proyectos
def get_projects():
    url = f"{API_ENDPOINT}?api_key={API_KEY}&action=get_projects"
    response = requests.get(url)
    return response.json()
```

## Funcionamiento

1. **Exportación de Tableros**: Los usuarios exportan sus tableros de Trello en formato JSON
2. **Procesamiento de Datos**: La aplicación procesa los datos y los almacena en PostgreSQL
3. **Gestión de Tareas**: Los usuarios organizan, filtran y analizan sus tareas
4. **Integración con Dolibarr**: La información se sincroniza con proyectos en Dolibarr
5. **Automatización**: Se generan scripts para automatizar tareas repetitivas

## Estructura del Proyecto

- `app.py`: Aplicación principal Streamlit
- `cargar_datos.py`: Funciones para procesar archivos JSON de Trello
- `db_manager.py`: Gestión de la base de datos PostgreSQL
- `gestor_flujo_trabajo.py`: Lógica para flujos de trabajo
- `automatizacion_tareas.py`: Detección y gestión de automatizaciones
- `generador_scripts.py`: Generación de scripts para tareas automatizables
- `dolibarr_api_client.py`: Cliente para la comunicación con Dolibarr
- `dolibarr_integration/`: Módulo para integración con Dolibarr

## Contribución

Este proyecto es parte de una solución personalizada para la gestión de tareas. Si deseas contribuir, por favor contacta al equipo de desarrollo.