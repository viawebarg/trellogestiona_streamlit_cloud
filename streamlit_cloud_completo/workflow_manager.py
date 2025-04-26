def create_workflow(workflow_stages=None):
    """
    Create a workflow with the specified stages or return default stages.
    
    Args:
        workflow_stages (list, optional): List of workflow stage names
        
    Returns:
        list: List of workflow stage names
    """
    default_stages = ['To Do', 'In Progress', 'Review', 'Done']
    
    if workflow_stages and isinstance(workflow_stages, list) and len(workflow_stages) > 0:
        return workflow_stages
    
    return default_stages

def update_task_status(trello_api, card_id, target_list_name, board_id):
    """
    Update the status of a task by moving it to a different list.
    
    Args:
        trello_api (TrelloAPI): Initialized TrelloAPI instance
        card_id (str): ID of the card to update
        target_list_name (str): Name of the target list
        board_id (str): ID of the board containing the lists
        
    Returns:
        bool: True if successful, False otherwise
    """
    try:
        # Get the ID of the target list
        target_list_id = trello_api.get_list_id_by_name(board_id, target_list_name)
        
        if not target_list_id:
            return False
        
        # Update the card's list
        return trello_api.update_card_list(card_id, target_list_id)
    except Exception as e:
        print(f"Error updating task status: {str(e)}")
        return False

def get_task_workflow_stage(task, workflow_stages):
    """
    Determine the current workflow stage of a task.
    
    Args:
        task (dict): Task dictionary with list_name
        workflow_stages (list): List of workflow stage names
        
    Returns:
        str: Current workflow stage name or None if not in workflow
    """
    if 'list_name' in task and task['list_name'] in workflow_stages:
        return task['list_name']
    
    return None

def get_next_workflow_stage(current_stage, workflow_stages):
    """
    Get the next stage in the workflow.
    
    Args:
        current_stage (str): Current workflow stage name
        workflow_stages (list): List of workflow stage names
        
    Returns:
        str: Next workflow stage name or None if at last stage
    """
    if current_stage in workflow_stages:
        current_index = workflow_stages.index(current_stage)
        
        if current_index < len(workflow_stages) - 1:
            return workflow_stages[current_index + 1]
    
    return None

def get_previous_workflow_stage(current_stage, workflow_stages):
    """
    Get the previous stage in the workflow.
    
    Args:
        current_stage (str): Current workflow stage name
        workflow_stages (list): List of workflow stage names
        
    Returns:
        str: Previous workflow stage name or None if at first stage
    """
    if current_stage in workflow_stages:
        current_index = workflow_stages.index(current_stage)
        
        if current_index > 0:
            return workflow_stages[current_index - 1]
    
    return None
