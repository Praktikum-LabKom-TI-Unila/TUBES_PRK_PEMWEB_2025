'use strict';

(function () {
  const LOGIN_PAGE = 'admin-login.php';

  const resolveBackendBaseUrl = () => {
    const metaOverride = document.querySelector('meta[name="sipinda-backend-url"]');
    if (metaOverride?.content) {
      return metaOverride.content.replace(/\/+$/, '');
    }

    const globalOverride = window.SIPINDA_BACKEND_URL || window.__SIPINDA_BACKEND_URL__;
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

  const ensureAlertBox = () => {
    let alertBox = document.getElementById('adminLogoutAlert');
    if (!alertBox) {
      alertBox = document.createElement('div');
      alertBox.id = 'adminLogoutAlert';
      alertBox.className = 'hidden fixed top-6 right-6 max-w-md text-sm px-4 py-3 rounded-lg border shadow-lg z-50';
      document.body.appendChild(alertBox);
    }
    return alertBox;
  };

  const showAlert = (alertBox, message = '', type = 'error') => {
    if (!message) {
      alertBox.classList.add('hidden');
      alertBox.textContent = '';
      return;
    }

    const baseClass = 'fixed top-6 right-6 max-w-md text-sm px-4 py-3 rounded-lg border shadow-lg z-50';
    const palette = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
    alertBox.className = `${baseClass} ${palette}`;
    alertBox.textContent = message;
    alertBox.classList.remove('hidden');
  };

  const clearAdminSession = () => {
    const keys = ['sipinda_admin_token', 'sipinda_admin_user', 'sipinda_admin_role', 'sipinda_token', 'sipinda_user', 'sipinda_role'];
    keys.forEach((key) => {
      localStorage.removeItem(key);
      sessionStorage.removeItem(key);
    });
  };

  const handleLogoutClick = (options) => async (event) => {
    event.preventDefault();
    const { button, alertBox, apiBaseUrl } = options;

    const defaultText = button.dataset.defaultText || button.textContent.trim();
    const loadingText = button.dataset.loadingText || 'Sedang logout...';

    const token = localStorage.getItem('sipinda_admin_token') || sessionStorage.getItem('sipinda_admin_token');

    if (!token) {
      clearAdminSession();
      showAlert(alertBox, 'Sesi admin tidak ditemukan. Silakan login ulang.', 'error');
      window.location.href = LOGIN_PAGE;
      return;
    }

    button.disabled = true;
    button.classList.add('opacity-70', 'cursor-not-allowed');
    button.textContent = loadingText;
    showAlert(alertBox);

    try {
      const response = await fetch(`${apiBaseUrl}/api/auth/logout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        mode: 'cors',
        body: JSON.stringify({}),
      });

      const payload = await safeParseJson(response);

      if (payload?.__parseError) {
        showAlert(alertBox, 'Respon server tidak valid. Coba ulang beberapa saat lagi.');
        return;
      }

      if (response.ok && payload?.status === 'success') {
        clearAdminSession();
        showAlert(alertBox, payload?.message || 'Logout berhasil.', 'success');
        setTimeout(() => {
          window.location.href = LOGIN_PAGE;
        }, 800);
        return;
      }

      const apiErrors = Array.isArray(payload?.errors) ? payload.errors : [];
      const firstError = apiErrors.find((err) => err?.message)?.message;
      const message = firstError || payload?.message || 'Logout gagal. Silakan coba lagi.';
      showAlert(alertBox, message);
    } catch (error) {
      console.error('[AdminLogout] error', error);
      const message = (error?.message || '').toLowerCase().includes('failed to fetch')
        ? 'Tidak bisa menjangkau server. Pastikan backend/ngrok sedang berjalan.'
        : 'Terjadi gangguan jaringan. Silakan coba lagi sesaat lagi.';
      showAlert(alertBox, message);
    } finally {
      button.disabled = false;
      button.classList.remove('opacity-70', 'cursor-not-allowed');
      button.textContent = defaultText;
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    const logoutButtons = document.querySelectorAll('[data-admin-logout]');
    if (!logoutButtons.length) {
      return;
    }

    const alertBox = ensureAlertBox();
    const apiBaseUrl = resolveBackendBaseUrl();
    console.info('[AdminLogout] API_BASE_URL', apiBaseUrl);

    logoutButtons.forEach((button) => {
      if (!button.dataset.defaultText) {
        button.dataset.defaultText = button.textContent.trim() || 'Logout';
      }
      button.addEventListener('click', handleLogoutClick({ button, alertBox, apiBaseUrl }));
    });
  });
})();
