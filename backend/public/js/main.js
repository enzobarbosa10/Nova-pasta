// ===========================
// Global Utilities
// ===========================

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    const colors = {
        success: '#0F6E56',
        error:   '#A32D2D',
        info:    '#3B4A2F',
        warning: '#B8860B'
    };
    notification.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        background: ${colors[type] || colors.info};
        color: #F5EFE0;
        padding: 13px 20px;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(44,31,20,0.15);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        font-weight: 400;
        font-size: 13.5px;
        font-family: 'Inter', sans-serif;
        letter-spacing: 0.01em;
        border: 0.5px solid rgba(245,239,224,0.2);
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 0
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ===========================
// Global Search
// ===========================
const globalSearchInput = document.getElementById('globalSearch');

if (globalSearchInput) {
    const performSearch = debounce(async (query) => {
        if (query.length < 2) return;
        
        try {
            // Add search functionality here
            console.log('Searching for:', query);
        } catch (error) {
            console.error('Search error:', error);
        }
    }, 300);

    globalSearchInput.addEventListener('input', (e) => {
        performSearch(e.target.value);
    });
}

// ===========================
// Notifications Toggle
// ===========================
function toggleNotifications() {
    showNotification('Notifications feature coming soon!', 'info');
}

// ===========================
// Mobile Menu Toggle
// ===========================
let isMobileMenuOpen = false;

function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    isMobileMenuOpen = !isMobileMenuOpen;
    
    if (isMobileMenuOpen) {
        sidebar.classList.add('mobile-open');
    } else {
        sidebar.classList.remove('mobile-open');
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        const sidebar = document.querySelector('.sidebar');
        const isClickInsideSidebar = sidebar.contains(e.target);
        const isMenuButton = e.target.closest('.menu-button');
        
        if (!isClickInsideSidebar && !isMenuButton && isMobileMenuOpen) {
            toggleMobileMenu();
        }
    }
});

// ===========================
// Add CSS Animations
// ===========================
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
`;
document.head.appendChild(style);

// ===========================
// Initialize on Page Load
// ===========================
document.addEventListener('DOMContentLoaded', () => {
    console.log('ExpeditionOS initialized');
    
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
});

// Export utilities to window
window.showNotification = showNotification;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.toggleNotifications = toggleNotifications;
window.toggleMobileMenu = toggleMobileMenu;
