<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Guest - store score temporarily
    $_SESSION['temp_score'] = [
        'score' => $_POST['score'] ?? 0,
        'total' => $_POST['total'] ?? 0,
        'category' => $_POST['category'] ?? 'Unknown',
        'quiz_name' => $_POST['quiz_name'] ?? 'Quiz'
    ];
    
    header("Location: auth/register.php?redirect=save_score");
    exit();
}

// User is logged in - save to database
$user_id = $_SESSION['user_id'];
$score = $_POST['score'] ?? 0;
$total = $_POST['total'] ?? 0;
$category = $_POST['category'] ?? 'Unknown';
$quiz_name = $_POST['quiz_name'] ?? 'Quiz';

try {
    include_once 'app/Database.php';
    $db = new Database();
    $conn = $db->conn;
    
    $stmt = $conn->prepare("INSERT INTO quiz_results (user_id, quiz_name, category, score, total_questions) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $quiz_name, $category, $score, $total]);
    
    header("Location: index.php?msg=score_saved");
    exit();
    
} catch (PDOException $e) {
    die("Error saving score: " . $e->getMessage());
}
?>