<?php
/**
 * Configurazione Database - To-Do List Application
 * 
 * Questo file gestisce la connessione al database e la creazione delle tabelle.
 * Include la gestione automatica della colonna 'category' per la categorizzazione dei task.
 * 
 * Caratteristiche:
 * - Creazione automatica del database se non esiste
 * - Creazione automatica delle tabelle users e tasks
 * - Verifica e aggiunta automatica della colonna 'category' per compatibilità
 * - Inserimento di un utente admin di default
 * 
 * @author Portfolio Project
 * @version 2.0
 */

// Configurazione database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sito_2');

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            // Prima connessione senza database per verificare/creare
            $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Crea database se non esiste
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Connessione al database specifico
            $this->connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Crea tabelle se non esistono
            $this->createTables();
            
        } catch(PDOException $e) {
            die("Errore di connessione: " . $e->getMessage());
        }
    }
    
    public function createTables() {
        // Tabella utenti (se serve per login)
        $userTable = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        
        // Tabella tasks
        $taskTable = "
            CREATE TABLE IF NOT EXISTS tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT DEFAULT 1,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                category ENUM('health', 'work', 'mental', 'others') DEFAULT 'others',
                status ENUM('pending', 'completed') DEFAULT 'pending',
                priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
                due_date DATE NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        
        $this->connection->exec($userTable);
        $this->connection->exec($taskTable);
        
        // Verifica e aggiungi colonna category se non esiste
        // Questo metodo assicura la compatibilità con versioni precedenti del database
        // e garantisce che la colonna category sia sempre presente per il sistema di categorizzazione
        $this->ensureCategoryColumn();
        
        // Inserisci utente di default se non esiste
        $checkUser = $this->connection->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($checkUser == 0) {
            $defaultUser = "
                INSERT INTO users (username, email, password) 
                VALUES ('admin', 'admin@todo.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "')
            ";
            $this->connection->exec($defaultUser);
        }
    }
    
    private function ensureCategoryColumn() {
        try {
            // Verifica se la colonna category esiste già
            $stmt = $this->connection->prepare("
                SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = 'tasks' 
                AND COLUMN_NAME = 'category'
            ");
            $stmt->execute([DB_NAME]);
            $columnExists = $stmt->fetchColumn() > 0;
            
            // Se la colonna non esiste, aggiungila
            if (!$columnExists) {
                $alterQuery = "
                    ALTER TABLE tasks 
                    ADD COLUMN category ENUM('health', 'work', 'mental', 'others') DEFAULT 'others' 
                    AFTER description
                ";
                $this->connection->exec($alterQuery);
                
                // Log per debug (opzionale)
                error_log("Colonna 'category' aggiunta con successo alla tabella 'tasks'");
            }
        } catch(PDOException $e) {
            // Log dell'errore ma non interrompere l'esecuzione
            error_log("Errore durante la verifica/aggiunta della colonna category: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
?>