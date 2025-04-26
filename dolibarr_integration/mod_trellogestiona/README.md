# Módulo TrelloGestiona para Dolibarr

Este módulo integra la aplicación TrelloGestiona (gestor de tareas Trello y automatización) con Dolibarr.

## Características

- Visualización de tableros y tareas de Trello dentro de Dolibarr
- Análisis y automatización de tareas repetitivas
- Generación de scripts para automatizar procesos

## Requisitos

- Dolibarr >= 11.0
- Una instalación operativa de la aplicación Streamlit TrelloGestiona
- Credenciales de la API de Trello (opcional)

## Instalación

1. Descargar el módulo en la carpeta `custom` de Dolibarr:
   ```bash
   cd /ruta/a/dolibarr/htdocs/custom/
   git clone https://[repositorio]/mod_trellogestiona.git trellogestiona
   ```

2. En Dolibarr, ir a **Inicio > Configuración > Módulos** y buscar "TrelloGestiona"

3. Activar el módulo haciendo clic en el botón "Activar"

4. Una vez activado, navegar a **TrelloGestiona > Configuración** en el menú principal

5. Configurar la URL de la aplicación Streamlit y opcionalmente, las credenciales de la API de Trello

## Uso

### Acceso a la aplicación

Para acceder a la aplicación, simplemente haz clic en **TrelloGestiona** en el menú principal de Dolibarr.

### Tablero de control

El tablero de control muestra una visión general de tus tareas y tableros de Trello.

### Automatización

La sección de automatización te permite:

1. Identificar tareas repetitivas que pueden ser automatizadas
2. Generar scripts de automatización para diversas tareas
3. Programar la ejecución de scripts generados

## Notas técnicas

### Estructura del módulo

```
trellogestiona/
├── class/
│   └── api_trellogestiona.class.php   # Clase para la API
├── core/
│   └── modules/
│       └── modTrelloGestiona.class.php   # Definición del módulo
├── img/
│   └── trellogestiona.svg   # Icono del módulo
├── langs/
│   └── es_ES/
│       └── trellogestiona.lang   # Traducciones
├── sql/
│   └── llx_trellogestiona_config.sql   # Tabla de configuración
├── index.php   # Página principal
├── dashboard.php   # Tablero de control
├── automatizacion.php   # Automatización
├── setup.php   # Configuración
└── README.md   # Este archivo
```

### Tablas de la base de datos

- `llx_trellogestiona_config`: Almacena la configuración del módulo

## Soporte

Para soporte técnico, contactar a [contacto].

## Licencia

Este módulo está licenciado bajo la Licencia Pública General GNU v3.0 o posterior.