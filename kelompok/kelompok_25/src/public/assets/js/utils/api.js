/**
 * API Client Utility
 * Handles all HTTP requests to backend API
 */
const ApiClient = {
    baseURL: '/api',

    /**
     * Make HTTP request
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);
            
            // Check if response is HTML (redirect to login)
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                // Redirect to login page
                window.location.href = '/login';
                const error = new Error('Sesi telah berakhir. Silakan login kembali.');
                error.status = 401;
                error.errors = {};
                throw error;
            }
            
            // Try to parse JSON response
            let data;
            try {
                data = await response.json();
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                const error = new Error('Server mengembalikan response yang tidak valid');
                error.status = response.status;
                error.errors = {};
                throw error;
            }

            if (!response.ok) {
                const error = new Error(data.message || 'Terjadi kesalahan');
                error.status = response.status;
                error.errors = data.errors || {};
                throw error;
            }

            return data;
        } catch (error) {
            // Network error or other fetch errors
            if (!error.status) {
                const netError = new Error('Tidak dapat terhubung ke server');
                netError.status = 0;
                netError.errors = {};
                throw netError;
            }
            throw error;
        }
    },

    /**
     * GET request
     */
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        
        return this.request(url, {
            method: 'GET'
        });
    },

    /**
     * POST request
     */
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    /**
     * PUT request (using POST with _method override)
     */
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify({
                ...data,
                _method: 'PUT'
            })
        });
    },

    /**
     * DELETE request (using POST with _method override)
     */
    async delete(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify({
                ...data,
                _method: 'DELETE'
            })
        });
    },

    /**
     * Upload file
     */
    async upload(endpoint, formData) {
        const url = `${this.baseURL}${endpoint}`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
                // Don't set Content-Type, browser will set it with boundary
            });

            const data = await response.json();

            if (!response.ok) {
                throw {
                    status: response.status,
                    message: data.message || 'Terjadi kesalahan',
                    errors: data.errors || {}
                };
            }

            return data;
        } catch (error) {
            if (!error.status) {
                throw {
                    status: 0,
                    message: 'Tidak dapat terhubung ke server',
                    errors: {}
                };
            }
            throw error;
        }
    }
};
