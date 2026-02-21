// ==========================================
// ADMIN.JS - Complete JavaScript for Admin Panel
// ==========================================

// ==========================================
// 1. MOBILE MENU & SIDEBAR TOGGLE
// ==========================================

function toggleMenu() {
    const navLinks = document.getElementById('navLinks');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    // Toggle navbar menu (mobile)
    if (navLinks) {
        navLinks.classList.toggle('active');
    }
    
    // Toggle sidebar (mobile)
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
    
    // Toggle overlay
    if (overlay) {
        overlay.classList.toggle('active');
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const navLinks = document.getElementById('navLinks');
    
    if (sidebar) sidebar.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
    if (navLinks) navLinks.classList.remove('active');
}

// Close sidebar when clicking overlay
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.querySelector('.nav-toggle');
    
    if (overlay && overlay.classList.contains('active')) {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
            closeSidebar();
        }
    }
});

// ==========================================
// 2. SIDEBAR NAVIGATION
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // Handle sidebar item clicks
    const sidebarItems = document.querySelectorAll('.sidebar-item[data-page]');
    
    sidebarItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const page = this.getAttribute('data-page');
            
            // Remove active from all
            sidebarItems.forEach(i => i.classList.remove('active'));
            // Add active to clicked
            this.classList.add('active');
            
            // Close sidebar on mobile
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
            
            // Load page content (for future use)
            loadPageContent(page);
        });
    });
    
    // Animate stats on load
    animateStats();
});

// ==========================================
// 3. MODAL FUNCTIONS
// ==========================================

function showAddQuizModal() {
    const modal = document.getElementById('quizModal');
    if (modal) {
        modal.style.display = 'block';
        // Focus on first input
        setTimeout(() => {
            document.getElementById('quizTitle').focus();
        }, 100);
    }
}

function closeModal() {
    const modal = document.getElementById('quizModal');
    if (modal) {
        modal.style.display = 'none';
        // Reset form
        const form = document.getElementById('quizForm');
        if (form) form.reset();
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('quizModal');
    if (event.target === modal) {
        closeModal();
    }
};

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// ==========================================
// 4. QUIZ FORM HANDLING
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    const quizForm = document.getElementById('quizForm');
    
    if (quizForm) {
        quizForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const title = document.getElementById('quizTitle').value.trim();
            const category = document.getElementById('quizCategory').value;
            const questions = document.getElementById('quizQuestions').value;
            
            if (!title || !category || !questions) {
                showToast('Please fill all fields', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = quizForm.querySelector('.btn-submit');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Adding...';
            submitBtn.disabled = true;
            
            // Send AJAX request
            fetch('admin_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=add_quiz&title=' + encodeURIComponent(title) + 
                      '&category=' + encodeURIComponent(category) + 
                      '&questions=' + encodeURIComponent(questions)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Quiz added successfully!', 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// ==========================================
// 5. QUIZ ACTIONS (EDIT/DELETE)
// ==========================================

function editQuiz(id) {
    showToast('Opening edit for quiz #' + id, 'info');
    
    // For future: fetch quiz data and populate edit modal
    // fetch('admin_ajax.php?action=get_quiz&id=' + id)
    //     .then(res => res.json())
    //     .then(data => { ... });
}

function deleteQuiz(id) {
    if (!id || id === 0) {
        showToast('Invalid quiz ID', 'error');
        return;
    }
    
    if (confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
        fetch('admin_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete_quiz&id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Quiz deleted successfully!', 'success');
                // Remove card from DOM with animation
                const card = event.target.closest('.quiz-card');
                if (card) {
                    card.style.transition = 'all 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => card.remove(), 300);
                }
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred', 'error');
            console.error('Error:', error);
        });
    }
}

// ==========================================
// 6. USER ACTIONS (EDIT/DELETE)
// ==========================================

function editUser(id) {
    showToast('Opening edit for user #' + id, 'info');
    
    // For future: fetch user data and populate edit modal
}

function deleteUser(id) {
    if (!id || id === 0) {
        showToast('Invalid user ID', 'error');
        return;
    }
    
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch('admin_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete_user&id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('User deleted successfully!', 'success');
                // Remove row from DOM
                const row = event.target.closest('tr');
                if (row) {
                    row.style.transition = 'all 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred', 'error');
            console.error('Error:', error);
        });
    }
}

// ==========================================
// 7. TOAST NOTIFICATIONS
// ==========================================

function showToast(message, type) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    // Set message
    toast.textContent = message;
    
    // Remove all type classes
    toast.classList.remove('success', 'error', 'info');
    
    // Set type
    toast.classList.add(type);
    
    // Show toast
    toast.classList.add('show');
    
    // Play sound (optional)
    // playNotificationSound();
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Optional: Notification sound
function playNotificationSound() {
    try {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2Onp2PfnF0hZSdlIx7cHV/jZSdlIx7cHV/jZSdlIx7cHV/jZSdlIx7cHV/jZSdlIx7cA==');
        audio.volume = 0.3;
        audio.play().catch(() => {});
    } catch (e) {}
}

// ==========================================
// 8. PAGE CONTENT LOADING (Future Use)
// ==========================================

function loadPageContent(page) {
    console.log('Loading page:', page);
    // For future: dynamically load content based on sidebar clicks
    // fetch('admin_ajax.php?action=load_page&page=' + page)
    //     .then(res => res.text())
    //     .then(html => { document.getElementById('content').innerHTML = html; });
}

// ==========================================
// 9. STATS ANIMATION
// ==========================================

function animateStats() {
    const statNumbers = document.querySelectorAll('.stat-info p');
    
    statNumbers.forEach(stat => {
        const text = stat.textContent.replace(/,/g, '');
        const finalValue = parseInt(text);
        
        if (!isNaN(finalValue) && finalValue > 0) {
            // Start from 0
            stat.textContent = '0';
            
            // Animate to final value
            const duration = 1000;
            const start = 0;
            const startTime = performance.now();
            
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(easeOut * (finalValue - start) + start);
                
                stat.textContent = current.toLocaleString();
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }
            
            requestAnimationFrame(update);
        }
    });
}

// ==========================================
// 10. TABLE SEARCH
// ==========================================

function searchTable(tableId, searchTerm) {
    const table = document.querySelector('#' + tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ==========================================
// 11. KEYBOARD SHORTCUTS
// ==========================================

document.addEventListener('keydown', function(e) {
    // Escape to close modal
    if (e.key === 'Escape') {
        closeModal();
    }
    
    // Ctrl + N for new quiz
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        showAddQuizModal();
    }
    
    // Ctrl + R to refresh (only if not in input)
    if (e.ctrlKey && e.key === 'r') {
        if (document.activeElement.tagName !== 'INPUT' && 
            document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            refreshData();
        }
    }
});

// ==========================================
// 12. REFRESH DATA
// ==========================================

function refreshData() {
    showToast('Refreshing data...', 'info');
    location.reload();
}

// ==========================================
// 13. EXPORT FUNCTIONALITY (Future Use)
// ==========================================

function exportData(type) {
    showToast('Preparing ' + type + ' data for export...', 'info');
    
    // For future: generate CSV/Excel
    // const data = fetch data from server
    // const csv = convertToCSV(data);
    // downloadFile(csv, type + '_export.csv');
}

function downloadFile(content, filename) {
    const blob = new Blob([content], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

// ==========================================
// 14. PAGINATION (Future Use)
// ==========================================

let currentPage = 1;
const itemsPerPage = 10;

function loadMore(type) {
    currentPage++;
    showToast('Loading more ' + type + '...', 'info');
    // Fetch and append more data
}

// ==========================================
// 15. CONFIRM DIALOG HELPER
// ==========================================

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// ==========================================
// 16. FORM VALIDATION
// ==========================================

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#ef4444';
            isValid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });
    
    return isValid;
}

// ==========================================
// 17. SORT TABLE
// ==========================================

function sortTable(tableId, columnIndex) {
    const table = document.querySelector('#' + tableId);
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Toggle sort direction
    const isAscending = table.getAttribute('data-sort') !== 'asc';
    table.setAttribute('data-sort', isAscending ? 'asc' : 'desc');
    
    rows.sort((a, b) => {
        const aText = a.cells[columnIndex].textContent.trim();
        const bText = b.cells[columnIndex].textContent.trim();
        
        if (isAscending) {
            return aText.localeCompare(bText, undefined, { numeric: true });
        } else {
            return bText.localeCompare(aText, undefined, { numeric: true });
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// ==========================================
// 18. HANDLE WINDOW RESIZE
// ==========================================

let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        // Close sidebar on resize to desktop
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    }, 250);
});

// ==========================================
// 19. INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin panel initialized');
    
    // Add click handlers to all action buttons
    initializeActionButtons();
});

function initializeActionButtons() {
    // Add hover effects or additional handlers if needed
    const buttons = document.querySelectorAll('.btn-add, .action-btn, .play-btn');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Prevent double-clicks
            if (this.disabled) {
                e.preventDefault();
                return;
            }
        });
    });
}

// ==========================================
// 20. ERROR HANDLING
// ==========================================

window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.message);
});

// Handle unhandled promise rejections
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled Promise Rejection:', e.reason);
});