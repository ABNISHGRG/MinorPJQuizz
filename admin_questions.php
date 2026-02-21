<?php
// admin_questions.php
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
        $category_id = $_POST['category_id'] ?? '';
        $question = $_POST['question'] ?? '';
        $option_a = $_POST['option_a'] ?? '';
        $option_b = $_POST['option_b'] ?? '';
        $option_c = $_POST['option_c'] ?? '';
        $option_d = $_POST['option_d'] ?? '';
        $correct = $_POST['correct_answer'] ?? '';
        $difficulty = $_POST['difficulty'] ?? 'medium';
        
        try {
            $stmt = $conn->prepare("INSERT INTO questions (category_id, question_text, option_a, option_b, option_c, option_d, correct_answer, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category_id, $question, $option_a, $option_b, $option_c, $option_d, $correct, $difficulty]);
            $message = 'Question added successfully!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $category_id = $_POST['category_id'] ?? '';
        $question = $_POST['question'] ?? '';
        $option_a = $_POST['option_a'] ?? '';
        $option_b = $_POST['option_b'] ?? '';
        $option_c = $_POST['option_c'] ?? '';
        $option_d = $_POST['option_d'] ?? '';
        $correct = $_POST['correct_answer'] ?? '';
        $difficulty = $_POST['difficulty'] ?? 'medium';
        
        try {
            $stmt = $conn->prepare("UPDATE questions SET category_id = ?, question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, difficulty = ? WHERE id = ?");
            $stmt->execute([$category_id, $question, $option_a, $option_b, $option_c, $option_d, $correct, $difficulty, $id]);
            $message = 'Question updated successfully!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        
        try {
            $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Question deleted!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Get all categories
$stmt = $conn->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all questions with category name
$stmt = $conn->query("
    SELECT q.*, c.name as category_name 
    FROM questions q 
    LEFT JOIN categories c ON q.category_id = c.id 
    ORDER BY q.id DESC
");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions | QuiZone Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="admin_questions.css">
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
        <li><a href="admin_categories.php" class="sidebar-item"><i class="fas fa-list"></i> Categories</a></li>
        <li><a href="admin_questions.php" class="sidebar-item active"><i class="fas fa-question-circle"></i> Questions</a></li>
        <li><a href="admin.php" class="sidebar-item"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="index.php"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
    </ul>
</aside>

<div class="main-content">
    <div class="page-header">
        <h1>Manage Questions</h1>
        <button class="btn-primary" onclick="openModal('add')">
            <i class="fas fa-plus"></i> Add Question
        </button>
    </div>
    
    <?php if ($message): ?>
        <div class="toast show <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <!-- Filter Bar -->
    <div class="filter-bar">
        <select id="categoryFilter" onchange="filterQuestions()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select id="difficultyFilter" onchange="filterQuestions()">
            <option value="">All Difficulties</option>
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>
    </div>
    
    <!-- Questions List -->
    <div class="question-list" id="questionList">
        <?php foreach ($questions as $q): ?>
            <div class="question-card" 
                 data-category="<?php echo $q['category_id']; ?>" 
                 data-difficulty="<?php echo $q['difficulty']; ?>"
                 data-id="<?php echo $q['id']; ?>"
                 data-question="<?php echo htmlspecialchars($q['question_text']); ?>"
                 data-option-a="<?php echo htmlspecialchars($q['option_a']); ?>"
                 data-option-b="<?php echo htmlspecialchars($q['option_b']); ?>"
                 data-option-c="<?php echo htmlspecialchars($q['option_c']); ?>"
                 data-option-d="<?php echo htmlspecialchars($q['option_d']); ?>"
                 data-correct="<?php echo $q['correct_answer']; ?>"
                 data-category-id="<?php echo $q['category_id']; ?>"
                 data-difficulty-value="<?php echo $q['difficulty']; ?>">
                
                <div class="question-header">
                    <span class="question-category"><?php echo htmlspecialchars($q['category_name']); ?></span>
                    <span class="question-difficulty difficulty-<?php echo $q['difficulty']; ?>"><?php echo ucfirst($q['difficulty']); ?></span>
                </div>
                
                <div class="question-text"><?php echo htmlspecialchars($q['question_text']); ?></div>
                
                <div class="options-grid">
                    <div class="option-item <?php echo $q['correct_answer'] === 'a' ? 'correct' : ''; ?>">A. <?php echo htmlspecialchars($q['option_a']); ?></div>
                    <div class="option-item <?php echo $q['correct_answer'] === 'b' ? 'correct' : ''; ?>">B. <?php echo htmlspecialchars($q['option_b']); ?></div>
                    <div class="option-item <?php echo $q['correct_answer'] === 'c' ? 'correct' : ''; ?>">C. <?php echo htmlspecialchars($q['option_c']); ?></div>
                    <div class="option-item <?php echo $q['correct_answer'] === 'd' ? 'correct' : ''; ?>">D. <?php echo htmlspecialchars($q['option_d']); ?></div>
                </div>
                
                <div class="question-actions">
                    <button class="btn-sm btn-edit" onclick="openModal('edit', this.closest('.question-card').dataset)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this question?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $q['id']; ?>">
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal" id="questionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Question</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" id="questionForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="questionId" value="">
            
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="questionCategory" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Question</label>
                <textarea name="question" id="questionText" rows="3" required placeholder="Enter your question..."></textarea>
            </div>
            
            <div class="options-group">
                <div class="form-group">
                    <label>Option A</label>
                    <input type="text" name="option_a" id="optionA" required placeholder="Option A">
                </div>
                <div class="form-group">
                    <label>Option B</label>
                    <input type="text" name="option_b" id="optionB" required placeholder="Option B">
                </div>
                <div class="form-group">
                    <label>Option C</label>
                    <input type="text" name="option_c" id="optionC" required placeholder="Option C">
                </div>
                <div class="form-group">
                    <label>Option D</label>
                    <input type="text" name="option_d" id="optionD" required placeholder="Option D">
                </div>
            </div>
            
            <div class="form-group">
                <label>Correct Answer</label>
                <div class="correct-answer-group">
                    <label class="radio-label">
                        <input type="radio" name="correct_answer" id="correctA" value="a"> A
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="correct_answer" id="correctB" value="b"> B
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="correct_answer" id="correctC" value="c"> C
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="correct_answer" id="correctD" value="d"> D
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Difficulty</label>
                <select name="difficulty" id="questionDifficulty">
                    <option value="easy">Easy</option>
                    <option value="medium" selected>Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;" id="submitBtn">Add Question</button>
        </form>
    </div>
</div>

<div class="toast" id="toast"></div>

<script src="admin.js"></script>
<script src="admin_questions.js"></script>

</body>
</html>