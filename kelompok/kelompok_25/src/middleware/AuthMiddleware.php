<?php

/**
 * Auth Middleware
 */

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     */
    public static function check()
    {
        if (!Auth::check()) {
            set_flash('error', 'Silakan login terlebih dahulu.');
            redirect('/login');
        }
    }

    /**
     * Check if user is guest (not authenticated)
     */
    public static function guest()
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
    }
}
