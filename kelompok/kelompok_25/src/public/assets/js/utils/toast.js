/**
 * Toast Notification Utility
 * Shows success/error/warning messages
 */
const Toast = {
    container: null,
    icon: null,
    title: null,
    message: null,
    timeout: null,

    init() {
        this.container = document.getElementById('toast');
        this.icon = document.getElementById('toastIcon');
        this.title = document.getElementById('toastTitle');
        this.message = document.getElementById('toastMessage');
    },

    show(type, titleText, messageText, duration = 5000) {
        if (!this.container) this.init();

        // Set icon based on type
        const icons = {
            success: `<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            error: `<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>`,
            warning: `<svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>`,
            info: `<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>`
        };

        this.icon.innerHTML = icons[type] || icons.info;
        this.title.textContent = titleText;
        this.message.textContent = messageText;

        // Clear previous timeout
        if (this.timeout) {
            clearTimeout(this.timeout);
        }

        // Show toast
        this.container.classList.remove('hidden');
        
        // Add animation
        this.container.style.animation = 'slideInRight 0.3s ease-out';

        // Auto hide
        if (duration > 0) {
            this.timeout = setTimeout(() => {
                this.hide();
            }, duration);
        }
    },

    hide() {
        if (!this.container) return;
        
        this.container.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            this.container.classList.add('hidden');
        }, 300);
    },

    success(title, message, duration) {
        this.show('success', title, message, duration);
    },

    error(title, message, duration) {
        this.show('error', title, message, duration);
    },

    warning(title, message, duration) {
        this.show('warning', title, message, duration);
    },

    info(title, message, duration) {
        this.show('info', title, message, duration);
    }
};

// Add CSS animations
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
`;
document.head.appendChild(style);
