(() => {
    const STORAGE_PREFIX = 'sipinda_petugas_';
    const KEYS = {
        token: `${STORAGE_PREFIX}token`,
        role: `${STORAGE_PREFIX}role`,
        user: `${STORAGE_PREFIX}user`
    };
    const LOGIN_PAGE = 'petugas-login.php';
    const DASHBOARD_PAGE = 'petugas-task-list.php';
    const API_BASE_URL = 'http://localhost:9090/api';
    const FILE_BASE_URL = 'http://localhost:9090';

    const storage = {
        get(key) {
            try {
                return localStorage.getItem(key);
            } catch (error) {
                console.warn('[PetugasAuth] Gagal baca storage', error);
                return null;
            }
        },
        set(key, value) {
            try {
                localStorage.setItem(key, value);
            } catch (error) {
                console.warn('[PetugasAuth] Gagal tulis storage', error);
            }
        },
        remove(key) {
            try {
                localStorage.removeItem(key);
            } catch (error) {
                console.warn('[PetugasAuth] Gagal hapus storage', error);
            }
        }
    };

    const PetugasAuth = {
        API_BASE_URL,
        FILE_BASE_URL,
        getToken() {
            return storage.get(KEYS.token);
        },
        getRole() {
            return storage.get(KEYS.role);
        },
        getUser() {
            const raw = storage.get(KEYS.user);
            if (!raw) return null;
            try {
                return JSON.parse(raw);
            } catch (error) {
                console.warn('[PetugasAuth] Format user tidak valid', error);
                return null;
            }
        },
        setSession(token, user) {
            if (!token) return;
            storage.set(KEYS.token, token);
            storage.set(KEYS.role, user?.role || '');
            storage.set(KEYS.user, JSON.stringify(user || {}));
        },
        clearSession() {
            storage.remove(KEYS.token);
            storage.remove(KEYS.role);
            storage.remove(KEYS.user);
        },
        isOfficer() {
            return !!(this.getToken() && this.getRole() === 'officer');
        },
        getAuthHeaders() {
            const token = this.getToken();
            if (!token) return {};
            return {
                Authorization: `Bearer ${token}`
            };
        },
        resolveFileUrl(path) {
            if (!path) return '';
            if (/^https?:\/\//i.test(path)) return path;
            if (path.startsWith('/')) return `${FILE_BASE_URL}${path}`;
            return `${FILE_BASE_URL}/${path}`;
        },
        requireOfficer() {
            if (!this.isOfficer()) {
                this.clearSession();
                window.location.href = LOGIN_PAGE;
            }
        },
        redirectToDashboard() {
            if (this.isOfficer()) {
                window.location.href = DASHBOARD_PAGE;
            }
        },
        handleLogout() {
            this.clearSession();
            window.location.href = LOGIN_PAGE;
        }
    };

    const PetugasAPI = {
        async request(path, options = {}) {
            const {
                method = 'GET',
                body = null,
                formData = null,
                query = null,
                headers = {}
            } = options;

            const normalizedBase = PetugasAuth.API_BASE_URL.replace(/\/$/, '');
            const normalizedPath = path.startsWith('/')
                ? path
                : `/${path}`;
            const url = new URL(`${normalizedBase}${normalizedPath}`);

            if (query && typeof query === 'object') {
                Object.entries(query).forEach(([key, value]) => {
                    if (value !== undefined && value !== null && value !== '') {
                        url.searchParams.set(key, value);
                    }
                });
            }

            const fetchOptions = {
                method,
                headers: {
                    ...PetugasAuth.getAuthHeaders(),
                    ...headers
                }
            };

            if (formData) {
                fetchOptions.body = formData;
            } else if (body !== null) {
                fetchOptions.headers['Content-Type'] = 'application/json';
                fetchOptions.body = JSON.stringify(body);
            }

            let response;
            let payload;
            try {
                response = await fetch(url.toString(), fetchOptions);
                payload = await response.json().catch(() => null);
            } catch (error) {
                console.error('[PetugasAPI] Jaringan gagal', error);
                throw new Error('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
            }

            if (response.status === 401) {
                PetugasAuth.handleLogout();
                throw new Error('Sesi Anda berakhir, silakan login kembali.');
            }

            if (!response.ok || payload?.status === 'error') {
                const message = payload?.message || 'Permintaan gagal diproses.';
                const error = new Error(message);
                error.payload = payload;
                throw error;
            }

            return payload;
        }
    };

    window.PetugasAuth = PetugasAuth;
    window.PetugasAPI = PetugasAPI;
})();
