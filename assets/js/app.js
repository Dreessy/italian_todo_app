class TodoApp {
    constructor() {
        this.apiUrl = 'api/tasks.php';
        this.currentEditId = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadTasks();
        this.loadStats();
    }

    bindEvents() {
        // Form submission
        document.getElementById('task-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit();
        });

        // Cancel edit button
        document.getElementById('cancel-btn').addEventListener('click', () => {
            this.cancelEdit();
        });
    }

    async loadTasks() {
        try {
            this.showLoading();
            const response = await fetch(this.apiUrl);
            const result = await response.json();
            
            if (result.success) {
                this.renderTasks(result.data);
            } else {
                this.showMessage('Errore nel caricamento dei task', 'error');
            }
        } catch (error) {
            console.error('Errore loadTasks:', error);
            this.showMessage('Errore di connessione nel caricamento task: ' + error.message, 'error');
        }
    }

    async loadStats() {
        try {
            const response = await fetch(`${this.apiUrl}?action=stats`);
            const result = await response.json();
            
            if (result.success) {
                this.renderStats(result.data);
            }
        } catch (error) {
            console.error('Errore nel caricamento delle statistiche:', error);
        }
    }

    renderStats(stats) {
        // Validate stats data before updating
        if (!stats || typeof stats !== 'object') {
            console.warn('Invalid stats data received');
            return;
        }
        
        // Update category counts with validation
        const healthCount = document.getElementById('health-count');
        const workCount = document.getElementById('work-count');
        const mentalCount = document.getElementById('mental-count');
        const othersCount = document.getElementById('others-count');
        
        if (healthCount) healthCount.textContent = parseInt(stats.health) || 0;
        if (workCount) workCount.textContent = parseInt(stats.work) || 0;
        if (mentalCount) mentalCount.textContent = parseInt(stats.mental) || 0;
        if (othersCount) othersCount.textContent = parseInt(stats.others) || 0;
    }

    renderTasks(tasks) {
        const container = document.getElementById('tasks-list');
        if (!container) {
            console.error('Element tasks-list not found');
            return;
        }
        
        if (tasks.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>Nessun task trovato</h3>
                    <p>Inizia creando il tuo primo task!</p>
                </div>
            `;
            return;
        }

        container.innerHTML = tasks.map(task => this.createTaskHTML(task)).join('');
        
        // Bind events for task actions
        this.bindTaskEvents();
    }

    createTaskHTML(task) {
        const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('it-IT') : '';
        const createdDate = new Date(task.created_at).toLocaleDateString('it-IT');
        const isOverdue = task.due_date && new Date(task.due_date) < new Date() && task.status === 'pending';
        
        return `
            <div class="task-item ${task.status}" data-id="${task.id}">
                <div class="task-header">
                    <h3 class="task-title">${this.escapeHtml(task.title)}</h3>
                    <div class="task-actions">
                        <button class="btn btn-sm ${task.status === 'pending' ? 'btn-success' : 'btn-warning'}" 
                                onclick="todoApp.toggleTask(${task.id})">
                            ${task.status === 'pending' ? '✓' : '↶'}
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="todoApp.editTask(${task.id})">
                            ✎
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="todoApp.deleteTask(${task.id})">
                            🗑
                        </button>
                    </div>
                </div>
                
                <div class="task-meta">
                    <span class="task-category category-${task.category || 'others'}">${this.getCategoryLabel(task.category || 'others')}</span>
                    <span class="task-priority priority-${task.priority}">${task.priority}</span>
                    <span class="task-status">Status: ${task.status === 'pending' ? 'Da fare' : 'Completato'}</span>
                    ${dueDate ? ` <span class="due-date ${isOverdue ? 'overdue' : ''}">Scadenza: ${dueDate}</span>` : ''}
                </div>
                
                ${task.description ? `<div class="task-description">${this.escapeHtml(task.description)}</div>` : ''}
                
                <div class="task-dates">
                    Creato: ${createdDate}
                    ${task.updated_at !== task.created_at ? ` • Modificato: ${new Date(task.updated_at).toLocaleDateString('it-IT')}` : ''}
                </div>
            </div>
        `;
    }

    getCategoryLabel(category) {
        const labels = {
            'health': 'Salute',
            'work': 'Lavoro', 
            'mental': 'Salute Mentale',
            'others': 'Altri'
        };
        return labels[category] || 'Altri';
    }

    bindTaskEvents() {
        // Events are bound via onclick in HTML for simplicity
        // In a larger app, you'd use event delegation
    }

    async handleFormSubmit() {
        const formData = this.getFormData();
        
        if (!formData.title.trim()) {
            this.showMessage('Il titolo è obbligatorio', 'error');
            return;
        }

        try {
            let response;
            
            if (this.currentEditId) {
                // Update existing task
                response = await fetch(`${this.apiUrl}?id=${this.currentEditId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
            } else {
                // Create new task
                response = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
            }

            const result = await response.json();
            
            if (result.success) {
                this.showMessage(result.message, 'success');
                this.resetForm();
                this.loadTasks();
                this.loadStats();
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Errore di connessione', 'error');
        }
    }

    async toggleTask(id) {
        try {
            const response = await fetch(`${this.apiUrl}?id=${id}&action=toggle`, {
                method: 'PUT'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage(result.message, 'success');
                this.loadTasks();
                this.loadStats();
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Errore di connessione', 'error');
        }
    }

    async editTask(id) {
        try {
            const response = await fetch(`${this.apiUrl}?action=single&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                this.populateForm(result.data);
                this.currentEditId = id;
                document.getElementById('form-title').textContent = 'Modifica Attività';
                document.getElementById('submit-btn').textContent = 'Aggiorna Attività';
                document.getElementById('cancel-btn').style.display = 'inline-block';
            } else {
                this.showMessage('Errore nel caricamento del task', 'error');
            }
        } catch (error) {
            this.showMessage('Errore di connessione', 'error');
        }
    }

    async deleteTask(id) {
        if (!confirm('Sei sicuro di voler eliminare questo task?')) {
            return;
        }

        try {
            const response = await fetch(`${this.apiUrl}?id=${id}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage(result.message, 'success');
                this.loadTasks();
                this.loadStats();
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('Errore di connessione', 'error');
        }
    }

    getFormData() {
        return {
            title: document.getElementById('title').value,
            description: document.getElementById('description').value,
            priority: document.getElementById('priority').value,
            category: document.getElementById('category').value,
            due_date: document.getElementById('due_date').value || null
        };
    }

    populateForm(task) {
        document.getElementById('title').value = task.title;
        document.getElementById('description').value = task.description || '';
        document.getElementById('priority').value = task.priority;
        document.getElementById('category').value = task.category || 'others';
        document.getElementById('due_date').value = task.due_date || '';
    }

    resetForm() {
        document.getElementById('task-form').reset();
        this.cancelEdit();
    }

    cancelEdit() {
        this.currentEditId = null;
        document.getElementById('form-title').textContent = 'Nuova Attività';
        document.getElementById('submit-btn').textContent = 'Aggiungi Attività';
        document.getElementById('cancel-btn').style.display = 'none';
        document.getElementById('task-form').reset();
    }

    showLoading() {
        const container = document.getElementById('tasks-list');
        if (container) {
            container.innerHTML = `
                <div class="loading">
                    <p>Caricamento task...</p>
                </div>
            `;
        }
    }

    showMessage(message, type) {
        const messageContainer = document.getElementById('message-container');
        if (!messageContainer) {
            console.error('Element message-container not found');
            return;
        }
        messageContainer.innerHTML = `
            <div class="message ${type}">
                ${message}
            </div>
        `;
        
        // Auto-hide message after 3 seconds
        setTimeout(() => {
            if (messageContainer) {
                messageContainer.innerHTML = '';
            }
        }, 3000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.todoApp = new TodoApp();
    
    // Set European date format for date input
    const dateInput = document.getElementById('due_date');
    if (dateInput) {
        // Set locale to Italian for European date format
        dateInput.setAttribute('lang', 'it-IT');
        
        // Add event listener to format date display
        dateInput.addEventListener('change', function() {
            if (this.value) {
                // The input value is always in YYYY-MM-DD format internally
                // but will display in dd/mm/yyyy format due to Italian locale
                console.log('Data selezionata:', this.value);
            }
        });
    }
});