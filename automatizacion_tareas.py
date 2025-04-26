"""
Módulo para automatizar la resolución de tareas según su análisis.
Este módulo proporciona funciones para identificar tareas que pueden ser automatizadas,
clasificarlas según criterios específicos y ejecutar acciones automáticas.
"""

import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import re
import json
import db_manager

# Definir criterios para identificar tareas automatizables
PALABRAS_CLAVE_AUTOMATIZABLES = [
    'generar', 'actualizar', 'compilar', 'sincronizar', 'exportar', 'importar', 
    'migrar', 'construir', 'desplegar', 'publicar', 'notificar', 'enviar', 
    'calcular', 'procesar', 'convertir', 'transferir', 'verificar', 'validar',
    'generate', 'update', 'compile', 'sync', 'export', 'import', 'migrate',
    'build', 'deploy', 'publish', 'notify', 'send', 'calculate', 'process',
    'convert', 'transfer', 'check', 'validate', 'automatizar', 'automatizado',
    'programado', 'periódico', 'recurrente', 'diario', 'semanal', 'mensual',
    'automático', 'cron', 'script', 'robot', 'bot'
]

# Patrones para reconocer tipos específicos de tareas
PATRON_EMAIL = r'correo|email|mail|mensaje|notificación|newsletter'
PATRON_REPORTE = r'reporte|informe|estadísticas|métricas|dashboard|tablero|resumen'
PATRON_BACKUP = r'backup|respaldo|copia de seguridad|sincronización|sync'
PATRON_LIMPIEZA = r'limpiar|limpieza|eliminar|depurar|archivar|organizar'
PATRON_RECORDATORIO = r'recordar|recordatorio|aviso|alerta|seguimiento|reminder'
PATRON_ACTUALIZACION = r'actualizar|update|upgrade|patch|versión|nueva versión'

def analizar_automatizacion(df):
    """
    Analiza un DataFrame de tareas para identificar cuáles pueden ser automatizadas.
    
    Args:
        df (pandas.DataFrame): DataFrame con las tareas a analizar
        
    Returns:
        pandas.DataFrame: El mismo DataFrame con columnas adicionales de automatización
    """
    # Crear una copia para evitar SettingWithCopyWarning
    df = df.copy()
    
    # Inicializar columna de automatización
    df['automatizable'] = False
    df['tipo_automatizacion'] = 'No automatizable'
    df['accion_recomendada'] = ''
    df['porcentaje_automatizacion'] = 0.0
    
    # Analizar cada tarea para determinar si es automatizable
    for idx, tarea in df.iterrows():
        nombre_tarea = tarea['nombre'].lower() if isinstance(tarea['nombre'], str) else ''
        descripcion = tarea['descripcion'].lower() if isinstance(tarea['descripcion'], str) else ''
        
        # Combinar nombre y descripción para análisis
        texto_completo = f"{nombre_tarea} {descripcion}"
        
        # Calcular puntaje base de automatización basado en palabras clave
        palabras_encontradas = [palabra for palabra in PALABRAS_CLAVE_AUTOMATIZABLES 
                               if palabra in texto_completo]
        
        puntaje_automatizacion = len(palabras_encontradas) * 10
        
        # Analizar patrones específicos
        tipo_auto = 'General'
        accion = ''
        
        if re.search(PATRON_EMAIL, texto_completo):
            tipo_auto = 'Envío de correos'
            accion = 'Configurar envío automático de correos mediante script programado'
            puntaje_automatizacion += 20
        
        elif re.search(PATRON_REPORTE, texto_completo):
            tipo_auto = 'Generación de reportes'
            accion = 'Implementar script para generar y distribuir reportes automáticamente'
            puntaje_automatizacion += 25
        
        elif re.search(PATRON_BACKUP, texto_completo):
            tipo_auto = 'Backup/Sincronización'
            accion = 'Configurar proceso automatizado de respaldo o sincronización'
            puntaje_automatizacion += 30
        
        elif re.search(PATRON_LIMPIEZA, texto_completo):
            tipo_auto = 'Limpieza de datos'
            accion = 'Programar tareas de limpieza/archivo de datos antiguos'
            puntaje_automatizacion += 15
        
        elif re.search(PATRON_RECORDATORIO, texto_completo):
            tipo_auto = 'Recordatorios'
            accion = 'Configurar sistema automático de recordatorios o notificaciones'
            puntaje_automatizacion += 10
        
        elif re.search(PATRON_ACTUALIZACION, texto_completo):
            tipo_auto = 'Actualizaciones'
            accion = 'Programar proceso automático de actualización'
            puntaje_automatizacion += 20
            
        # Determinar si es automatizable basado en puntaje (umbral de 30)
        if puntaje_automatizacion >= 30:
            df.at[idx, 'automatizable'] = True
            df.at[idx, 'tipo_automatizacion'] = tipo_auto
            df.at[idx, 'accion_recomendada'] = accion
        
        # Guardar el porcentaje de automatización (normalizado a 100%)
        porcentaje = min(100, puntaje_automatizacion)
        df.at[idx, 'porcentaje_automatizacion'] = porcentaje
    
    return df

def obtener_tareas_automatizables(df=None):
    """
    Obtiene las tareas que pueden ser automatizadas, ya sea de un DataFrame proporcionado
    o directamente de la base de datos.
    
    Args:
        df (pandas.DataFrame, optional): DataFrame de tareas, opcional
        
    Returns:
        pandas.DataFrame: DataFrame con las tareas automatizables
    """
    if df is None:
        # Obtener tareas de la base de datos
        df = db_manager.cargar_tareas()
    
    # Si no hay datos, devolver DataFrame vacío
    if df.empty:
        return pd.DataFrame()
    
    # Analizar la automatización
    df_con_auto = analizar_automatizacion(df)
    
    # Filtrar solo las tareas automatizables
    tareas_automatizables = df_con_auto[df_con_auto['automatizable'] == True].copy()
    
    # Ordenar por porcentaje de automatización (descendente)
    tareas_automatizables = tareas_automatizables.sort_values(
        by='porcentaje_automatizacion', ascending=False
    )
    
    return tareas_automatizables

def generar_plan_automatizacion(tarea_id):
    """
    Genera un plan detallado para automatizar una tarea específica.
    
    Args:
        tarea_id (str): ID de la tarea a automatizar
        
    Returns:
        dict: Diccionario con el plan de automatización
    """
    # Obtener la tarea específica de la base de datos
    tareas_df = db_manager.cargar_tareas({'tarea_id': tarea_id})
    
    if tareas_df.empty:
        return {"error": "Tarea no encontrada"}
    
    tarea = tareas_df.iloc[0]
    
    # Analizar si es automatizable
    tarea_analizada = analizar_automatizacion(pd.DataFrame([tarea])).iloc[0]
    
    if not tarea_analizada['automatizable']:
        return {
            "resultado": "No automatizable",
            "mensaje": "Esta tarea no se considera automatizable según los criterios de análisis.",
            "score": tarea_analizada['porcentaje_automatizacion']
        }
    
    # Generar plan de automatización según el tipo
    tipo = tarea_analizada['tipo_automatizacion']
    plan = {
        "resultado": "Automatizable",
        "tipo": tipo,
        "score": tarea_analizada['porcentaje_automatizacion'],
        "accion_recomendada": tarea_analizada['accion_recomendada'],
        "pasos": [],
        "herramientas_sugeridas": [],
        "tiempo_estimado": ""
    }
    
    # Completar plan según el tipo de automatización
    if tipo == "Envío de correos":
        plan["pasos"] = [
            "Identificar destinatarios y contenido del correo",
            "Crear plantilla de correo electrónico",
            "Implementar script para envío automático",
            "Configurar programación de envío (diaria/semanal/etc.)",
            "Establecer mecanismo de seguimiento y reportes"
        ]
        plan["herramientas_sugeridas"] = ["Python con biblioteca SMTP", "Servicios como SendGrid o Mailchimp", "Cron para programación"]
        plan["tiempo_estimado"] = "2-4 horas para implementación inicial"
    
    elif tipo == "Generación de reportes":
        plan["pasos"] = [
            "Definir formato y contenido del reporte",
            "Identificar fuentes de datos necesarias",
            "Implementar script de generación automática",
            "Configurar distribución del reporte (email, almacenamiento, etc.)",
            "Programar ejecución periódica"
        ]
        plan["herramientas_sugeridas"] = ["Python con pandas/numpy", "Herramientas de visualización (matplotlib, Plotly)", "SQL para consultas"]
        plan["tiempo_estimado"] = "4-8 horas para implementación inicial"
    
    elif tipo == "Backup/Sincronización":
        plan["pasos"] = [
            "Identificar datos/sistemas a respaldar",
            "Definir frecuencia y política de retención",
            "Seleccionar herramientas de backup apropiadas",
            "Implementar scripts de respaldo automático",
            "Configurar alertas y monitoreo"
        ]
        plan["herramientas_sugeridas"] = ["Rsync", "Scripts de backup", "Herramientas cloud (AWS S3, etc.)", "Cron para programación"]
        plan["tiempo_estimado"] = "3-6 horas para implementación inicial"
    
    elif tipo == "Limpieza de datos":
        plan["pasos"] = [
            "Definir criterios de limpieza/archivado",
            "Identificar datos a procesar",
            "Desarrollar script de limpieza automática",
            "Implementar proceso de verificación/validación",
            "Programar ejecución periódica"
        ]
        plan["herramientas_sugeridas"] = ["Python con pandas", "Scripts SQL", "Cron para programación"]
        plan["tiempo_estimado"] = "3-5 horas para implementación inicial"
    
    elif tipo == "Recordatorios":
        plan["pasos"] = [
            "Definir eventos o condiciones que generan recordatorios",
            "Configurar sistema de notificaciones",
            "Implementar mecanismo de entrega (email, push, etc.)",
            "Configurar frecuencia y persistencia",
            "Establecer sistema de confirmación/seguimiento"
        ]
        plan["herramientas_sugeridas"] = ["Calendario/sistema de tareas con API", "Scripts para monitoreo", "Servicios de notificaciones"]
        plan["tiempo_estimado"] = "2-4 horas para implementación inicial"
    
    elif tipo == "Actualizaciones":
        plan["pasos"] = [
            "Identificar componentes a actualizar",
            "Definir proceso de verificación previo",
            "Implementar script de actualización",
            "Configurar verificación posterior y rollback",
            "Programar ejecución automática"
        ]
        plan["herramientas_sugeridas"] = ["Scripts de actualización", "Herramientas de control de versiones", "Cron para programación"]
        plan["tiempo_estimado"] = "4-8 horas para implementación inicial"
    
    else:  # General
        plan["pasos"] = [
            "Analizar el proceso actual en detalle",
            "Identificar componentes automatizables",
            "Seleccionar herramientas apropiadas",
            "Implementar automatización paso a paso",
            "Verificar y optimizar"
        ]
        plan["herramientas_sugeridas"] = ["Python/Bash scripts", "Herramientas de automatización específicas del dominio"]
        plan["tiempo_estimado"] = "Depende de la complejidad de la tarea"
    
    return plan

def actualizar_estado_automatizacion(tarea_id, estado_automatizacion, detalles=None):
    """
    Actualiza el estado de automatización de una tarea en la base de datos.
    
    Args:
        tarea_id (str): ID de la tarea
        estado_automatizacion (str): Estado de automatización ('En proceso', 'Completada', etc.)
        detalles (dict, optional): Detalles adicionales sobre la automatización
        
    Returns:
        bool: True si se actualizó correctamente, False en caso contrario
    """
    # Aquí implementaríamos la actualización en la base de datos
    # Como es una función demostrativa, simplemente devolvemos True
    return True

def ejecutar_automatizacion_simulada(tarea_id):
    """
    Simula la ejecución de una automatización para una tarea específica.
    En una implementación real, esto conectaría con sistemas externos o ejecutaría scripts.
    
    Args:
        tarea_id (str): ID de la tarea a automatizar
        
    Returns:
        dict: Resultado de la simulación
    """
    # Obtener la tarea
    tareas_df = db_manager.cargar_tareas({'tarea_id': tarea_id})
    
    if tareas_df.empty:
        return {"error": "Tarea no encontrada"}
    
    tarea = tareas_df.iloc[0]
    
    # Simular resultado de automatización
    resultado = {
        "tarea_id": tarea_id,
        "nombre_tarea": tarea['nombre'],
        "estado": "Simulación exitosa",
        "tiempo_ejecucion": f"{np.random.randint(1, 60)} segundos",
        "detalles": "Esta es una simulación de automatización. En un entorno real, se ejecutaría el proceso correspondiente.",
        "fecha_ejecucion": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    
    return resultado

def obtener_estadisticas_automatizacion(df=None):
    """
    Calcula estadísticas sobre la automatización de tareas.
    
    Args:
        df (pandas.DataFrame, optional): DataFrame de tareas, opcional
        
    Returns:
        dict: Diccionario con estadísticas de automatización
    """
    if df is None:
        # Obtener tareas de la base de datos
        df = db_manager.cargar_tareas()
    
    # Si no hay datos, devolver estadísticas vacías
    if df.empty:
        return {
            "total_tareas": 0,
            "tareas_automatizables": 0,
            "porcentaje_automatizable": 0,
            "distribucion_tipos": {}
        }
    
    # Analizar automatización
    df_analizado = analizar_automatizacion(df)
    
    # Calcular estadísticas
    total_tareas = len(df_analizado)
    tareas_automatizables = df_analizado['automatizable'].sum()
    porcentaje_automatizable = (tareas_automatizables / total_tareas) * 100 if total_tareas > 0 else 0
    
    # Distribución por tipo de automatización
    tipos_count = df_analizado[df_analizado['automatizable']]['tipo_automatizacion'].value_counts().to_dict()
    
    # Porcentaje promedio de automatización
    prom_porcentaje = df_analizado['porcentaje_automatizacion'].mean()
    
    # Potencial de automatización por categoría de tarea
    potencial_por_categoria = df_analizado.groupby('categoria')['porcentaje_automatizacion'].mean().to_dict()
    
    # Devolver estadísticas
    estadisticas = {
        "total_tareas": int(total_tareas),
        "tareas_automatizables": int(tareas_automatizables),
        "porcentaje_automatizable": round(porcentaje_automatizable, 2),
        "distribucion_tipos": tipos_count,
        "promedio_porcentaje_automatizacion": round(prom_porcentaje, 2),
        "potencial_por_categoria": {k: round(v, 2) for k, v in potencial_por_categoria.items()}
    }
    
    return estadisticas