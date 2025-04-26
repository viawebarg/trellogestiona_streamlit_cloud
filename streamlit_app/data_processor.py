import pandas as pd
import numpy as np
from datetime import datetime

def process_tasks(cards):
    """
    Process Trello cards into a structured DataFrame.
    
    Args:
        cards (list): List of card objects from Trello API
        
    Returns:
        pandas.DataFrame: Processed DataFrame with tasks
    """
    # Create an empty list to store processed tasks
    tasks = []
    
    for card in cards:
        # Extract basic card information
        task = {
            'id': card.get('id', ''),
            'name': card.get('name', 'Untitled Task'),
            'description': card.get('desc', ''),
            'list_name': card.get('list_name', 'Unknown'),
            'url': card.get('url', ''),
        }
        
        # Extract labels
        if 'labels' in card and card['labels']:
            task['labels'] = [label.get('name', '') for label in card['labels'] if label.get('name')]
        else:
            task['labels'] = []
        
        # Extract due date
        if 'due' in card and card['due']:
            task['due_date'] = pd.to_datetime(card['due'])
        else:
            task['due_date'] = pd.NaT
        
        # Extract members
        if 'members' in card and card['members']:
            task['members'] = [member.get('fullName', member.get('username', '')) 
                              for member in card['members']]
        else:
            task['members'] = []
        
        # Extract checklists and calculate completion
        if 'checklists' in card and card['checklists']:
            total_items = 0
            completed_items = 0
            
            for checklist in card['checklists']:
                if 'checkItems' in checklist:
                    total_items += len(checklist['checkItems'])
                    completed_items += sum(1 for item in checklist['checkItems'] 
                                         if item.get('state') == 'complete')
            
            task['completion_percentage'] = (completed_items / total_items * 100) if total_items > 0 else 0
        else:
            task['completion_percentage'] = 0
        
        # Extract last activity date
        if 'dateLastActivity' in card and card['dateLastActivity']:
            task['last_activity'] = pd.to_datetime(card['dateLastActivity'])
        else:
            task['last_activity'] = pd.NaT
        
        # Add to the tasks list
        tasks.append(task)
    
    # Convert to DataFrame
    df = pd.DataFrame(tasks)
    
    # Calculate priority based on due date, labels, and list position
    df = prioritize_tasks(df)
    
    # Categorize tasks
    df = categorize_tasks(df)
    
    return df

def prioritize_tasks(df):
    """
    Assign priority levels to tasks based on due dates and other factors.
    
    Args:
        df (pandas.DataFrame): DataFrame containing tasks
        
    Returns:
        pandas.DataFrame: DataFrame with added priority column
    """
    # Create a copy to avoid SettingWithCopyWarning
    df = df.copy()
    
    # Initialize priority column
    df['priority'] = 'Medium'
    
    # Priority based on due date
    today = pd.Timestamp.now().normalize()
    
    # Overdue tasks (due date in the past)
    overdue_mask = (df['due_date'].notna()) & (df['due_date'] < today)
    df.loc[overdue_mask, 'priority'] = 'Critical'
    
    # Due today
    due_today_mask = (df['due_date'].notna()) & (df['due_date'] == today)
    df.loc[due_today_mask, 'priority'] = 'High'
    
    # Due within 3 days
    due_soon_mask = (df['due_date'].notna()) & (df['due_date'] > today) & (df['due_date'] <= today + pd.Timedelta(days=3))
    df.loc[due_soon_mask, 'priority'] = 'High'
    
    # Due in more than a week
    due_later_mask = (df['due_date'].notna()) & (df['due_date'] > today + pd.Timedelta(days=7))
    df.loc[due_later_mask, 'priority'] = 'Low'
    
    # Priority based on labels
    def check_priority_labels(labels):
        if not isinstance(labels, list):
            return False
            
        priority_keywords = ['urgent', 'important', 'critical', 'priority', 'high']
        return any(any(keyword in label.lower() for keyword in priority_keywords) for label in labels)
    
    # Upgrade priority based on labels
    priority_label_mask = df['labels'].apply(check_priority_labels)
    
    # Upgrade Medium to High, Low to Medium
    df.loc[(df['priority'] == 'Medium') & priority_label_mask, 'priority'] = 'High'
    df.loc[(df['priority'] == 'Low') & priority_label_mask, 'priority'] = 'Medium'
    
    return df

def categorize_tasks(df):
    """
    Categorize tasks based on labels, list position, and other factors.
    
    Args:
        df (pandas.DataFrame): DataFrame containing tasks
        
    Returns:
        pandas.DataFrame: DataFrame with added category column
    """
    # Create a copy to avoid SettingWithCopyWarning
    df = df.copy()
    
    # Initialize category column based on existing labels
    df['category'] = 'General'
    
    # Define category keywords mapping
    category_keywords = {
        'Development': ['dev', 'code', 'programming', 'feature', 'bug', 'fix'],
        'Design': ['design', 'ui', 'ux', 'visual', 'interface'],
        'Marketing': ['marketing', 'social', 'content', 'seo', 'campaign'],
        'Documentation': ['doc', 'documentation', 'guide', 'manual', 'wiki'],
        'Meeting': ['meeting', 'call', 'conference', 'discussion'],
        'Research': ['research', 'analysis', 'study', 'explore', 'investigation'],
        'Planning': ['plan', 'strategy', 'roadmap', 'backlog'],
        'Admin': ['admin', 'management', 'organize', 'setup']
    }
    
    # Function to determine category based on labels and description
    def determine_category(row):
        if not isinstance(row['labels'], list):
            return 'General'
            
        # Combine labels and task name for better categorization
        text_to_check = ' '.join(row['labels']).lower() + ' ' + row['name'].lower()
        
        for category, keywords in category_keywords.items():
            if any(keyword in text_to_check for keyword in keywords):
                return category
                
        return 'General'
    
    # Apply categorization
    df['category'] = df.apply(determine_category, axis=1)
    
    return df
