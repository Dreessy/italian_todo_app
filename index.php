<?php
// Initialize database and create tables if needed
require_once 'config/database.php';

try {
    $database = new Database();
    $database->createTables();
    $db_status = "Database connesso correttamente";
} catch (Exception $e) {
    // Show error on page for debugging
    $db_status = "Errore database: " . $e->getMessage();
    error_log('Database initialization error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App - Gestione Attività</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Todo App</h1>
            <p>Gestisci le tue attività giornaliere</p>
        </div>

        <div class="debug-info" style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px;">
            <strong>Status Database:</strong> <?php echo htmlspecialchars($db_status); ?>
        </div>

        <div id="message-container"></div>

        <section class="categories-section">
            <div class="categories-grid">
                <div class="category-card health" data-category="health">
                    <div class="category-icon">💚</div>
                    <div class="category-count" id="health-count">0</div>
                    <div class="category-label">Salute</div>
                </div>
                <div class="category-card work" data-category="work">
                    <div class="category-icon">💼</div>
                    <div class="category-count" id="work-count">0</div>
                    <div class="category-label">Lavoro</div>
                </div>
                <div class="category-card mental" data-category="mental">
                    <div class="category-icon">🧠</div>
                    <div class="category-count" id="mental-count">0</div>
                    <div class="category-label">Salute Mentale</div>
                </div>
                <div class="category-card others" data-category="others">
                    <div class="category-icon">📁</div>
                    <div class="category-count" id="others-count">0</div>
                    <div class="category-label">Altri</div>
                </div>
            </div>
        </section>

        <main class="main-content">
            <div class="task-form-container">
                <h2 id="form-title">Nuova Attività</h2>
                <form id="task-form" class="task-form">
                    <div class="form-group">
                        <label for="title">Titolo *</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Inserisci il titolo del task" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrizione</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Descrizione dettagliata (opzionale)" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select id="category" name="category" class="form-control">
                            <option value="health">💚 Salute</option>
                            <option value="work">💼 Lavoro</option>
                            <option value="mental">🧠 Salute Mentale</option>
                            <option value="others" selected>📁 Altri</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority">Priorità</label>
                        <select id="priority" name="priority" class="form-control">
                            <option value="low">Bassa Priorità</option>
                            <option value="medium" selected>Media Priorità</option>
                            <option value="high">Alta Priorità</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Data di scadenza</label>
                        <input type="date" id="due_date" name="due_date" class="form-control" placeholder="gg/mm/aaaa">
                    </div>
                    <div class="form-actions">
                        <button type="submit" id="submit-btn" class="btn btn-primary">Aggiungi Task</button>
                        <button type="button" id="cancel-btn" class="btn btn-warning" style="display: none;">Annulla</button>
                    </div>
                </form>
            </div>
            
            <!-- Lista delle attività -->
            <div class="tasks-container">
                <div class="tasks-header">
                    <h2>Le tue attività</h2>
                </div>
                <div id="tasks-list"></div>
            </div>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
    <!-- L'app viene inizializzata automaticamente da app.js -->
</body>
</html>