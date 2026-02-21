<?php
// admin_ajax.php - Handles AJAX requests from admin.js

session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

include_once 'app/Database.php';
$db = new Database();
$conn = $db->conn;

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_quiz':
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? '';
        $questions = $_POST['questions'] ?? 10;
        
        try {
            $stmt = $conn->prepare("INSERT INTO quizzes (title, category, question_count) VALUES (?, ?, ?)");
            $stmt->execute([$title, $category, $questions]);
            echo json_encode(['success' => true, 'message' => 'Quiz added successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'delete_quiz':
        $id = $_POST['id'] ?? 0;
        
        try {
            $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Quiz deleted']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'delete_user':
        $id = $_POST['id'] ?? 0;
        
        try {
            $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'User deleted']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
?>