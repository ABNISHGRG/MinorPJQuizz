<?php
// Start session and check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?error=admin_only");
    exit();
}

$admin_name = $_SESSION['user_name'];

// Database connection
include_once 'app/Database.php';

$db = new Database();
$conn = $db->conn;

// Get statistics
try {
    // Total Users
    $stmt = $conn->query("SELECT COUNT(*) as total FROM user");
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total Quizzes
    $stmt = $conn->query("SELECT COUNT(*) as total FROM quizzes");
    $total_quizzes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total Categories
    $stmt = $conn->query("SELECT COUNT(DISTINCT category) as total FROM quizzes");
    $total_categories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total Plays
    $stmt = $conn->query("SELECT COUNT(*) as total FROM quiz_results");
    $total_plays = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Popular Quizzes
    $stmt = $conn->query("
        SELECT q.id, q.title, q.category, q.question_count, 
               COALESCE(COUNT(r.id), 0) as play_count
        FROM quizzes q
        LEFT JOIN quiz_results r ON q.id = r.quiz_id
        GROUP BY q.id
        ORDER BY play_count DESC
        LIMIT 6
    ");
    $popular_quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent Users
    $stmt = $conn->query("SELECT name, email, created_at FROM user ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $total_users = 0;
    $total_quizzes = 0;
    $total_categories = 0;
    $total_plays = 0;
    $popular_quizzes = [];
    $recent_users = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>QuiZone Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-logo">QuiZone <span>Admin</span></div>
    <button class="nav-toggle" onclick="toggleMenu()">☰</button>
    <!-- Add this after <aside class="sidebar"> -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="auth/logout.php" class="nav-btn">Logout (<?php echo htmlspecialchars($admin_name); ?>)</a></li>
    </ul>
</nav>

<!-- Sidebar -->
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li><a href="admin.php" class="sidebar-item" data-page="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="admin_categories.php" class="sidebar-item" data-page="categories"><i class="fas fa-list"></i> Categories</a></li>
        <li><a href="admin_questions.php" class="sidebar-item" data-page="questions"><i class="fas fa-question-circle"></i> Questions</a></li>
        <li><a href="#" class="sidebar-item" data-page="users"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="#" class="sidebar-item" data-page="reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="index.php" class="sidebar-item"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
    </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Quizzes</h3>
                <p><?php echo $total_quizzes; ?></p>
            </div>
            <div class="stat-icon icon-blue"><i class="fas fa-clipboard-list"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Users</h3>
                <p><?php echo number_format($total_users); ?></p>
            </div>
            <div class="stat-icon icon-green"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Categories</h3>
                <p><?php echo $total_categories; ?></p>
            </div>
            <div class="stat-icon icon-orange"><i class="fas fa-folder"></i></div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Plays</h3>
                <p><?php echo number_format($total_plays); ?></p>
            </div>
            <div class="stat-icon icon-purple"><i class="fas fa-play"></i></div>
        </div>
    </div>

    <!-- Popular Quizzes -->
    <div class="content-wrapper">
        <div class="section-header">
            <h2>Popular Quizzes</h2>
            <a href="#" class="btn-add" onclick="showAddQuizModal()">+ Add Quiz</a>
        </div>
        
        <?php if (count($popular_quizzes) > 0): ?>
            <div class="quiz-grid">
                <?php foreach ($popular_quizzes as $quiz): ?>
                <div class="quiz-card">
                    <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                    <p><?php echo htmlspecialchars($quiz['category']); ?> • <?php echo $quiz['question_count']; ?> Questions</p>
                    <div class="quiz-meta">
                        <span class="quiz-plays"><i class="fas fa-play"></i> <?php echo number_format($quiz['play_count']); ?> plays</span>
                        <div class="action-btns">
                            <button class="play-btn" onclick="editQuiz(<?php echo $quiz['id']; ?>)">Edit</button>
                            <button class="play-btn" style="background:#ef4444;" onclick="deleteQuiz(<?php echo $quiz['id']; ?>)">Delete</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No quizzes yet. Click "+ Add Quiz" to create one!</p>
        <?php endif; ?>
    </div>

    <!-- Users Table -->
    <div class="content-wrapper">
        <div class="section-header">
            <h2>Recent Users</h2>
            <a href="#" class="btn-add">View All</a>
        </div>
        
        <?php if (count($recent_users) > 0): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn btn-edit" onclick="editUser(<?php echo $user['id'] ?? 0; ?>)">Edit</button>
                                <button class="action-btn btn-delete" onclick="deleteUser(<?php echo $user['id'] ?? 0; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No users registered yet.</p>
        <?php endif; ?>
    </div>

</div>

<!-- Modal for Add Quiz -->
<div id="quizModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Quiz</h2>
        <form id="quizForm">
            <div class="form-group">
                <label>Quiz Title</label>
                <input type="text" id="quizTitle" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select id="quizCategory" required>
                    <option value="">Select Category</option>
                    <option value="India">India</option>
                    <option value="Science">Science</option>
                    <option value="Math">Math</option>
                    <option value="History">History</option>
                    <option value="Sports">Sports</option>
                    <option value="Movies">Movies</option>
                    <option value="Music">Music</option>
                    <option value="Technology">Technology</option>
                </select>
            </div>
            <div class="form-group">
                <label>Number of Questions</label>
                <input type="number" id="quizQuestions" min="1" value="10" required>
            </div>
            <button type="submit" class="btn-submit">Add Quiz</button>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<script src="admin.js"></script>
</body>
</html>