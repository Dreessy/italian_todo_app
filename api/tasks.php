<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../classes/Task.php';

$task = new Task();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        if (isset($_GET['action'])) {
            switch($_GET['action']) {
                case 'stats':
                    $stats = $task->getStats();
                    echo json_encode(['success' => true, 'data' => $stats]);
                    break;
                case 'single':
                    if (isset($_GET['id'])) {
                        $taskData = $task->getTask($_GET['id']);
                        if ($taskData) {
                            echo json_encode(['success' => true, 'data' => $taskData]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Task non trovato']);
                        }
                    }
                    break;
                default:
                    $tasks = $task->getAllTasks();
                    echo json_encode(['success' => true, 'data' => $tasks]);
            }
        } else {
            $tasks = $task->getAllTasks();
            echo json_encode(['success' => true, 'data' => $tasks]);
        }
        break;
        
    case 'POST':
        if (isset($input['title']) && !empty(trim($input['title']))) {
            $taskId = $task->createTask($input);
            if ($taskId) {
                $newTask = $task->getTask($taskId);
                echo json_encode(['success' => true, 'message' => 'Task creato con successo', 'data' => $newTask]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nella creazione del task']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Titolo del task richiesto']);
        }
        break;
        
    case 'PUT':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            if (isset($_GET['action']) && $_GET['action'] === 'toggle') {
                // Toggle status
                if ($task->toggleStatus($id)) {
                    $updatedTask = $task->getTask($id);
                    echo json_encode(['success' => true, 'message' => 'Status aggiornato', 'data' => $updatedTask]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiornamento dello status']);
                }
            } else {
                // Update task
                if (isset($input['title']) && !empty(trim($input['title']))) {
                    if ($task->updateTask($id, $input)) {
                        $updatedTask = $task->getTask($id);
                        echo json_encode(['success' => true, 'message' => 'Task aggiornato con successo', 'data' => $updatedTask]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiornamento del task']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Titolo del task richiesto']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID del task richiesto']);
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['id'])) {
            if ($task->deleteTask($_GET['id'])) {
                echo json_encode(['success' => true, 'message' => 'Task eliminato con successo']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore nell\'eliminazione del task']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID del task richiesto']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Metodo non supportato']);
        break;
}
?>