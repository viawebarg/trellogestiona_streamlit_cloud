import os
import pandas as pd
from sqlalchemy import create_engine, Column, Integer, String, DateTime, Text, Float, Boolean, MetaData, Table, ForeignKey, insert, select, update, delete
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, relationship
import datetime
import json
import time
import logging

# Configurar logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Obtener la URL de la base de datos desde las variables de entorno
DATABASE_URL = os.environ.get('DATABASE_URL')

# Número máximo de intentos de conexión
MAX_RETRIES = 3
RETRY_DELAY = 2  # segundos

# Crear el motor SQLAlchemy con manejo de errores
def get_engine():
    for attempt in range(MAX_RETRIES):
        try:
            logger.info(f"Intento {attempt+1} de conexión a la base de datos")
            # Crear el motor con opciones de conexión más robustas
            engine = create_engine(
                DATABASE_URL, 
                pool_pre_ping=True,  # Verifica que la conexión esté viva
                pool_recycle=3600,   # Recicla conexiones después de una hora
                connect_args={'connect_timeout': 10}  # Timeout de conexión
            )
            # Probar la conexión
            with engine.connect() as conn:
                conn.execute("SELECT 1")
            logger.info("Conexión a la base de datos establecida correctamente")
            return engine
        except Exception as e:
            logger.error(f"Error al conectar a la base de datos: {str(e)}")
            if attempt < MAX_RETRIES - 1:
                logger.info(f"Reintentando en {RETRY_DELAY} segundos...")
                time.sleep(RETRY_DELAY)
            else:
                logger.error("Número máximo de intentos alcanzado. No se pudo conectar a la base de datos.")
                raise

# Inicializar el motor
try:
    engine = get_engine()
except Exception as e:
    logger.error(f"No se pudo establecer la conexión a la base de datos: {str(e)}")
    # Crear un motor en memoria como fallback para que la aplicación no se caiga
    logger.warning("Usando base de datos en memoria como fallback")
    engine = create_engine('sqlite:///:memory:')

# Crear la base declarativa
Base = declarative_base()

# Definir las clases de modelos
class Tablero(Base):
    __tablename__ = 'tableros'
    
    id = Column(String, primary_key=True)
    nombre = Column(String, nullable=False)
    descripcion = Column(Text)
    fecha_actualizacion = Column(DateTime, default=datetime.datetime.utcnow)
    
    # Relación con tareas
    tareas = relationship("Tarea", back_populates="tablero", cascade="all, delete-orphan")

class Lista(Base):
    __tablename__ = 'listas'
    
    id = Column(String, primary_key=True)
    nombre = Column(String, nullable=False)
    tablero_id = Column(String, ForeignKey('tableros.id'))
    posicion = Column(Integer)
    etapa_flujo = Column(String)  # Mapeo a etapa de flujo de trabajo
    
    # Relación con tareas
    tareas = relationship("Tarea", back_populates="lista")

class Tarea(Base):
    __tablename__ = 'tareas'
    
    id = Column(String, primary_key=True)
    nombre = Column(String, nullable=False)
    descripcion = Column(Text)
    tablero_id = Column(String, ForeignKey('tableros.id'))
    lista_id = Column(String, ForeignKey('listas.id'))
    fecha_creacion = Column(DateTime)
    fecha_vencimiento = Column(DateTime)
    fecha_ultima_actividad = Column(DateTime)
    etiquetas = Column(Text)  # Almacenar como JSON
    url = Column(String)
    prioridad = Column(String)
    categoria = Column(String)
    porcentaje_completado = Column(Float, default=0.0)
    
    # Relaciones
    tablero = relationship("Tablero", back_populates="tareas")
    lista = relationship("Lista", back_populates="tareas")

class ConfiguracionFlujoDeTrabajo(Base):
    __tablename__ = 'configuracion_flujo_trabajo'
    
    id = Column(Integer, primary_key=True, autoincrement=True)
    nombre = Column(String, unique=True)
    etapas = Column(Text)  # Almacenar como JSON
    creado_en = Column(DateTime, default=datetime.datetime.utcnow)
    es_default = Column(Boolean, default=False)
    
    def get_etapas(self):
        """Convierte el JSON de etapas a una lista Python"""
        return json.loads(self.etapas) if self.etapas else []
    
    def set_etapas(self, etapas_lista):
        """Convierte una lista Python a JSON para almacenar"""
        self.etapas = json.dumps(etapas_lista)

# Inicializar la base de datos
def inicializar_db():
    # Crear todas las tablas si no existen
    Base.metadata.create_all(engine)
    
    # Crear sesión
    Session = sessionmaker(bind=engine)
    session = Session()
    
    # Crear configuración de flujo de trabajo predeterminada si no existe
    flujo_default = session.query(ConfiguracionFlujoDeTrabajo).filter_by(es_default=True).first()
    if not flujo_default:
        etapas_default = ['Por Hacer', 'En Progreso', 'Revisión', 'Completado']
        flujo_default = ConfiguracionFlujoDeTrabajo(
            nombre="Flujo Predeterminado",
            etapas=json.dumps(etapas_default),
            es_default=True
        )
        session.add(flujo_default)
        session.commit()
    
    session.close()
    return "Base de datos inicializada correctamente"

# Funciones CRUD para tableros
def guardar_tablero(id, nombre, descripcion=""):
    Session = sessionmaker(bind=engine)
    session = Session()
    
    # Verificar si ya existe el tablero
    tablero_existente = session.query(Tablero).filter_by(id=id).first()
    
    if tablero_existente:
        # Actualizar tablero existente
        tablero_existente.nombre = nombre
        tablero_existente.descripcion = descripcion
        tablero_existente.fecha_actualizacion = datetime.datetime.utcnow()
    else:
        # Crear nuevo tablero
        nuevo_tablero = Tablero(
            id=id,
            nombre=nombre,
            descripcion=descripcion
        )
        session.add(nuevo_tablero)
    
    session.commit()
    session.close()

# Funciones CRUD para listas
def guardar_lista(id, nombre, tablero_id, posicion=0, etapa_flujo=None):
    Session = sessionmaker(bind=engine)
    session = Session()
    
    # Verificar si ya existe la lista
    lista_existente = session.query(Lista).filter_by(id=id).first()
    
    if lista_existente:
        # Actualizar lista existente
        lista_existente.nombre = nombre
        lista_existente.tablero_id = tablero_id
        lista_existente.posicion = posicion
        if etapa_flujo:
            lista_existente.etapa_flujo = etapa_flujo
    else:
        # Crear nueva lista
        nueva_lista = Lista(
            id=id,
            nombre=nombre,
            tablero_id=tablero_id,
            posicion=posicion,
            etapa_flujo=etapa_flujo
        )
        session.add(nueva_lista)
    
    session.commit()
    session.close()

# Funciones CRUD para tareas
def guardar_tarea(tarea_dict):
    Session = sessionmaker(bind=engine)
    session = Session()
    
    # Extraer datos de la tarea
    id = tarea_dict.get('id')
    
    # Verificar si ya existe la tarea
    tarea_existente = session.query(Tarea).filter_by(id=id).first()
    
    if tarea_existente:
        # Actualizar tarea existente
        tarea_existente.nombre = tarea_dict.get('nombre', tarea_existente.nombre)
        tarea_existente.descripcion = tarea_dict.get('descripcion', tarea_existente.descripcion)
        tarea_existente.tablero_id = tarea_dict.get('tablero_id', tarea_existente.tablero_id)
        tarea_existente.lista_id = tarea_dict.get('lista_id', tarea_existente.lista_id)
        
        if 'fecha_vencimiento' in tarea_dict and tarea_dict['fecha_vencimiento']:
            tarea_existente.fecha_vencimiento = tarea_dict['fecha_vencimiento']
        
        if 'fecha_ultima_actividad' in tarea_dict and tarea_dict['fecha_ultima_actividad']:
            tarea_existente.fecha_ultima_actividad = tarea_dict['fecha_ultima_actividad']
        
        if 'etiquetas' in tarea_dict:
            tarea_existente.etiquetas = json.dumps(tarea_dict['etiquetas']) if tarea_dict['etiquetas'] else None
        
        tarea_existente.url = tarea_dict.get('url', tarea_existente.url)
        tarea_existente.prioridad = tarea_dict.get('prioridad', tarea_existente.prioridad)
        tarea_existente.categoria = tarea_dict.get('categoria', tarea_existente.categoria)
        tarea_existente.porcentaje_completado = tarea_dict.get('porcentaje_completado', tarea_existente.porcentaje_completado)
    else:
        # Preparar datos para nueva tarea
        nueva_tarea = Tarea(
            id=id,
            nombre=tarea_dict.get('nombre', 'Sin título'),
            descripcion=tarea_dict.get('descripcion', ''),
            tablero_id=tarea_dict.get('tablero_id'),
            lista_id=tarea_dict.get('lista_id'),
            fecha_creacion=datetime.datetime.utcnow(),
            url=tarea_dict.get('url', '')
        )
        
        if 'fecha_vencimiento' in tarea_dict and tarea_dict['fecha_vencimiento']:
            nueva_tarea.fecha_vencimiento = tarea_dict['fecha_vencimiento']
        
        if 'fecha_ultima_actividad' in tarea_dict and tarea_dict['fecha_ultima_actividad']:
            nueva_tarea.fecha_ultima_actividad = tarea_dict['fecha_ultima_actividad']
        
        if 'etiquetas' in tarea_dict and tarea_dict['etiquetas']:
            nueva_tarea.etiquetas = json.dumps(tarea_dict['etiquetas'])
        
        nueva_tarea.prioridad = tarea_dict.get('prioridad', 'Media')
        nueva_tarea.categoria = tarea_dict.get('categoria', 'General')
        nueva_tarea.porcentaje_completado = tarea_dict.get('porcentaje_completado', 0.0)
        
        session.add(nueva_tarea)
    
    session.commit()
    session.close()

# Funciones para cargar datos
def cargar_tableros():
    Session = sessionmaker(bind=engine)
    session = Session()
    
    tableros = session.query(Tablero).all()
    
    resultado = []
    for tablero in tableros:
        resultado.append({
            'id': tablero.id,
            'nombre': tablero.nombre,
            'descripcion': tablero.descripcion,
            'fecha_actualizacion': tablero.fecha_actualizacion
        })
    
    session.close()
    
    return pd.DataFrame(resultado) if resultado else pd.DataFrame()

def cargar_tareas(filtros=None):
    Session = sessionmaker(bind=engine)
    session = Session()
    
    query = session.query(Tarea)
    
    # Aplicar filtros si se proporcionan
    if filtros:
        if 'tablero_id' in filtros and filtros['tablero_id']:
            query = query.filter(Tarea.tablero_id == filtros['tablero_id'])
        
        if 'lista_id' in filtros and filtros['lista_id']:
            query = query.filter(Tarea.lista_id == filtros['lista_id'])
        
        if 'prioridad' in filtros and filtros['prioridad']:
            query = query.filter(Tarea.prioridad == filtros['prioridad'])
    
    tareas = query.all()
    
    resultado = []
    for tarea in tareas:
        tarea_dict = {
            'id': tarea.id,
            'nombre': tarea.nombre,
            'descripcion': tarea.descripcion,
            'tablero_id': tarea.tablero_id,
            'lista_id': tarea.lista_id,
            'fecha_creacion': tarea.fecha_creacion,
            'fecha_vencimiento': tarea.fecha_vencimiento,
            'fecha_ultima_actividad': tarea.fecha_ultima_actividad,
            'url': tarea.url,
            'prioridad': tarea.prioridad,
            'categoria': tarea.categoria,
            'porcentaje_completado': tarea.porcentaje_completado
        }
        
        # Obtener el nombre de la lista
        if tarea.lista:
            tarea_dict['nombre_lista'] = tarea.lista.nombre
        else:
            tarea_dict['nombre_lista'] = 'Desconocido'
        
        # Obtener el nombre del tablero
        if tarea.tablero:
            tarea_dict['tablero'] = tarea.tablero.nombre
        else:
            tarea_dict['tablero'] = 'Desconocido'
        
        # Convertir etiquetas de JSON a lista
        if tarea.etiquetas:
            try:
                tarea_dict['etiquetas'] = json.loads(tarea.etiquetas)
            except:
                tarea_dict['etiquetas'] = []
        else:
            tarea_dict['etiquetas'] = []
        
        resultado.append(tarea_dict)
    
    session.close()
    
    return pd.DataFrame(resultado) if resultado else pd.DataFrame()

# Funciones para gestionar el flujo de trabajo
def obtener_configuracion_flujo_trabajo(nombre=None):
    Session = sessionmaker(bind=engine)
    session = Session()
    
    # Si no se especifica un nombre, obtener la configuración predeterminada
    if not nombre:
        config = session.query(ConfiguracionFlujoDeTrabajo).filter_by(es_default=True).first()
    else:
        config = session.query(ConfiguracionFlujoDeTrabajo).filter_by(nombre=nombre).first()
    
    if not config:
        # Si no hay configuración, crear la predeterminada
        etapas_default = ['Por Hacer', 'En Progreso', 'Revisión', 'Completado']
        config = ConfiguracionFlujoDeTrabajo(
            nombre="Flujo Predeterminado",
            etapas=json.dumps(etapas_default),
            es_default=True
        )
        session.add(config)
        session.commit()
    
    # Obtener las etapas como lista
    etapas = config.get_etapas()
    
    session.close()
    
    return etapas

def guardar_configuracion_flujo_trabajo(nombre, etapas, es_default=False):
    Session = sessionmaker(bind=engine)
    session = Session()
    
    # Verificar si ya existe la configuración
    config_existente = session.query(ConfiguracionFlujoDeTrabajo).filter_by(nombre=nombre).first()
    
    if config_existente:
        # Actualizar configuración existente
        config_existente.set_etapas(etapas)
        config_existente.es_default = es_default
    else:
        # Crear nueva configuración
        nueva_config = ConfiguracionFlujoDeTrabajo(
            nombre=nombre,
            es_default=es_default
        )
        nueva_config.set_etapas(etapas)
        session.add(nueva_config)
    
    # Si esta configuración es predeterminada, actualizar las demás configuraciones
    if es_default:
        otras_configs = session.query(ConfiguracionFlujoDeTrabajo).filter(
            ConfiguracionFlujoDeTrabajo.nombre != nombre
        ).all()
        
        for otra_config in otras_configs:
            otra_config.es_default = False
    
    session.commit()
    session.close()

def actualizar_posicion_tarea(tarea_id, lista_id):
    Session = sessionmaker(bind=engine)
    session = Session()
    
    # Buscar la tarea
    tarea = session.query(Tarea).filter_by(id=tarea_id).first()
    
    if tarea:
        # Actualizar la lista de la tarea
        tarea.lista_id = lista_id
        
        # Obtener el nombre de la lista
        lista = session.query(Lista).filter_by(id=lista_id).first()
        if lista:
            # También actualizar el nombre de la lista en la respuesta
            nombre_lista = lista.nombre
        else:
            nombre_lista = "Desconocido"
        
        session.commit()
        session.close()
        
        return True, nombre_lista
    
    session.close()
    return False, None

# Cargar datos desde JSON de Trello y guardar en la base de datos
def cargar_datos_trello_a_db(tableros_json):
    """
    Carga los datos de tableros JSON de Trello a la base de datos.
    
    Args:
        tableros_json (list): Lista de diccionarios con datos de tableros JSON de Trello
    
    Returns:
        tuple: (int, int, int) - (tableros_guardados, listas_guardadas, tareas_guardadas)
    """
    tableros_guardados = 0
    listas_guardadas = 0
    tareas_guardadas = 0
    
    for tablero_json in tableros_json:
        # Guardar el tablero
        id_tablero = tablero_json.get('id', '')
        nombre_tablero = tablero_json.get('name', 'Tablero sin nombre')
        descripcion_tablero = tablero_json.get('desc', '')
        
        guardar_tablero(id_tablero, nombre_tablero, descripcion_tablero)
        tableros_guardados += 1
        
        # Guardar las listas
        if 'lists' in tablero_json:
            for posicion, lista in enumerate(tablero_json['lists']):
                if lista.get('closed', False):
                    continue  # Ignorar listas archivadas
                
                id_lista = lista.get('id', '')
                nombre_lista = lista.get('name', 'Lista sin nombre')
                
                guardar_lista(id_lista, nombre_lista, id_tablero, posicion)
                listas_guardadas += 1
        
        # Guardar las tarjetas
        if 'cards' in tablero_json:
            for tarjeta in tablero_json['cards']:
                if tarjeta.get('closed', False):
                    continue  # Ignorar tarjetas archivadas
                
                id_tarjeta = tarjeta.get('id', '')
                nombre_tarjeta = tarjeta.get('name', 'Tarea sin título')
                descripcion_tarjeta = tarjeta.get('desc', '')
                id_lista = tarjeta.get('idList', '')
                url_tarjeta = tarjeta.get('url', '')
                
                # Procesar etiquetas
                etiquetas = []
                if 'labels' in tarjeta and tarjeta['labels']:
                    etiquetas = [etiqueta.get('name', '') for etiqueta in tarjeta['labels'] if etiqueta.get('name')]
                
                # Procesar fechas
                fecha_vencimiento = None
                if 'due' in tarjeta and tarjeta['due']:
                    fecha_vencimiento = tarjeta['due']
                
                fecha_ultima_actividad = None
                if 'dateLastActivity' in tarjeta and tarjeta['dateLastActivity']:
                    fecha_ultima_actividad = tarjeta['dateLastActivity']
                
                # Crear diccionario de tarea
                tarea_dict = {
                    'id': id_tarjeta,
                    'nombre': nombre_tarjeta,
                    'descripcion': descripcion_tarjeta,
                    'tablero_id': id_tablero,
                    'lista_id': id_lista,
                    'etiquetas': etiquetas,
                    'fecha_vencimiento': fecha_vencimiento,
                    'fecha_ultima_actividad': fecha_ultima_actividad,
                    'url': url_tarjeta
                }
                
                guardar_tarea(tarea_dict)
                tareas_guardadas += 1
    
    return tableros_guardados, listas_guardadas, tareas_guardadas