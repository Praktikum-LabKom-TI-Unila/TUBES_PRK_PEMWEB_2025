/**
 * Auth Module
 * Komponen untuk autentikasi
 */

const AuthModule = {
    /**
     * Login via AJAX
     */
    login: async function(email, password, remember = false) {
        try {
            const response = await Ajax.post('/api/auth/login', {
                email: email,
                password: password,
                remember: remember
            });

            if (response.success) {
                showNotification('Login berhasil!', 'success');
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1000);
            } else {
                showNotification(response.message || 'Login gagal', 'error');
            }

            return response;
        } catch (error) {
            showNotification('Terjadi kesalahan saat login', 'error');
            throw error;
        }
    },

    /**
     * Register via AJAX
     */
    register: async function(name, email, password) {
        try {
            const response = await Ajax.post('/api/auth/register', {
                name: name,
                email: email,
                password: password
            });

            if (response.success) {
                showNotification('Registrasi berhasil! Silakan login.', 'success');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
            } else {
                showNotification(response.message || 'Registrasi gagal', 'error');
            }

            return response;
        } catch (error) {
            showNotification('Terjadi kesalahan saat registrasi', 'error');
            throw error;
        }
    },

    /**
     * Check authentication status
     */
    checkAuth: async function() {
        try {
            const response = await Ajax.get('/api/auth/check');
            return response.data.authenticated;
        } catch (error) {
            console.error('Check auth error:', error);
            return false;
        }
    },

    /**
     * Get current user
     */
    getCurrentUser: async function() {
        try {
            const response = await Ajax.get('/api/auth/me');
            return response.data.user;
        } catch (error) {
            console.error('Get current user error:', error);
            return null;
        }
    },

    /**
     * Logout
     */
    logout: function() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '/logout';
        }
    }
};

// Export to global scope
window.AuthModule = AuthModule;
