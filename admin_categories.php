<?php
// admin_categories.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php?error=admin_only");
    exit();
}

$admin_name = $_SESSION['user_name'];
include_once 'app/Database.php';
$db = new Database();
$conn = $db->conn;

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $icon = $_POST['icon'] ?? 'fa-book';
        $color = $_POST['color'] ?? '#4a90d9';
        
        try {
            $stmt = $conn->prepare("INSERT INTO categories (name, description, icon, color) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $description, $icon, $color]);
            $message = 'Category added successfully!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $icon = $_POST['icon'] ?? 'fa-book';
        $color = $_POST['color'] ?? '#4a90d9';
        
        try {
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, icon = ?, color = ? WHERE id = ?");
            $stmt->execute([$name, $description, $icon, $color, $id]);
            $message = 'Category updated successfully!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        
        try {
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Category deleted!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Cannot delete: Category has questions assigned.';
            $message_type = 'error';
        }
    }
}

// Get all categories
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | QuiZone Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="admin_categories.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-logo">QuiZone <span>Admin</span></div>
    <button class="nav-toggle" onclick="toggleMenu()">☰</button>
    <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="auth/logout.php" class="nav-btn">Logout</a></li>
    </ul>
</nav>

<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li><a href="admin.php" class="sidebar-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="admin_categories.php" class="sidebar-item active"><i class="fas fa-list"></i> Categories</a></li>
        <li><a href="admin_questions.php" class="sidebar-item"><i class="fas fa-question-circle"></i> Questions</a></li>
        <li><a href="admin.php" class="sidebar-item"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="index.php"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
    </ul>
</aside>

<div class="main-content">
    <div class="page-header">
        <h1>Manage Categories</h1>
        <button class="btn-primary" onclick="openModal('add')">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>
    
    <?php if ($message): ?>
        <div class="toast show <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="category-grid" id="categoryGrid">
        <?php foreach ($categories as $cat): ?>
            <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE category_id = ?");
                $stmt->execute([$cat['id']]);
                $question_count = $stmt->fetchColumn();
            ?>
            <div class="category-card" style="border-color: <?php echo $cat['color']; ?>;">
                <div class="category-header">
                    <div class="category-icon" style="background: <?php echo $cat['color']; ?>;">
                        <i class="fas <?php echo $cat['icon']; ?>"></i>
                    </div>
                </div>
                <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                <div class="category-desc"><?php echo htmlspecialchars($cat['description'] ?? 'No description'); ?></div>
                <div class="category-stats">
                    <span><i class="fas fa-question-circle"></i> <?php echo $question_count; ?> questions</span>
                </div>
                <div class="category-actions">
                    <button class="btn-sm btn-edit" 
                        data-id="<?php echo $cat['id']; ?>"
                        data-name="<?php echo htmlspecialchars($cat['name']); ?>"
                        data-desc="<?php echo htmlspecialchars($cat['description'] ?? ''); ?>"
                        data-icon="<?php echo $cat['icon']; ?>"
                        data-color="<?php echo $cat['color']; ?>"
                        onclick="openModal('edit', this.dataset)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this category?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal" id="categoryModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Category</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" id="categoryForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="categoryId" value="">
            
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" id="categoryName" required placeholder="e.g., India, Science">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="categoryDesc" rows="3" placeholder="Brief description..."></textarea>
            </div>
            <div class="form-group">
                <label>Icon</label>
                <select name="icon" id="categoryIcon">
                    <option value="fa-globe">🌍 Globe</option>
                    <option value="fa-flask">🧪 Flask</option>
                    <option value="fa-calculator">🧮 Calculator</option>
                    <option value="fa-landmark">🏛️ Landmark</option>
                    <option value="fa-football">⚽ Football</option>
                    <option value="fa-film">🎬 Film</option>
                    <option value="fa-music">🎵 Music</option>
                    <option value="fa-laptop">💻 Laptop</option>
                    <option value="fa-book">📖 Book</option>
                    <option value="fa-star">⭐ Star</option>
                </select>
            </div>
            <div class="form-group">
                <label>Color</label>
                <div class="color-options">
                    <div class="color-option" style="background: #4a90d9;" data-color="#4a90d9"></div>
                    <div class="color-option" style="background: #10b981;" data-color="#10b981"></div>
                    <div class="color-option" style="background: #f59e0b;" data-color="#f59e0b"></div>
                    <div class="color-option" style="background: #8b5cf6;" data-color="#8b5cf6"></div>
                    <div class="color-option" style="background: #ef4444;" data-color="#ef4444"></div>
                    <div class="color-option" style="background: #ec4899;" data-color="#ec4899"></div>
                    <div class="color-option" style="background: #06b6d4;" data-color="#06b6d4"></div>
                    <div class="color-option" style="background: #6366f1;" data-color="#6366f1"></div>
                </div>
                <input type="hidden" name="color" id="selectedColor" value="#4a90d9">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;" id="submitBtn">Add Category</button>
        </form>
    </div>
</div>

<div class="toast" id="toast"></div>

<script src="admin.js"></script>
<script src="admin_categories.js"></script>

</body>
</html>