import os
import streamlit as st
import pandas as pd
import plotly.express as px
import json
import shutil
import numpy as np
import subprocess
from datetime import datetime, timedelta
from cargar_datos import procesar_todos_los_json, convertir_a_dataframe, priorizar_tareas, categorizar_tareas
from gestor_flujo_trabajo import crear_flujo_trabajo, mapear_listas_trello_a_flujo_trabajo
import db_manager
import automatizacion_tareas
import generador_scripts
from dolibarr_api_client import DolibarrAPIClient, get_dolibarr_client

# Configuración para CORS en Streamlit Cloud
try:
    # Obtener la aplicación FastAPI de Streamlit (solo funciona en Streamlit Cloud)
    from fastapi import FastAPI
    from fastapi.middleware.cors import CORSMiddleware
    
    # Intentar obtener la aplicación FastAPI subyacente
    # (esto solo funciona en entorno de Streamlit Cloud)
    try:
        app = st._get_script_run_ctx().streamlit_server.server_app
        
        # Agregar middleware CORS para permitir conexiones desde Dolibarr
        app.add_middleware(
            CORSMiddleware,
            allow_origins=["*"],  # En producción, límitalo a tu dominio de Dolibarr
            allow_credentials=True,
            allow_methods=["*"],
            allow_headers=["*"],
        )
        st.write("CORS configurado correctamente para Streamlit Cloud")
    except:
        # Si falla, no hay problema, estamos en local
        pass
except ImportError:
    # Si fastapi no está disponible, estamos ejecutando localmente
    pass

# Configuración de la página
st.set_page_config(
    page_title="Gestor de Tareas Trello",
    page_icon="📋",
    layout="wide",
    initial_sidebar_state="expanded"
)

# Inicializar la base de datos
try:
    db_status = db_manager.inicializar_db()
except Exception as e:
    st.error(f"Error al inicializar la base de datos: {str(e)}")
    db_status = False

# Inicializa el estado de la sesión para almacenar datos
if 'trello_data' not in st.session_state:
    st.session_state.trello_data = None
if 'filtered_data' not in st.session_state:
    st.session_state.filtered_data = None
if 'workflow_stages' not in st.session_state:
    # Obtener etapas del flujo de trabajo desde la base de datos
    try:
        etapas_flujo = db_manager.obtener_configuracion_flujo_trabajo()
        st.session_state.workflow_stages = etapas_flujo
    except:
        # Si falla, usar etapas predeterminadas
        st.session_state.workflow_stages = ["Por hacer", "En progreso", "Completado"]
if 'all_lists' not in st.session_state:
    st.session_state.all_lists = []
if 'list_workflow_mapping' not in st.session_state:
    st.session_state.list_workflow_mapping = {}
if 'files_processed' not in st.session_state:
    st.session_state.files_processed = False
if 'db_initialized' not in st.session_state:
    st.session_state.db_initialized = db_status
    if db_status:
        st.success("Base de datos inicializada correctamente.")
    else:
        st.warning("La base de datos no pudo inicializarse. La aplicación funcionará con funcionalidad limitada.")

# Variables de sesión para la integración con Dolibarr
if 'dolibarr_url' not in st.session_state:
    st.session_state.dolibarr_url = os.environ.get('DOLIBARR_URL', '')
if 'dolibarr_api_token' not in st.session_state:
    st.session_state.dolibarr_api_token = os.environ.get('DOLIBARR_API_TOKEN', '')

# Título y descripción
st.title("Gestor de Tareas Trello")
st.write("Procesá, organizá y gestioná tus tareas de Trello de manera eficiente con este sistema de flujo de trabajo simplificado.")

# Barra de estado para mostrar conexión con Dolibarr
if st.session_state.dolibarr_url and st.session_state.dolibarr_api_token:
    st.success(f"✓ Conectado a Dolibarr: {st.session_state.dolibarr_url}", icon="✓")
else:
    st.info("ℹ️ Sin conexión con Dolibarr. Configura la conexión en la pestaña 'Integración Dolibarr'.")

# Barra lateral para configuración y carga de datos
with st.sidebar:
    st.header("Configuración")
    
    # Asegurarse de que existe la carpeta 'datos'
    if not os.path.exists('datos'):
        os.makedirs('datos')
    
    # Sección para cargar archivos JSON
    st.subheader("Archivos JSON de Trello")
    
    # Verificar archivos en la carpeta attached_assets
    archivos_json_adjuntos = []
    if os.path.exists('attached_assets'):
        for archivo in os.listdir('attached_assets'):
            if archivo.endswith('.json'):
                archivos_json_adjuntos.append(archivo)
    
    if archivos_json_adjuntos:
        st.info(f"Se encontraron {len(archivos_json_adjuntos)} archivos JSON en 'attached_assets':")
        for archivo in archivos_json_adjuntos:
            st.write(f"- {archivo}")
        
        if st.button("Copiar archivos a carpeta 'datos'"):
            # Copiar los archivos JSON de attached_assets a datos
            for archivo in archivos_json_adjuntos:
                origen = os.path.join('attached_assets', archivo)
                destino = os.path.join('datos', archivo)
                shutil.copy2(origen, destino)
            
            st.success(f"Se copiaron {len(archivos_json_adjuntos)} archivos a la carpeta 'datos'.")
            st.session_state.files_processed = False
    
    # Opción para cargar archivos manualmente
    st.subheader("Cargar archivos adicionales")
    uploaded_files = st.file_uploader("Subí archivos JSON adicionales de Trello", 
                                     type=['json'], 
                                     accept_multiple_files=True)
    
    if uploaded_files:
        # Guardar los archivos cargados en la carpeta datos
        for uploaded_file in uploaded_files:
            file_path = os.path.join('datos', uploaded_file.name)
            with open(file_path, 'wb') as f:
                f.write(uploaded_file.getbuffer())
        
        st.success(f"Se subieron {len(uploaded_files)} archivos adicionales correctamente.")
        st.session_state.files_processed = False
    
    # Botón para procesar los archivos
    col1, col2 = st.columns(2)
    
    proceso_json = col1.button("Procesar Tareas (JSON)")
    guardar_db = col2.button("Guardar en Base de Datos")
    
    if proceso_json:
        with st.spinner("Procesando tareas desde archivos JSON..."):
            # Procesar todos los archivos JSON en la carpeta 'datos'
            tarjetas = procesar_todos_los_json()
            
            if tarjetas:
                # Convertir a DataFrame y aplicar priorización y categorización
                tareas_df = convertir_a_dataframe(tarjetas)
                tareas_df = priorizar_tareas(tareas_df)
                tareas_df = categorizar_tareas(tareas_df)
                
                # Extraer todas las listas únicas para configurar el mapeo de flujo de trabajo
                todas_las_listas = tareas_df['nombre_lista'].unique().tolist()
                st.session_state.all_lists = todas_las_listas
                
                # Crear mapeo predeterminado de listas a etapas de flujo de trabajo
                st.session_state.list_workflow_mapping = mapear_listas_trello_a_flujo_trabajo(
                    todas_las_listas, 
                    st.session_state.workflow_stages
                )
                
                # Guardar en el estado de la sesión
                st.session_state.trello_data = tareas_df
                st.session_state.filtered_data = tareas_df.copy()
                st.session_state.files_processed = True
                
                # Mostrar resultados
                st.success(f"Se procesaron {len(tareas_df)} tareas exitosamente desde archivos JSON!")
            else:
                st.warning("No se encontraron tareas en los archivos JSON o no hay archivos para procesar.")
    
    if guardar_db and st.session_state.files_processed:
        with st.spinner("Guardando datos en la base de datos..."):
            try:
                # Leer cada archivo JSON en la carpeta datos
                archivos_json = []
                for archivo in os.listdir('datos'):
                    if archivo.endswith('.json'):
                        ruta_completa = os.path.join('datos', archivo)
                        with open(ruta_completa, 'r', encoding='utf-8') as f:
                            try:
                                tablero_json = json.load(f)
                                archivos_json.append(tablero_json)
                            except json.JSONDecodeError:
                                st.error(f"Error al decodificar el archivo JSON: {archivo}")
                
                # Guardar en la base de datos
                tableros, listas, tareas = db_manager.cargar_datos_trello_a_db(archivos_json)
                
                # Actualizar mensaje
                mensaje = f"Datos guardados en base de datos: {tableros} tableros, {listas} listas, {tareas} tareas"
                st.success(mensaje)
                
                # Cargar datos desde la base de datos
                tareas_db = db_manager.cargar_tareas()
                if not tareas_db.empty:
                    st.session_state.trello_data = tareas_db
                    st.session_state.filtered_data = tareas_db.copy()
                    
            except Exception as e:
                st.error(f"Error al guardar en la base de datos: {str(e)}")
    
    # Si no se ha procesado el archivo, mostrar un mensaje
    if not st.session_state.files_processed and guardar_db:
        st.warning("Primero debes procesar las tareas desde los archivos JSON.")
    
    # Opciones de exportación
    if st.session_state.filtered_data is not None:
        st.header("Opciones de Exportación")
        
        col1, col2 = st.columns(2)
        
        with col1:
            if st.button("Exportar a CSV"):
                csv = st.session_state.filtered_data.to_csv(index=False)
                st.download_button(
                    label="Descargar CSV",
                    data=csv,
                    file_name="tareas_trello.csv",
                    mime="text/csv"
                )
        
        with col2:
            if st.button("Exportar a Excel"):
                # Crear archivo Excel en memoria
                buffer = pd.io.excel.BytesIO()
                with pd.ExcelWriter(buffer) as writer:
                    st.session_state.filtered_data.to_excel(writer, index=False, sheet_name="Tareas")
                
                st.download_button(
                    label="Descargar Excel",
                    data=buffer.getvalue(),
                    file_name="tareas_trello.xlsx",
                    mime="application/vnd.ms-excel"
                )

# Área de contenido principal
if st.session_state.trello_data is not None:
    # Pestañas para diferentes vistas
    tab1, tab2, tab3, tab4, tab5 = st.tabs(["Panel de Tareas", "Vista de Flujo", "Análisis", "Automatización", "Integración Dolibarr"])
    
    # Pestaña de Panel de Tareas
    with tab1:
        st.header("Panel de Tareas")
        
        # Filtros
        st.subheader("Filtros")
        
        col1, col2, col3 = st.columns(3)
        
        with col1:
            # Filtro de prioridad
            prioridades = ["Todas"] + sorted(st.session_state.trello_data["prioridad"].unique().tolist())
            prioridad_filtro = st.selectbox("Prioridad", prioridades)
        
        with col2:
            # Filtro de categoría
            categorias = ["Todas"] + sorted(st.session_state.trello_data["categoria"].unique().tolist())
            categoria_filtro = st.selectbox("Categoría", categorias)
        
        with col3:
            # Filtro de etapa de flujo
            listas = ["Todas"] + sorted(st.session_state.trello_data["nombre_lista"].unique().tolist())
            lista_filtro = st.selectbox("Lista / Etapa", listas)
        
        # Filtro de texto
        texto_filtro = st.text_input("Buscar en nombre o descripción")
        
        # Aplicar filtros
        df_filtrado = st.session_state.trello_data.copy()
        
        if prioridad_filtro != "Todas":
            df_filtrado = df_filtrado[df_filtrado["prioridad"] == prioridad_filtro]
        
        if categoria_filtro != "Todas":
            df_filtrado = df_filtrado[df_filtrado["categoria"] == categoria_filtro]
        
        if lista_filtro != "Todas":
            df_filtrado = df_filtrado[df_filtrado["nombre_lista"] == lista_filtro]
        
        if texto_filtro:
            # Buscar en nombre y descripción
            df_filtrado = df_filtrado[
                df_filtrado["nombre"].str.contains(texto_filtro, case=False, na=False) |
                df_filtrado["descripcion"].str.contains(texto_filtro, case=False, na=False)
            ]
        
        # Actualizar el DataFrame filtrado en la sesión
        st.session_state.filtered_data = df_filtrado
        
        # Mostrar las tareas filtradas
        st.subheader(f"Tareas ({len(df_filtrado)})")
        
        if not df_filtrado.empty:
            # Ordenar por prioridad
            df_filtrado = df_filtrado.sort_values(by="prioridad", ascending=False)
            
            # Mostrar en forma de tarjetas
            for _, tarea in df_filtrado.iterrows():
                # Determinar color según prioridad
                if tarea["prioridad"] == "Alta":
                    color = "#FFCCCC"  # Rojo claro
                elif tarea["prioridad"] == "Media":
                    color = "#FFFFCC"  # Amarillo claro
                else:
                    color = "#CCFFCC"  # Verde claro
                
                # Crear tarjeta
                with st.container(border=True):
                    col1, col2 = st.columns([4, 1])
                    
                    with col1:
                        st.markdown(f"#### {tarea['nombre']}")
                        
                        if not pd.isna(tarea["descripcion"]) and tarea["descripcion"]:
                            with st.expander("Ver descripción"):
                                st.write(tarea["descripcion"])
                        
                        # Metadatos
                        st.caption(f"Lista: {tarea['nombre_lista']} | Prioridad: {tarea['prioridad']} | Categoría: {tarea['categoria']}")
                        
                        # Información de fechas
                        fechas_info = []
                        
                        if not pd.isna(tarea["fecha_creacion"]):
                            fechas_info.append(f"Creada: {tarea['fecha_creacion'].strftime('%d/%m/%Y')}")
                        
                        if not pd.isna(tarea["fecha_vencimiento"]):
                            fechas_info.append(f"Vence: {tarea['fecha_vencimiento'].strftime('%d/%m/%Y')}")
                        
                        if fechas_info:
                            st.caption(" | ".join(fechas_info))
                        
                        # Etiquetas
                        if not pd.isna(tarea["etiquetas"]) and tarea["etiquetas"]:
                            etiquetas = tarea["etiquetas"].split(",")
                            for etiqueta in etiquetas:
                                st.caption(f":label: {etiqueta.strip()}")
                    
                    with col2:
                        # Enlaces y acciones
                        if not pd.isna(tarea["url"]) and tarea["url"]:
                            st.link_button("Ver en Trello", tarea["url"])
                        
                        # Mostrar porcentaje de completado si está disponible
                        if "porcentaje_completado" in tarea and not pd.isna(tarea["porcentaje_completado"]):
                            st.progress(float(tarea["porcentaje_completado"]) / 100)
                            st.caption(f"{int(tarea['porcentaje_completado'])}% completado")
    
    # Pestaña de Vista de Flujo
    with tab2:
        st.header("Vista de Flujo de Trabajo")
        
        # Sección de configuración del flujo de trabajo
        with st.expander("Configurar Flujo de Trabajo", expanded=False):
            st.subheader("Etapas del Flujo de Trabajo")
            
            # Mostrar las etapas actuales
            etapas_actuales = st.session_state.workflow_stages
            
            st.write("Etapas actuales:")
            for i, etapa in enumerate(etapas_actuales):
                st.write(f"{i+1}. {etapa}")
            
            # Formulario para editar las etapas
            with st.form("workflow_form"):
                etapas_texto = st.text_area(
                    "Editar etapas (una por línea)",
                    value="\n".join(etapas_actuales)
                )
                
                # Botón para guardar cambios
                submit_button = st.form_submit_button("Guardar cambios")
            
            if submit_button:
                # Procesar las etapas ingresadas
                nuevas_etapas = [etapa.strip() for etapa in etapas_texto.split("\n") if etapa.strip()]
                
                if nuevas_etapas:
                    # Actualizar las etapas en la sesión
                    st.session_state.workflow_stages = nuevas_etapas
                    
                    # Guardar en la base de datos si está inicializada
                    if st.session_state.db_initialized:
                        try:
                            db_manager.guardar_configuracion_flujo_trabajo(
                                "Default",
                                nuevas_etapas,
                                es_default=True
                            )
                            st.success("Configuración del flujo de trabajo guardada en la base de datos.")
                        except Exception as e:
                            st.error(f"Error al guardar en la base de datos: {str(e)}")
                    
                    # Actualizar el mapeo de listas a etapas
                    if st.session_state.all_lists:
                        st.session_state.list_workflow_mapping = mapear_listas_trello_a_flujo_trabajo(
                            st.session_state.all_lists,
                            nuevas_etapas
                        )
                    
                    st.success("Etapas del flujo de trabajo actualizadas correctamente.")
                    st.rerun()
                else:
                    st.error("Debes especificar al menos una etapa.")
            
            # Mapeo de listas de Trello a etapas del flujo
            if st.session_state.all_lists:
                st.subheader("Mapeo de Listas a Etapas")
                st.write("Asigna cada lista de Trello a una etapa del flujo de trabajo:")
                
                # Crear un formulario para el mapeo
                with st.form("mapping_form"):
                    mappings = {}
                    
                    for lista in st.session_state.all_lists:
                        # Determinar la etapa actual (si existe)
                        etapa_actual = st.session_state.list_workflow_mapping.get(lista, st.session_state.workflow_stages[0])
                        
                        # Selectbox para asignar la etapa
                        selected_etapa = st.selectbox(
                            f"Lista: {lista}",
                            options=st.session_state.workflow_stages,
                            index=st.session_state.workflow_stages.index(etapa_actual) if etapa_actual in st.session_state.workflow_stages else 0
                        )
                        
                        # Guardar la selección
                        mappings[lista] = selected_etapa
                    
                    # Botón para guardar el mapeo
                    submit_mapping = st.form_submit_button("Guardar mapeo")
                
                if submit_mapping:
                    # Actualizar el mapeo en la sesión
                    st.session_state.list_workflow_mapping = mappings
                    st.success("Mapeo de listas a etapas actualizado correctamente.")
                    st.rerun()
        
        # Visualización del flujo de trabajo
        st.subheader("Distribución de Tareas por Etapa")
        
        if not df_filtrado.empty:
            # Contar tareas por etapa
            tareas_por_lista = df_filtrado.groupby("nombre_lista").size().reset_index(name="count")
            
            # Para cada lista, asignar su etapa según el mapeo
            if st.session_state.list_workflow_mapping:
                tareas_por_lista["etapa"] = tareas_por_lista["nombre_lista"].map(st.session_state.list_workflow_mapping)
            else:
                tareas_por_lista["etapa"] = "Sin asignar"
            
            # Agrupar por etapa
            tareas_por_etapa = tareas_por_lista.groupby("etapa")["count"].sum().reset_index()
            
            # Ordenar según el orden de las etapas en el flujo
            # Crear un diccionario con el orden de las etapas
            orden_etapas = {etapa: i for i, etapa in enumerate(st.session_state.workflow_stages)}
            
            # Función para obtener el orden
            def get_etapa_orden(etapa):
                return orden_etapas.get(etapa, 999)  # Etapas no mapeadas al final
            
            # Ordenar el dataframe
            tareas_por_etapa["orden"] = tareas_por_etapa["etapa"].apply(get_etapa_orden)
            tareas_por_etapa = tareas_por_etapa.sort_values("orden")
            
            # Crear gráfico de barras horizontal
            fig = px.bar(
                tareas_por_etapa,
                y="etapa",
                x="count",
                orientation="h",
                title="Tareas por etapa de flujo de trabajo",
                labels={"count": "Número de tareas", "etapa": "Etapa"},
                color="etapa",
                color_discrete_sequence=px.colors.qualitative.Pastel,
            )
            
            fig.update_layout(showlegend=False)
            st.plotly_chart(fig, use_container_width=True)
            
            # Mostrar tareas por etapa
            for etapa in st.session_state.workflow_stages:
                # Obtener listas asociadas a esta etapa
                listas_en_etapa = [lista for lista, e in st.session_state.list_workflow_mapping.items() if e == etapa]
                
                # Filtrar tareas en estas listas
                tareas_en_etapa = df_filtrado[df_filtrado["nombre_lista"].isin(listas_en_etapa)]
                
                if not tareas_en_etapa.empty:
                    with st.expander(f"{etapa} ({len(tareas_en_etapa)} tareas)", expanded=False):
                        # Mostrar tareas en forma de lista
                        for _, tarea in tareas_en_etapa.iterrows():
                            st.markdown(f"**{tarea['nombre']}** - {tarea['nombre_lista']} (Prioridad: {tarea['prioridad']})")
                            
                            # Mostrar descripción abreviada si existe
                            if not pd.isna(tarea["descripcion"]) and tarea["descripcion"]:
                                desc_short = tarea["descripcion"][:100] + "..." if len(tarea["descripcion"]) > 100 else tarea["descripcion"]
                                st.caption(desc_short)
                            
                            st.divider()
        else:
            st.warning("No hay tareas para mostrar en el flujo de trabajo.")
    
    # Pestaña de Análisis
    with tab3:
        st.header("Análisis de Tareas")
        
        if not df_filtrado.empty:
            # Dividir en columnas
            col1, col2 = st.columns(2)
            
            with col1:
                # Distribución por prioridad
                st.subheader("Distribución por Prioridad")
                prioridad_counts = df_filtrado["prioridad"].value_counts().reset_index()
                prioridad_counts.columns = ["Prioridad", "Cantidad"]
                
                # Definir colores según prioridad
                colors = {
                    "Alta": "#FF9999",
                    "Media": "#FFCC99",
                    "Baja": "#99CC99"
                }
                
                color_map = {row["Prioridad"]: colors.get(row["Prioridad"], "#CCCCCC") for _, row in prioridad_counts.iterrows()}
                
                fig1 = px.pie(
                    prioridad_counts,
                    values="Cantidad",
                    names="Prioridad",
                    title="Tareas por Prioridad",
                    color="Prioridad",
                    color_discrete_map=color_map
                )
                
                st.plotly_chart(fig1, use_container_width=True)
            
            with col2:
                # Distribución por categoría
                st.subheader("Distribución por Categoría")
                if "categoria" in df_filtrado.columns:
                    categoria_counts = df_filtrado["categoria"].value_counts().reset_index()
                    categoria_counts.columns = ["Categoría", "Cantidad"]
                    
                    fig2 = px.pie(
                        categoria_counts,
                        values="Cantidad",
                        names="Categoría",
                        title="Tareas por Categoría",
                        color="Categoría",
                        color_discrete_sequence=px.colors.qualitative.Pastel
                    )
                    
                    st.plotly_chart(fig2, use_container_width=True)
                else:
                    st.info("No hay datos de categoría disponibles para el análisis.")
            
            # Análisis temporal
            st.subheader("Análisis Temporal")
            
            # Verificar si hay fechas de vencimiento
            if "fecha_vencimiento" in df_filtrado.columns and not df_filtrado["fecha_vencimiento"].isna().all():
                # Agrupar por fecha de vencimiento
                df_con_fecha = df_filtrado.dropna(subset=["fecha_vencimiento"])
                df_con_fecha["fecha_vencimiento"] = pd.to_datetime(df_con_fecha["fecha_vencimiento"]).dt.date
                tareas_por_fecha = df_con_fecha.groupby("fecha_vencimiento").size().reset_index(name="count")
                
                # Crear gráfico de línea
                fig3 = px.line(
                    tareas_por_fecha,
                    x="fecha_vencimiento",
                    y="count",
                    title="Tareas por Fecha de Vencimiento",
                    markers=True
                )
                
                st.plotly_chart(fig3, use_container_width=True)
                
                # Identificar tareas próximas a vencer
                today = datetime.now().date()
                
                # Tareas vencidas
                tareas_vencidas = df_con_fecha[df_con_fecha["fecha_vencimiento"] < today]
                
                # Tareas que vencen hoy
                tareas_hoy = df_con_fecha[df_con_fecha["fecha_vencimiento"] == today]
                
                # Tareas que vencen en los próximos 7 días
                next_week = today + timedelta(days=7)
                tareas_proximas = df_con_fecha[(df_con_fecha["fecha_vencimiento"] > today) & (df_con_fecha["fecha_vencimiento"] <= next_week)]
                
                # Mostrar resumen
                col1, col2, col3 = st.columns(3)
                
                with col1:
                    st.metric("Tareas vencidas", len(tareas_vencidas))
                    if not tareas_vencidas.empty:
                        with st.expander("Ver tareas vencidas"):
                            for _, tarea in tareas_vencidas.iterrows():
                                st.markdown(f"**{tarea['nombre']}** - Venció el {tarea['fecha_vencimiento']}")
                
                with col2:
                    st.metric("Tareas que vencen hoy", len(tareas_hoy))
                    if not tareas_hoy.empty:
                        with st.expander("Ver tareas de hoy"):
                            for _, tarea in tareas_hoy.iterrows():
                                st.markdown(f"**{tarea['nombre']}**")
                
                with col3:
                    st.metric("Próximos 7 días", len(tareas_proximas))
                    if not tareas_proximas.empty:
                        with st.expander("Ver tareas próximas"):
                            for _, tarea in tareas_proximas.iterrows():
                                st.markdown(f"**{tarea['nombre']}** - Vence el {tarea['fecha_vencimiento']}")
            else:
                st.info("No hay fechas de vencimiento disponibles para el análisis temporal.")
            
            # Análisis de productividad
            st.subheader("Análisis de Productividad")
            
            # Total de tareas por lista
            tareas_por_lista = df_filtrado["nombre_lista"].value_counts().reset_index()
            tareas_por_lista.columns = ["Lista", "Cantidad"]
            
            fig4 = px.bar(
                tareas_por_lista,
                x="Lista",
                y="Cantidad",
                title="Distribución de Tareas por Lista",
                color="Cantidad",
                color_continuous_scale="Viridis"
            )
            
            st.plotly_chart(fig4, use_container_width=True)
            
            # Eficiencia y sugerencias
            if "porcentaje_completado" in df_filtrado.columns:
                # Calcular promedio de completado
                promedio_completado = df_filtrado["porcentaje_completado"].mean()
                
                st.metric("Porcentaje promedio de completado", f"{promedio_completado:.1f}%")
                
                # Mostrar tareas con bajo porcentaje de completado
                tareas_bajas = df_filtrado[df_filtrado["porcentaje_completado"] < 25].sort_values("prioridad", ascending=False)
                
                if not tareas_bajas.empty:
                    with st.expander(f"Tareas con bajo avance ({len(tareas_bajas)})", expanded=False):
                        for _, tarea in tareas_bajas.iterrows():
                            st.markdown(f"**{tarea['nombre']}** - {tarea['porcentaje_completado']}% completado (Prioridad: {tarea['prioridad']})")
        else:
            st.warning("No hay datos suficientes para el análisis. Aplica filtros menos restrictivos.")
    
    # Pestaña de Automatización
    with tab4:
        st.header("Automatización de Tareas")
        
        # Opciones
        if not df_filtrado.empty:
            st.subheader("Detección de Automatización")
            
            # Analizar tareas para automatización
            df_con_auto = automatizacion_tareas.analizar_automatizacion(df_filtrado)
            
            # Tareas automatizables
            tareas_auto = df_con_auto[df_con_auto["automatizable"] == True]
            
            if not tareas_auto.empty:
                st.success(f"Se detectaron {len(tareas_auto)} tareas potencialmente automatizables!")
                
                for _, tarea in tareas_auto.iterrows():
                    with st.expander(f"{tarea['nombre']} ({tarea['tipo_automatizacion']})", expanded=False):
                        st.write(f"**Descripción:** {tarea['descripcion']}")
                        st.write(f"**Tipo de automatización:** {tarea['tipo_automatizacion']}")
                        st.write(f"**Confianza:** {tarea['confianza_automatizacion']*100:.1f}%")
                        
                        # Botón para generar plan de automatización
                        if st.button("Generar plan de automatización", key=f"plan_{tarea['id']}"):
                            with st.spinner("Generando plan de automatización..."):
                                plan = automatizacion_tareas.generar_plan_automatizacion(tarea['id'])
                                
                                if plan:
                                    st.subheader("Plan de Automatización")
                                    st.write(f"**Pasos:**")
                                    for i, paso in enumerate(plan.get("pasos", [])):
                                        st.write(f"{i+1}. {paso}")
                                    
                                    st.write(f"**Herramientas recomendadas:**")
                                    for herramienta in plan.get("herramientas", []):
                                        st.write(f"- {herramienta}")
                                    
                                    # Botón para generar script
                                    if st.button("Generar script", key=f"script_{tarea['id']}"):
                                        with st.spinner("Generando script..."):
                                            script = generador_scripts.generar_script_automatizacion(
                                                tarea,
                                                tipo=tarea['tipo_automatizacion']
                                            )
                                            
                                            if script:
                                                st.code(script, language="python")
                                                
                                                # Opción para descargar
                                                st.download_button(
                                                    label="Descargar script",
                                                    data=script,
                                                    file_name=f"automatizacion_{tarea['id']}.py",
                                                    mime="text/plain"
                                                )
                        
                        # Botón para simular automatización
                        if st.button("Simular automatización", key=f"simular_{tarea['id']}"):
                            with st.spinner("Simulando automatización..."):
                                resultado = automatizacion_tareas.ejecutar_automatizacion_simulada(tarea['id'])
                                
                                if resultado:
                                    st.success("Simulación exitosa!")
                                    st.json(resultado)
            else:
                st.info("No se detectaron tareas automatizables en la selección actual.")
                
                # Sugerencias
                st.write("""
                Para identificar tareas automatizables:
                
                1. Asegúrate de incluir detalles específicos en las descripciones de tus tareas
                2. Usa etiquetas como 'automatizable', 'repetitiva', 'informe', etc.
                3. Describe procedimientos paso a paso cuando sea posible
                """)
            
            # Estadísticas de automatización
            st.subheader("Estadísticas de Automatización")
            
            estadisticas = automatizacion_tareas.obtener_estadisticas_automatizacion(df_con_auto)
            
            # Mostrar estadísticas
            col1, col2, col3 = st.columns(3)
            
            with col1:
                st.metric("Tareas automatizables", f"{estadisticas['porcentaje_automatizable']:.1f}%", 
                          f"{estadisticas['num_automatizable']} de {estadisticas['total']}")
            
            with col2:
                st.metric("Ahorro potencial de tiempo", f"{estadisticas['ahorro_tiempo_estimado']:.1f} horas")
            
            with col3:
                st.metric("Tipo más común", estadisticas['tipo_mas_comun'])
            
            # Visualización
            if 'tipos_distribucion' in estadisticas and estadisticas['tipos_distribucion']:
                # Convertir a DataFrame para la visualización
                tipos_df = pd.DataFrame(
                    [(tipo, cantidad) for tipo, cantidad in estadisticas['tipos_distribucion'].items()],
                    columns=["Tipo", "Cantidad"]
                )
                
                # Ordenar por cantidad
                tipos_df = tipos_df.sort_values("Cantidad", ascending=False)
                
                # Crear gráfico de barras
                fig = px.bar(
                    tipos_df,
                    x="Tipo",
                    y="Cantidad",
                    title="Distribución por Tipo de Automatización",
                    color="Tipo"
                )
                
                st.plotly_chart(fig, use_container_width=True)
        else:
            st.warning("No hay datos para analizar automatización. Aplica filtros menos restrictivos.")
    
    # Pestaña de Integración Dolibarr
    with tab5:
        st.header("Integración con Dolibarr ERP")
        
        # Sección de configuración
        st.subheader("Configuración de la Conexión")
        
        with st.form("dolibarr_config_form"):
            col1, col2 = st.columns(2)
            
            with col1:
                # URL de Dolibarr
                dolibarr_url = st.text_input(
                    "URL de Dolibarr",
                    value=st.session_state.dolibarr_url,
                    help="Dirección completa del servidor Dolibarr (ej: http://miservidor.com/dolibarr)"
                )
            
            with col2:
                # Token de API
                dolibarr_api_token = st.text_input(
                    "Token de API",
                    value=st.session_state.dolibarr_api_token,
                    type="password",
                    help="Token de API generado en el módulo TrelloGestiona de Dolibarr"
                )
            
            # Botón para guardar configuración
            submit_button = st.form_submit_button("Guardar Configuración")
            
            if submit_button:
                # Actualizar estado de la sesión
                st.session_state.dolibarr_url = dolibarr_url
                st.session_state.dolibarr_api_token = dolibarr_api_token
                
                # Crear o actualizar el cliente
                if 'dolibarr_client' in st.session_state:
                    del st.session_state.dolibarr_client
                
                st.success("Configuración guardada correctamente")
                st.rerun()
        
        # Separador
        st.divider()
        
        # Obtener el cliente de Dolibarr
        dolibarr_client = get_dolibarr_client()
        
        # Verificar si el cliente está configurado
        if not dolibarr_client.is_configured():
            st.warning("Por favor, completa la configuración de conexión a Dolibarr.")
        else:
            # Menú de operaciones
            st.subheader("Operaciones disponibles")
            
            # Definir pestañas para operaciones
            op_tab1, op_tab2, op_tab3 = st.tabs(["Proyectos", "Tableros Vinculados", "Sincronización"])
            
            # Pestaña de Proyectos
            with op_tab1:
                st.subheader("Proyectos en Dolibarr")
                
                if st.button("Obtener Proyectos", key="get_projects_btn"):
                    with st.spinner("Obteniendo proyectos desde Dolibarr..."):
                        proyectos = dolibarr_client.get_projects()
                        
                        if proyectos:
                            st.session_state.dolibarr_projects = proyectos
                            st.success(f"Se encontraron {len(proyectos)} proyectos")
                        else:
                            st.warning("No se encontraron proyectos o hubo un error de comunicación")
                
                # Mostrar proyectos si están disponibles
                if 'dolibarr_projects' in st.session_state and st.session_state.dolibarr_projects:
                    for proyecto in st.session_state.dolibarr_projects:
                        with st.expander(f"{proyecto['ref']} - {proyecto['title']}"):
                            st.write(f"ID: {proyecto['id']}")
                            st.write(f"Etiquetas: {', '.join(proyecto.get('tags', []))}")
                            st.write(f"Estado: {proyecto.get('status', 'Desconocido')}")
                            
                            if 'description' in proyecto and proyecto['description']:
                                st.write(f"Descripción: {proyecto['description']}")
            
            # Pestaña de Tableros Vinculados
            with op_tab2:
                st.subheader("Tableros vinculados con Proyectos")
                
                if st.button("Obtener Vinculaciones", key="get_links_btn"):
                    with st.spinner("Obteniendo vinculaciones desde Dolibarr..."):
                        vinculaciones = dolibarr_client.get_linked_boards()
                        
                        if vinculaciones:
                            st.session_state.dolibarr_links = vinculaciones
                            st.success(f"Se encontraron {len(vinculaciones)} vinculaciones")
                        else:
                            st.warning("No se encontraron vinculaciones o hubo un error de comunicación")
                
                # Mostrar vinculaciones si están disponibles
                if 'dolibarr_links' in st.session_state and st.session_state.dolibarr_links:
                    for vinculacion in st.session_state.dolibarr_links:
                        with st.container():
                            st.markdown(f"**Proyecto:** {vinculacion['project_ref']} - {vinculacion['project_title']}")
                            st.markdown(f"**Tablero:** {vinculacion['board_name']} (ID: {vinculacion['board_id']})")
                            
                            # Botón para desvincular
                            if st.button("Desvincular", key=f"unlink_{vinculacion['project_id']}"):
                                if dolibarr_client.unlink_project_board(vinculacion['project_id']):
                                    st.success("Proyecto desvinculado correctamente")
                                    # Actualizar lista de vinculaciones
                                    if 'dolibarr_links' in st.session_state:
                                        del st.session_state.dolibarr_links
                                    st.rerun()
                                else:
                                    st.error("Error al desvincular el proyecto")
                            
                            st.divider()
                
                # Sección para crear nueva vinculación
                st.subheader("Vincular proyecto con tablero")
                
                # Seleccionar proyecto
                proyectos_opciones = []
                if 'dolibarr_projects' in st.session_state and st.session_state.dolibarr_projects:
                    proyectos_opciones = [(p['id'], f"{p['ref']} - {p['title']}") for p in st.session_state.dolibarr_projects]
                
                proyecto_seleccionado = None
                if proyectos_opciones:
                    proyecto_id, proyecto_nombre = st.selectbox(
                        "Seleccionar proyecto",
                        options=proyectos_opciones,
                        format_func=lambda x: x[1]
                    )
                    proyecto_seleccionado = proyecto_id
                else:
                    st.info("Primero debes obtener la lista de proyectos.")
                
                # Seleccionar tablero
                tableros = []
                if st.session_state.trello_data is not None:
                    # Obtener tableros únicos
                    tableros_df = st.session_state.trello_data[["tablero_id", "tablero_nombre"]].drop_duplicates()
                    tableros = [(row["tablero_id"], row["tablero_nombre"]) for _, row in tableros_df.iterrows()]
                
                tablero_seleccionado = None
                if tableros:
                    tablero_id, tablero_nombre = st.selectbox(
                        "Seleccionar tablero",
                        options=tableros,
                        format_func=lambda x: x[1]
                    )
                    tablero_seleccionado = tablero_id
                else:
                    st.info("No hay tableros disponibles. Procesa archivos JSON de Trello primero.")
                
                # Botón para vincular
                if proyecto_seleccionado and tablero_seleccionado:
                    if st.button("Vincular Proyecto y Tablero"):
                        # Encontrar el nombre del tablero
                        tablero_nombre = next((nombre for id, nombre in tableros if id == tablero_seleccionado), "")
                        
                        with st.spinner("Vinculando proyecto y tablero..."):
                            if dolibarr_client.link_project_board(proyecto_seleccionado, tablero_seleccionado, tablero_nombre):
                                st.success("Proyecto y tablero vinculados correctamente")
                                # Actualizar lista de vinculaciones
                                if 'dolibarr_links' in st.session_state:
                                    del st.session_state.dolibarr_links
                                st.rerun()
                            else:
                                st.error("Error al vincular el proyecto y el tablero")
            
            # Pestaña de Sincronización
            with op_tab3:
                st.subheader("Sincronización de Tareas")
                
                # Verificar si hay vinculaciones
                if 'dolibarr_links' not in st.session_state or not st.session_state.dolibarr_links:
                    st.warning("No hay vinculaciones de proyectos y tableros. Configura las vinculaciones primero.")
                else:
                    # Seleccionar vinculación para sincronizar
                    vinculaciones_opciones = [(v['project_id'], v['board_id'], f"{v['project_ref']} - {v['board_name']}") for v in st.session_state.dolibarr_links]
                    
                    if vinculaciones_opciones:
                        proyecto_id, tablero_id, nombre_display = st.selectbox(
                            "Seleccionar vinculación para sincronizar",
                            options=vinculaciones_opciones,
                            format_func=lambda x: x[2]
                        )
                        
                        # Filtrar tareas del tablero seleccionado
                        if st.session_state.trello_data is not None:
                            tareas_tablero = st.session_state.trello_data[st.session_state.trello_data["tablero_id"] == tablero_id]
                            
                            if not tareas_tablero.empty:
                                st.write(f"Se encontraron {len(tareas_tablero)} tareas para sincronizar.")
                                
                                # Botón para sincronizar
                                if st.button("Sincronizar Tareas"):
                                    with st.spinner("Sincronizando tareas con Dolibarr..."):
                                        # Convertir tareas a formato para la API
                                        tareas_para_api = tareas_tablero.to_dict('records')
                                        
                                        if dolibarr_client.sync_tasks(proyecto_id, tablero_id, tareas_para_api):
                                            st.success("Tareas sincronizadas correctamente con Dolibarr")
                                        else:
                                            st.error("Error al sincronizar las tareas con Dolibarr")
                            else:
                                st.warning(f"No hay tareas disponibles para el tablero seleccionado.")
                    else:
                        st.info("No hay vinculaciones disponibles. Configura al menos una vinculación primero.")
else:
    # No hay datos cargados - Mostrar instrucciones
    st.subheader("Bienvenido al Gestor de Tareas Trello")
    
    # Crear pestañas para diferentes secciones de ayuda
    help_tab1, help_tab2, help_tab3 = st.tabs(["Cómo empezar", "Funcionalidades", "Preguntas frecuentes"])
    
    with help_tab1:
        st.header("Cómo empezar")
        
        st.write("""
        Para comenzar a usar el Gestor de Tareas Trello, seguí estos pasos:
        
        1. **Exportá tus tableros de Trello** en formato JSON (ver instrucciones más abajo)
        2. **Subí los archivos JSON** usando el panel lateral
        3. **Procesá las tareas** haciendo clic en el botón "Procesar Tareas (JSON)"
        4. **Explorá y analizá** tus tareas usando las diferentes pestañas
        """)
        
        st.subheader("Sugerencias para mejorar los resultados")
        
        st.write("""
        Para obtener mejores resultados de análisis y automatización:
        
        1. **Usa etiquetas consistentes**: Etiquetar tus tareas de manera uniforme ayuda a la categorización
        2. **Agrega fechas de vencimiento**: Para un mejor análisis temporal y priorización
        3. **Detalla las tareas**: Cuanto más detallada sea la descripción, mejor podrá el sistema identificar oportunidades.
        """)
        
        # Instrucciones para exportar tableros de Trello
        with st.expander("Cómo exportar tableros de Trello", expanded=True):
            st.write("""
            ### Cómo exportar tus tableros de Trello en formato JSON
            
            1. Iniciá sesión en tu cuenta de Trello
            2. Abrí el tablero que querés exportar
            3. Hacé clic en el botón "Mostrar menú" (arriba a la derecha)
            4. Seleccioná "Más" y luego "Imprimir y exportar"
            5. Elegí la opción "Exportar como JSON"
            6. Guardá el archivo en tu computadora
            7. Repetí estos pasos para cada tablero que quieras analizar
            """)
            
            st.info("Consejo: Para una mejor experiencia, exportá varios tableros relacionados para obtener una vista completa de tus proyectos.")
    
    with help_tab2:
        st.header("Funcionalidades principales")
        
        st.write("""
        El Gestor de Tareas Trello ofrece las siguientes funcionalidades:
        
        ### Panel de Tareas
        - Visualización completa de todas tus tareas
        - Filtros por prioridad, categoría, lista y texto
        - Subí los archivos JSON exportados de Trello usando el panel lateral
        - Podés exportar estos archivos desde tu tablero de Trello
        
        ### Vista de Flujo
        - Configuración personalizada de las etapas de tu flujo de trabajo
        - Mapeo de listas de Trello a etapas de flujo
        - Visualización de la distribución de tareas por etapa
        - Exportá tus tareas organizadas
        
        ### Análisis
        - Gráficos de distribución por prioridad y categoría
        - Análisis temporal de fechas de vencimiento
        - Identificación de tareas vencidas y próximas a vencer
        - Análisis de productividad
        
        ### Automatización
        - Detección automática de tareas automatizables
        - Generación de planes de automatización
        - Generación de scripts para tareas repetitivas
        - Estadísticas de oportunidades de automatización
        
        ### Integración con Dolibarr
        - Conexión con tu ERP Dolibarr
        - Vinculación de tableros Trello con proyectos Dolibarr
        - Sincronización de tareas entre ambas plataformas
        """)
    
    with help_tab3:
        st.header("Preguntas frecuentes")
        
        with st.expander("¿Cómo se procesan mis datos?"):
            st.write("""
            Tus datos se procesan localmente y se almacenan en una base de datos PostgreSQL segura.
            No se comparten con terceros ni se utilizan para otros fines más allá de los análisis
            y funcionalidades proporcionadas por esta aplicación.
            """)
        
        with st.expander("¿Cómo puedo conectar con Dolibarr?"):
            st.write("""
            Para conectar con Dolibarr:
            
            1. Asegúrate de tener instalado el módulo TrelloGestiona en tu Dolibarr
            2. Genera un token de API en la configuración del módulo
            3. En la pestaña "Integración Dolibarr", introduce la URL y el token
            4. Haz clic en "Guardar configuración"
            
            Una vez conectado, podrás vincular tus tableros de Trello con proyectos de Dolibarr
            y sincronizar las tareas entre ambas plataformas.
            """)
        
        with st.expander("¿Cómo funcionan las recomendaciones de automatización?"):
            st.write("""
            El sistema analiza las descripciones, etiquetas y patrones de tus tareas para identificar
            oportunidades de automatización. Los tipos de tareas que pueden automatizarse incluyen:
            
            1. **Tareas repetitivas**: Acciones que siguen un patrón regular
            2. **Scripts de reportes**: Generación automática de informes y estadísticas
            3. **Notificaciones**: Envío programado de actualizaciones y recordatorios
            
            La detección se basa en palabras clave y patrones comunes en las descripciones.
            Cuanto más detalladas sean tus descripciones, mejores serán las recomendaciones.
            """)
        
        with st.expander("¿Cómo puedo mejorar la categorización de mis tareas?"):
            st.write("""
            Para mejorar la categorización automática:
            
            1. Usa etiquetas consistentes en Trello
            2. Incluye palabras clave relevantes en los títulos y descripciones
            3. Agrupa tareas similares en las mismas listas
            4. Establece un sistema de priorización usando etiquetas de colores en Trello
            
            El sistema aprenderá de tus patrones para mejorar la categorización con el tiempo.
            """)