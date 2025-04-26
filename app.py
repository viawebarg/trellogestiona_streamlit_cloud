import os
import streamlit as st
import pandas as pd
import plotly.express as px
from trello_api import TrelloAPI
from data_processor import process_tasks, prioritize_tasks, categorize_tasks
from workflow_manager import create_workflow, update_task_status

# Page configuration
st.set_page_config(
    page_title="Trello Task Manager",
    page_icon="📋",
    layout="wide",
    initial_sidebar_state="expanded"
)

# Initialize session state for storing data
if 'trello_data' not in st.session_state:
    st.session_state.trello_data = None
if 'filtered_data' not in st.session_state:
    st.session_state.filtered_data = None
if 'workflow_stages' not in st.session_state:
    st.session_state.workflow_stages = ['To Do', 'In Progress', 'Review', 'Done']
if 'api_configured' not in st.session_state:
    st.session_state.api_configured = False
if 'boards' not in st.session_state:
    st.session_state.boards = []
if 'selected_board' not in st.session_state:
    st.session_state.selected_board = None

# Title and description
st.title("Trello Task Manager")
st.write("Efficiently process, organize, and manage your Trello tasks with this streamlined workflow system.")

# Sidebar for API configuration and data loading
with st.sidebar:
    st.header("Configuration")
    
    # API Key and Token inputs
    trello_api_key = st.text_input("Trello API Key", type="password", value=os.getenv("TRELLO_API_KEY", ""))
    trello_token = st.text_input("Trello Token", type="password", value=os.getenv("TRELLO_TOKEN", ""))
    
    # Test connection button
    if st.button("Test Connection"):
        if trello_api_key and trello_token:
            trello_api = TrelloAPI(trello_api_key, trello_token)
            try:
                boards = trello_api.get_boards()
                st.session_state.boards = boards
                st.session_state.api_configured = True
                st.success("Connection successful! Found {} boards.".format(len(boards)))
            except Exception as e:
                st.error(f"Connection failed: {str(e)}")
        else:
            st.error("Please enter both API key and token.")
    
    # Board selection (only shown if connection is successful)
    if st.session_state.api_configured and st.session_state.boards:
        board_options = {board['name']: board['id'] for board in st.session_state.boards}
        selected_board_name = st.selectbox("Select Board", list(board_options.keys()))
        
        if selected_board_name:
            selected_board_id = board_options[selected_board_name]
            st.session_state.selected_board = {"id": selected_board_id, "name": selected_board_name}
            
            # Load cards button
            if st.button("Load Tasks"):
                with st.spinner("Loading tasks from Trello..."):
                    trello_api = TrelloAPI(trello_api_key, trello_token)
                    cards = trello_api.get_cards(selected_board_id)
                    
                    if cards:
                        # Process the cards and store in session state
                        tasks_df = process_tasks(cards)
                        st.session_state.trello_data = tasks_df
                        st.session_state.filtered_data = tasks_df.copy()
                        st.success(f"Successfully loaded {len(tasks_df)} tasks!")
                    else:
                        st.warning("No tasks found on this board.")
    
    # Export options
    st.header("Export Options")
    if st.session_state.filtered_data is not None:
        if st.button("Export to CSV"):
            csv = st.session_state.filtered_data.to_csv(index=False)
            st.download_button(
                label="Download CSV",
                data=csv,
                file_name="trello_tasks.csv",
                mime="text/csv"
            )
        
        if st.button("Export to Excel"):
            # Create Excel file in memory
            buffer = pd.io.excel.BytesIO()
            with pd.ExcelWriter(buffer) as writer:
                st.session_state.filtered_data.to_excel(writer, index=False, sheet_name="Tasks")
            
            st.download_button(
                label="Download Excel",
                data=buffer.getvalue(),
                file_name="trello_tasks.xlsx",
                mime="application/vnd.ms-excel"
            )

# Main content area
if st.session_state.trello_data is not None:
    # Tabs for different views
    tab1, tab2, tab3 = st.tabs(["Task Dashboard", "Workflow View", "Analytics"])
    
    # Task Dashboard Tab
    with tab1:
        st.header("Task Dashboard")
        
        # Filters
        col1, col2, col3 = st.columns(3)
        
        with col1:
            # Filter by priority
            priorities = ['All'] + sorted(st.session_state.trello_data['priority'].unique().tolist())
            priority_filter = st.multiselect("Filter by Priority", priorities, default='All')
        
        with col2:
            # Filter by label/category
            all_labels = []
            for labels in st.session_state.trello_data['labels'].dropna():
                if isinstance(labels, list):
                    all_labels.extend(labels)
            unique_labels = ['All'] + sorted(list(set(all_labels)))
            label_filter = st.multiselect("Filter by Label", unique_labels, default='All')
        
        with col3:
            # Search by name
            search_query = st.text_input("Search Tasks", "")
        
        # Apply filters
        filtered_df = st.session_state.trello_data.copy()
        
        # Priority filter
        if priority_filter and 'All' not in priority_filter:
            filtered_df = filtered_df[filtered_df['priority'].isin(priority_filter)]
        
        # Label filter
        if label_filter and 'All' not in label_filter:
            filtered_df = filtered_df[filtered_df['labels'].apply(
                lambda x: isinstance(x, list) and any(label in x for label in label_filter)
            )]
        
        # Search filter
        if search_query:
            filtered_df = filtered_df[filtered_df['name'].str.contains(search_query, case=False)]
        
        # Update filtered data in session state
        st.session_state.filtered_data = filtered_df
        
        # Display filtered tasks
        if not filtered_df.empty:
            st.write(f"Showing {len(filtered_df)} tasks")
            st.dataframe(filtered_df[['name', 'list_name', 'priority', 'labels', 'due_date', 'url']], 
                         height=400,
                         column_config={
                             "name": "Task Name",
                             "list_name": "List",
                             "priority": "Priority",
                             "labels": "Labels",
                             "due_date": "Due Date",
                             "url": st.column_config.LinkColumn("Trello Link")
                         })
        else:
            st.warning("No tasks match the current filters.")
    
    # Workflow View Tab
    with tab2:
        st.header("Workflow Management")
        
        # Create columns for each workflow stage
        columns = st.columns(len(st.session_state.workflow_stages))
        
        # Display tasks in each column based on their stage
        for i, stage in enumerate(st.session_state.workflow_stages):
            with columns[i]:
                st.subheader(stage)
                stage_tasks = filtered_df[filtered_df['list_name'] == stage]
                
                if not stage_tasks.empty:
                    for _, task in stage_tasks.iterrows():
                        with st.container():
                            st.markdown(f"**{task['name']}**")
                            
                            # Show labels if available
                            if isinstance(task['labels'], list) and task['labels']:
                                st.markdown(f"Labels: {', '.join(task['labels'])}")
                            
                            # Show due date if available
                            if pd.notna(task['due_date']):
                                st.markdown(f"Due: {task['due_date'].strftime('%Y-%m-%d')}")
                            
                            # Buttons for moving tasks
                            cols = st.columns(2)
                            
                            # Only show left move button if not the first stage
                            if i > 0:
                                if cols[0].button(f"← Move", key=f"left_{task['id']}"):
                                    prev_stage = st.session_state.workflow_stages[i-1]
                                    trello_api = TrelloAPI(trello_api_key, trello_token)
                                    success = update_task_status(trello_api, task['id'], prev_stage, st.session_state.selected_board['id'])
                                    if success:
                                        st.success(f"Moved task to {prev_stage}")
                                        st.rerun()
                            
                            # Only show right move button if not the last stage
                            if i < len(st.session_state.workflow_stages) - 1:
                                if cols[1].button(f"Move →", key=f"right_{task['id']}"):
                                    next_stage = st.session_state.workflow_stages[i+1]
                                    trello_api = TrelloAPI(trello_api_key, trello_token)
                                    success = update_task_status(trello_api, task['id'], next_stage, st.session_state.selected_board['id'])
                                    if success:
                                        st.success(f"Moved task to {next_stage}")
                                        st.rerun()
                            
                            st.markdown("---")
                else:
                    st.caption("No tasks in this stage")
    
    # Analytics Tab
    with tab3:
        st.header("Task Analytics")
        
        if not filtered_df.empty:
            col1, col2 = st.columns(2)
            
            with col1:
                # Tasks by priority
                priority_counts = filtered_df['priority'].value_counts().reset_index()
                priority_counts.columns = ['Priority', 'Count']
                
                fig1 = px.pie(priority_counts, values='Count', names='Priority', 
                              title='Tasks by Priority',
                              color_discrete_sequence=px.colors.qualitative.Set3)
                st.plotly_chart(fig1)
            
            with col2:
                # Tasks by status
                status_counts = filtered_df['list_name'].value_counts().reset_index()
                status_counts.columns = ['Status', 'Count']
                
                fig2 = px.bar(status_counts, x='Status', y='Count',
                              title='Tasks by Status',
                              color='Status',
                              color_discrete_sequence=px.colors.qualitative.Pastel)
                st.plotly_chart(fig2)
            
            # Tasks by due date (if available)
            if 'due_date' in filtered_df.columns and filtered_df['due_date'].notna().any():
                # Filter out rows with NaN due dates
                due_date_df = filtered_df.dropna(subset=['due_date'])
                
                if not due_date_df.empty:
                    # Convert to datetime if not already
                    due_date_df['due_date'] = pd.to_datetime(due_date_df['due_date'])
                    
                    # Sort by due date
                    due_date_df = due_date_df.sort_values('due_date')
                    
                    fig3 = px.timeline(due_date_df, x_start='due_date', y='name',
                                      color='priority', title='Task Timeline by Due Date',
                                      color_discrete_sequence=px.colors.qualitative.Pastel)
                    
                    # Customize layout
                    fig3.update_yaxes(autorange="reversed")
                    fig3.update_layout(height=400)
                    
                    st.plotly_chart(fig3, use_container_width=True)
                else:
                    st.info("No tasks with due dates available for timeline visualization.")
            else:
                st.info("No due dates available for timeline visualization.")
        else:
            st.warning("No data available for analytics.")

else:
    # Show instructions when no data is loaded
    st.info("Welcome to the Trello Task Manager! Follow these steps to get started:")
    
    col1, col2, col3 = st.columns(3)
    
    with col1:
        st.markdown("### 1. Connect to Trello")
        st.markdown("""
        - Enter your Trello API key and token in the sidebar
        - Test the connection to verify your credentials
        """)
    
    with col2:
        st.markdown("### 2. Select a Board")
        st.markdown("""
        - Choose which Trello board to work with
        - Load the tasks from that board
        """)
    
    with col3:
        st.markdown("### 3. Manage Tasks")
        st.markdown("""
        - Filter and sort your tasks
        - Update task status through the workflow view
        - Analyze task distribution
        - Export your organized tasks
        """)
    
    st.markdown("---")
    st.markdown("""
    ### How to get your Trello API Key and Token
    
    1. Log in to your Trello account
    2. Visit: https://trello.com/app-key to get your API key
    3. Click on the "Token" link on that page to generate a token
    4. Copy both the API key and token to the sidebar inputs
    """)
