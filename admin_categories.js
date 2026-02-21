// admin_categories.js

// Color mapping for selection
const colorMap = {
    '#4a90d9': 0,
    '#10b981': 1,
    '#f59e0b': 2,
    '#8b5cf6': 3,
    '#ef4444': 4,
    '#ec4899': 5,
    '#06b6d4': 6,
    '#6366f1': 7
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize color option click handlers
    initColorOptions();
    
    // Auto-hide toast messages
    initToast();
});

/**
 * Initialize color option click handlers
 */
function initColorOptions() {
    const colorOptions = document.querySelectorAll('.color-option');
    
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            const color = this.getAttribute('data-color');
            selectColor(this, color);
        });
    });
}

/**
 * Open modal for add or edit
 * @param {string} mode - 'add' or 'edit'
 * @param {object} data - Data object for edit mode (optional)
 */
function openModal(mode, data) {
    const modal = document.getElementById('categoryModal');
    const formAction = document.getElementById('formAction');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    
    // Reset form
    document.getElementById('categoryForm').reset();
    
    if (mode === 'edit' && data) {
        // Edit mode - populate form with data
        formAction.value = 'edit';
        document.getElementById('categoryId').value = data.id || '';
        document.getElementById('categoryName').value = data.name || '';
        document.getElementById('categoryDesc').value = data.desc || '';
        document.getElementById('categoryIcon').value = data.icon || 'fa-book';
        
        const color = data.color || '#4a90d9';
        document.getElementById('selectedColor').value = color;
        
        // Update color selection
        document.querySelectorAll('.color-option').forEach(el => el.classList.remove('selected'));
        if (colorMap[color] !== undefined) {
            document.querySelectorAll('.color-option')[colorMap[color]].classList.add('selected');
        }
        
        modalTitle.textContent = 'Edit Category';
        submitBtn.textContent = 'Update Category';
    } else {
        // Add mode
        formAction.value = 'add';
        document.getElementById('categoryId').value = '';
        document.getElementById('selectedColor').value = '#4a90d9';
        
        // Reset color selection
        document.querySelectorAll('.color-option').forEach(el => el.classList.remove('selected'));
        document.querySelector('.color-option').classList.add('selected');
        
        modalTitle.textContent = 'Add New Category';
        submitBtn.textContent = 'Add Category';
    }
    
    // Show modal
    modal.classList.add('show');
    
    // Focus on name input
    setTimeout(function() {
        document.getElementById('categoryName').focus();
    }, 100);
}

/**
 * Close modal
 */
function closeModal() {
    const modal = document.getElementById('categoryModal');
    modal.classList.remove('show');
}

/**
 * Select a color
 * @param {HTMLElement} element - The clicked color element
 * @param {string} color - The color value
 */
function selectColor(element, color) {
    // Remove selected from all
    document.querySelectorAll('.color-option').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selected to clicked
    element.classList.add('selected');
    
    // Update hidden input
    document.getElementById('selectedColor').value = color;
}

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

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('categoryModal');
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
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    const name = document.getElementById('categoryName').value.trim();
    
    if (!name) {
        e.preventDefault();
        showToast('Please enter a category name', 'error');
        document.getElementById('categoryName').focus();
        return false;
    }
    
    return true;
});

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