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
            // Network error or JSON parse error
            if (!error.status) {
                throw {
                    status: 0,
                    message: 'Tidak dapat terhubung ke server',
                    errors: {}
                };
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
