'use strict';

(function (global) {
  const LOGIN_PAGE = 'admin-login.php';

  const ensureToastContainer = () => {
    let container = document.getElementById('adminToastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'adminToastContainer';
      container.className = 'fixed top-4 right-4 flex flex-col gap-3 z-50';
      document.body.appendChild(container);
    }
    return container;
  };

  const resolveBackendBaseUrl = () => {
    const metaOverride = document.querySelector('meta[name="sipinda-backend-url"]');
    if (metaOverride?.content) {
      return metaOverride.content.replace(/\/+$/, '');
    }

    const globalOverride = global.SIPINDA_BACKEND_URL || global.__SIPINDA_BACKEND_URL__;
    if (typeof globalOverride === 'string' && globalOverride.trim()) {
      return globalOverride.trim().replace(/\/+$/, '');
    }

    const protocol = window.location.protocol === 'https:' ? 'https:' : 'http:';
    const hostname = window.location.hostname || 'localhost';
    const backendPort = hostname === 'localhost' ? 9090 : window.location.port || 80;
    return `${protocol}//${hostname}:${backendPort}`;
  };

  const safeParseJson = async (response) => {
    const rawText = await response.text();
    if (!rawText) return {};
    try {
      return JSON.parse(rawText);
    } catch (error) {
      return { __parseError: true, rawText };
    }
  };

  const getAdminToken = () => {
    return localStorage.getItem('sipinda_admin_token') || sessionStorage.getItem('sipinda_admin_token');
  };

  const redirectToLogin = () => {
    window.location.replace(LOGIN_PAGE);
  };

  const buildErrorMessage = (payload, response) => {
    if (!payload) {
      return 'Terjadi kesalahan tak terduga.';
    }

    const apiErrors = Array.isArray(payload?.errors) ? payload.errors : [];
    const fieldMessages = apiErrors
      .map((err) => err?.message)
      .filter(Boolean)
      .join(', ');

    const code = payload?.code || response?.status;
    const baseMessage = payload?.message || 'Permintaan gagal diproses.';
    const detail = fieldMessages ? ` Detail: ${fieldMessages}` : '';
    return `${baseMessage}${detail} (kode ${code}).`;
  };

  const requestWithAuth = async (endpoint, options = {}) => {
    const token = getAdminToken();
    if (!token) {
      redirectToLogin();
      throw new Error('Sesi admin tidak ditemukan.');
    }

    const isFormData = options.body instanceof FormData;
    const headers = {
      ...(options.headers || {}),
      Authorization: `Bearer ${token}`,
    };

    if (!headers['Content-Type'] && options.body && !isFormData) {
      headers['Content-Type'] = 'application/json';
    }

    const backendUrl = resolveBackendBaseUrl();
    const fullUrl = `${backendUrl}${endpoint}`;
    const bodyPreview = isFormData
      ? '[FormData]'
      : typeof options.body === 'string'
      ? options.body.substring(0, 200)
      : null;
    console.log('[AdminAPI] 🚀 REQUEST:', {
      url: fullUrl,
      method: options.method || 'GET',
      headers: headers,
      bodyPreview,
    });

    const response = await fetch(fullUrl, {
      method: options.method || 'GET',
      headers,
      mode: 'cors',
      body: options.body,
    });

    console.log('[AdminAPI] 📥 RAW RESPONSE:', {
      status: response.status,
      statusText: response.statusText,
      headers: Object.fromEntries([...response.headers.entries()]),
      ok: response.ok,
    });

    const payload = await safeParseJson(response);
    console.log('[AdminAPI] 📦 PARSED PAYLOAD:', payload);

    if (response.status === 401) {
      redirectToLogin();
      throw new Error('Sesi admin berakhir. Silakan login ulang.');
    }

    if (payload?.__parseError) {
      console.error('[AdminAPI] Parse error, raw text:', payload.rawText);
      throw new Error('Respon server tidak valid.');
    }

    if (!response.ok || payload?.status === 'error') {
      throw new Error(buildErrorMessage(payload, response));
    }

    return payload?.data ?? payload ?? null;
  };

  const showToast = (message, type = 'success') => {
    if (!message) return;
    const container = ensureToastContainer();
    const toast = document.createElement('div');
    const palette =
      type === 'error'
        ? 'bg-red-50 border-red-200 text-red-700'
        : type === 'info'
        ? 'bg-blue-50 border-blue-200 text-blue-700'
        : 'bg-green-50 border-green-200 text-green-700';
    toast.className = `px-4 py-3 rounded-xl border shadow-lg flex items-center gap-3 text-sm ${palette}`;
    toast.innerHTML = `<span class="material-icons text-base">${
      type === 'error' ? 'error' : type === 'info' ? 'info' : 'check_circle'
    }</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('opacity-0', 'translate-y-1');
      setTimeout(() => toast.remove(), 250);
    }, 2800);
  };

  const showInlineAlert = (element, message = '', type = 'error') => {
    if (!element) return;
    if (!message) {
      element.classList.add('hidden');
      element.textContent = '';
      return;
    }
    const palette =
      type === 'success'
        ? 'bg-green-50 border-green-200 text-green-700'
        : type === 'info'
        ? 'bg-blue-50 border-blue-200 text-blue-700'
        : 'bg-red-50 border-red-200 text-red-700';
    element.className = `mb-4 px-4 py-3 rounded-xl border text-sm ${palette}`;
    element.textContent = message;
    element.classList.remove('hidden');
  };

  const setButtonLoading = (button, isLoading, loadingText = 'Memproses...') => {
    if (!button) return;
    const defaultText = button.dataset.defaultText || button.textContent.trim();
    if (!button.dataset.defaultText) {
      button.dataset.defaultText = defaultText;
    }
    button.disabled = isLoading;
    button.classList.toggle('opacity-70', isLoading);
    button.classList.toggle('cursor-not-allowed', isLoading);
    button.innerHTML = isLoading
      ? `<span class="flex items-center gap-2"><span class="material-icons animate-spin text-base">autorenew</span><span>${loadingText}</span></span>`
      : defaultText;
  };

  const formatDate = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(date);
  };

  const formatDateTime = (value) => {
    if (!value) return '-';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
  };

  const updateStoredAdminProfile = (profile = {}) => {
    const currentRaw = localStorage.getItem('sipinda_admin_user') || sessionStorage.getItem('sipinda_admin_user');
    let current = {};
    if (currentRaw) {
      try {
        current = JSON.parse(currentRaw);
      } catch (error) {
        current = {};
      }
    }
    const nextProfile = { ...current, ...profile };
    const serialized = JSON.stringify(nextProfile);
    localStorage.setItem('sipinda_admin_user', serialized);
    sessionStorage.setItem('sipinda_admin_user', serialized);
  };

  global.AdminAPI = {
    resolveBackendBaseUrl,
    request: requestWithAuth,
    getToken: getAdminToken,
    redirectToLogin,
    showToast,
    showInlineAlert,
    setButtonLoading,
    formatDate,
    formatDateTime,
    updateStoredAdminProfile,
  };
})(window);
