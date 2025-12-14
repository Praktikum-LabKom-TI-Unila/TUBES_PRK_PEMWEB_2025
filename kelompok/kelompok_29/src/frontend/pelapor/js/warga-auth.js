(() => {
    const STORAGE_PREFIX = 'sipinda_pelapor_';
    const KEYS = {
        token: `${STORAGE_PREFIX}token`,
        role: `${STORAGE_PREFIX}role`,
        user: `${STORAGE_PREFIX}user`
    };
    const LOGIN_PAGE = 'warga-login.php';
    const DASHBOARD_PAGE = 'warga-dashboard.php';

    const resolveBackendBaseUrl = () => {
        const metaOverride = document.querySelector('meta[name="sipinda-backend-url"]');
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
    const API_BASE_URL = `${BACKEND_BASE_URL}/api`;

    const storageEngines = [];
    try {
        if (window.localStorage) storageEngines.push(window.localStorage);
    } catch (error) {
        console.warn('[PelaporAuth] localStorage tidak tersedia', error);
    }
    try {
        if (window.sessionStorage) storageEngines.push(window.sessionStorage);
    } catch (error) {
        console.warn('[PelaporAuth] sessionStorage tidak tersedia', error);
    }

    const storage = {
        get(key) {
            for (const engine of storageEngines) {
                try {
                    const value = engine.getItem(key);
                    if (value) return value;
                } catch (error) {
                    console.warn('[PelaporAuth] Gagal baca storage', error);
                }
            }
            return null;
        },
        set(key, value) {
            storageEngines.forEach(engine => {
                try {
                    engine.setItem(key, value);
                } catch (error) {
                    console.warn('[PelaporAuth] Gagal tulis storage', error);
                }
            });
        },
        remove(key) {
            storageEngines.forEach(engine => {
                try {
                    engine.removeItem(key);
                } catch (error) {
                    console.warn('[PelaporAuth] Gagal hapus storage', error);
                }
            });
        }
    };

    const PelaporAuth = {
        API_BASE_URL,
        FILE_BASE_URL: BACKEND_BASE_URL,
        getToken() {
            return storage.get(KEYS.token);
        },
        getRole() {
            const raw = storage.get(KEYS.role);
            return raw ? raw.toLowerCase() : '';
        },
        getUser() {
            const raw = storage.get(KEYS.user);
            if (!raw) return null;
            try {
                return JSON.parse(raw);
            } catch (error) {
                return null;
            }
        },
        setSession(token, user) {
            if (!token) return;
            const normalizedRole = (user?.role || '').toLowerCase();
            storage.set(KEYS.token, token);
            storage.set(KEYS.role, normalizedRole);
            storage.set(KEYS.user, JSON.stringify(user || {}));
        },
        clearSession() {
            storage.remove(KEYS.token);
            storage.remove(KEYS.role);
            storage.remove(KEYS.user);
        },
        isPelapor() {
            const role = this.getRole();
            return !!(this.getToken() && (role === 'pelapor' || role === 'warga'));
        },
        requirePelapor() {
            if (!this.isPelapor()) {
                this.clearSession();
                window.location.href = LOGIN_PAGE;
            }
        },
        redirectToDashboard() {
            if (this.isPelapor()) {
                window.location.href = DASHBOARD_PAGE;
            }
        },
        handleLogout() {
            this.clearSession();
            window.location.href = LOGIN_PAGE;
        },
        resolveFileUrl(path) {
            if (!path) return '';
            const trimmed = path.trim().replace(/\/admin(?=\/uploads)/gi, '');
            try {
                const parsed = new URL(trimmed, BACKEND_BASE_URL);
                return parsed.toString();
            } catch (error) {
                if (trimmed.startsWith('/')) return `${BACKEND_BASE_URL}${trimmed}`;
                return `${BACKEND_BASE_URL}/${trimmed}`;
            }
        }
    };

    const PelaporAPI = {
        async request(path, options = {}) {
            const token = PelaporAuth.getToken();
            const headers = {
                ...(options.headers || {})
            };

            if (token) {
                headers.Authorization = `Bearer ${token}`;
            }

            const isFormData = options.formData instanceof FormData;
            const hasBody = options.body !== undefined && options.body !== null;
            const fetchOptions = {
                method: options.method || 'GET',
                headers: { ...headers }
            };

            if (options.formData instanceof FormData) {
                fetchOptions.body = options.formData;
            } else if (hasBody) {
                fetchOptions.headers['Content-Type'] = fetchOptions.headers['Content-Type'] || 'application/json';
                fetchOptions.body = typeof options.body === 'string' ? options.body : JSON.stringify(options.body);
            }

            const normalizedPath = path.startsWith('/') ? path : `/${path}`;
            const url = new URL(`${PelaporAuth.API_BASE_URL.replace(/\/+$/, '')}${normalizedPath}`);

            if (options.query && typeof options.query === 'object') {
                Object.entries(options.query).forEach(([key, value]) => {
                    if (value !== undefined && value !== null && value !== '') {
                        url.searchParams.set(key, value);
                    }
                });
            }

            let response;
            let payload;
            const requestMeta = {
                url: url.toString(),
                method: fetchOptions.method,
                hasBody: Boolean(fetchOptions.body),
                usesFormData: isFormData || fetchOptions.body instanceof FormData
            };
            console.info('[PelaporAPI] request', requestMeta);

            try {
                response = await fetch(requestMeta.url, fetchOptions);
                payload = await response.json().catch(() => null);
            } catch (error) {
                console.error('[PelaporAPI] Jaringan gagal', error);
                throw new Error('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
            }

            console.info('[PelaporAPI] response', {
                url: requestMeta.url,
                status: response?.status,
                ok: response?.ok,
                payload
            });

            if (response.status === 401) {
                PelaporAuth.handleLogout();
                throw new Error('Sesi Anda berakhir, silakan login kembali.');
            }

            if (!response.ok || payload?.status === 'error') {
                const message = payload?.message || 'Permintaan gagal diproses.';
                const detail = Array.isArray(payload?.errors)
                    ? payload.errors.map(err => err?.message).filter(Boolean).join(', ')
                    : '';
                throw new Error(detail ? `${message} (${detail})` : message);
            }

            return payload;
        }
    };

    window.PelaporAuth = PelaporAuth;
    window.PelaporAPI = PelaporAPI;
})();
