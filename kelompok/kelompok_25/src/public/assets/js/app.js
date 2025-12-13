/**
 * Main Application JavaScript
 * Komponen global untuk aplikasi
 */

// App initialization
document.addEventListener('DOMContentLoaded', function() {
    initApp();
});

/**
 * Initialize application
 */
function initApp() {
    // Initialize all components
    initNotifications();
    initFormValidation();
    initNavigation();
    
    console.log('Inventory Manager App initialized');
}

/**
 * Notification Component
 */
function initNotifications() {
    // Auto-hide notifications after 5 seconds
    const notifications = document.querySelectorAll('[data-notification]');
    notifications.forEach(notification => {
        setTimeout(() => {
            hideNotification(notification);
        }, 5000);
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info', duration = 3000) {
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    const icons = {
        success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
        error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
        info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} border px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transition-all transform translate-x-0`;
    notification.innerHTML = `
        ${icons[type]}
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.add('animate-fade-in');
    }, 10);

    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            hideNotification(notification);
        }, duration);
    }
}

/**
 * Hide notification
 */
function hideNotification(notification) {
    notification.style.transition = 'opacity 0.5s, transform 0.5s';
    notification.style.opacity = '0';
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 500);
}

/**
 * Form Validation Component
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Validate form
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            showFieldError(input, 'Field ini wajib diisi');
        } else {
            clearFieldError(input);
        }
    });
    
    return isValid;
}

/**
 * Show field error
 */
function showFieldError(input, message) {
    input.classList.add('border-red-500');
    
    // Remove existing error message
    const existingError = input.parentElement.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-red-600 text-sm mt-1';
    errorDiv.textContent = message;
    input.parentElement.appendChild(errorDiv);
}

/**
 * Clear field error
 */
function clearFieldError(input) {
    input.classList.remove('border-red-500');
    
    const errorDiv = input.parentElement.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Navigation Component
 */
function initNavigation() {
    // Active menu highlighting khusus sidebar
    const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
    const sidebarLinks = document.querySelectorAll('.sidebar-link');

    sidebarLinks.forEach(link => {
        const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '') || '/';
        if (currentPath === linkPath) {
            link.classList.add('sidebar-link-active');
        } else {
            link.classList.remove('sidebar-link-active');
        }
    });
}

/**
 * AJAX Helper Component
 */
const Ajax = {
    /**
     * GET request
     */
    get: async function(url) {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Ajax GET error:', error);
            throw error;
        }
    },

    /**
     * POST request
     */
    post: async function(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('Ajax POST error:', error);
            throw error;
        }
    },

    /**
     * POST with FormData (for file uploads)
     */
    postFormData: async function(url, formData) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            return await response.json();
        } catch (error) {
            console.error('Ajax POST FormData error:', error);
            throw error;
        }
    }
};

/**
 * Utility Functions
 */
const Utils = {
    /**
     * Debounce function
     */
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Format number
     */
    formatNumber: function(number, decimals = 0) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    },

    /**
     * Format currency (IDR)
     */
    formatCurrency: function(amount) {
        return 'Rp ' + this.formatNumber(amount, 2);
    },

    /**
     * Format date
     */
    formatDate: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
};

// Export to global scope
window.showNotification = showNotification;
window.Ajax = Ajax;
window.Utils = Utils;
