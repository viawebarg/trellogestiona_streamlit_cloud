# TrelloGestiona - Módulo de integración Trello para Dolibarr

Este módulo permite integrar la aplicación de gestión de tareas Trello con Dolibarr ERP, utilizando una aplicación Streamlit embebida para visualizar y gestionar las tareas.

## Características principales

- Vinculación de proyectos Dolibarr con tableros Trello
- Visualización de tableros Trello directamente en Dolibarr mediante iframe
- Automatización de tareas repetitivas
- Gestión de flujos de trabajo personalizados
- Interfaz en español argentino

## Requisitos previos

- Dolibarr 16.0.0 o superior
- Aplicación Streamlit en ejecución (incluida en este proyecto)
- Permisos de administración para instalar módulos en Dolibarr

## Instalación

1. **Copiar la carpeta del módulo**
   - Copiar la carpeta `mod_trellogestiona` a la carpeta `htdocs/custom/` de tu instalación de Dolibarr.

2. **Activar el módulo**
   - Acceder a Dolibarr como administrador
   - Ir a Inicio > Configuración > Módulos
   - Buscar "TrelloGestiona" en la lista de módulos
   - Activar el módulo haciendo clic en el interruptor

3. **Configuración inicial**
   - Ir a Inicio > TrelloGestiona > Configuración
   - Configurar la URL de la aplicación Streamlit
   - Si deseas usar la API de Trello, configura también las credenciales de API

## Uso básico

### Vinculación de proyectos con tableros Trello

1. Acceder a la ficha de un proyecto en Dolibarr
2. Hacer clic en el botón "Vincular con Trello" en la barra de acciones
3. Introducir el ID y nombre del tablero de Trello
4. Hacer clic en "Vincular"

### Acceso a tableros vinculados

- **Desde la ficha del proyecto**: Pestaña "TrelloGestiona"
- **Desde el menú principal**: TrelloGestiona > Proyectos Trello

### Automatización de tareas

1. Acceder a TrelloGestiona > Automatización
2. Seleccionar las tareas a automatizar
3. Configurar parámetros de automatización
4. Guardar y programar la automatización

## Estructura de directorios

- `/automatizacion.php`: Página de automatización de tareas
- `/dashboard.php`: Panel principal del módulo
- `/proyecto_trello.php`: Gestión de vinculaciones entre proyectos y tableros
- `/setup.php`: Configuración del módulo
- `/class/`: Clases del módulo
- `/core/modules/`: Fichero principal del módulo
- `/sql/`: Scripts SQL para la creación de tablas
- `/langs/`: Traducciones

## Notas de implementación

Este módulo utiliza una integración mediante iframes en lugar de API, lo que simplifica considerablemente la configuración y uso, pero limita algunas funcionalidades avanzadas. Para una integración completa con Trello, configura las credenciales de API en la página de configuración.

## Desarrollado por

VIAWEB S.A.S - https://web.viaweb.net.ar/