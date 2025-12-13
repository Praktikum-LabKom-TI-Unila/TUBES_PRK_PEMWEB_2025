// src/js/components/toast.js
// Simple Toast Notification System

(function (window, document) {
    'use strict';

    const TOAST_DURATION = 3000; // 3 seconds

    function createToastContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 z-[9999] space-y-2';
            document.body.appendChild(container);
        }
        return container;
    }

    function show(type, message) {
        const container = createToastContainer();
        
        // Determine colors based on type
        let bgColor, borderColor, icon;
        switch (type) {
            case 'success':
                bgColor = 'bg-green-900/90';
                borderColor = 'border-green-500';
                icon = '✓';
                break;
            case 'error':
                bgColor = 'bg-red-900/90';
                borderColor = 'border-red-500';
                icon = '✕';
                break;
            case 'warning':
                bgColor = 'bg-yellow-900/90';
                borderColor = 'border-yellow-500';
                icon = '⚠';
                break;
            default:
                bgColor = 'bg-warkops-panel';
                borderColor = 'border-white/20';
                icon = 'ℹ';
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `${bgColor} ${borderColor} border px-4 py-3 min-w-[300px] shadow-2xl backdrop-blur-sm animate-slide-in-right flex items-center gap-3`;
        
        toast.innerHTML = `
            <span class="text-xl">${icon}</span>
            <span class="text-sm text-white font-mono flex-1">${message}</span>
            <button class="text-white/50 hover:text-white text-lg" onclick="this.parentElement.remove()">×</button>
        `;

        container.appendChild(toast);

        // Auto remove after duration
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, TOAST_DURATION);
    }

    // Expose global
    window.ToastNotification = {
        show
    };

})(window, document);