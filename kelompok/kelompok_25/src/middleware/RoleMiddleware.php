<?php

/**
 * Role Middleware
 */

class RoleMiddleware
{
    /**
     * Check if user has required role
     */
    public static function check($roles)
    {
        if (!Auth::check()) {
            set_flash('error', 'Silakan login terlebih dahulu.');
            redirect('/login');
        }

        if (!Auth::hasRole($roles)) {
            set_flash('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('/dashboard');
        }
    }

    /**
     * Check admin role
     */
    public static function admin()
    {
        self::check('admin');
    }

    /**
     * Check manager role
     */
    public static function manager()
    {
        self::check(['admin', 'manager']);
    }

    /**
     * Check staff role
     */
    public static function staff()
    {
        self::check(['admin', 'manager', 'staff']);
    }
}
