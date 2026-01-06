'use strict';

(function () {
  const LOGIN_PAGE = 'admin-login.php';
  const DASHBOARD_PAGE = 'admin-dashboard.php';

  const normalizePath = (path) => {
    if (!path) return '';
    return path.split('?')[0].split('#')[0];
  };

  const currentPath = normalizePath(window.location.pathname.split('/').pop());
  const isLoginPage = currentPath === LOGIN_PAGE;

  const token = localStorage.getItem('sipinda_admin_token') || sessionStorage.getItem('sipinda_admin_token');

  const userRaw = localStorage.getItem('sipinda_admin_user') || sessionStorage.getItem('sipinda_admin_user');

  let userRole = '';
  if (userRaw) {
    try {
      const parsedUser = JSON.parse(userRaw);
      userRole = (parsedUser?.role || '').toLowerCase();
    } catch (error) {
      console.warn('[AdminAuthGuard] Failed to parse stored user', error);
    }
  }

  const hasAdminSession = Boolean(token) && userRole === 'admin';

  if (!hasAdminSession && !isLoginPage) {
    console.info('[AdminAuthGuard] No admin session, redirecting to login');
    window.location.replace(LOGIN_PAGE);
    return;
  }

  if (hasAdminSession && isLoginPage) {
    console.info('[AdminAuthGuard] Admin already logged in, redirecting to dashboard');
    window.location.replace(DASHBOARD_PAGE);
  }
})();
