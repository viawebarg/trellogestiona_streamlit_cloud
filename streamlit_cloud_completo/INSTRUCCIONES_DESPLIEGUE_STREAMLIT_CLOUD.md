# Instrucciones para Desplegar en Streamlit Cloud y Conectar con Dolibarr en cPanel (Wiroos)

Este documento explica cómo desplegar tu aplicación TrelloGestiona en Streamlit Cloud para utilizarla con tu Dolibarr alojado en cPanel de Wiroos.

## Ventajas de usar Streamlit Cloud

1. No necesitas instalar Streamlit ni Python en tu servidor cPanel
2. Despliegue sencillo y rápido
3. Escalabilidad automática
4. Alta disponibilidad
5. URL pública accesible desde cualquier lugar

## Requisitos Previos

- Una cuenta de GitHub (gratuita)
- Una cuenta en Streamlit Cloud (puedes registrarte en https://streamlit.io/cloud)

## Paso 1: Preparar el repositorio en GitHub

1. Crea un nuevo repositorio en GitHub (puede ser público o privado)
2. Sube los siguientes archivos de tu aplicación al repositorio:
   - Todos los archivos `.py` (app.py, db_manager.py, etc.)
   - La carpeta `datos` con los archivos JSON de ejemplo
   - El archivo `requirements_streamlit_cloud.txt` (renómbralo a `requirements.txt` al subirlo)
   - Crea una carpeta `.streamlit` y coloca dentro el archivo `config.toml` basado en `config_streamlit_cloud.toml`

## Paso 2: Configurar la aplicación en Streamlit Cloud

1. Inicia sesión en [Streamlit Cloud](https://streamlit.io/cloud)
2. Haz clic en "New app"
3. Conecta tu cuenta de GitHub si no lo has hecho
4. Selecciona el repositorio donde subiste tu aplicación
5. En "Main file path", escribe: `app.py`
6. En "Advanced settings", configura las siguientes variables de entorno:
   - `DOLIBARR_URL`: https://tu-dominio.wiroos.com/dolibarr (URL de tu Dolibarr)
   - `DOLIBARR_API_TOKEN`: Tu token de API de Dolibarr (si utilizas la API)
   - `DATABASE_URL`: Si utilizas una base de datos externa (opcional)

7. Haz clic en "Deploy!"

## Paso 3: Configurar tu Dolibarr en cPanel para trabajar con Streamlit Cloud

### Módulo TrelloGestiona

1. Descomprime `mod_trellogestiona.zip` y sube la carpeta al directorio `htdocs/custom/` de tu Dolibarr
2. Accede a Dolibarr como administrador y activa el módulo
3. Ve a la configuración del módulo y configura la URL de la aplicación Streamlit:
   - Introduce la URL proporcionada por Streamlit Cloud (algo como `https://tu-app.streamlit.app`)

### Módulo de Tema VIAWEB

1. Descomprime `mod_viaweb_theme.zip` y sube la carpeta al directorio `htdocs/custom/` de tu Dolibarr
2. Accede a Dolibarr como administrador y activa el módulo

## Paso 4: Configurar permisos CORS en Streamlit Cloud

Para permitir que tu Dolibarr se comunique con Streamlit Cloud, debes configurar CORS. Puedes hacerlo añadiendo este código al principio de tu `app.py`:

```python
import streamlit as st
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

# Get the FastAPI app from Streamlit
app = st._get_script_run_ctx().streamlit_server.server_app

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["https://tu-dominio.wiroos.com"],  # Reemplaza con tu dominio
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)
```

## Consideraciones para cPanel/Wiroos

- Si tu Dolibarr utiliza HTTPS, asegúrate de que las llamadas desde Dolibarr a Streamlit Cloud también sean HTTPS
- Si tu cPanel tiene restricciones de firewall, asegúrate de que permite conexiones salientes a `*.streamlit.app`
- Para actualizar la aplicación, simplemente actualiza los archivos en tu repositorio de GitHub y Streamlit Cloud se actualizará automáticamente

## Mantenimiento y Actualización

Para actualizar tu aplicación en Streamlit Cloud:

1. Actualiza los archivos en tu repositorio de GitHub
2. Streamlit Cloud detectará los cambios y volverá a implementar automáticamente

## Soporte

Para cualquier consulta o soporte, contacte a:

VIAWEB S.A.S
https://web.viaweb.net.ar/