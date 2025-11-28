// ======================
// AUTHENTICATION FUNCTIONS
// ======================

function checkAuthentication() {
    const user = localStorage.getItem('quizUser');
    if (!user) {
        window.location.href = 'index.html';
        return false;
    }
    return true;
}

function getCurrentUser() {
    return localStorage.getItem('quizUser');
}

// ======================
// RESULTS PAGE FUNCTIONALITY
// ======================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Results page loaded');
    
    if (!checkAuthentication()) return;
    
    const results = JSON.parse(localStorage.getItem('quizResults'));
    if (!results) {
        alert('No quiz results found. Redirecting to home.');
        window.location.href = 'home.html';
        return;
    }
    
    displayResults(results);
});

function displayResults(results) {
    const scorePercent = Math.round((results.score / results.totalQuestions) * 100);
    
    // Update main score display
    document.getElementById('scorePercent').textContent = `${scorePercent}%`;
    document.getElementById('correctAnswers').textContent = results.score;
    document.getElementById('totalQuestionsResult').textContent = results.totalQuestions;
    document.getElementById('timeSpent').textContent = `${results.timeSpent}s`;
    document.getElementById('accuracy').textContent = `${scorePercent}%`;
    
    // Add performance feedback
    const feedbackElement = document.getElementById('performanceFeedback');
    let feedback = '';
    
    if (scorePercent >= 90) {
        feedback = `
            <h3>🎉 Excellent Work!</h3>
            <p>You've demonstrated outstanding knowledge in this category. Your performance is exceptional!</p>
        `;
    } else if (scorePercent >= 70) {
        feedback = `
            <h3>👍 Great Job!</h3>
            <p>You have a solid understanding of this topic. Keep up the good work!</p>
        `;
    } else if (scorePercent >= 50) {
        feedback = `
            <h3>💪 Good Effort!</h3>
            <p>You're on the right track. With a bit more practice, you'll master this category.</p>
        `;
    } else {
        feedback = `
            <h3>📚 Keep Learning!</h3>
            <p>This was a challenging quiz. Review the material and try again to improve your score.</p>
        `;
    }
    
    feedbackElement.innerHTML = feedback;
    
    // Add category-specific feedback
    const categoryNames = {
        science: 'Science',
        gk: 'General Knowledge',
        history: 'History',
        computer: 'Computer Science',
        nepal: 'Nepal',
        city: 'Cities'
    };
    
    const categoryName = categoryNames[results.category] || results.category;
    document.querySelector('.results-title').textContent = `${categoryName} Quiz Completed!`;
}

function restartQuiz() {
    const category = localStorage.getItem('quizCategory');
    if (category) {
        window.location.href = 'quiz.html';
    } else {
        window.location.href = 'home.html';
    }
}

function goHome() {
    window.location.href = 'home.html';
}