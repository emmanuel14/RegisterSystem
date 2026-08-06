<?php

namespace Helpers;

/**
 * Session – Secure session management with flash messages and CSRF tokens.
 */
class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        session_name(SESSION_NAME);

        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;

        // Regenerate periodically to prevent session fixation
        if (!isset($_SESSION['_initiated'])) {
            session_regenerate_id(true);
            $_SESSION['_initiated'] = true;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_unset();
        session_destroy();
        self::$started = false;
    }

    // ── Flash Messages ──────────────────────────────────────────────────────

    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    public static function getFlash(string $type): array
    {
        $messages = $_SESSION['_flash'][$type] ?? [];
        unset($_SESSION['_flash'][$type]);
        return $messages;
    }

    public static function hasFlash(string $type): bool
    {
        return !empty($_SESSION['_flash'][$type]);
    }

    // ── CSRF ────────────────────────────────────────────────────────────────

    public static function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_NAME] = [
            'token' => $token,
            'expires' => time() + CSRF_TOKEN_TTL,
        ];
        return $token;
    }

    public static function getCsrfToken(): string
    {
        if (
            !isset($_SESSION[CSRF_TOKEN_NAME]) ||
            $_SESSION[CSRF_TOKEN_NAME]['expires'] < time()
        ) {
            return self::generateCsrfToken();
        }
        return $_SESSION[CSRF_TOKEN_NAME]['token'];
    }

    public static function validateCsrfToken(string $token): bool
    {
        $stored = $_SESSION[CSRF_TOKEN_NAME] ?? null;
        if (!$stored || $stored['expires'] < time()) {
            return false;
        }
        return hash_equals($stored['token'], $token);
    }

    // ── Admin Auth ──────────────────────────────────────────────────────────

    public static function setAdmin(array $admin): void
    {
        self::set('admin', [
            'id'    => $admin['id'],
            'name'  => $admin['name'],
            'email' => $admin['email'],
            'role'  => $admin['role'],
        ]);
    }

    public static function getAdmin(): ?array
    {
        return self::get('admin');
    }

    public static function isLoggedIn(): bool
    {
        return self::has('admin');
    }

    public static function logout(): void
    {
        self::delete('admin');
        session_regenerate_id(true);
    }
}
