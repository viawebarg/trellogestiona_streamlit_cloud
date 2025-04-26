# Modulo generador de scripts de automatizacion

import os
import sys
import json
import re
from datetime import datetime, timedelta
import subprocess

def obtener_fecha_formateada():
    """Obtiene la fecha actual en formato legible."""
    return datetime.now().strftime("%d/%m/%Y %H:%M:%S")

def crear_directorio_scripts():
    """Crea el directorio para los scripts generados si no existe."""
    directorio = os.path.join(os.getcwd(), "scripts_automatizacion")
    if not os.path.exists(directorio):
        os.makedirs(directorio)
    return directorio

def generar_script_correo(tarea, configuracion=None):
    """Genera un script de automatizacion para envio de correos."""
    if configuracion is None:
        configuracion = {
            "smtp_server": "smtp.gmail.com",
            "smtp_port": 587,
            "email_remitente": "usuario@gmail.com",
            "destinatarios": ["destinatario1@example.com"],
            "asunto": "Informe Automatizado",
            "cuerpo_mensaje": "Este es un correo automatizado."
        }
    
    # Contenido del script
    script_content = f"""#!/usr/bin/env python3
# Script de automatizacion generado el {obtener_fecha_formateada()}
# Tipo: Envio de correos
# Descripcion: {tarea.get('descripcion', 'Envio de correo automatizado')}

import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime
import os
import logging

# Configurar logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler("email_automation.log"),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger("EmailAutomation")

# Configuracion
SMTP_SERVER = "{configuracion.get('smtp_server', 'smtp.gmail.com')}"
SMTP_PORT = {configuracion.get('smtp_port', 587)}
SENDER_EMAIL = "{configuracion.get('email_remitente', 'usuario@gmail.com')}"
PASSWORD = os.environ.get('EMAIL_PASSWORD')
RECIPIENTS = {configuracion.get('destinatarios', ["destinatario@example.com"])}
SUBJECT = "{configuracion.get('asunto', 'Informe Automatizado')}"

def enviar_correo():
    try:
        # Crear mensaje
        mensaje = MIMEMultipart()
        mensaje['From'] = SENDER_EMAIL
        mensaje['To'] = ", ".join(RECIPIENTS)
        mensaje['Subject'] = SUBJECT
        
        # Cuerpo del mensaje
        cuerpo = \"\"\"
{configuracion.get('cuerpo_mensaje', 'Este es un correo automatizado.')}
\"\"\"
        
        # Agregar fecha actual
        cuerpo += f"\\n\\nGenerado automaticamente el {{datetime.now().strftime('%d/%m/%Y %H:%M:%S')}}"
        
        mensaje.attach(MIMEText(cuerpo, 'plain'))
        
        # Conectar al servidor
        logger.info(f"Conectando al servidor SMTP: {{SMTP_SERVER}}:{{SMTP_PORT}}")
        servidor = smtplib.SMTP(SMTP_SERVER, SMTP_PORT)
        servidor.starttls()
        
        # Iniciar sesion
        if PASSWORD:
            logger.info(f"Iniciando sesion como {{SENDER_EMAIL}}")
            servidor.login(SENDER_EMAIL, PASSWORD)
        else:
            logger.error("No se ha configurado la password de email")
            return False
        
        # Enviar correo
        texto = mensaje.as_string()
        servidor.sendmail(SENDER_EMAIL, RECIPIENTS, texto)
        logger.info(f"Correo enviado exitosamente a {{len(RECIPIENTS)}} destinatarios")
        
        # Cerrar conexion
        servidor.quit()
        return True
        
    except Exception as e:
        logger.error(f"Error al enviar correo: {{str(e)}}")
        return False

if __name__ == "__main__":
    logger.info("Iniciando proceso de envio de correo automatizado")
    resultado = enviar_correo()
    if resultado:
        logger.info("Proceso completado exitosamente")
        sys.exit(0)
    else:
        logger.error("El proceso fallo")
        sys.exit(1)
"""
    
    # Generar nombre para el script
    id_script = re.sub(r'\W+', '_', tarea.get('nombre', 'correo')).lower()
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    nombre_script = f"correo_{id_script}_{timestamp}.py"
    
    # Guardar script
    directorio = crear_directorio_scripts()
    ruta_script = os.path.join(directorio, nombre_script)
    
    with open(ruta_script, 'w') as f:
        f.write(script_content)
    
    # Hacer ejecutable en sistemas Unix
    if os.name != 'nt':  # No es Windows
        import stat
        os.chmod(ruta_script, os.stat(ruta_script).st_mode | stat.S_IEXEC)
    
    return ruta_script

def generar_script_reporte(tarea, configuracion=None):
    """Genera un script de automatizacion para generacion de reportes."""
    if configuracion is None:
        configuracion = {
            "fuente_datos": "datos/ventas.csv",
            "ruta_salida": "reportes/reporte_ventas.xlsx",
            "titulo_reporte": "Reporte de Ventas"
        }
    
    # Contenido del script
    script_content = f"""#!/usr/bin/env python3
# Script de automatizacion generado el {obtener_fecha_formateada()}
# Tipo: Generacion de reportes
# Descripcion: {tarea.get('descripcion', 'Generacion de reporte automatizado')}

import pandas as pd
import matplotlib.pyplot as plt
import numpy as np
from datetime import datetime, timedelta
import os
import logging
import json

# Configurar logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler("report_automation.log"),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger("ReportAutomation")

# Configuracion
FUENTE_DATOS = "{configuracion.get('fuente_datos', 'datos/datos.csv')}"
RUTA_SALIDA = "{configuracion.get('ruta_salida', 'reportes/reporte.xlsx')}"
TITULO_REPORTE = "{configuracion.get('titulo_reporte', 'Reporte Automatizado')}"

def cargar_datos():
    try:
        logger.info(f"Cargando datos desde {{FUENTE_DATOS}}")
        
        # Determinar tipo de fuente de datos
        if FUENTE_DATOS.endswith('.csv'):
            df = pd.read_csv(FUENTE_DATOS)
        elif FUENTE_DATOS.endswith('.xlsx') or FUENTE_DATOS.endswith('.xls'):
            df = pd.read_excel(FUENTE_DATOS)
        elif FUENTE_DATOS.endswith('.json'):
            df = pd.read_json(FUENTE_DATOS)
        else:
            # Si es una conexion a base de datos
            from sqlalchemy import create_engine
            engine = create_engine(FUENTE_DATOS)
            df = pd.read_sql("SELECT * FROM datos", engine)
        
        logger.info(f"Datos cargados exitosamente. Filas: {{len(df)}}, Columnas: {{len(df.columns)}}")
        return df
    
    except Exception as e:
        logger.error(f"Error al cargar datos: {{str(e)}}")
        return None

def generar_reporte(df):
    try:
        if df is None or df.empty:
            logger.error("No hay datos para generar el reporte")
            return False
        
        # Asegurar que exista el directorio de salida
        os.makedirs(os.path.dirname(RUTA_SALIDA), exist_ok=True)
        
        # Preparar datos para el reporte
        logger.info("Procesando datos para el reporte")
        
        # Fecha del reporte
        fecha_reporte = datetime.now().strftime('%d/%m/%Y')
        
        # Crear el reporte (ejemplo simple)
        with pd.ExcelWriter(RUTA_SALIDA) as writer:
            # Hoja de resumen
            df.describe().to_excel(writer, sheet_name='Resumen')
            
            # Datos completos
            df.to_excel(writer, sheet_name='Datos', index=False)
        
        logger.info(f"Reporte generado exitosamente en {{RUTA_SALIDA}}")
        return True
        
    except Exception as e:
        logger.error(f"Error al generar reporte: {{str(e)}}")
        return False

if __name__ == "__main__":
    logger.info("Iniciando generacion de reporte automatizado")
    df = cargar_datos()
    if df is not None:
        exito_generacion = generar_reporte(df)
        if exito_generacion:
            logger.info("Proceso completado exitosamente")
            print(f"Reporte generado exitosamente en {{RUTA_SALIDA}}")
        else:
            logger.error("Fallo la generacion del reporte")
    else:
        logger.error("Fallo la carga de datos")
"""
    
    # Generar nombre para el script
    id_script = re.sub(r'\W+', '_', tarea.get('nombre', 'reporte')).lower()
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    nombre_script = f"reporte_{id_script}_{timestamp}.py"
    
    # Guardar script
    directorio = crear_directorio_scripts()
    ruta_script = os.path.join(directorio, nombre_script)
    
    with open(ruta_script, 'w') as f:
        f.write(script_content)
    
    # Hacer ejecutable en sistemas Unix
    if os.name != 'nt':  # No es Windows
        import stat
        os.chmod(ruta_script, os.stat(ruta_script).st_mode | stat.S_IEXEC)
    
    return ruta_script

def generar_script_automatizacion(tarea, tipo=None, configuracion=None):
    """Genera un script de automatizacion segun el tipo de tarea."""
    # Determinar el tipo de automatizacion
    if tipo is None:
        tipo = tarea.get('tipo_automatizacion', 'General')
    
    # Seleccionar la funcion adecuada según el tipo
    if tipo == 'Envio de correos' or tipo == 'Email':
        return generar_script_correo(tarea, configuracion)
    else:
        # Por defecto generar un reporte
        return generar_script_reporte(tarea, configuracion)

def obtener_script_para_tarea(tarea_id, df_tareas, tipo=None, configuracion=None):
    """Busca una tarea por ID y genera un script para ella."""
    try:
        tarea = df_tareas[df_tareas['id'] == tarea_id].iloc[0].to_dict()
        return generar_script_automatizacion(tarea, tipo, configuracion)
    except (IndexError, KeyError):
        return None

def ejecutar_script(ruta_script):
    """Ejecuta un script de automatizacion."""
    try:
        # Asegurar que el script existe
        if not os.path.exists(ruta_script):
            return {"error": f"El script {ruta_script} no existe"}
        
        # Ejecutar el script
        comando = [sys.executable, ruta_script]
        
        resultado = subprocess.run(
            comando, 
            capture_output=True, 
            text=True
        )
        
        if resultado.returncode == 0:
            return {
                "exito": True,
                "mensaje": "Script ejecutado correctamente",
                "salida": resultado.stdout
            }
        else:
            return {
                "exito": False,
                "mensaje": f"Error al ejecutar el script: {resultado.stderr}",
                "codigo_error": resultado.returncode
            }
    except Exception as e:
        return {"error": str(e)}

def programar_script(ruta_script, programacion='diaria', hora='09:00'):
    """Programa la ejecucion automatizada de un script."""
    try:
        # Comprobar que el script existe
        if not os.path.exists(ruta_script):
            return {"error": f"El script {ruta_script} no existe"}
        
        # Simulamos la programacion (en un entorno real esto conectaría con cron o task scheduler)
        return {
            "exito": True,
            "mensaje": f"Script programado para ejecucion {programacion} a las {hora} (simulado)"
        }
    except Exception as e:
        return {"error": str(e)}