<?php
/**
 * EMS - Application Configuration
 * Copy this to config.local.php for environment-specific overrides.
 */

// ── Environment ────────────────────────────────────────────────────────────
define('APP_ENV',    getenv('APP_ENV')  ?: 'production');
define('APP_DEBUG',  APP_ENV === 'development');

// ── Paths ──────────────────────────────────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('APP_PATH',     ROOT_PATH . '/app');
define('VIEW_PATH',    ROOT_PATH . '/views');
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// ── Database ───────────────────────────────────────────────────────────────
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_NAME',    getenv('DB_NAME')    ?: 'ems_db');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── Session ────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'EMS_SESSION');
define('SESSION_LIFETIME', 7200);        // 2 hours

// ── Security ───────────────────────────────────────────────────────────────
define('CSRF_TOKEN_NAME', '_csrf_token');
define('CSRF_TOKEN_TTL',  3600);         // 1 hour

// ── Upload Limits ──────────────────────────────────────────────────────────
define('MAX_UPLOAD_SIZE',    5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// ── Pagination ─────────────────────────────────────────────────────────────
define('DEFAULT_PER_PAGE', 20);

// ── Registration Codes ─────────────────────────────────────────────────────
define('REG_CODE_PREFIX', 'EMS');
define('REG_CODE_YEAR',   date('Y'));

// ── QR Code ────────────────────────────────────────────────────────────────
define('QR_STORAGE_PATH', PUBLIC_PATH . '/uploads/qrcodes');
define('BANNER_STORAGE_PATH', PUBLIC_PATH . '/uploads/banners');

// ── Autoloader ─────────────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $map = [
        'Controllers\\' => APP_PATH . '/Controllers/',
        'Models\\'      => APP_PATH . '/Models/',
        'Helpers\\'     => APP_PATH . '/Helpers/',
        'Middleware\\'  => APP_PATH . '/Middleware/',
    ];

    foreach ($map as $prefix => $base) {
        if (str_starts_with($class, $prefix)) {
            $file = $base . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// ── Error Handling ─────────────────────────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');
}

// ── Timezone ────────────────────────────────────────────────────────────────
date_default_timezone_set('Africa/Lagos');
