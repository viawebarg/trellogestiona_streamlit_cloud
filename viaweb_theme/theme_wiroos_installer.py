#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de instalación del tema VIAWEB para Streamlit en entorno cPanel/Wiroos
Este archivo debe colocarse en la raíz del proyecto Streamlit
"""

import os
import shutil
import sys

def crear_directorio(ruta):
    """Crea un directorio si no existe"""
    if not os.path.exists(ruta):
        try:
            os.makedirs(ruta)
            print(f"✅ Directorio creado: {ruta}")
        except Exception as e:
            print(f"❌ Error al crear directorio {ruta}: {str(e)}")
            return False
    return True

def crear_archivo(ruta, contenido):
    """Crea un archivo con el contenido especificado"""
    try:
        with open(ruta, 'w', encoding='utf-8') as f:
            f.write(contenido)
        print(f"✅ Archivo creado: {ruta}")
        return True
    except Exception as e:
        print(f"❌ Error al crear archivo {ruta}: {str(e)}")
        return False

def main():
    """Función principal de instalación"""
    print("🚀 Instalando tema VIAWEB para Streamlit en entorno cPanel/Wiroos...\n")
    
    # Ruta base - en cPanel será el directorio home del usuario
    base_dir = os.path.expanduser("~")
    streamlit_dir = os.path.join(base_dir, ".streamlit")
    
    # Crear directorio .streamlit en el directorio home del usuario
    if not crear_directorio(streamlit_dir):
        print("❌ No se pudo continuar con la instalación")
        sys.exit(1)
    
    # Configuración de Streamlit para cPanel/Wiroos
    config_content = """[server]
headless = true
address = "0.0.0.0"
port = 5000
enableCORS = true
enableXsrfProtection = false

[theme]
# Paleta de colores principal de VIAWEB
primaryColor = "#543088"  # Morado principal
backgroundColor = "#FFFFFF"  # Fondo blanco en modo claro
secondaryBackgroundColor = "#B3BDD7"  # Gris azulado claro
textColor = "#0E4895"  # Azul oscuro para texto
font = "sans serif"
"""
    
    # Crear archivo config.toml
    if not crear_archivo(os.path.join(streamlit_dir, "config.toml"), config_content):
        print("⚠️ Advertencia: No se pudo crear el archivo de configuración")
    
    # Contenido del archivo custom.css para la personalización adicional
    css_content = """/* Tema VIAWEB para Streamlit - Adaptado para cPanel/Wiroos */
/* Importar fuentes */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

/* Estilos globales */
* {
  font-family: 'Roboto', sans-serif;
}

/* Personalización de barra lateral */
.css-1d391kg, .css-12oz5g7 {
  background-color: #0E4895 !important;
}

/* Personalización de títulos */
h1, h2, h3 {
  color: #0E4895 !important;
  font-weight: 600 !important;
}

h1 {
  font-size: 2.5rem !important;
  border-bottom: 2px solid #543088;
  padding-bottom: 0.5rem;
}

h2 {
  font-size: 1.8rem !important;
  border-bottom: 1px solid #536AAF;
  padding-bottom: 0.3rem;
}

/* Personalización de botones */
button, .stButton>button {
  background-color: #0E4895 !important;
  color: white !important;
  border-radius: 6px !important;
  border: none !important;
  padding: 0.5rem 1rem !important;
  font-weight: 500 !important;
  transition: all 0.3s !important;
}

button:hover, .stButton>button:hover {
  background-color: #543088 !important;
  transform: translateY(-2px);
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1) !important;
}

/* Personalización de widgets de entrada */
input, textarea, select, .stTextInput>div>div>input, .stTextArea>div>div>textarea, .stDateInput>div>div>input {
  border-radius: 4px !important;
  border: 1px solid #B3BDD7 !important;
  padding: 0.5rem !important;
}

input:focus, textarea:focus, select:focus, .stTextInput>div>div>input:focus, .stTextArea>div>div>textarea:focus {
  border: 1px solid #543088 !important;
  box-shadow: 0 0 0 2px rgba(84, 48, 136, 0.2) !important;
}

/* Personalización de pestañas */
.stTabs [data-baseweb="tab-list"] {
  background-color: transparent !important;
  border-bottom: 1px solid #B3BDD7 !important;
}

.stTabs [data-baseweb="tab"] {
  color: #0E4895 !important;
  border-bottom: 3px solid transparent !important;
  transition: all 0.2s !important;
}

.stTabs [data-baseweb="tab"][aria-selected="true"] {
  color: #543088 !important;
  border-bottom-color: #543088 !important;
  font-weight: 500 !important;
}

/* Personalización para modo oscuro */
@media (prefers-color-scheme: dark) {
  .stApp, body {
    background-color: #121212 !important;
    color: #FFFFFF !important;
  }
  
  h1, h2, h3, p, span, label {
    color: #FFFFFF !important;
  }
}

/* Personalización de cabecera principal */
.main-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  background: linear-gradient(90deg, #0E4895, #543088);
  border-radius: 8px;
  padding: 1rem 2rem;
  color: white;
}

.main-header h1 {
  color: white !important;
  border-bottom: none;
  margin: 0;
}

/* Personalización de tarjetas de métricas */
.metric-card {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
  text-align: center;
  border-top: 3px solid #543088;
}

.metric-value {
  font-size: 2.5rem;
  font-weight: 700;
  color: #543088;
  margin: 0.5rem 0;
}

.metric-label {
  font-size: 1rem;
  color: #536AAF;
  font-weight: 500;
}

/* Footer personalizado */
.viaweb-footer {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 3rem;
  padding-top: 1rem;
  border-top: 1px solid #B3BDD7;
}

.viaweb-footer-text {
  color: #536AAF;
  font-size: 0.8rem;
}
"""
    
    # Crear archivo custom.css
    if not crear_archivo(os.path.join(streamlit_dir, "custom.css"), css_content):
        print("⚠️ Advertencia: No se pudo crear el archivo CSS personalizado")
    
    # Crear archivo de ejemplo de uso
    example_content = """import streamlit as st
import base64
from pathlib import Path

# Función para mostrar la cabecera personalizada de VIAWEB
def mostrar_cabecera(titulo="Dashboard TrelloGestiona"):
    # Crear diseño de columnas para la cabecera
    col1, col2 = st.columns([3, 1])
    
    with col1:
        st.markdown(f"<h1>{titulo}</h1>", unsafe_allow_html=True)
    
    with col2:
        # En cPanel, la ruta será relativa a la ubicación del script
        logo_path = "assets/viaweb_logo.png"
        if Path(logo_path).exists():
            st.image(logo_path, width=100)

# Función para agregar el pie de página personalizado
def agregar_footer():
    st.markdown("""
    <div class="viaweb-footer">
        <p class="viaweb-footer-text">© 2024 VIAWEB S.A.S - Todos los derechos reservados</p>
    </div>
    """, unsafe_allow_html=True)

# Función para mostrar tarjetas de métricas
def metric_card(titulo, valor, col):
    with col:
        st.markdown(f"""
        <div class="metric-card">
            <h3>{titulo}</h3>
            <div class="metric-value">{valor}</div>
        </div>
        """, unsafe_allow_html=True)

# Ejemplo de implementación
def main():
    # Mostrar cabecera
    mostrar_cabecera("Dashboard TrelloGestiona")
    
    # Contenido principal
    st.markdown("## Panel de Control")
    
    # Tarjetas de métricas
    col1, col2, col3, col4 = st.columns(4)
    metric_card("Tareas Totales", "156", col1)
    metric_card("Completadas", "78", col2)
    metric_card("En Progreso", "45", col3)
    metric_card("Pendientes", "33", col4)
    
    # Contenido adicional
    st.markdown("### Actividad Reciente")
    
    # Tablas y gráficos aquí...
    
    # Pie de página
    agregar_footer()

if __name__ == "__main__":
    main()
"""
    
    # Crear archivo de ejemplo
    if not crear_archivo("example_viaweb_theme.py", example_content):
        print("⚠️ Advertencia: No se pudo crear el archivo de ejemplo")
    
    # Crear directorio para assets
    assets_dir = "assets"
    if not crear_directorio(assets_dir):
        print("⚠️ Advertencia: No se pudo crear el directorio de assets")
    
    # Copiar imágenes si existen
    logo_origen = "viaweb_theme/img/viaweb_logo.png"
    logo_destino = os.path.join(assets_dir, "viaweb_logo.png")
    
    try:
        if os.path.exists(logo_origen):
            shutil.copy(logo_origen, logo_destino)
            print(f"✅ Logo copiado a: {logo_destino}")
        else:
            print("⚠️ Archivo de logo no encontrado. Por favor, copia manualmente un logo a la carpeta assets/")
    except Exception as e:
        print(f"⚠️ Error al copiar logo: {str(e)}")
    
    print("\n✅ Instalación del tema VIAWEB completada!")
    print("\nPara usar el tema, sigue estos pasos:")
    print("1. Asegúrate de que tu aplicación se ejecute con: streamlit run tu_app.py --server.port 5000")
    print("2. Revisa el archivo de ejemplo 'example_viaweb_theme.py' para ver cómo implementar componentes del tema")
    print("3. Para modificar colores o estilos, edita el archivo ~/.streamlit/config.toml\n")
    print("🚀 Disfruta de tu aplicación Streamlit con el tema de VIAWEB!\n")

if __name__ == "__main__":
    main()