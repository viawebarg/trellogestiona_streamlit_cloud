import json
import pandas as pd
from datetime import datetime
import os

def cargar_json_trello(ruta_archivo):
    """
    Carga un archivo JSON de Trello y extrae la información relevante.
    
    Args:
        ruta_archivo (str): Ruta al archivo JSON exportado de Trello
        
    Returns:
        dict: Datos procesados del tablero de Trello
    """
    try:
        with open(ruta_archivo, 'r', encoding='utf-8') as file:
            datos = json.load(file)
        return datos
    except Exception as e:
        print(f"Error al cargar el archivo JSON: {str(e)}")
        return None

def extraer_tarjetas_desde_json(datos_json):
    """
    Extrae las tarjetas (cards) desde los datos JSON de Trello.
    
    Args:
        datos_json (dict): Datos JSON del tablero de Trello
        
    Returns:
        list: Lista de tarjetas con la información procesada
    """
    tarjetas = []
    
    if not datos_json or 'cards' not in datos_json:
        return tarjetas
        
    # Crear un mapeo de listas para asociar tarjetas con sus listas
    listas = {}
    if 'lists' in datos_json:
        for lista in datos_json['lists']:
            listas[lista['id']] = lista['name']
    
    # Procesar cada tarjeta
    for tarjeta in datos_json['cards']:
        if tarjeta.get('closed', False):  # Ignorar tarjetas archivadas
            continue
            
        # Información básica
        tarjeta_procesada = {
            'id': tarjeta.get('id', ''),
            'nombre': tarjeta.get('name', 'Tarea sin título'),
            'descripcion': tarjeta.get('desc', ''),
            'url': tarjeta.get('url', ''),
        }
        
        # Obtener el nombre de la lista
        id_lista = tarjeta.get('idList', '')
        tarjeta_procesada['nombre_lista'] = listas.get(id_lista, 'Desconocido')
        
        # Procesar etiquetas
        if 'labels' in tarjeta and tarjeta['labels']:
            tarjeta_procesada['etiquetas'] = [etiqueta.get('name', '') for etiqueta in tarjeta['labels'] if etiqueta.get('name')]
        else:
            tarjeta_procesada['etiquetas'] = []
        
        # Procesar fecha de vencimiento
        if 'due' in tarjeta and tarjeta['due']:
            tarjeta_procesada['fecha_vencimiento'] = tarjeta['due']
        else:
            tarjeta_procesada['fecha_vencimiento'] = None
        
        # Procesar miembros
        if 'idMembers' in tarjeta and tarjeta['idMembers']:
            tarjeta_procesada['miembros'] = tarjeta['idMembers']
        else:
            tarjeta_procesada['miembros'] = []
        
        # Procesar checklists
        total_items = 0
        completed_items = 0
        
        if 'idChecklists' in tarjeta and tarjeta['idChecklists']:
            tarjeta_procesada['checklists'] = tarjeta['idChecklists']
            
            # Si tenemos acceso a los datos reales de los checklists
            if 'checklists' in datos_json:
                for checklist_id in tarjeta['idChecklists']:
                    for checklist in datos_json['checklists']:
                        if checklist['id'] == checklist_id:
                            if 'checkItems' in checklist:
                                total_items += len(checklist['checkItems'])
                                completed_items += sum(1 for item in checklist['checkItems'] 
                                                      if item.get('state') == 'complete')
        
        tarjeta_procesada['porcentaje_completado'] = (completed_items / total_items * 100) if total_items > 0 else 0
        
        # Añadir fecha de última actividad si está disponible
        if 'dateLastActivity' in tarjeta and tarjeta['dateLastActivity']:
            tarjeta_procesada['ultima_actividad'] = tarjeta['dateLastActivity']
        else:
            tarjeta_procesada['ultima_actividad'] = None
        
        tarjetas.append(tarjeta_procesada)
    
    return tarjetas

def procesar_todos_los_json():
    """
    Procesa todos los archivos JSON en la carpeta de datos
    
    Returns:
        list: Lista combinada de todas las tarjetas de todos los tableros
    """
    carpeta_datos = 'datos'
    todas_las_tarjetas = []
    
    # Asegurar que la carpeta existe
    if not os.path.exists(carpeta_datos):
        os.makedirs(carpeta_datos)
        print(f"Se creó la carpeta '{carpeta_datos}'. Por favor coloca tus archivos JSON de Trello ahí.")
        return []
    
    # Encontrar todos los archivos JSON en la carpeta
    archivos_json = [os.path.join(carpeta_datos, f) for f in os.listdir(carpeta_datos) if f.endswith('.json')]
    
    if not archivos_json:
        print(f"No se encontraron archivos JSON en la carpeta '{carpeta_datos}'.")
        return []
    
    # Procesar cada archivo JSON
    for archivo in archivos_json:
        datos_json = cargar_json_trello(archivo)
        if datos_json:
            nombre_tablero = datos_json.get('name', 'Tablero sin nombre')
            tarjetas = extraer_tarjetas_desde_json(datos_json)
            
            # Agregar el nombre del tablero a cada tarjeta
            for tarjeta in tarjetas:
                tarjeta['tablero'] = nombre_tablero
            
            todas_las_tarjetas.extend(tarjetas)
    
    return todas_las_tarjetas

def convertir_a_dataframe(tarjetas):
    """
    Convierte la lista de tarjetas a un DataFrame de pandas
    
    Args:
        tarjetas (list): Lista de diccionarios con datos de tarjetas
        
    Returns:
        pandas.DataFrame: DataFrame con todas las tarjetas
    """
    if not tarjetas:
        return pd.DataFrame()
    
    df = pd.DataFrame(tarjetas)
    
    # Convertir fechas a formato datetime
    for col in ['fecha_vencimiento', 'ultima_actividad']:
        if col in df.columns:
            df[col] = pd.to_datetime(df[col])
    
    return df

def priorizar_tareas(df):
    """
    Asigna niveles de prioridad a las tareas basándose en fechas de vencimiento y otros factores.
    
    Args:
        df (pandas.DataFrame): DataFrame con las tareas
        
    Returns:
        pandas.DataFrame: DataFrame con columna de prioridad añadida
    """
    # Crear una copia para evitar SettingWithCopyWarning
    df = df.copy()
    
    # Inicializar columna de prioridad
    df['prioridad'] = 'Media'
    
    # Prioridad basada en fecha de vencimiento
    hoy = pd.Timestamp.now().normalize()
    
    # Tareas vencidas (fecha de vencimiento en el pasado)
    mascara_vencidas = (df['fecha_vencimiento'].notna()) & (df['fecha_vencimiento'] < hoy)
    df.loc[mascara_vencidas, 'prioridad'] = 'Crítica'
    
    # Vence hoy
    mascara_vence_hoy = (df['fecha_vencimiento'].notna()) & (df['fecha_vencimiento'] == hoy)
    df.loc[mascara_vence_hoy, 'prioridad'] = 'Alta'
    
    # Vence dentro de 3 días
    mascara_vence_pronto = (df['fecha_vencimiento'].notna()) & (df['fecha_vencimiento'] > hoy) & (df['fecha_vencimiento'] <= hoy + pd.Timedelta(days=3))
    df.loc[mascara_vence_pronto, 'prioridad'] = 'Alta'
    
    # Vence en más de una semana
    mascara_vence_tarde = (df['fecha_vencimiento'].notna()) & (df['fecha_vencimiento'] > hoy + pd.Timedelta(days=7))
    df.loc[mascara_vence_tarde, 'prioridad'] = 'Baja'
    
    # Prioridad basada en etiquetas
    def verificar_etiquetas_prioridad(etiquetas):
        if not isinstance(etiquetas, list):
            return False
            
        palabras_clave_prioridad = ['urgente', 'importante', 'crítico', 'crítica', 'prioridad', 'alta', 'urgent', 'important', 'critical', 'priority', 'high']
        return any(any(palabra in etiqueta.lower() for palabra in palabras_clave_prioridad) for etiqueta in etiquetas)
    
    # Subir prioridad basada en etiquetas
    mascara_etiqueta_prioridad = df['etiquetas'].apply(verificar_etiquetas_prioridad)
    
    # Subir Media a Alta, Baja a Media
    df.loc[(df['prioridad'] == 'Media') & mascara_etiqueta_prioridad, 'prioridad'] = 'Alta'
    df.loc[(df['prioridad'] == 'Baja') & mascara_etiqueta_prioridad, 'prioridad'] = 'Media'
    
    return df

def categorizar_tareas(df):
    """
    Categoriza tareas basándose en etiquetas, posición en lista, y otros factores.
    
    Args:
        df (pandas.DataFrame): DataFrame con las tareas
        
    Returns:
        pandas.DataFrame: DataFrame con columna de categoría añadida
    """
    # Crear una copia para evitar SettingWithCopyWarning
    df = df.copy()
    
    # Inicializar columna de categoría basada en etiquetas existentes
    df['categoria'] = 'General'
    
    # Definir mapeo de palabras clave por categoría
    palabras_clave_categoria = {
        'Desarrollo': ['dev', 'código', 'programación', 'feature', 'bug', 'fix', 'desarrollo', 'code'],
        'Diseño': ['diseño', 'ui', 'ux', 'visual', 'interfaz', 'design', 'interface'],
        'Marketing': ['marketing', 'social', 'contenido', 'seo', 'campaña', 'content', 'campaign'],
        'Documentación': ['doc', 'documentación', 'guía', 'manual', 'wiki', 'documentation', 'guide'],
        'Reunión': ['reunión', 'llamada', 'conferencia', 'discusión', 'meeting', 'call', 'conference'],
        'Investigación': ['investigación', 'análisis', 'estudio', 'explorar', 'research', 'analysis', 'study'],
        'Planificación': ['plan', 'estrategia', 'hoja de ruta', 'backlog', 'strategy', 'roadmap'],
        'Administración': ['admin', 'administración', 'organizar', 'configuración', 'management', 'organize', 'setup'],
        'Implementación': ['implementación', 'deployment', 'entrega', 'delivery', 'instalación', 'integration', 'erp', 'web']
    }
    
    # Función para determinar categoría basada en etiquetas y descripción
    def determinar_categoria(fila):
        if not isinstance(fila['etiquetas'], list):
            return 'General'
            
        # Combinar etiquetas y nombre de tarea para mejor categorización
        texto_a_verificar = ' '.join(fila['etiquetas']).lower() + ' ' + fila['nombre'].lower() + ' ' + fila.get('tablero', '').lower()
        
        for categoria, palabras_clave in palabras_clave_categoria.items():
            if any(palabra in texto_a_verificar for palabra in palabras_clave):
                return categoria
                
        return 'General'
    
    # Aplicar categorización
    df['categoria'] = df.apply(determinar_categoria, axis=1)
    
    return df