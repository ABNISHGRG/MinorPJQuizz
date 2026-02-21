// admin_questions.js

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize toast auto-hide
    initToast();
    
    // Initialize filter placeholders (if needed)
    initFilters();
});

/**
 * Initialize toast auto-hide
 */
function initToast() {
    setTimeout(function() {
        const toast = document.querySelector('.toast');
        if (toast) {
            toast.classList.remove('show');
        }
    }, 3000);
}

/**
 * Initialize filter functionality
 */
function initFilters() {
    // Additional filter initialization if needed
}

/**
 * Filter questions by category and difficulty
 */
function filterQuestions() {
    const categoryFilter = document.getElementById('categoryFilter');
    const difficultyFilter = document.getElementById('difficultyFilter');
    const questionCards = document.querySelectorAll('.question-card');
    
    const selectedCategory = categoryFilter ? categoryFilter.value : '';
    const selectedDifficulty = difficultyFilter ? difficultyFilter.value : '';
    
    questionCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        const cardDifficulty = card.getAttribute('data-difficulty');
        
        const categoryMatch = !selectedCategory || cardCategory === selectedCategory;
        const difficultyMatch = !selectedDifficulty || cardDifficulty === selectedDifficulty;
        
        if (categoryMatch && difficultyMatch) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update empty state
    updateEmptyState();
}

/**
 * Update empty state message
 */
function updateEmptyState() {
    const visibleCards = document.querySelectorAll('.question-card[style="display: block"], .question-card:not([style*="display: none"])');
    const questionList = document.getElementById('questionList');
    
    // Remove existing empty state
    const existingEmpty = document.querySelector('.empty-state');
    if (existingEmpty) {
        existingEmpty.remove();
    }
    
    // Check if any cards are visible
    let hasVisible = false;
    document.querySelectorAll('.question-card').forEach(card => {
        if (card.style.display !== 'none') {
            hasVisible = true;
        }
    });
    
    if (!hasVisible) {
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'empty-state';
        emptyDiv.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--subtitle-color);">
                <i class="fas fa-question-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3>No questions found</h3>
                <p>Try adjusting your filters or add new questions.</p>
            </div>
        `;
        questionList.appendChild(emptyDiv);
    }
}

/**
 * Open modal for add or edit
 * @param {string} mode - 'add' or 'edit'
 * @param {object} data - Data object for edit mode (optional)
 */
function openModal(mode, data) {
    const modal = document.getElementById('questionModal');
    const formAction = document.getElementById('formAction');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    
    // Reset form
    document.getElementById('questionForm').reset();
    
    if (mode === 'edit' && data) {
        // Edit mode - populate form with data
        formAction.value = 'edit';
        
        document.getElementById('questionId').value = data.id || '';
        document.getElementById('questionText').value = data.question || '';
        document.getElementById('optionA').value = data.optionA || '';
        document.getElementById('optionB').value = data.optionB || '';
        document.getElementById('optionC').value = data.optionC || '';
        document.getElementById('optionD').value = data.optionD || '';
        document.getElementById('questionCategory').value = data.categoryId || '';
        document.getElementById('questionDifficulty').value = data.difficultyValue || 'medium';
        
        // Set correct answer radio
        const correctAnswer = data.correct || 'a';
        document.querySelectorAll('input[name="correct_answer"]').forEach(radio => {
            radio.checked = radio.value === correctAnswer;
        });
        
        modalTitle.textContent = 'Edit Question';
        submitBtn.textContent = 'Update Question';
    } else {
        // Add mode
        formAction.value = 'add';
        document.getElementById('questionId').value = '';
        
        // Set default correct answer to 'a'
        document.getElementById('correctA').checked = true;
        
        modalTitle.textContent = 'Add New Question';
        submitBtn.textContent = 'Add Question';
    }
    
    // Show modal
    modal.classList.add('show');
    
    // Focus on category select
    setTimeout(function() {
        document.getElementById('questionCategory').focus();
    }, 100);
}

/**
 * Close modal
 */
function closeModal() {
    const modal = document.getElementById('questionModal');
    modal.classList.remove('show');
}

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - 'success', 'error', or 'info'
 */
function showToast(message, type) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.className = 'toast ' + type;
    toast.classList.add('show');
    
    setTimeout(function() {
        toast.classList.remove('show');
    }, 3000);
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('questionModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Form validation
document.getElementById('questionForm').addEventListener('submit', function(e) {
    const category = document.getElementById('questionCategory').value;
    const question = document.getElementById('questionText').value.trim();
    const optionA = document.getElementById('optionA').value.trim();
    const optionB = document.getElementById('optionB').value.trim();
    const optionC = document.getElementById('optionC').value.trim();
    const optionD = document.getElementById('optionD').value.trim();
    
    // Check if a correct answer is selected
    const correctAnswer = document.querySelector('input[name="correct_answer"]:checked');
    
    if (!category) {
        e.preventDefault();
        showToast('Please select a category', 'error');
        document.getElementById('questionCategory').focus();
        return false;
    }
    
    if (!question) {
        e.preventDefault();
        showToast('Please enter a question', 'error');
        document.getElementById('questionText').focus();
        return false;
    }
    
    if (!optionA || !optionB || !optionC || !optionD) {
        e.preventDefault();
        showToast('Please fill in all options', 'error');
        return false;
    }
    
    if (!correctAnswer) {
        e.preventDefault();
        showToast('Please select the correct answer', 'error');
        return false;
    }
    
    return true;
});