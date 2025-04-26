import os
import streamlit as st
import pandas as pd
import plotly.express as px
import json
import shutil
import numpy as np
from cargar_datos import procesar_todos_los_json, convertir_a_dataframe, priorizar_tareas, categorizar_tareas
from gestor_flujo_trabajo import crear_flujo_trabajo, mapear_listas_trello_a_flujo_trabajo
import db_manager
import automatizacion_tareas

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
    tab1, tab2, tab3, tab4 = st.tabs(["Panel de Tareas", "Vista de Flujo", "Análisis", "Automatización"])
    
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
        
        # Sección de configuración del flujo de trabajo
        with st.expander("Configuración del Flujo de Trabajo"):
            # Mostrar el flujo de trabajo actual
            st.subheader("Flujo de Trabajo Actual")
            st.write(f"Etapas actuales: {', '.join(st.session_state.workflow_stages)}")
            
            # Opción para personalizar el flujo de trabajo
            st.subheader("Personalizar Flujo de Trabajo")
            etapas_flujo_texto = st.text_area(
                "Ingresá las etapas del flujo de trabajo separadas por coma",
                ", ".join(st.session_state.workflow_stages)
            )
            
            nombre_flujo = st.text_input("Nombre para este flujo de trabajo", "Mi Flujo Personalizado")
            hacer_default = st.checkbox("Establecer como flujo predeterminado")
            
            if st.button("Guardar Configuración"):
                # Procesar las etapas ingresadas
                etapas_nuevas = [etapa.strip() for etapa in etapas_flujo_texto.split(",") if etapa.strip()]
                
                if etapas_nuevas:
                    # Guardar en la base de datos
                    db_manager.guardar_configuracion_flujo_trabajo(nombre_flujo, etapas_nuevas, hacer_default)
                    
                    # Actualizar el estado de la sesión
                    st.session_state.workflow_stages = etapas_nuevas
                    
                    # Actualizar el mapeo de listas si hay datos
                    if st.session_state.all_lists:
                        st.session_state.list_workflow_mapping = mapear_listas_trello_a_flujo_trabajo(
                            st.session_state.all_lists, 
                            etapas_nuevas
                        )
                    
                    st.success(f"Flujo de trabajo '{nombre_flujo}' guardado correctamente con {len(etapas_nuevas)} etapas.")
                    st.rerun()
                else:
                    st.error("Por favor, ingresá al menos una etapa para el flujo de trabajo.")
        
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
                                fecha_str = pd.to_datetime(tarea['fecha_vencimiento']).strftime('%d/%m/%Y')
                                st.markdown(f"Vence: {fecha_str}")
                            
                            # Mostrar prioridad
                            prioridad_color = {
                                'Crítica': 'red',
                                'Alta': 'orange',
                                'Media': 'blue',
                                'Baja': 'green'
                            }
                            
                            color = prioridad_color.get(tarea['prioridad'], 'gray')
                            st.markdown(f"Prioridad: <span style='color:{color};font-weight:bold'>{tarea['prioridad']}</span>", unsafe_allow_html=True)
                            
                            # Botones para mover tareas entre etapas
                            cols = st.columns(2)
                            
                            # Solo mostrar botón de mover a la izquierda si no es la primera etapa
                            if i > 0:
                                if cols[0].button(f"← Mover", key=f"left_{tarea['id']}"):
                                    # Implementación real para mover la tarea a una lista diferente
                                    prev_etapa = st.session_state.workflow_stages[i-1]
                                    
                                    # Buscar una lista que corresponda a la etapa anterior
                                    listas_etapa_anterior = [lista for lista, etapa_mapeada in 
                                                            st.session_state.list_workflow_mapping.items() 
                                                            if etapa_mapeada == prev_etapa]
                                    
                                    if listas_etapa_anterior:
                                        # Tomar la primera lista que corresponda a esa etapa
                                        lista_destino = listas_etapa_anterior[0]
                                        
                                        # Encontrar el ID de la lista
                                        lista_id = None
                                        if st.session_state.trello_data is not None:
                                            listas_df = st.session_state.trello_data[
                                                st.session_state.trello_data['nombre_lista'] == lista_destino
                                            ]
                                            if not listas_df.empty:
                                                for _, tarea_info in listas_df.iterrows():
                                                    if 'lista_id' in tarea_info:
                                                        lista_id = tarea_info['lista_id']
                                                        break
                                        
                                        if lista_id:
                                            # Actualizar la posición de la tarea en la base de datos
                                            exito, _ = db_manager.actualizar_posicion_tarea(tarea['id'], lista_id)
                                            if exito:
                                                st.success(f"Tarea movida a {prev_etapa}")
                                                st.rerun()
                                            else:
                                                st.error("No se pudo mover la tarea. Intenta de nuevo.")
                                        else:
                                            st.warning(f"No se encontró el ID de la lista '{lista_destino}'")
                                    else:
                                        st.warning(f"No hay listas mapeadas a la etapa '{prev_etapa}'")
                            
                            # Solo mostrar botón de mover a la derecha si no es la última etapa
                            if i < len(st.session_state.workflow_stages) - 1:
                                if cols[1].button(f"Mover →", key=f"right_{tarea['id']}"):
                                    # Implementación real para mover la tarea a una lista diferente
                                    next_etapa = st.session_state.workflow_stages[i+1]
                                    
                                    # Buscar una lista que corresponda a la etapa siguiente
                                    listas_etapa_siguiente = [lista for lista, etapa_mapeada in 
                                                            st.session_state.list_workflow_mapping.items() 
                                                            if etapa_mapeada == next_etapa]
                                    
                                    if listas_etapa_siguiente:
                                        # Tomar la primera lista que corresponda a esa etapa
                                        lista_destino = listas_etapa_siguiente[0]
                                        
                                        # Encontrar el ID de la lista
                                        lista_id = None
                                        if st.session_state.trello_data is not None:
                                            listas_df = st.session_state.trello_data[
                                                st.session_state.trello_data['nombre_lista'] == lista_destino
                                            ]
                                            if not listas_df.empty:
                                                for _, tarea_info in listas_df.iterrows():
                                                    if 'lista_id' in tarea_info:
                                                        lista_id = tarea_info['lista_id']
                                                        break
                                        
                                        if lista_id:
                                            # Actualizar la posición de la tarea en la base de datos
                                            exito, _ = db_manager.actualizar_posicion_tarea(tarea['id'], lista_id)
                                            if exito:
                                                st.success(f"Tarea movida a {next_etapa}")
                                                st.rerun()
                                            else:
                                                st.error("No se pudo mover la tarea. Intenta de nuevo.")
                                        else:
                                            st.warning(f"No se encontró el ID de la lista '{lista_destino}'")
                                    else:
                                        st.warning(f"No hay listas mapeadas a la etapa '{next_etapa}'")
                            
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
                    
                    # Crear una fecha de fin (requerida para la gráfica de línea de tiempo)
                    # Añadiremos 1 día a la fecha de vencimiento para visualizar mejor
                    df_fechas_vencimiento['fecha_fin'] = df_fechas_vencimiento['fecha_vencimiento'] + pd.Timedelta(days=1)
                    
                    # Ordenar por fecha de vencimiento
                    df_fechas_vencimiento = df_fechas_vencimiento.sort_values('fecha_vencimiento')
                    
                    # Asegurarse de que el nombre sea único para la visualización
                    df_fechas_vencimiento['nombre_unico'] = df_fechas_vencimiento['nombre'] + ' (' + df_fechas_vencimiento.index.astype(str) + ')'
                    
                    # Usar gráfico de barras horizontales en lugar de timeline
                    fig5 = px.bar(df_fechas_vencimiento, 
                                  x='fecha_vencimiento', 
                                  y='nombre',
                                  color='prioridad', 
                                  title='Tareas por Fecha de Vencimiento',
                                  color_discrete_sequence=px.colors.qualitative.Pastel,
                                  orientation='h')
                    
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
            
    # Pestaña de Automatización
    with tab4:
        st.header("Automatización de Tareas")
        
        # Introducción a la automatización
        st.markdown("""
        Esta sección te permite identificar y automatizar tareas repetitivas o rutinarias.
        El sistema analiza tus tareas y sugiere cuáles pueden ser automatizadas según su descripción y características.
        """)
        
        if not filtered_df.empty:
            # Aplicar análisis de automatización
            with st.spinner("Analizando tareas para automatización..."):
                # Obtener el dataframe con análisis de automatización
                df_auto = automatizacion_tareas.analizar_automatizacion(filtered_df)
                
                # Actualizar el dataframe filtrado
                st.session_state.filtered_data = df_auto
                
                # Obtener estadísticas de automatización
                estadisticas = automatizacion_tareas.obtener_estadisticas_automatizacion(df_auto)
                
            # Mostrar resumen de estadísticas
            col1, col2, col3 = st.columns(3)
            
            with col1:
                st.metric(
                    label="Tareas Automatizables",
                    value=f"{estadisticas['tareas_automatizables']}/{estadisticas['total_tareas']}",
                    delta=f"{estadisticas['porcentaje_automatizable']:.1f}%"
                )
                
            with col2:
                st.metric(
                    label="Potencial Promedio de Automatización",
                    value=f"{estadisticas['promedio_porcentaje_automatizacion']:.1f}%"
                )
                
            with col3:
                # Mostrar distribución de tipos de automatización
                tipos_auto = estadisticas.get('distribucion_tipos', {})
                if tipos_auto:
                    tipo_principal = max(tipos_auto.items(), key=lambda x: x[1])[0]
                    st.metric(
                        label="Tipo Principal de Automatización",
                        value=tipo_principal
                    )
                else:
                    st.metric(
                        label="Tipo Principal de Automatización",
                        value="No identificado"
                    )
                    
            # Mostrar gráficos de automatización
            if estadisticas['tareas_automatizables'] > 0:
                st.subheader("Distribución de Automatizaciones")
                
                col1, col2 = st.columns(2)
                
                with col1:
                    # Gráfico de distribución por tipo
                    if estadisticas.get('distribucion_tipos'):
                        df_tipos = pd.DataFrame({
                            'Tipo': list(estadisticas['distribucion_tipos'].keys()),
                            'Cantidad': list(estadisticas['distribucion_tipos'].values())
                        })
                        
                        fig_tipos = px.pie(
                            df_tipos, 
                            values='Cantidad', 
                            names='Tipo',
                            title='Tipos de Automatización',
                            color_discrete_sequence=px.colors.qualitative.Bold
                        )
                        
                        st.plotly_chart(fig_tipos, use_container_width=True)
                
                with col2:
                    # Gráfico de potencial por categoría
                    if estadisticas.get('potencial_por_categoria'):
                        df_categorias = pd.DataFrame({
                            'Categoría': list(estadisticas['potencial_por_categoria'].keys()),
                            'Potencial (%)': list(estadisticas['potencial_por_categoria'].values())
                        })
                        
                        # Ordenar de mayor a menor
                        df_categorias = df_categorias.sort_values('Potencial (%)', ascending=False)
                        
                        fig_cat = px.bar(
                            df_categorias,
                            x='Categoría',
                            y='Potencial (%)',
                            title='Potencial de Automatización por Categoría',
                            color='Potencial (%)',
                            color_continuous_scale=px.colors.sequential.Viridis
                        )
                        
                        st.plotly_chart(fig_cat, use_container_width=True)
                        
            # Mostrar tareas automatizables
            st.subheader("Tareas Automatizables")
            
            # Filtrar solo tareas automatizables
            tareas_automatizables = df_auto[df_auto['automatizable'] == True].sort_values(
                by='porcentaje_automatizacion', ascending=False
            )
            
            if not tareas_automatizables.empty:
                # Mostrar tabla de tareas automatizables
                st.dataframe(
                    tareas_automatizables[['nombre', 'tipo_automatizacion', 'porcentaje_automatizacion', 'accion_recomendada']],
                    column_config={
                        "nombre": "Nombre de la Tarea",
                        "tipo_automatizacion": "Tipo de Automatización",
                        "porcentaje_automatizacion": st.column_config.ProgressColumn(
                            "Potencial de Automatización",
                            format="%d%%",
                            min_value=0,
                            max_value=100,
                        ),
                        "accion_recomendada": "Acción Recomendada"
                    },
                    height=300
                )
                
                # Seleccionar una tarea para ver detalles de automatización
                tareas_seleccionables = tareas_automatizables['nombre'].tolist()
                
                tarea_seleccionada = st.selectbox(
                    "Selecciona una tarea para ver detalles de automatización:",
                    options=tareas_seleccionables
                )
                
                if tarea_seleccionada:
                    # Encontrar el ID de la tarea seleccionada
                    tarea_id = tareas_automatizables[tareas_automatizables['nombre'] == tarea_seleccionada]['id'].iloc[0]
                    
                    # Mostrar detalles en un expander
                    with st.expander("Plan de Automatización", expanded=True):
                        # Generar plan detallado
                        plan = automatizacion_tareas.generar_plan_automatizacion(tarea_id)
                        
                        if plan.get("error"):
                            st.error(plan["error"])
                        else:
                            st.markdown(f"### Plan para automatizar: {tarea_seleccionada}")
                            st.markdown(f"**Tipo de automatización**: {plan['tipo']}")
                            st.markdown(f"**Potencial de automatización**: {plan['score']:.1f}%")
                            st.markdown(f"**Acción recomendada**: {plan['accion_recomendada']}")
                            
                            # Pasos de automatización
                            st.markdown("#### Pasos para automatizar:")
                            for i, paso in enumerate(plan['pasos'], 1):
                                st.markdown(f"{i}. {paso}")
                            
                            # Herramientas sugeridas
                            st.markdown("#### Herramientas sugeridas:")
                            for herramienta in plan['herramientas_sugeridas']:
                                st.markdown(f"- {herramienta}")
                            
                            st.markdown(f"**Tiempo estimado**: {plan['tiempo_estimado']}")
                            
                            # Botón para simular la automatización
                            if st.button("Simular Automatización", key=f"simular_{tarea_id}"):
                                with st.spinner("Realizando automatización simulada..."):
                                    # Simulación
                                    resultado = automatizacion_tareas.ejecutar_automatizacion_simulada(tarea_id)
                                    
                                    if resultado.get("error"):
                                        st.error(resultado["error"])
                                    else:
                                        st.success(f"Automatización simulada exitosamente en {resultado['tiempo_ejecucion']}")
                                        st.json(resultado)
            else:
                st.info("No se encontraron tareas automatizables según los criterios de análisis.")
                
                # Mostrar sugerencias
                with st.expander("Consejos para identificar tareas automatizables"):
                    st.markdown("""
                    ### Consejos para identificar tareas automatizables:
                    
                    1. **Busca patrones repetitivos**: Tareas que sigues realizando de la misma manera.
                    2. **Identifica palabras clave**: Utiliza términos como "generar", "actualizar", "enviar", "sincronizar" en tus descripciones.
                    3. **Detalla las tareas**: Cuanto más detallada sea la descripción, mejor podrá el sistema identificar oportunidades.
                    4. **Establece categorías claras**: Categoriza tus tareas adecuadamente para facilitar el análisis.
                    5. **Considera la periodicidad**: Tareas que se realizan diaria, semanal o mensualmente son buenas candidatas.
                    """)
        else:
            st.warning("No hay datos disponibles para análisis de automatización.")

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
