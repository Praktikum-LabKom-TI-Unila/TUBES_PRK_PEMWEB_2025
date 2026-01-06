(() => {
    const STORAGE_PREFIX = 'sipinda_petugas_';
    const KEYS = {
        token: `${STORAGE_PREFIX}token`,
        role: `${STORAGE_PREFIX}role`,
        user: `${STORAGE_PREFIX}user`
    };
    const LOGIN_PAGE = 'petugas-login.php';
    const DASHBOARD_PAGE = 'petugas-task-list.php';

    const resolveBackendBaseUrl = () => {
        const metaOverride = document.querySelector('meta[name=\"sipinda-backend-url\"]');
        if (metaOverride?.content) {
            return metaOverride.content.trim().replace(/\/+$/, '');
        }
        const globalOverride = window.SIPINDA_BACKEND_URL || window.__SIPINDA_BACKEND_URL__;
        if (typeof globalOverride === 'string' && globalOverride.trim()) {
            return globalOverride.trim().replace(/\/+$/, '');
        }
        const protocol = window.location.protocol === 'https:' ? 'https:' : 'http:';
        const hostname = window.location.hostname || 'localhost';
        if (hostname === 'localhost' || hostname === '127.0.0.1') {
            return `${protocol}//${hostname}:9090`;
        }
        const port = window.location.port ? `:${window.location.port}` : '';
        return `${protocol}//${hostname}${port}`;
    };

    const BACKEND_BASE_URL = resolveBackendBaseUrl();
    const API_BASE_URL = `${BACKEND_BASE_URL.replace(/\/+$/, '')}/api`;
    const FILE_BASE_URL = BACKEND_BASE_URL.replace(/\/+$/, '');

    const storageEngines = [];
    try {
        if (window.localStorage) storageEngines.push(window.localStorage);
    } catch (error) {
        console.warn('[PetugasAuth] localStorage tidak tersedia', error);
    }
    try {
        if (window.sessionStorage) storageEngines.push(window.sessionStorage);
    } catch (error) {
        console.warn('[PetugasAuth] sessionStorage tidak tersedia', error);
    }

    const storage = {
        get(key) {
            for (const engine of storageEngines) {
                try {
                    const value = engine.getItem(key);
                    if (value) return value;
                } catch (error) {
                    console.warn('[PetugasAuth] Gagal baca storage', error);
                }
            }
            return null;
        },
        set(key, value) {
            storageEngines.forEach(engine => {
                try {
                    engine.setItem(key, value);
                } catch (error) {
                    console.warn('[PetugasAuth] Gagal tulis storage', error);
                }
            });
        },
        remove(key) {
            storageEngines.forEach(engine => {
                try {
                    engine.removeItem(key);
                } catch (error) {
                    console.warn('[PetugasAuth] Gagal hapus storage', error);
                }
            });
        }
    };

    const PetugasAuth = {
        API_BASE_URL,
        FILE_BASE_URL,
        getToken() {
            return storage.get(KEYS.token);
        },
        getRole() {
            const stored = storage.get(KEYS.role);
            return stored ? stored.toLowerCase() : '';
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
            const normalizedRole = (user?.role || '').toLowerCase();
            storage.set(KEYS.role, normalizedRole);
            storage.set(KEYS.user, JSON.stringify(user || {}));
        },
        clearSession() {
            storage.remove(KEYS.token);
            storage.remove(KEYS.role);
            storage.remove(KEYS.user);
        },
        isOfficer() {
            const role = this.getRole();
            const normalizedRole = role.replace(/\s+/g, '_');
            const allowedRoles = ['officer', 'petugas', 'field_officer', 'petugas_lapangan'];
            const matches = allowedRoles.includes(normalizedRole) || normalizedRole.includes('officer');
            return !!(this.getToken() && matches);
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
            let sanitizedPath = path.trim();
            sanitizedPath = sanitizedPath.replace(/\/admin(?=\/uploads)/gi, '');
            const baseUrl = new URL(FILE_BASE_URL);
            try {
                const parsed = new URL(sanitizedPath, FILE_BASE_URL);
                parsed.protocol = baseUrl.protocol;
                parsed.hostname = baseUrl.hostname;
                const fallbackPort =
                    baseUrl.hostname === 'localhost'
                        ? baseUrl.port || '9090'
                        : baseUrl.port || (baseUrl.protocol === 'https:' ? '443' : '80');
                parsed.port = fallbackPort;
                return parsed.toString();
            } catch (error) {
                if (sanitizedPath.startsWith('/')) {
                    return `${FILE_BASE_URL}${sanitizedPath}`;
                }
                return `${FILE_BASE_URL}/${sanitizedPath}`;
            }
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
