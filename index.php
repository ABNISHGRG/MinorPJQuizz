<?php
// Start session and check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'app/Database.php';
include_once 'app/user.php';

// Check user status
if (isset($_SESSION['user_id'])) {
    $is_logged_in = true;
    $user_name = $_SESSION['user_name'];
    $user_role = $_SESSION['user_role'];
} else {
    $is_logged_in = false;
    $user_name = "Guest";
    $user_role = "guest";
}

// Database connection
$db = new Database();
$conn = $db->conn;

// Get categories for display
$categories = [];
try {
    $stmt = $conn->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// If no categories in DB, use default
if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'India', 'icon' => 'fa-globe', 'color' => '#4a90d9'],
        ['id' => 2, 'name' => 'Science', 'icon' => 'fa-flask', 'color' => '#10b981'],
        ['id' => 3, 'name' => 'Math', 'icon' => 'fa-calculator', 'color' => '#f59e0b'],
        ['id' => 4, 'name' => 'History', 'icon' => 'fa-landmark', 'color' => '#8b5cf6'],
        ['id' => 5, 'name' => 'Sports', 'icon' => 'fa-football', 'color' => '#ef4444'],
        ['id' => 6, 'name' => 'Movies', 'icon' => 'fa-film', 'color' => '#ec4899'],
        ['id' => 7, 'name' => 'Music', 'icon' => 'fa-music', 'color' => '#06b6d4'],
        ['id' => 8, 'name' => 'Technology', 'icon' => 'fa-laptop', 'color' => '#6366f1'],
    ];
}

// Get quiz history - RECENTLY PLAYED
$recentCategories = [];
if ($is_logged_in) {
    try {
        $stmt = $conn->prepare("
            SELECT DISTINCT c.id, c.name, c.icon, c.color, MAX(r.created_at) as last_played
            FROM categories c
            INNER JOIN questions q ON c.id = q.category_id
            INNER JOIN quiz_results r ON r.category = c.name AND r.user_id = ?
            GROUP BY c.id, c.name, c.icon, c.color
            ORDER BY last_played DESC
            LIMIT 8
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $recentCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $recentCategories = [];
    }
}

// Get user's quiz history with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$history = [];
$totalRecords = 0;

if ($is_logged_in) {
    try {
        // Get total count
        $stmt = $conn->prepare("SELECT COUNT(*) FROM quiz_results WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $totalRecords = $stmt->fetchColumn();
        
        // Get paginated results
        $stmt = $conn->prepare("SELECT * FROM quiz_results WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalPages = ceil($totalRecords / $limit);
    } catch (PDOException $e) {
        $history = [];
        $totalRecords = 0;
        $totalPages = 1;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logo.png">
    <title>QuiZone</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<nav class="navbar">
    <div class="nav-logo">QuiZone</div>
    <button class="nav-toggle" onclick="toggleMenu()" aria-label="Toggle Menu">☰</button>
    <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        
        <!-- Categories Dropdown -->
        <li class="dropdown">
            <a href="categories.php" class="dropdown-toggle">
                Categories <i class="fas fa-chevron-down"></i>
            </a>
            <div class="dropdown-menu">
                <!-- Main Categories Link -->
                <a href="categories.php" class="dropdown-item dropdown-header">
                    <i class="fas fa-th"></i> All Categories
                </a>
                <div class="dropdown-divider"></div>
                
                <!-- Sort Options -->
                <a href="categories.php?sort=az" class="dropdown-item">
                    <i class="fas fa-sort-alpha-up"></i> A to Z
                </a>
                <a href="categories.php?sort=popular" class="dropdown-item">
                    <i class="fas fa-fire"></i> Popular
                </a>
                <a href="categories.php?sort=recent" class="dropdown-item">
                    <i class="fas fa-clock"></i> Recent
                </a>
                
                <div class="dropdown-divider"></div>
                
                <!-- Quick Links to Categories -->
                <?php 
                // Get top 5 categories for quick access
                $quickCategories = array_slice($categories, 0, 5);
                foreach ($quickCategories as $cat): ?>
                    <a href="quiz.php?category=<?php echo strtolower($cat['name']); ?>" class="dropdown-item">
                        <i class="fas <?php echo $cat['icon'] ?? 'fa-book'; ?>" 
                           style="color: <?php echo $cat['color'] ?? '#4a90d9'; ?>"></i>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
                
                <a href="categories.php" class="dropdown-item dropdown-footer">
                    View All Categories <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </li>
        
        <li><a href="#">Report</a></li>
        <li><a href="#">Profile</a></li>
        
        <?php if ($is_logged_in): ?>
            <?php if ($user_role === 'admin'): ?>
                <li><a href="admin.php" class="nav-btn" style="background: #8b5cf6;">Admin Panel</a></li>
            <?php endif; ?>
            <li><span class="nav-user">Hello, <?php echo htmlspecialchars($user_name); ?></span></li>
            <li><a href="auth/logout.php" class="nav-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="auth/login.php" class="nav-btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="main-container">
    <div class="welcome-banner">
        <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
        <p>Ready to test your knowledge today?</p>
    </div>

    <div class="content-wrapper">
        <section>
            <h2>Recently Played</h2>
            
            <?php if (!empty($recentCategories)): ?>
                <div class="category-grid" id="recent-grid">
                    <?php foreach ($recentCategories as $cat): ?>
                        <button class="start-btn"
                                data-category="<?php echo strtolower($cat['name']); ?>" 
                                onclick="startQuiz('<?php echo strtolower($cat['name']); ?>')"
                                style="background: <?php echo $cat['color']; ?>;">
                            <i class="fas <?php echo $cat['icon']; ?>"></i>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <?php 
                $recentIds = array_column($recentCategories, 'id');
                $otherCategories = array_filter($categories, function($cat) use ($recentIds) {
                    return !in_array($cat['id'], $recentIds);
                });
                ?>
                <?php if (!empty($otherCategories)): ?>
                    <div class="category-grid">
                        <?php foreach (array_slice($otherCategories, 0, 8) as $cat): ?>
                            <button class="start-btn" 
                                    data-category="<?php echo strtolower($cat['name']); ?>" 
                                    onclick="startQuiz('<?php echo strtolower($cat['name']); ?>')"
                                    style="background: <?php echo $cat['color'] ?? '#4a90d9'; ?>;">
                                <i class="fas <?php echo $cat['icon'] ?? 'fa-book'; ?>"></i>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="category-grid">
                    <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                        <button class="start-btn" 
                                data-category="<?php echo strtolower($cat['name']); ?>" 
                                onclick="startQuiz('<?php echo strtolower($cat['name']); ?>')"
                                style="background: <?php echo $cat['color'] ?? '#4a90d9'; ?>;">
                            <i class="fas <?php echo $cat['icon'] ?? 'fa-book'; ?>"></i>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="category-grid">
                    <?php foreach (array_slice($categories, 4, 4) as $cat): ?>
                        <button class="start-btn" 
                                data-category="<?php echo strtolower($cat['name']); ?>" 
                                onclick="startQuiz('<?php echo strtolower($cat['name']); ?>')"
                                style="background: <?php echo $cat['color'] ?? '#4a90d9'; ?>;">
                            <i class="fas <?php echo $cat['icon'] ?? 'fa-book'; ?>"></i>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <div class="history-section">
    <h2>Your History</h2>
    
    <?php if ($is_logged_in && count($history) > 0): ?>
        <div class="history-table-container">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Quiz Name</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['quiz_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php 
                                $percentage = ($row['score'] / $row['total_questions']) * 100;
                                $badge_class = $percentage >= 70 ? 'score-high' : ($percentage >= 40 ? 'score-mid' : 'score-low');
                            ?>
                            <span class="score-badge <?php echo $badge_class; ?>">
                                <?php echo $row['score'] . '/' . $row['total_questions']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="page-btn">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <span class="page-info">
                Page <?php echo $page; ?> of <?php echo $totalPages; ?>
            </span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="page-btn">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
    <?php elseif ($is_logged_in): ?>
        <p class="text-muted">No quiz history yet. Start a quiz to see your results here!</p>
    <?php else: ?>
        <div class="history-table-container">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Quiz Name</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Score</th>
                    </tr>
                </thead>
            </table>
        </div>
        <p class="text-muted mt-3">⚠️ You're viewing demo data. <a href="auth/register.php">Register</a> to save your scores!</p>
    <?php endif; ?>
</div>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2024 QuiZone. All rights reserved.</p>
</footer>

<script>
    function toggleMenu() {
        document.getElementById('navLinks').classList.toggle('active');
    }

    function startQuiz(category) {
        window.location.href = 'quiz.php?category=' + category;
    }
</script>

</body>
</html>