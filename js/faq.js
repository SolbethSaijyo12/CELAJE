// FAQ Page Functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeFAQ();
    initializeFAQSearch();
    initializeSmoothScrolling();
});

function initializeFAQ() {
    // Add click event to all FAQ questions
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            toggleFAQ(this);
        });
    });
}

function toggleFAQ(questionElement) {
    const faqItem = questionElement.parentElement;
    const isActive = faqItem.classList.contains('active');
    
    // Close all other FAQ items
    document.querySelectorAll('.faq-item.active').forEach(item => {
        if (item !== faqItem) {
            item.classList.remove('active');
        }
    });
    
    // Toggle current item
    if (isActive) {
        faqItem.classList.remove('active');
    } else {
        faqItem.classList.add('active');
        
        // Scroll to the question if it's not fully visible
        setTimeout(() => {
            const rect = questionElement.getBoundingClientRect();
            const headerHeight = 80; // Approximate header height
            
            if (rect.top < headerHeight) {
                window.scrollTo({
                    top: window.pageYOffset + rect.top - headerHeight - 20,
                    behavior: 'smooth'
                });
            }
        }, 300);
    }
}

function initializeFAQSearch() {
    const searchInput = document.getElementById('faqSearch');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim().toLowerCase();
        
        searchTimeout = setTimeout(() => {
            searchFAQ(query);
        }, 300);
    });
    
    // Clear search on escape
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            searchFAQ('');
        }
    });
}

function searchFAQ(query) {
    const faqItems = document.querySelectorAll('.faq-item');
    const faqCategories = document.querySelectorAll('.faq-category');
    let hasResults = false;
    
    if (query === '') {
        // Show all items
        faqItems.forEach(item => {
            item.classList.remove('hidden', 'highlight');
            item.style.display = 'block';
        });
        
        faqCategories.forEach(category => {
            category.style.display = 'block';
        });
        
        return;
    }
    
    faqCategories.forEach(category => {
        let categoryHasResults = false;
        const categoryItems = category.querySelectorAll('.faq-item');
        
        categoryItems.forEach(item => {
            const question = item.querySelector('.faq-question h3').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(query) || answer.includes(query)) {
                item.classList.remove('hidden');
                item.classList.add('highlight');
                item.style.display = 'block';
                categoryHasResults = true;
                hasResults = true;
            } else {
                item.classList.add('hidden');
                item.classList.remove('highlight');
                item.style.display = 'none';
            }
        });
        
        // Show/hide category based on results
        if (categoryHasResults) {
            category.style.display = 'block';
        } else {
            category.style.display = 'none';
        }
    });
    
    // Show no results message
    showNoResultsMessage(!hasResults, query);
}

function showNoResultsMessage(show, query) {
    let noResultsDiv = document.getElementById('noFAQResults');
    
    if (show) {
        if (!noResultsDiv) {
            noResultsDiv = document.createElement('div');
            noResultsDiv.id = 'noFAQResults';
            noResultsDiv.className = 'no-results-message';
            noResultsDiv.innerHTML = `
                <div class="no-results-content">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron resultados</h3>
                    <p>No encontramos preguntas frecuentes que coincidan con "<strong>${query}</strong>"</p>
                    <div class="no-results-actions">
                        <button onclick="clearFAQSearch()" class="btn-secondary">
                            <i class="fas fa-times"></i> Limpiar búsqueda
                        </button>
                        <a href="secciones/contactos.php" class="btn-primary">
                            <i class="fas fa-envelope"></i> Contactar soporte
                        </a>
                    </div>
                </div>
            `;
            
            document.querySelector('.faq-content .container').appendChild(noResultsDiv);
        } else {
            noResultsDiv.querySelector('strong').textContent = query;
            noResultsDiv.style.display = 'block';
        }
    } else {
        if (noResultsDiv) {
            noResultsDiv.style.display = 'none';
        }
    }
}

function clearFAQSearch() {
    const searchInput = document.getElementById('faqSearch');
    if (searchInput) {
        searchInput.value = '';
        searchFAQ('');
        searchInput.focus();
    }
}

function initializeSmoothScrolling() {
    // Handle quick link clicks
    document.querySelectorAll('.quick-link-card').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const headerHeight = 80;
                const targetPosition = targetElement.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Highlight the category briefly
                targetElement.style.background = 'rgba(103, 78, 130, 0.1)';
                setTimeout(() => {
                    targetElement.style.background = '';
                }, 2000);
            }
        });
    });
}

// Utility function to expand all FAQs (for testing)
function expandAllFAQ() {
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.add('active');
    });
}

// Utility function to collapse all FAQs
function collapseAllFAQ() {
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
    });
}

// Add keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'f':
                e.preventDefault();
                const searchInput = document.getElementById('faqSearch');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
                break;
        }
    }
});

