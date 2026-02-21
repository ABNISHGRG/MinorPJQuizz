// categories.js

/**
 * Filter categories by search term
 */
function filterCategories() {
    const searchInput = document.getElementById('categorySearch');
    const searchTerm = searchInput.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.category-card');
    const noResults = document.getElementById('noResults');
    
    let visibleCount = 0;
    
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        const text = card.textContent.toLowerCase();
        
        if (name.includes(searchTerm) || text.includes(searchTerm)) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

/**
 * Toggle mobile menu
 */
function toggleMenu() {
    const navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to cards
    const cards = document.querySelectorAll('.category-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.3s, transform 0.3s';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});