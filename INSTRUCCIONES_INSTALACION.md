# Instrucciones de Instalación del Sistema TrelloGestiona

Este paquete contiene dos componentes principales:
1. `mod_trellogestiona.zip` - El módulo para Dolibarr
2. `streamlit_app.zip` - La aplicación Streamlit para procesar y visualizar los tableros de Trello

## Instalación del Módulo en Dolibarr

### Requisitos previos
- Dolibarr 13.0.0 o superior
- Permisos de administrador en Dolibarr
- Acceso al sistema de archivos del servidor

### Pasos de instalación

1. **Descomprimir el módulo**
   - Descomprima el archivo `mod_trellogestiona.zip`
   - Copie la carpeta `mod_trellogestiona` al directorio de módulos personalizados de Dolibarr:
     ```
     /ruta/a/dolibarr/htdocs/custom/
     ```
   - Para su instalación, la ruta completa según su configuración sería:
     ```
     /home/webviaw/public_html/erp/custom/mod_trellogestiona
     ```

2. **Activar el módulo**
   - Acceda a Dolibarr como administrador
   - Vaya a Inicio > Configuración > Módulos/Aplicaciones
   - Busque "TrelloGestiona" en la lista de módulos
   - Active el módulo haciendo clic en el interruptor

3. **Configurar el módulo**
   - Vaya a Inicio > TrelloGestiona > Configuración
   - Configure la URL de la aplicación Streamlit (ver siguiente sección)
   - Si desea usar la API de Trello, configure también las credenciales de API

## Instalación de la Aplicación Streamlit

### Requisitos previos
- Python 3.8 o superior
- PostgreSQL (recomendado) o SQLite
- Paquetes: streamlit, pandas, plotly, requests, sqlalchemy, psycopg2-binary

### Pasos de instalación

1. **Descomprimir la aplicación**
   - Descomprima el archivo `streamlit_app.zip` en su servidor
   - Ubíquela donde prefiera, por ejemplo:
     ```
     /home/webviaw/streamlit_trello_app/
     ```

2. **Instalar dependencias**
   ```bash
   pip install streamlit pandas plotly requests sqlalchemy psycopg2-binary
   ```

3. **Configurar base de datos**
   - Cree una base de datos PostgreSQL (recomendado)
   - Configure la variable de entorno `DATABASE_URL` con la conexión:
     ```bash
     export DATABASE_URL="postgresql://usuario:contraseña@localhost:5432/nombre_db"
     ```
   - Si no configura esta variable, la aplicación usará una base de datos SQLite en memoria

4. **Configurar conexión con Dolibarr (opcional)**
   ```bash
   export DOLIBARR_URL="https://web.viaweb.net.ar/erp"
   export DOLIBARR_API_TOKEN="su_token_de_api"
   ```

5. **Crear archivo de configuración de Streamlit**
   Cree un archivo `.streamlit/config.toml` con el siguiente contenido:
   ```toml
   [server]
   headless = true
   address = "0.0.0.0"
   port = 5000
   ```

6. **Iniciar la aplicación**
   ```bash
   cd /ruta/a/streamlit_trello_app
   streamlit run app.py --server.port 5000
   ```

7. **Configurar como servicio (opcional, recomendado)**
   - Para asegurar que la aplicación se ejecute siempre, configúrela como un servicio del sistema.
   - Ejemplo de archivo de servicio systemd:
     ```
     [Unit]
     Description=Streamlit TrelloGestiona
     After=network.target

     [Service]
     User=webviaw
     WorkingDirectory=/home/webviaw/streamlit_trello_app
     ExecStart=/usr/bin/streamlit run app.py --server.port 5000
     Restart=always
     Environment="DATABASE_URL=postgresql://usuario:contraseña@localhost:5432/nombre_db"
     Environment="DOLIBARR_URL=https://web.viaweb.net.ar/erp"
     Environment="DOLIBARR_API_TOKEN=su_token_de_api"

     [Install]
     WantedBy=multi-user.target
     ```

## Configuración final

1. Una vez que ambos componentes estén instalados y funcionando, configure el módulo de Dolibarr para que apunte a la URL de la aplicación Streamlit:
   - URL: `http://su_servidor:5000` o la URL pública si ha configurado un dominio

2. Pruebe la conexión desde la página de configuración del módulo.

3. Ya puede comenzar a vincular proyectos con tableros de Trello desde la sección de proyectos en Dolibarr.

## Soporte

Para cualquier consulta o soporte, contacte a:
VIAWEB S.A.S - https://web.viaweb.net.ar/