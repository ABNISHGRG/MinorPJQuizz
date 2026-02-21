<?php
// quiz.php
session_start();

include_once 'app/Database.php';

$db = new Database();
$conn = $db->conn;

// Get category from URL
$category = $_GET['category'] ?? 'india';
$category = strtolower(htmlspecialchars($category));

// Check if logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'Guest';

// Get questions from database
$questions = [];
try {
    $stmt = $conn->prepare("
        SELECT * FROM questions 
        WHERE category_id IN (SELECT id FROM categories WHERE LOWER(name) = ?)
        AND is_active = 1
        ORDER BY RAND() LIMIT 10
    ");
    $stmt->execute([$category]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $questions = [];
}

// If no questions in DB, use sample data
if (empty($questions)) {
    $sampleQuestions = getSampleQuestions();
    $questions = $sampleQuestions[$category] ?? $sampleQuestions['india'];
}

$total_questions = count($questions);

function getSampleQuestions() {
    return [
        'india' => [
            ['question_text' => 'What is the capital of India?', 'option_a' => 'Mumbai', 'option_b' => 'New Delhi', 'option_c' => 'Kolkata', 'option_d' => 'Chennai', 'correct_answer' => 'b'],
            ['question_text' => 'Which is the largest state in India by area?', 'option_a' => 'Madhya Pradesh', 'option_b' => 'Maharashtra', 'option_c' => 'Rajasthan', 'option_d' => 'Uttar Pradesh', 'correct_answer' => 'c'],
            ['question_text' => 'Who wrote the national anthem of India?', 'option_a' => 'Bankim Chandra', 'option_b' => 'Rabindranath Tagore', 'option_c' => 'Mahatma Gandhi', 'option_d' => 'Jawaharlal Nehru', 'correct_answer' => 'b'],
            ['question_text' => 'Which river is known as "Ganga of South"?', 'option_a' => 'Krishna', 'option_b' => 'Kaveri', 'option_c' => 'Godavari', 'option_d' => 'Narmada', 'correct_answer' => 'c'],
            ['question_text' => 'Who was the first Prime Minister of India?', 'option_a' => 'Mahatma Gandhi', 'option_b' => 'Jawaharlal Nehru', 'option_c' => 'Sardar Patel', 'option_d' => 'Rajendra Prasad', 'correct_answer' => 'b'],
        ],
        'science' => [
            ['question_text' => 'What is the chemical symbol for water?', 'option_a' => 'H2O', 'option_b' => 'CO2', 'option_c' => 'NaCl', 'option_d' => 'O2', 'correct_answer' => 'a'],
            ['question_text' => 'What planet is known as the Red Planet?', 'option_a' => 'Venus', 'option_b' => 'Mars', 'option_c' => 'Jupiter', 'option_d' => 'Saturn', 'correct_answer' => 'b'],
            ['question_text' => 'What is the speed of light?', 'option_a' => '300,000 km/s', 'option_b' => '150,000 km/s', 'option_c' => '500,000 km/s', 'option_d' => '200,000 km/s', 'correct_answer' => 'a'],
            ['question_text' => 'What is the largest organ in the human body?', 'option_a' => 'Heart', 'option_b' => 'Liver', 'option_c' => 'Brain', 'option_d' => 'Skin', 'correct_answer' => 'd'],
            ['question_text' => 'What gas do plants absorb from the atmosphere?', 'option_a' => 'Oxygen', 'option_b' => 'Nitrogen', 'option_c' => 'Carbon Dioxide', 'option_d' => 'Hydrogen', 'correct_answer' => 'c'],
        ],
        'math' => [
            ['question_text' => 'What is 15 + 27?', 'option_a' => '40', 'option_b' => '42', 'option_c' => '44', 'option_d' => '46', 'correct_answer' => 'b'],
            ['question_text' => 'What is 12 × 12?', 'option_a' => '124', 'option_b' => '144', 'option_c' => '134', 'option_d' => '154', 'correct_answer' => 'b'],
            ['question_text' => 'What is the square root of 64?', 'option_a' => '6', 'option_b' => '7', 'option_c' => '8', 'option_d' => '9', 'correct_answer' => 'c'],
            ['question_text' => 'What is 100 ÷ 4?', 'option_a' => '20', 'option_b' => '25', 'option_c' => '30', 'option_d' => '35', 'correct_answer' => 'b'],
            ['question_text' => 'What is 3³?', 'option_a' => '9', 'option_b' => '18', 'option_c' => '27', 'option_d' => '36', 'correct_answer' => 'c'],
        ],
        'history' => [
            ['question_text' => 'In which year did World War II end?', 'option_a' => '1943', 'option_b' => '1944', 'option_c' => '1945', 'option_d' => '1946', 'correct_answer' => 'c'],
            ['question_text' => 'Who was the first President of the United States?', 'option_a' => 'Abraham Lincoln', 'option_b' => 'George Washington', 'option_c' => 'Thomas Jefferson', 'option_d' => 'John Adams', 'correct_answer' => 'b'],
            ['question_text' => 'Which empire built the Taj Mahal?', 'option_a' => 'Mughal Empire', 'option_b' => 'British Empire', 'option_c' => 'Ottoman Empire', 'option_d' => 'Persian Empire', 'correct_answer' => 'a'],
            ['question_text' => 'When did India gain independence?', 'option_a' => '1945', 'option_b' => '1946', 'option_c' => '1947', 'option_d' => '1948', 'correct_answer' => 'c'],
            ['question_text' => 'Who discovered America?', 'option_a' => 'Vasco da Gama', 'option_b' => 'Christopher Columbus', 'option_c' => 'Ferdinand Magellan', 'option_d' => 'Marco Polo', 'correct_answer' => 'b'],
        ],
        'sports' => [
            ['question_text' => 'How many players are in a cricket team?', 'option_a' => '9', 'option_b' => '10', 'option_c' => '11', 'option_d' => '12', 'correct_answer' => 'c'],
            ['question_text' => 'Which country won the 2018 FIFA World Cup?', 'option_a' => 'Brazil', 'option_b' => 'Germany', 'option_c' => 'France', 'option_d' => 'Argentina', 'correct_answer' => 'c'],
            ['question_text' => 'In which sport is the term "home run" used?', 'option_a' => 'Football', 'option_b' => 'Baseball', 'option_c' => 'Cricket', 'option_d' => 'Basketball', 'correct_answer' => 'b'],
            ['question_text' => 'How many rings are on the Olympic flag?', 'option_a' => '4', 'option_b' => '5', 'option_c' => '6', 'option_d' => '7', 'correct_answer' => 'b'],
            ['question_text' => 'Which sport is known as "The King of Sports"?', 'option_a' => 'Football', 'option_b' => 'Cricket', 'option_c' => 'Basketball', 'option_d' => 'Tennis', 'correct_answer' => 'b'],
        ],
        'movies' => [
            ['question_text' => 'Who directed the movie "Titanic"?', 'option_a' => 'Steven Spielberg', 'option_b' => 'James Cameron', 'option_c' => 'Christopher Nolan', 'option_d' => 'Martin Scorsese', 'correct_answer' => 'b'],
            ['question_text' => 'Which movie won the first Oscar for Best Picture?', 'option_a' => 'Wings', 'option_b' => 'Sunrise', 'option_c' => 'The Jazz Singer', 'option_d' => '7th Heaven', 'correct_answer' => 'a'],
            ['question_text' => 'Who played Jack in the movie "Titanic"?', 'option_a' => 'Brad Pitt', 'option_b' => 'Tom Cruise', 'option_c' => 'Leonardo DiCaprio', 'option_d' => 'Matt Damon', 'correct_answer' => 'c'],
            ['question_text' => 'What is the highest-grossing film of all time?', 'option_a' => 'Titanic', 'option_b' => 'Avatar', 'option_c' => 'Avengers: Endgame', 'option_d' => 'Star Wars', 'correct_answer' => 'b'],
            ['question_text' => 'In which year was the first Harry Potter movie released?', 'option_a' => '1999', 'option_b' => '2000', 'option_c' => '2001', 'option_d' => '2002', 'correct_answer' => 'c'],
        ],
        'music' => [
            ['question_text' => 'Who is known as the "King of Pop"?', 'option_a' => 'Elvis Presley', 'option_b' => 'Michael Jackson', 'option_c' => 'Prince', 'option_d' => 'Freddie Mercury', 'correct_answer' => 'b'],
            ['question_text' => 'Which band performed "Bohemian Rhapsody"?', 'option_a' => 'The Beatles', 'option_b' => 'Led Zeppelin', 'option_c' => 'Queen', 'option_d' => 'Pink Floyd', 'correct_answer' => 'c'],
            ['question_text' => 'What instrument has 88 keys?', 'option_a' => 'Guitar', 'option_b' => 'Violin', 'option_c' => 'Piano', 'option_d' => 'Drums', 'correct_answer' => 'c'],
            ['question_text' => 'Who sang "Shape of You"?', 'option_a' => 'Justin Bieber', 'option_b' => 'Ed Sheeran', 'option_c' => 'Bruno Mars', 'option_d' => 'The Weeknd', 'correct_answer' => 'b'],
            ['question_text' => 'Which country is famous for Jazz music?', 'option_a' => 'UK', 'option_b' => 'France', 'option_c' => 'USA', 'option_d' => 'Germany', 'correct_answer' => 'c'],
        ],
        'technology' => [
            ['question_text' => 'Who founded Microsoft?', 'option_a' => 'Steve Jobs', 'option_b' => 'Bill Gates', 'option_c' => 'Mark Zuckerberg', 'option_d' => 'Elon Musk', 'correct_answer' => 'b'],
            ['question_text' => 'What does CPU stand for?', 'option_a' => 'Central Processing Unit', 'option_b' => 'Computer Personal Unit', 'option_c' => 'Central Program Unit', 'option_d' => 'Computer Processing Unit', 'correct_answer' => 'a'],
            ['question_text' => 'In which year was the iPhone first released?', 'option_a' => '2005', 'option_b' => '2006', 'option_c' => '2007', 'option_d' => '2008', 'correct_answer' => 'c'],
            ['question_text' => 'What does HTML stand for?', 'option_a' => 'Hyper Text Markup Language', 'option_b' => 'High Tech Modern Language', 'option_c' => 'Home Tool Markup Language', 'option_d' => 'Hyper Transfer Markup Language', 'correct_answer' => 'a'],
            ['question_text' => 'Who is the CEO of Tesla?', 'option_a' => 'Jeff Bezos', 'option_b' => 'Tim Cook', 'option_c' => 'Elon Musk', 'option_d' => 'Sundar Pichai', 'correct_answer' => 'c'],
        ],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - <?php echo ucfirst($category); ?> | QuiZone</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="quiz.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar">
    <div class="nav-logo">QuiZone</div>
    <button class="nav-toggle" onclick="toggleMenu()" aria-label="Toggle Menu">☰</button>
    <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        <li><a href="#">Categories</a></li>
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

<div class="quiz-container">
    <!-- Quiz Header -->
    <div class="quiz-header">
        <div>
            <h2><?php echo ucfirst($category); ?> Quiz</h2>
            <p>Answer all questions to complete the quiz</p>
        </div>
        <div class="quiz-timer">
            <i class="fas fa-clock"></i> <span id="timer">05:00</span>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="quiz-progress">
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill" style="width: 0%"></div>
        </div>
        <p style="margin-top: 0.5rem; color: #666;">
            <span id="currentQ">1</span> of <?php echo $total_questions; ?> questions
        </p>
    </div>
    
    <!-- Question Card -->
    <div class="question-card" id="questionCard">
        <div class="question-number">Question <span id="qNumber">1</span></div>
        <div class="question-text" id="questionText">Loading...</div>
        
        <div class="options-list" id="optionsList">
            <!-- Options will be loaded by JavaScript -->
        </div>
        
        <!-- Navigation Buttons -->
        <div class="quiz-navigation">
            <button class="btn-nav btn-prev" id="prevBtn" onclick="prevQuestion()" disabled>
                <i class="fas fa-arrow-left"></i> Previous
            </button>
            <button class="btn-nav btn-next" id="nextBtn" onclick="nextQuestion()">
                Next <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Results Overlay -->
<div class="results-overlay" id="resultsOverlay">
    <div class="results-card">
        <div class="results-icon">🏆</div>
        <div class="results-label">Your Score</div>
        <div class="results-score"><span id="finalScore">0</span>/<?php echo $total_questions; ?></div>
        <div class="results-percentage" id="resultsPercentage">0%</div>
        <div class="results-message" id="resultMessage">Keep practicing!</div>
        <div class="results-buttons">
            <a href="quiz.php?category=<?php echo $category; ?>" class="btn-retry">Try Again</a>
            <a href="index.php" class="btn-home">Go Home</a>
        </div>
    </div>
</div>

<!-- Hidden form for submitting score -->
<form id="scoreForm" method="POST" action="submit_quiz.php" style="display: none;">
    <input type="hidden" name="score" id="scoreInput">
    <input type="hidden" name="total" id="totalInput">
    <input type="hidden" name="category" value="<?php echo $category; ?>">
    <input type="hidden" name="quiz_name" value="<?php echo ucfirst($category); ?> Quiz">
</form>

<!-- Quiz Data (hidden, loaded by JavaScript) -->
<script>
    // Quiz data from PHP
    const quizData = <?php echo json_encode($questions); ?>;
    const totalQuestions = <?php echo $total_questions; ?>;
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const category = '<?php echo $category; ?>';
</script>

<script src="quiz.js"></script>

</body>
</html>