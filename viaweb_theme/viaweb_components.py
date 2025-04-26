#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Componentes personalizados del tema VIAWEB para Streamlit
Adaptado específicamente para entornos cPanel/Wiroos
"""

import streamlit as st
import base64
from pathlib import Path
import os

# Constantes para colores
VIAWEB_MORADO = "#543088"
VIAWEB_AZUL_OSCURO = "#0E4895"
VIAWEB_AZUL_MEDIO = "#536AAF"
VIAWEB_AZUL_CLARO = "#B3BDD7"

def get_base64_encoded_image(image_path):
    """Obtiene una imagen codificada en base64"""
    with open(image_path, "rb") as img_file:
        return base64.b64encode(img_file.read()).decode()

def add_logo(logo_path, width=150):
    """Agrega un logo como imagen base64 para evitar problemas de ruta en cPanel/Wiroos"""
    if os.path.exists(logo_path):
        logo_base64 = get_base64_encoded_image(logo_path)
        st.markdown(
            f"""
            <img src="data:image/png;base64,{logo_base64}" width="{width}px">
            """,
            unsafe_allow_html=True,
        )
    else:
        st.warning(f"Logo no encontrado en: {logo_path}")

def set_page_config(page_title="VIAWEB - TrelloGestiona", layout="wide"):
    """Configura la página con el estilo VIAWEB"""
    st.set_page_config(
        page_title=page_title,
        page_icon="📊",
        layout=layout,
        initial_sidebar_state="expanded",
    )

def add_custom_css():
    """Agrega estilos CSS personalizados adicionales"""
    custom_css = """
    <style>
    /* Gradiente de fondo para sidebar */
    section[data-testid="stSidebar"] > div {
        background: linear-gradient(180deg, #0E4895 0%, #543088 100%);
    }
    
    /* Estilos para texto en sidebar */
    section[data-testid="stSidebar"] label, 
    section[data-testid="stSidebar"] .stMarkdown,
    section[data-testid="stSidebar"] .stSelectbox,
    section[data-testid="stSidebar"] span {
        color: white !important;
    }
    
    /* Estilos para widgets en modo claro */
    .stApp.light div[data-testid="stMetric"] {
        background-color: #f0f2f6;
        border-radius: 10px;
        padding: 10px 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border-left: 5px solid #0E4895;
    }
    
    /* Estilos para widgets en modo oscuro */
    .stApp.dark div[data-testid="stMetric"] {
        background-color: #283142;
        border-radius: 10px;
        padding: 10px 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        border-left: 5px solid #543088;
    }
    
    /* Personalización de enlaces */
    a {
        color: #536AAF !important;
        text-decoration: none !important;
    }
    
    a:hover {
        color: #543088 !important;
        text-decoration: underline !important;
    }
    </style>
    """
    st.markdown(custom_css, unsafe_allow_html=True)

def header_with_logo(title, logo_path=None, logo_width=100):
    """Crea un encabezado con logo y título lado a lado"""
    col1, col2 = st.columns([3, 1])
    
    with col1:
        st.markdown(f"<h1>{title}</h1>", unsafe_allow_html=True)
    
    with col2:
        if logo_path and os.path.exists(logo_path):
            add_logo(logo_path, logo_width)

def gradient_header(title, subtitle=None):
    """Encabezado con fondo degradado"""
    subtitle_html = f"<p>{subtitle}</p>" if subtitle else ""
    
    st.markdown(
        f"""
        <div style="background: linear-gradient(90deg, {VIAWEB_AZUL_OSCURO}, {VIAWEB_MORADO}); 
                    color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <h2 style="color: white !important; border-bottom: none; margin: 0;">{title}</h2>
            {subtitle_html}
        </div>
        """,
        unsafe_allow_html=True,
    )

def metric_card(label, value, description=None, delta=None, delta_color="normal"):
    """Tarjeta de métrica con estilo personalizado"""
    if delta is not None:
        st.metric(label=label, value=value, delta=delta, delta_color=delta_color)
    else:
        st.metric(label=label, value=value)
    
    if description:
        st.caption(description)

def action_button(label, key=None, help=None, on_click=None):
    """Botón de acción personalizado"""
    btn = st.button(
        label,
        key=key,
        help=help,
        on_click=on_click,
        use_container_width=True,
    )
    return btn

def info_card(title, content, icon="ℹ️"):
    """Tarjeta de información con icono"""
    st.markdown(
        f"""
        <div style="background-color: rgba(83, 106, 175, 0.1); 
                    border-left: 5px solid {VIAWEB_AZUL_MEDIO}; 
                    padding: 15px; border-radius: 5px; margin-bottom: 10px;">
            <h4 style="color: {VIAWEB_AZUL_OSCURO}; margin-top: 0;">{icon} {title}</h4>
            <p>{content}</p>
        </div>
        """,
        unsafe_allow_html=True,
    )

def viaweb_footer():
    """Pie de página personalizado de VIAWEB"""
    st.markdown(
        f"""
        <div style="border-top: 1px solid #B3BDD7; margin-top: 50px; padding-top: 20px; 
                    text-align: center; font-size: 0.8em; color: {VIAWEB_AZUL_MEDIO};">
            <p>© 2024 VIAWEB S.A.S - Todos los derechos reservados</p>
            <p>Desarrollado con ❤️ por el equipo de VIAWEB</p>
        </div>
        """,
        unsafe_allow_html=True,
    )

def dashboard_template():
    """Template básico para dashboard con estilo VIAWEB"""
    # Configuración de página
    set_page_config("VIAWEB Dashboard")
    add_custom_css()
    
    # Sidebar
    with st.sidebar:
        logo_path = "assets/viaweb_logo.png"
        if os.path.exists(logo_path):
            add_logo(logo_path, 120)
        
        st.markdown("### Navegación")
        page = st.selectbox(
            "Ir a:", 
            ["Dashboard", "Proyectos", "Tareas", "Configuración"]
        )
        
        st.markdown("### Filtros")
        st.date_input("Fecha inicio")
        st.date_input("Fecha fin")
        
        st.markdown("---")
        st.markdown("v1.0.0 - TrelloGestiona")
    
    # Contenido principal
    header_with_logo("Dashboard TrelloGestiona", "assets/viaweb_logo.png")
    
    # Tarjetas de métricas
    col1, col2, col3, col4 = st.columns(4)
    with col1:
        metric_card("Tareas", "156", "Total de tareas")
    with col2:
        metric_card("Completadas", "78", "50%", "+12%", "normal")
    with col3:
        metric_card("En Progreso", "45", "29%", "+5%", "normal")
    with col4:
        metric_card("Pendientes", "33", "21%", "-8%", "inverse")
    
    # Secciones de contenido
    gradient_header("Resumen de Actividad", "Últimos 30 días")
    
    # Fin de la página
    viaweb_footer()

# Ejemplo de uso
if __name__ == "__main__":
    dashboard_template()