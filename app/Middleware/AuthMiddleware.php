<?php

namespace Middleware;

use Helpers\Session;
use Helpers\Helper;

/**
 * AuthMiddleware – Guards admin routes; enforces role checks.
 */
class AuthMiddleware
{
    /**
     * Require the user to be logged in.
     * If not, redirect to the login page with a flash message.
     */
    public static function requireAuth(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('warning', 'Please log in to continue.');
            Helper::redirect('/admin/login');
        }
    }

    /**
     * Require a specific role (or higher in hierarchy).
     */
    public static function requireRole(string $role): void
    {
        self::requireAuth();

        $hierarchy = ['viewer' => 1, 'moderator' => 2, 'admin' => 3, 'superadmin' => 4];
        $admin     = Session::getAdmin();
        $userLevel = $hierarchy[$admin['role']] ?? 0;
        $reqLevel  = $hierarchy[$role] ?? 99;

        if ($userLevel < $reqLevel) {
            Session::flash('error', 'You do not have permission to access that page.');
            Helper::redirect('/admin/dashboard');
        }
    }

    /**
     * Validate the CSRF token from a POST request.
     */
    public static function validateCsrf(): void
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!Session::validateCsrfToken($token)) {
            if (self::isAjax()) {
                Helper::json(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
            }
            Session::flash('error', 'Security token mismatch. Please try again.');
            Helper::redirect($_SERVER['HTTP_REFERER'] ?? '/admin/dashboard');
        }
    }

    private static function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
