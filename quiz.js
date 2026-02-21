// quiz.js

// Quiz state
let currentQuestion = 0;
let score = 0;
let answers = [];
let timerInterval;
let timeLeft = 300; // 5 minutes in seconds

// Initialize quiz on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if quiz data exists
    if (typeof quizData === 'undefined' || quizData.length === 0) {
        showError('No questions available for this category.');
        return;
    }
    
    // Initialize answers array
    answers = new Array(quizData.length).fill(null);
    
    // Load first question
    loadQuestion(0);
    
    // Start timer
    startTimer();
    
    // Add keyboard navigation
    document.addEventListener('keydown', handleKeyboard);
});

/**
 * Load a question by index
 */
function loadQuestion(index) {
    const question = quizData[index];
    
    // Update question number
    document.getElementById('qNumber').textContent = index + 1;
    document.getElementById('currentQ').textContent = index + 1;
    
    // Update question text
    document.getElementById('questionText').textContent = question.question_text;
    
    // Update progress bar
    const progress = ((index + 1) / quizData.length) * 100;
    document.getElementById('progressFill').style.width = progress + '%';
    
    // Generate options
    const optionsList = document.getElementById('optionsList');
    optionsList.innerHTML = '';
    
    const options = [
        { letter: 'a', text: question.option_a },
        { letter: 'b', text: question.option_b },
        { letter: 'c', text: question.option_c },
        { letter: 'd', text: question.option_d }
    ];
    
    options.forEach(opt => {
        const btn = document.createElement('button');
        btn.className = 'option-btn';
        btn.dataset.answer = opt.letter;
        
        // Check if this option was previously selected
        if (answers[index] === opt.letter) {
            btn.classList.add('selected');
        }
        
        btn.innerHTML = `
            <span class="option-letter">${opt.letter.toUpperCase()}</span>
            <span>${opt.text}</span>
        `;
        
        btn.addEventListener('click', function() {
            selectOption(opt.letter);
        });
        
        optionsList.appendChild(btn);
    });
    
    // Update navigation buttons
    updateNavigation();
}

/**
 * Select an option
 */
function selectOption(answer) {
    // Store answer
    answers[currentQuestion] = answer;
    
    // Update UI
    const options = document.querySelectorAll('.option-btn');
    options.forEach(opt => {
        opt.classList.remove('selected');
        if (opt.dataset.answer === answer) {
            opt.classList.add('selected');
        }
    });
}

/**
 * Go to next question
 */
function nextQuestion() {
    if (currentQuestion < quizData.length - 1) {
        currentQuestion++;
        loadQuestion(currentQuestion);
    } else {
        // Last question - show results
        showResults();
    }
}

/**
 * Go to previous question
 */
function prevQuestion() {
    if (currentQuestion > 0) {
        currentQuestion--;
        loadQuestion(currentQuestion);
    }
}

/**
 * Update navigation buttons
 */
function updateNavigation() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    // Previous button
    prevBtn.disabled = currentQuestion === 0;
    
    // Next button text
    if (currentQuestion === quizData.length - 1) {
        nextBtn.innerHTML = 'Finish <i class="fas fa-check"></i>';
    } else {
        nextBtn.innerHTML = 'Next <i class="fas fa-arrow-right"></i>';
    }
}

/**
 * Start timer
 */
function startTimer() {
    updateTimerDisplay();
    
    timerInterval = setInterval(function() {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            showResults();
        }
    }, 1000);
}

/**
 * Update timer display
 */
function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    
    const timerElement = document.getElementById('timer');
    timerElement.textContent = 
        String(minutes).padStart(2, '0') + ':' + 
        String(seconds).padStart(2, '0');
    
    // Change color when time is low
    if (timeLeft < 60) {
        timerElement.parentElement.style.background = 'rgba(239, 68, 68, 0.8)';
    }
}

/**
 * Show results
 */
function showResults() {
    // Stop timer
    clearInterval(timerInterval);
    
    // Calculate score
    score = 0;
    quizData.forEach((q, index) => {
        if (answers[index] === q.correct_answer) {
            score++;
        }
    });
    
    // Calculate percentage
    const percentage = Math.round((score / quizData.length) * 100);
    
    // Update results UI
    document.getElementById('finalScore').textContent = score;
    document.getElementById('resultsPercentage').textContent = percentage + '%';
    
    // Set message based on score
    let message = 'Keep practicing!';
    if (percentage >= 90) {
        message = '🌟 Excellent! You\'re a genius!';
    } else if (percentage >= 70) {
        message = '👏 Great job! Well done!';
    } else if (percentage >= 50) {
        message = '👍 Good effort! Keep it up!';
    }
    document.getElementById('resultMessage').textContent = message;
    
    // Show results overlay
    document.getElementById('resultsOverlay').classList.add('show');
    
    // Submit score if logged in
    if (isLoggedIn) {
        submitScore(score, quizData.length);
    }
}

/**
 * Submit score to server
 */
function submitScore(score, total) {
    const form = document.getElementById('scoreForm');
    document.getElementById('scoreInput').value = score;
    document.getElementById('totalInput').value = total;
    
    // Submit form
    setTimeout(function() {
        form.submit();
    }, 2000);
}

/**
 * Handle keyboard navigation
 */
function handleKeyboard(e) {
    // Number keys 1-4 for options
    if (e.key >= '1' && e.key <= '4') {
        const optionIndex = parseInt(e.key) - 1;
        const options = ['a', 'b', 'c', 'd'];
        if (optionIndex < options.length) {
            selectOption(options[optionIndex]);
        }
    }
    
    // Arrow keys for navigation
    if (e.key === 'ArrowRight' && currentQuestion < quizData.length - 1) {
        nextQuestion();
    }
    if (e.key === 'ArrowLeft' && currentQuestion > 0) {
        prevQuestion();
    }
    
    // Enter to finish
    if (e.key === 'Enter' && currentQuestion === quizData.length - 1) {
        showResults();
    }
}

/**
 * Show error message
 */
function showError(message) {
    const container = document.querySelector('.quiz-container');
    container.innerHTML = `
        <div class="question-card" style="text-align: center; padding: 3rem;">
            <h2 style="color: #ef4444;">⚠️ Error</h2>
            <p>${message}</p>
            <a href="index.php" class="btn-nav btn-next" style="margin-top: 1rem; display: inline-block;">
                Go Home
            </a>
        </div>
    `;
}