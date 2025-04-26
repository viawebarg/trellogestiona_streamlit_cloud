import requests
import json
import os
import streamlit as st

class DolibarrAPIClient:
    """
    Cliente para comunicarse con la API REST del módulo TrelloGestiona en Dolibarr.
    """
    
    def __init__(self, dolibarr_url=None, api_token=None):
        """
        Inicializa el cliente de la API de Dolibarr.
        
        Args:
            dolibarr_url (str, optional): URL base de Dolibarr (ej: 'https://mi-dolibarr.com')
            api_token (str, optional): Token de autenticación para la API
        """
        # Intentar obtener los valores de las variables de entorno si no se proporcionan
        self.dolibarr_url = dolibarr_url or os.environ.get('DOLIBARR_URL', '')
        self.api_token = api_token or os.environ.get('DOLIBARR_API_TOKEN', '')
        
        # Construir la URL completa del endpoint de la API
        if self.dolibarr_url:
            self.api_endpoint = f"{self.dolibarr_url}/custom/trellogestiona/api/api.php"
        else:
            self.api_endpoint = ""
    
    def is_configured(self):
        """Verifica si el cliente está correctamente configurado."""
        return bool(self.dolibarr_url and self.api_token and self.api_endpoint)
    
    def get_projects(self):
        """
        Obtiene la lista de proyectos desde Dolibarr.
        
        Returns:
            list: Lista de proyectos o lista vacía si hay error
        """
        if not self.is_configured():
            st.warning("Cliente API de Dolibarr no configurado. Establezca DOLIBARR_URL y DOLIBARR_API_TOKEN.")
            return []
            
        url = f"{self.api_endpoint}?api_key={self.api_token}&action=get_projects"
        
        try:
            response = requests.get(url, timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    return data.get("data", [])
                else:
                    st.error(f"Error al obtener proyectos: {data.get('error', 'Error desconocido')}")
            else:
                st.error(f"Error en la petición HTTP: {response.status_code}")
        except Exception as e:
            st.error(f"Error de comunicación con Dolibarr: {str(e)}")
        
        return []
    
    def get_linked_boards(self):
        """
        Obtiene los tableros de Trello vinculados a proyectos de Dolibarr.
        
        Returns:
            list: Lista de vinculaciones tablero-proyecto o lista vacía si hay error
        """
        if not self.is_configured():
            return []
            
        url = f"{self.api_endpoint}?api_key={self.api_token}&action=get_linked_boards"
        
        try:
            response = requests.get(url, timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get("success"):
                    return data.get("data", [])
                else:
                    st.error(f"Error al obtener tableros vinculados: {data.get('error', 'Error desconocido')}")
            else:
                st.error(f"Error en la petición HTTP: {response.status_code}")
        except Exception as e:
            st.error(f"Error de comunicación con Dolibarr: {str(e)}")
        
        return []
    
    def link_project_board(self, project_id, board_id, board_name=None):
        """
        Vincula un proyecto de Dolibarr con un tablero de Trello.
        
        Args:
            project_id (str): ID del proyecto en Dolibarr
            board_id (str): ID del tablero en Trello
            board_name (str, optional): Nombre del tablero
            
        Returns:
            bool: True si la operación fue exitosa, False en caso contrario
        """
        if not self.is_configured():
            return False
            
        data = {
            "api_key": self.api_token,
            "action": "link_project_board",
            "project_id": project_id,
            "board_id": board_id
        }
        
        if board_name:
            data["board_name"] = board_name
        
        try:
            response = requests.post(self.api_endpoint, data=data, timeout=10)
            if response.status_code == 200:
                result = response.json()
                if result.get("success"):
                    return True
                else:
                    st.error(f"Error al vincular: {result.get('error', 'Error desconocido')}")
            else:
                st.error(f"Error en la petición HTTP: {response.status_code}")
        except Exception as e:
            st.error(f"Error de comunicación con Dolibarr: {str(e)}")
        
        return False
    
    def unlink_project_board(self, project_id):
        """
        Desvincula un proyecto de Dolibarr de su tablero de Trello.
        
        Args:
            project_id (str): ID del proyecto en Dolibarr
            
        Returns:
            bool: True si la operación fue exitosa, False en caso contrario
        """
        if not self.is_configured():
            return False
            
        data = {
            "api_key": self.api_token,
            "action": "unlink_project_board",
            "project_id": project_id
        }
        
        try:
            response = requests.post(self.api_endpoint, data=data, timeout=10)
            if response.status_code == 200:
                result = response.json()
                if result.get("success"):
                    return True
                else:
                    st.error(f"Error al desvincular: {result.get('error', 'Error desconocido')}")
            else:
                st.error(f"Error en la petición HTTP: {response.status_code}")
        except Exception as e:
            st.error(f"Error de comunicación con Dolibarr: {str(e)}")
        
        return False
    
    def sync_tasks(self, project_id, board_id, tasks):
        """
        Sincroniza tareas de Trello con un proyecto de Dolibarr.
        
        Args:
            project_id (str): ID del proyecto en Dolibarr
            board_id (str): ID del tablero en Trello
            tasks (list): Lista de tareas a sincronizar
            
        Returns:
            bool: True si la operación fue exitosa, False en caso contrario
        """
        if not self.is_configured():
            return False
            
        data = {
            "api_key": self.api_token,
            "action": "sync_tasks",
            "project_id": project_id,
            "board_id": board_id,
            "tasks": json.dumps(tasks)
        }
        
        try:
            response = requests.post(self.api_endpoint, data=data, timeout=30)
            if response.status_code == 200:
                result = response.json()
                if result.get("success"):
                    return True
                else:
                    st.error(f"Error al sincronizar tareas: {result.get('error', 'Error desconocido')}")
            else:
                st.error(f"Error en la petición HTTP: {response.status_code}")
        except Exception as e:
            st.error(f"Error de comunicación con Dolibarr: {str(e)}")
        
        return False

# Función para crear un cliente Dolibarr desde la sesión de Streamlit
def get_dolibarr_client():
    """
    Obtiene o crea un cliente de API de Dolibarr utilizando la sesión de Streamlit.
    
    Returns:
        DolibarrAPIClient: Cliente configurado con los valores de la sesión
    """
    if 'dolibarr_client' not in st.session_state:
        # Obtener configuración de la sesión o de valores por defecto
        dolibarr_url = st.session_state.get('dolibarr_url', os.environ.get('DOLIBARR_URL', ''))
        api_token = st.session_state.get('dolibarr_api_token', os.environ.get('DOLIBARR_API_TOKEN', ''))
        
        # Crear y guardar el cliente en la sesión
        st.session_state.dolibarr_client = DolibarrAPIClient(dolibarr_url, api_token)
    
    return st.session_state.dolibarr_client

# Ejemplo de uso:
# client = get_dolibarr_client()
# projects = client.get_projects()