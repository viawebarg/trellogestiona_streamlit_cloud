import requests
import logging

class TrelloAPI:
    """
    Class to interact with the Trello API for fetching and updating tasks.
    """
    def __init__(self, api_key, token):
        """
        Initialize with Trello API credentials.
        
        Args:
            api_key (str): Trello API key
            token (str): Trello API token
        """
        self.api_key = api_key
        self.token = token
        self.base_url = "https://api.trello.com/1"
        
    def _get_auth_params(self):
        """Return the authentication parameters for Trello API calls."""
        return {
            'key': self.api_key,
            'token': self.token
        }
    
    def get_boards(self):
        """
        Fetch all boards accessible to the user.
        
        Returns:
            list: List of board objects with id and name
        """
        url = f"{self.base_url}/members/me/boards"
        params = self._get_auth_params()
        
        try:
            response = requests.get(url, params=params)
            response.raise_for_status()
            
            boards = response.json()
            return [{'id': board['id'], 'name': board['name']} for board in boards]
        except requests.exceptions.RequestException as e:
            logging.error(f"Error fetching boards: {str(e)}")
            raise Exception(f"Failed to fetch boards: {str(e)}")
    
    def get_lists(self, board_id):
        """
        Fetch all lists for a specific board.
        
        Args:
            board_id (str): ID of the Trello board
            
        Returns:
            list: List of list objects with id and name
        """
        url = f"{self.base_url}/boards/{board_id}/lists"
        params = self._get_auth_params()
        
        try:
            response = requests.get(url, params=params)
            response.raise_for_status()
            
            lists = response.json()
            return [{'id': list_obj['id'], 'name': list_obj['name']} for list_obj in lists]
        except requests.exceptions.RequestException as e:
            logging.error(f"Error fetching lists: {str(e)}")
            raise Exception(f"Failed to fetch lists: {str(e)}")
    
    def get_cards(self, board_id):
        """
        Fetch all cards (tasks) for a specific board with detailed information.
        
        Args:
            board_id (str): ID of the Trello board
            
        Returns:
            list: List of card objects with detailed information
        """
        url = f"{self.base_url}/boards/{board_id}/cards"
        params = self._get_auth_params()
        params.update({
            'fields': 'id,name,desc,idList,labels,due,url,dateLastActivity',
            'members': 'true',
            'member_fields': 'fullName,username',
            'checklists': 'all'
        })
        
        try:
            response = requests.get(url, params=params)
            response.raise_for_status()
            
            cards = response.json()
            
            # Get lists to map list IDs to names
            lists = {list_obj['id']: list_obj['name'] for list_obj in self.get_lists(board_id)}
            
            # Add list name to each card
            for card in cards:
                if 'idList' in card and card['idList'] in lists:
                    card['list_name'] = lists[card['idList']]
                else:
                    card['list_name'] = 'Unknown'
            
            return cards
        except requests.exceptions.RequestException as e:
            logging.error(f"Error fetching cards: {str(e)}")
            raise Exception(f"Failed to fetch cards: {str(e)}")
    
    def update_card_list(self, card_id, list_id):
        """
        Move a card to a different list.
        
        Args:
            card_id (str): ID of the card to update
            list_id (str): ID of the destination list
            
        Returns:
            bool: True if successful, False otherwise
        """
        url = f"{self.base_url}/cards/{card_id}"
        params = self._get_auth_params()
        params.update({
            'idList': list_id
        })
        
        try:
            response = requests.put(url, params=params)
            response.raise_for_status()
            return True
        except requests.exceptions.RequestException as e:
            logging.error(f"Error updating card: {str(e)}")
            return False
    
    def get_list_id_by_name(self, board_id, list_name):
        """
        Get the ID of a list by its name.
        
        Args:
            board_id (str): ID of the Trello board
            list_name (str): Name of the list
            
        Returns:
            str: ID of the list, or None if not found
        """
        lists = self.get_lists(board_id)
        
        for list_obj in lists:
            if list_obj['name'] == list_name:
                return list_obj['id']
        
        return None
