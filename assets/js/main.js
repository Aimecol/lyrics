/**
 * Main JavaScript for Song Lyrics Platform
 * Minimal JavaScript for enhanced user experience
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile menu toggle for admin panel
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    
    if (mobileMenuToggle && adminSidebar) {
        mobileMenuToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && 
                !adminSidebar.contains(e.target) && 
                !mobileMenuToggle.contains(e.target)) {
                adminSidebar.classList.remove('show');
            }
        });
    }
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.confirm-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const itemName = this.dataset.itemName || 'this item';
            if (!confirm(`Are you sure you want to delete "${itemName}"? This action cannot be undone.`)) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-hide flash messages
    const flashMessages = document.querySelectorAll('.alert');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
    
    // Search form enhancement
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    
    if (searchForm && searchInput) {
        // Focus search input with keyboard shortcut (Ctrl+K or Cmd+K)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
        
        // Clear search button
        const clearSearch = document.createElement('button');
        clearSearch.type = 'button';
        clearSearch.className = 'search-clear';
        clearSearch.innerHTML = '&times;';
        clearSearch.style.cssText = `
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 18px;
            color: #6c757d;
            cursor: pointer;
            display: none;
        `;
        
        const searchContainer = document.querySelector('.search-container');
        if (searchContainer) {
            searchContainer.style.position = 'relative';
            searchContainer.appendChild(clearSearch);
            
            // Show/hide clear button
            searchInput.addEventListener('input', function() {
                clearSearch.style.display = this.value ? 'block' : 'none';
            });
            
            // Clear search
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                searchInput.focus();
            });
        }
    }
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ced4da';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                const firstInvalidField = form.querySelector('[style*="border-color: rgb(220, 53, 69)"]');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            }
        });
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
        
        // Initial resize
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    });
    
    // Loading states for forms
    const submitButtons = document.querySelectorAll('button[type="submit"], input[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                this.disabled = true;
                this.innerHTML = '<span class="spinner"></span> Processing...';
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = this.dataset.originalText || 'Submit';
                }, 10000);
            }
        });
        
        // Store original text
        button.dataset.originalText = button.innerHTML;
    });
    
    // Keyboard navigation for pagination
    document.addEventListener('keydown', function(e) {
        const pagination = document.querySelector('.pagination');
        if (!pagination) return;
        
        const currentPage = pagination.querySelector('.pagination-link.current');
        if (!currentPage) return;
        
        let targetLink = null;
        
        if (e.key === 'ArrowLeft') {
            // Previous page
            const prevLink = currentPage.parentElement.previousElementSibling;
            if (prevLink && prevLink.querySelector('.pagination-link')) {
                targetLink = prevLink.querySelector('.pagination-link');
            }
        } else if (e.key === 'ArrowRight') {
            // Next page
            const nextLink = currentPage.parentElement.nextElementSibling;
            if (nextLink && nextLink.querySelector('.pagination-link')) {
                targetLink = nextLink.querySelector('.pagination-link');
            }
        }
        
        if (targetLink && !targetLink.classList.contains('current')) {
            e.preventDefault();
            targetLink.click();
        }
    });
    
    // Copy to clipboard functionality (for sharing lyrics)
    const copyButtons = document.querySelectorAll('.copy-lyrics');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const lyricsContainer = document.querySelector('.lyrics-container');
            if (lyricsContainer) {
                const text = lyricsContainer.innerText;
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        showToast('Lyrics copied to clipboard!');
                    });
                } else {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showToast('Lyrics copied to clipboard!');
                }
            }
        });
    });
    
    // Simple toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 1000;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        // Animate out and remove
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // Initialize any tooltips or other components
    initializeComponents();
});

function initializeComponents() {
    // Add any additional component initialization here
    console.log('Lyrics Platform initialized');
}
