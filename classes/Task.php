<?php
require_once '../config/database.php';

class Task {
    private $db;
    private $connection;
    
    public function __construct() {
        $this->db = new Database();
        $this->connection = $this->db->getConnection();
    }
    
    // Ottieni tutti i task di un utente
    public function getAllTasks($userId = 1) {
        try {
            $query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Ottieni un task specifico
    public function getTask($id) {
        try {
            $query = "SELECT * FROM tasks WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Crea nuovo task
    public function createTask($data) {
        try {
            $query = "INSERT INTO tasks (user_id, title, description, category, priority, due_date) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute([
                $data['user_id'] ?? 1,
                $data['title'],
                $data['description'] ?? '',
                $data['category'] ?? 'others',
                $data['priority'] ?? 'medium',
                $data['due_date'] ?? null
            ]);
            
            if ($result) {
                return $this->connection->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Aggiorna task
    public function updateTask($id, $data) {
        try {
            $query = "UPDATE tasks SET title = ?, description = ?, category = ?, priority = ?, due_date = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            return $stmt->execute([
                $data['title'],
                $data['description'] ?? '',
                $data['category'] ?? 'others',
                $data['priority'] ?? 'medium',
                $data['due_date'] ?? null,
                $id
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Cambia status del task
    public function toggleStatus($id) {
        try {
            $query = "UPDATE tasks SET status = CASE WHEN status = 'pending' THEN 'completed' ELSE 'pending' END WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Elimina task
    public function deleteTask($id) {
        try {
            $query = "DELETE FROM tasks WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Statistiche task
    public function getStats($userId = 1) {
        try {
            $query = "
                SELECT 
                    SUM(CASE WHEN category = 'health' THEN 1 ELSE 0 END) as health,
                    SUM(CASE WHEN category = 'work' THEN 1 ELSE 0 END) as work,
                    SUM(CASE WHEN category = 'mental' THEN 1 ELSE 0 END) as mental,
                    SUM(CASE WHEN category = 'others' THEN 1 ELSE 0 END) as others
                FROM tasks WHERE user_id = ?
            ";
            $stmt = $this->connection->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>