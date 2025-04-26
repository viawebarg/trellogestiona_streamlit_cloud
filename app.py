import os
import streamlit as st
import pandas as pd
import plotly.express as px
import json
import shutil
from cargar_datos import procesar_todos_los_json, convertir_a_dataframe, priorizar_tareas, categorizar_tareas
from gestor_flujo_trabajo import crear_flujo_trabajo, mapear_listas_trello_a_flujo_trabajo
import db_manager

# Configuración de la página
st.set_page_config(
    page_title="Gestor de Tareas Trello",
    page_icon="📋",
    layout="wide",
    initial_sidebar_state="expanded"
)

# Inicializar la base de datos
db_status = db_manager.inicializar_db()

# Inicializa el estado de la sesión para almacenar datos
if 'trello_data' not in st.session_state:
    st.session_state.trello_data = None
if 'filtered_data' not in st.session_state:
    st.session_state.filtered_data = None
if 'workflow_stages' not in st.session_state:
    # Obtener etapas del flujo de trabajo desde la base de datos
    etapas_flujo = db_manager.obtener_configuracion_flujo_trabajo()
    st.session_state.workflow_stages = etapas_flujo
if 'all_lists' not in st.session_state:
    st.session_state.all_lists = []
if 'list_workflow_mapping' not in st.session_state:
    st.session_state.list_workflow_mapping = {}
if 'files_processed' not in st.session_state:
    st.session_state.files_processed = False
if 'db_initialized' not in st.session_state:
    st.session_state.db_initialized = True
    st.success("Base de datos inicializada correctamente.")

# Título y descripción
st.title("Gestor de Tareas Trello")
st.write("Procesá, organizá y gestioná tus tareas de Trello de manera eficiente con este sistema de flujo de trabajo simplificado.")

# Barra lateral para configuración y carga de datos
with st.sidebar:
    st.header("Configuración")
    
    # Asegurarse de que existe la carpeta 'datos'
    if not os.path.exists('datos'):
        os.makedirs('datos')
    
    # Sección para cargar archivos JSON
    st.subheader("Cargar archivos JSON de Trello")
    
    uploaded_files = st.file_uploader("Subí los archivos JSON exportados de Trello", 
                                     type=['json'], 
                                     accept_multiple_files=True)
    
    if uploaded_files:
        # Guardar los archivos cargados en la carpeta datos
        for uploaded_file in uploaded_files:
            file_path = os.path.join('datos', uploaded_file.name)
            with open(file_path, 'wb') as f:
                f.write(uploaded_file.getbuffer())
        
        st.success(f"Se subieron {len(uploaded_files)} archivos correctamente.")
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
    tab1, tab2, tab3 = st.tabs(["Panel de Tareas", "Vista de Flujo", "Análisis"])
    
    # Pestaña Panel de Tareas
    with tab1:
        st.header("Panel de Tareas")
        
        # Filtros
        col1, col2, col3 = st.columns(3)
        
        with col1:
            # Filtro por prioridad
            prioridades = ['Todas'] + sorted(st.session_state.trello_data['prioridad'].unique().tolist())
            filtro_prioridad = st.multiselect("Filtrar por Prioridad", prioridades, default='Todas')
        
        with col2:
            # Filtro por etiqueta/categoría
            todas_etiquetas = []
            for etiquetas in st.session_state.trello_data['etiquetas'].dropna():
                if isinstance(etiquetas, list):
                    todas_etiquetas.extend(etiquetas)
            etiquetas_unicas = ['Todas'] + sorted(list(set(todas_etiquetas)))
            filtro_etiqueta = st.multiselect("Filtrar por Etiqueta", etiquetas_unicas, default='Todas')
        
        with col3:
            # Buscar por nombre
            consulta_busqueda = st.text_input("Buscar Tareas", "")
        
        # Aplicar filtros
        filtered_df = st.session_state.trello_data.copy()
        
        # Filtro de prioridad
        if filtro_prioridad and 'Todas' not in filtro_prioridad:
            filtered_df = filtered_df[filtered_df['prioridad'].isin(filtro_prioridad)]
        
        # Filtro de etiqueta
        if filtro_etiqueta and 'Todas' not in filtro_etiqueta:
            filtered_df = filtered_df[filtered_df['etiquetas'].apply(
                lambda x: isinstance(x, list) and any(etiqueta in x for etiqueta in filtro_etiqueta)
            )]
        
        # Filtro de búsqueda
        if consulta_busqueda:
            filtered_df = filtered_df[filtered_df['nombre'].str.contains(consulta_busqueda, case=False)]
        
        # Actualizar datos filtrados en el estado de la sesión
        st.session_state.filtered_data = filtered_df
        
        # Mostrar tareas filtradas
        if not filtered_df.empty:
            st.write(f"Mostrando {len(filtered_df)} tareas")
            st.dataframe(filtered_df[['nombre', 'nombre_lista', 'prioridad', 'etiquetas', 'fecha_vencimiento', 'url']], 
                         height=400,
                         column_config={
                             "nombre": "Nombre de la Tarea",
                             "nombre_lista": "Lista",
                             "prioridad": "Prioridad",
                             "etiquetas": "Etiquetas",
                             "fecha_vencimiento": "Fecha de Vencimiento",
                             "url": st.column_config.LinkColumn("Link a Trello")
                         })
        else:
            st.warning("No hay tareas que coincidan con los filtros actuales.")
    
    # Pestaña Vista de Flujo
    with tab2:
        st.header("Gestión del Flujo de Trabajo")
        
        # Crear columnas para cada etapa del flujo de trabajo
        columns = st.columns(len(st.session_state.workflow_stages))
        
        # Mostrar tareas en cada columna según su etapa
        for i, etapa in enumerate(st.session_state.workflow_stages):
            with columns[i]:
                st.subheader(etapa)
                
                # Aplicar mapeo de listas Trello a etapas del flujo de trabajo
                if st.session_state.list_workflow_mapping:
                    # Encontrar las listas de Trello que mapean a esta etapa del flujo
                    listas_mapeadas = [lista for lista, etapa_mapeada in 
                                      st.session_state.list_workflow_mapping.items() 
                                      if etapa_mapeada == etapa]
                    
                    # Filtrar tareas que están en cualquiera de las listas mapeadas
                    tareas_etapa = filtered_df[filtered_df['nombre_lista'].isin(listas_mapeadas)]
                else:
                    # Fallback directo si no hay mapeo configurado
                    tareas_etapa = filtered_df[filtered_df['nombre_lista'] == etapa]
                
                if not tareas_etapa.empty:
                    for _, tarea in tareas_etapa.iterrows():
                        with st.container():
                            st.markdown(f"**{tarea['nombre']}**")
                            
                            # Mostrar etiquetas si están disponibles
                            if isinstance(tarea['etiquetas'], list) and tarea['etiquetas']:
                                st.markdown(f"Etiquetas: {', '.join(tarea['etiquetas'])}")
                            
                            # Mostrar tablero si está disponible
                            if 'tablero' in tarea and tarea['tablero']:
                                st.markdown(f"Tablero: {tarea['tablero']}")
                            
                            # Mostrar fecha de vencimiento si está disponible
                            if pd.notna(tarea['fecha_vencimiento']):
                                st.markdown(f"Vence: {pd.to_datetime(tarea['fecha_vencimiento']).strftime('%d/%m/%Y')}")
                            
                            # Mostrar prioridad
                            prioridad_color = {
                                'Crítica': 'red',
                                'Alta': 'orange',
                                'Media': 'blue',
                                'Baja': 'green'
                            }
                            
                            color = prioridad_color.get(tarea['prioridad'], 'gray')
                            st.markdown(f"Prioridad: <span style='color:{color};font-weight:bold'>{tarea['prioridad']}</span>", unsafe_allow_html=True)
                            
                            # Botones para mover tareas entre etapas (solo visual, no API)
                            cols = st.columns(2)
                            
                            # Solo mostrar botón de mover a la izquierda si no es la primera etapa
                            if i > 0:
                                if cols[0].button(f"← Mover", key=f"left_{tarea['id']}"):
                                    prev_etapa = st.session_state.workflow_stages[i-1]
                                    st.info(f"En la versión completa: Tarea movida a {prev_etapa}")
                            
                            # Solo mostrar botón de mover a la derecha si no es la última etapa
                            if i < len(st.session_state.workflow_stages) - 1:
                                if cols[1].button(f"Mover →", key=f"right_{tarea['id']}"):
                                    next_etapa = st.session_state.workflow_stages[i+1]
                                    st.info(f"En la versión completa: Tarea movida a {next_etapa}")
                            
                            st.markdown("---")
                else:
                    st.caption("No hay tareas en esta etapa")
    
    # Pestaña de Análisis
    with tab3:
        st.header("Análisis de Tareas")
        
        if not filtered_df.empty:
            col1, col2 = st.columns(2)
            
            with col1:
                # Tareas por prioridad
                conteo_prioridad = filtered_df['prioridad'].value_counts().reset_index()
                conteo_prioridad.columns = ['Prioridad', 'Cantidad']
                
                fig1 = px.pie(conteo_prioridad, values='Cantidad', names='Prioridad', 
                              title='Tareas por Prioridad',
                              color_discrete_sequence=px.colors.qualitative.Set3)
                st.plotly_chart(fig1)
            
            with col2:
                # Tareas por estado/lista
                conteo_estado = filtered_df['nombre_lista'].value_counts().reset_index()
                conteo_estado.columns = ['Estado', 'Cantidad']
                
                fig2 = px.bar(conteo_estado, x='Estado', y='Cantidad',
                              title='Tareas por Estado',
                              color='Estado',
                              color_discrete_sequence=px.colors.qualitative.Pastel)
                st.plotly_chart(fig2)
            
            # Tareas por categoría
            if 'categoria' in filtered_df.columns:
                conteo_categoria = filtered_df['categoria'].value_counts().reset_index()
                conteo_categoria.columns = ['Categoría', 'Cantidad']
                
                fig3 = px.bar(conteo_categoria, x='Categoría', y='Cantidad',
                              title='Tareas por Categoría',
                              color='Categoría',
                              color_discrete_sequence=px.colors.qualitative.Bold)
                st.plotly_chart(fig3, use_container_width=True)
            
            # Tareas por tablero
            if 'tablero' in filtered_df.columns:
                conteo_tablero = filtered_df['tablero'].value_counts().reset_index()
                conteo_tablero.columns = ['Tablero', 'Cantidad']
                
                fig4 = px.pie(conteo_tablero, values='Cantidad', names='Tablero', 
                              title='Distribución de Tareas por Tablero',
                              color_discrete_sequence=px.colors.qualitative.Vivid)
                st.plotly_chart(fig4, use_container_width=True)
            
            # Tareas por fecha de vencimiento (si está disponible)
            if 'fecha_vencimiento' in filtered_df.columns and filtered_df['fecha_vencimiento'].notna().any():
                # Filtrar filas con fechas de vencimiento NaN
                df_fechas_vencimiento = filtered_df.dropna(subset=['fecha_vencimiento'])
                
                if not df_fechas_vencimiento.empty:
                    # Convertir a datetime si aún no lo es
                    df_fechas_vencimiento['fecha_vencimiento'] = pd.to_datetime(df_fechas_vencimiento['fecha_vencimiento'])
                    
                    # Ordenar por fecha de vencimiento
                    df_fechas_vencimiento = df_fechas_vencimiento.sort_values('fecha_vencimiento')
                    
                    fig5 = px.timeline(df_fechas_vencimiento, x_start='fecha_vencimiento', y='nombre',
                                      color='prioridad', title='Línea de Tiempo de Tareas por Fecha de Vencimiento',
                                      color_discrete_sequence=px.colors.qualitative.Pastel)
                    
                    # Personalizar diseño
                    fig5.update_yaxes(autorange="reversed")
                    fig5.update_layout(height=400)
                    
                    st.plotly_chart(fig5, use_container_width=True)
                else:
                    st.info("No hay tareas con fechas de vencimiento disponibles para visualización de línea de tiempo.")
            else:
                st.info("No hay fechas de vencimiento disponibles para visualización de línea de tiempo.")
        else:
            st.warning("No hay datos disponibles para análisis.")

else:
    # Verificar si hay datos en la base de datos
    try:
        tareas_db = db_manager.cargar_tareas()
        if not tareas_db.empty:
            # Mostrar mensaje de que hay datos en la base de datos
            st.info("¡Hay datos almacenados en la base de datos!")
            
            # Botón para cargar datos desde la base de datos
            if st.button("Cargar datos desde Base de Datos"):
                with st.spinner("Cargando datos desde la base de datos..."):
                    st.session_state.trello_data = tareas_db
                    st.session_state.filtered_data = tareas_db.copy()
                    st.success(f"Se cargaron {len(tareas_db)} tareas desde la base de datos.")
                    st.rerun()
        else:
            # Mostrar instrucciones cuando no hay datos cargados
            st.info("¡Bienvenido al Gestor de Tareas Trello! Seguí estos pasos para comenzar:")
            
            col1, col2, col3 = st.columns(3)
            
            with col1:
                st.markdown("### 1. Cargar archivos JSON")
                st.markdown("""
                - Subí los archivos JSON exportados de Trello usando el panel lateral
                - Podés exportar estos archivos desde tu tablero de Trello
                """)
            
            with col2:
                st.markdown("### 2. Procesar los datos")
                st.markdown("""
                - Hacé clic en "Procesar Tareas (JSON)" en el panel lateral
                - El sistema organizará automáticamente tus tareas
                - Luego guardá los datos en la base de datos para acceso permanente
                """)
            
            with col3:
                st.markdown("### 3. Gestionar tareas")
                st.markdown("""
                - Filtrá y ordená tus tareas
                - Visualizá el flujo de trabajo
                - Analizá la distribución de tareas
                - Exportá tus tareas organizadas
                """)
            
            st.markdown("---")
            st.markdown("""
            ### Cómo exportar tus tableros de Trello en formato JSON
            
            1. Iniciá sesión en tu cuenta de Trello
            2. Abrí el tablero que querés exportar
            3. Hacé clic en "Mostrar menú" (arriba a la derecha)
            4. Seleccioná "Más" y luego "Imprimir y exportar"
            5. Elegí la opción "Exportar como JSON"
            6. Guardá el archivo y luego subilo aquí usando el panel lateral
            """)
    except Exception as e:
        # Mostrar mensaje de error
        st.error(f"Error al verificar datos en la base de datos: {str(e)}")
        
        # Mostrar instrucciones cuando no hay datos cargados
        st.info("¡Bienvenido al Gestor de Tareas Trello! Seguí estos pasos para comenzar:")
        
        col1, col2, col3 = st.columns(3)
        
        with col1:
            st.markdown("### 1. Cargar archivos JSON")
            st.markdown("""
            - Subí los archivos JSON exportados de Trello usando el panel lateral
            - Podés exportar estos archivos desde tu tablero de Trello
            """)
        
        with col2:
            st.markdown("### 2. Procesar los datos")
            st.markdown("""
            - Hacé clic en "Procesar Tareas (JSON)" en el panel lateral
            - El sistema organizará automáticamente tus tareas
            """)
        
        with col3:
            st.markdown("### 3. Gestionar tareas")
            st.markdown("""
            - Filtrá y ordená tus tareas
            - Visualizá el flujo de trabajo
            - Analizá la distribución de tareas
            - Exportá tus tareas organizadas
            """)
