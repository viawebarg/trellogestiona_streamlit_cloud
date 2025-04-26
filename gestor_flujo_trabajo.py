def crear_flujo_trabajo(etapas_flujo=None):
    """
    Crea un flujo de trabajo con las etapas especificadas o devuelve etapas predeterminadas.
    
    Args:
        etapas_flujo (list, optional): Lista de nombres de etapas del flujo de trabajo
        
    Returns:
        list: Lista de nombres de etapas del flujo de trabajo
    """
    etapas_predeterminadas = ['Por Hacer', 'En Progreso', 'Revisión', 'Completado']
    
    if etapas_flujo and isinstance(etapas_flujo, list) and len(etapas_flujo) > 0:
        return etapas_flujo
    
    return etapas_predeterminadas

def obtener_etapa_flujo_tarea(tarea, etapas_flujo):
    """
    Determina la etapa actual del flujo de trabajo de una tarea.
    
    Args:
        tarea (dict): Diccionario de tarea con nombre_lista
        etapas_flujo (list): Lista de nombres de etapas del flujo de trabajo
        
    Returns:
        str: Nombre de la etapa actual del flujo de trabajo o None si no está en el flujo
    """
    if 'nombre_lista' in tarea and tarea['nombre_lista'] in etapas_flujo:
        return tarea['nombre_lista']
    
    return None

def obtener_siguiente_etapa_flujo(etapa_actual, etapas_flujo):
    """
    Obtiene la siguiente etapa en el flujo de trabajo.
    
    Args:
        etapa_actual (str): Nombre de la etapa actual del flujo de trabajo
        etapas_flujo (list): Lista de nombres de etapas del flujo de trabajo
        
    Returns:
        str: Nombre de la siguiente etapa del flujo de trabajo o None si está en la última etapa
    """
    if etapa_actual in etapas_flujo:
        indice_actual = etapas_flujo.index(etapa_actual)
        
        if indice_actual < len(etapas_flujo) - 1:
            return etapas_flujo[indice_actual + 1]
    
    return None

def obtener_etapa_anterior_flujo(etapa_actual, etapas_flujo):
    """
    Obtiene la etapa anterior en el flujo de trabajo.
    
    Args:
        etapa_actual (str): Nombre de la etapa actual del flujo de trabajo
        etapas_flujo (list): Lista de nombres de etapas del flujo de trabajo
        
    Returns:
        str: Nombre de la etapa anterior del flujo de trabajo o None si está en la primera etapa
    """
    if etapa_actual in etapas_flujo:
        indice_actual = etapas_flujo.index(etapa_actual)
        
        if indice_actual > 0:
            return etapas_flujo[indice_actual - 1]
    
    return None

def mapear_listas_trello_a_flujo_trabajo(nombres_listas, etapas_flujo):
    """
    Mapea nombres de listas de Trello a etapas del flujo de trabajo.
    
    Args:
        nombres_listas (list): Lista de nombres de listas de Trello
        etapas_flujo (list): Lista de etapas del flujo de trabajo
        
    Returns:
        dict: Diccionario mapeando nombres de listas a etapas del flujo
    """
    mapeo = {}
    
    # Mapeo directo si los nombres coinciden exactamente
    for nombre in nombres_listas:
        if nombre in etapas_flujo:
            mapeo[nombre] = nombre
    
    # Mapeo basado en similitudes para nombres que no coinciden exactamente
    palabras_clave_por_hacer = ['to do', 'por hacer', 'pendiente', 'backlog', 'nuevas', 'inbox']
    palabras_clave_en_progreso = ['in progress', 'en progreso', 'en curso', 'trabajando', 'doing']
    palabras_clave_revision = ['review', 'revisión', 'revisar', 'testing', 'qa', 'pruebas']
    palabras_clave_completado = ['done', 'completado', 'terminado', 'finalizado', 'completadas', 'closed']
    
    for nombre in nombres_listas:
        nombre_lower = nombre.lower()
        
        # Si ya está mapeado, continuar
        if nombre in mapeo:
            continue
        
        # Mapear según palabras clave
        if any(palabra in nombre_lower for palabra in palabras_clave_por_hacer):
            mapeo[nombre] = etapas_flujo[0]  # Por Hacer
        elif any(palabra in nombre_lower for palabra in palabras_clave_en_progreso):
            mapeo[nombre] = etapas_flujo[1]  # En Progreso
        elif any(palabra in nombre_lower for palabra in palabras_clave_revision):
            mapeo[nombre] = etapas_flujo[2]  # Revisión
        elif any(palabra in nombre_lower for palabra in palabras_clave_completado):
            mapeo[nombre] = etapas_flujo[3]  # Completado
    
    return mapeo