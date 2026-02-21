<?php
// categories.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'app/Database.php';

$db = new Database();
$conn = $db->conn;

// Check if logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'Guest';

// Get sort option
$sort = $_GET['sort'] ?? 'az';

// Build query based on sort
$orderBy = 'name ASC'; // Default A to Z
switch ($sort) {
    case 'popular':
        // Order by question count (most popular = most questions)
        $orderBy = '(SELECT COUNT(*) FROM questions q WHERE q.category_id = c.id) DESC';
        break;
    case 'recent':
        // This would need quiz_results table - fallback to name
        $orderBy = 'name ASC';
        break;
    case 'az':
    default:
        $orderBy = 'name ASC';
        break;
}

// Get all categories
$categories = [];
try {
    $stmt = $conn->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY $orderBy");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// If no categories in DB, use default
if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'India', 'icon' => 'fa-globe', 'color' => '#4a90d9', 'description' => 'Test your knowledge about India'],
        ['id' => 2, 'name' => 'Science', 'icon' => 'fa-flask', 'color' => '#10b981', 'description' => 'Physics, Chemistry, Biology and more'],
        ['id' => 3, 'name' => 'Math', 'icon' => 'fa-calculator', 'color' => '#f59e0b', 'description' => 'Numbers, calculations, and logic'],
        ['id' => 4, 'name' => 'History', 'icon' => 'fa-landmark', 'color' => '#8b5cf6', 'description' => 'Historical events and facts'],
        ['id' => 5, 'name' => 'Sports', 'icon' => 'fa-futbol', 'color' => '#ef4444', 'description' => 'Sports, games, and athletes'],
        ['id' => 6, 'name' => 'Movies', 'icon' => 'fa-film', 'color' => '#ec4899', 'description' => 'Films, actors, and directors'],
        ['id' => 7, 'name' => 'Music', 'icon' => 'fa-music', 'color' => '#06b6d4', 'description' => 'Music, artists, and songs'],
        ['id' => 8, 'name' => 'Technology', 'icon' => 'fa-laptop', 'color' => '#6366f1', 'description' => 'Tech, computers, and innovation'],
    ];
}

// Get question count for each category
foreach ($categories as &$cat) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE category_id = ? AND is_active = 1");
        $stmt->execute([$cat['id']]);
        $cat['question_count'] = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $cat['question_count'] = 0;
    }
}
unset($cat);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | QuiZone</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<nav class="navbar">
    <div class="nav-logo">QuiZone</div>
    <button class="nav-toggle" onclick="toggleMenu()" aria-label="Toggle Menu">☰</button>
    <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        <li><a href="categories.php" class="active">Categories</a></li>
        <li><a href="#">Report</a></li>
        <li><a href="#">Profile</a></li>
        
        <?php if ($is_logged_in): ?>
            <li><span class="nav-user">Hello, <?php echo htmlspecialchars($user_name); ?></span></li>
            <li><a href="auth/logout.php" class="nav-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="auth/login.php" class="nav-btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="main-container">
    <div class="page-header">
        <h1>Quiz Categories</h1>
        <p>Choose a category and test your knowledge!</p>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="categorySearch" placeholder="Search categories..." onkeyup="filterCategories()">
    </div>
    <!-- Sort Tabs -->
<div class="sort-tabs">
    <a href="categories.php?sort=az" class="sort-tab <?php echo ($sort ?? 'az') === 'az' ? 'active' : ''; ?>">
        <i class="fas fa-sort-alpha-up"></i> A to Z
    </a>
    <a href="categories.php?sort=popular" class="sort-tab <?php echo $sort === 'popular' ? 'active' : ''; ?>">
        <i class="fas fa-fire"></i> Popular
    </a>
    <a href="categories.php?sort=recent" class="sort-tab <?php echo $sort === 'recent' ? 'active' : ''; ?>">
        <i class="fas fa-clock"></i> Recent
    </a>
</div>

    <!-- Categories Grid -->
    <div class="categories-grid" id="categoriesGrid">
        <?php foreach ($categories as $cat): ?>
            <div class="category-card" data-name="<?php echo strtolower($cat['name']); ?>">
                <div class="category-icon" style="background: <?php echo $cat['color']; ?>;">
                    <i class="fas <?php echo $cat['icon']; ?>"></i>
                </div>
                <div class="category-info">
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <p><?php echo htmlspecialchars($cat['description'] ?? 'Test your knowledge'); ?></p>
                    <span class="question-count">
                        <i class="fas fa-question-circle"></i>
                        <?php echo $cat['question_count']; ?> questions
                    </span>
                </div>
                <a href="quiz.php?category=<?php echo strtolower($cat['name']); ?>" class="play-btn">
                    Play <i class="fas fa-play"></i>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- No Results Message -->
    <div class="no-results" id="noResults" style="display: none;">
        <i class="fas fa-search"></i>
        <h3>No categories found</h3>
        <p>Try a different search term</p>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2024 QuiZone. All rights reserved.</p>
</footer>

<script src="categories.js"></script>

</body>
</html>