<?php

namespace Helpers;

/**
 * Helper – General-purpose utility functions.
 */
class Helper
{
    // ── Output ──────────────────────────────────────────────────────────────

    /** Escape HTML output. */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** JSON response and exit. */
    public static function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Redirect and exit. */
    public static function redirect(string $url): never
    {
        // Prepend subfolder prefix for root-relative paths
        if (str_starts_with($url, '/')) {
            // Use the same normalised prefix as base()
            $prefix = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
            if ($prefix === '' || $prefix === '/') {
                // already at root, no prefix needed
            } else {
                $url = $prefix . $url;
            }
        }
        header('Location: ' . $url);
        exit;
    }

    // ── Slugs & Codes ───────────────────────────────────────────────────────

    public static function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^\w\s-]/', '', $text);
        $text = preg_replace('/[\s_]+/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }

    /**
     * Generate a unique registration code: PREFIX-YEAR-NNNNNN
     * The sequential number is derived from the last code in the DB.
     */
    public static function generateRegistrationCode(string $prefix, string $year): string
    {
        $db      = Database::getInstance();
        $pattern = "{$prefix}-{$year}-%";
        $last    = $db->fetchColumn(
            "SELECT registration_code FROM registrations
             WHERE registration_code LIKE ?
             ORDER BY id DESC LIMIT 1",
            [$pattern]
        );

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seq   = (int)end($parts) + 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $seq);
    }

    // ── Validation ──────────────────────────────────────────────────────────

    public static function validateRequired(array $fields, array $data): array
    {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[$field] = "{$label} is required.";
            }
        }
        return $errors;
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePhone(string $phone): bool
    {
        return (bool)preg_match('/^\+?[0-9\s\-]{7,20}$/', $phone);
    }

    public static function sanitizeString(string $value): string
    {
        return trim(strip_tags($value));
    }

    public static function sanitizeInt(mixed $value): int
    {
        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    // ── File Uploads ────────────────────────────────────────────────────────

    /**
     * Handle a single file upload.
     *
     * @return array{success:bool, path?:string, error?:string}
     */
    public static function uploadFile(array $file, string $destDir, array $allowedTypes = []): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload failed (error code ' . $file['error'] . ').'];
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return ['success' => false, 'error' => 'File is too large (max 5 MB).'];
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowed = empty($allowedTypes) ? ALLOWED_IMAGE_TYPES : $allowedTypes;
        if (!in_array($mimeType, $allowed, true)) {
            return ['success' => false, 'error' => 'Invalid file type.'];
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
        $destPath = rtrim($destDir, '/') . '/' . $filename;

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => false, 'error' => 'Could not save file.'];
        }

        return ['success' => true, 'path' => $filename];
    }

    // ── Dates ───────────────────────────────────────────────────────────────

    public static function formatDate(string $date, string $format = 'M j, Y'): string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '—';
        }
        return date($format, strtotime($date));
    }

    public static function formatDateTime(string $date, string $format = 'M j, Y g:i A'): string
    {
        return self::formatDate($date, $format);
    }

    // ── Pagination ──────────────────────────────────────────────────────────

    public static function paginate(int $total, int $page, int $perPage): array
    {
        $totalPages  = (int)ceil($total / $perPage);
        $currentPage = max(1, min($page, $totalPages));
        $offset      = ($currentPage - 1) * $perPage;

        return [
            'total'       => $total,
            'per_page'    => $perPage,
            'current'     => $currentPage,
            'total_pages' => $totalPages,
            'offset'      => $offset,
            'has_prev'    => $currentPage > 1,
            'has_next'    => $currentPage < $totalPages,
        ];
    }

    // ── Misc ────────────────────────────────────────────────────────────────

    public static function truncate(string $text, int $length = 150, string $suffix = '…'): string
    {
        if (mb_strlen($text) <= $length) return $text;
        return mb_substr($text, 0, $length) . $suffix;
    }

    public static function avatarInitials(string $name): string
    {
        $parts = explode(' ', trim($name));
        $init  = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) {
            $init .= strtoupper(substr(end($parts), 0, 1));
        }
        return $init;
    }

    public static function numberFormat(int|float $n): string
    {
        return number_format($n);
    }

    /** Build URL with existing query params merged. */
    public static function url(string $path, array $params = []): string
    {
        $base = rtrim(
            (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
            '/'
        );
        $url = $base . '/' . ltrim($path, '/');
        return empty($params) ? $url : $url . '?' . http_build_query($params);
    }

    /**
     * Return the subfolder prefix (e.g. "/ems/public" or "" if at root).
     * Handles Windows backslashes from XAMPP SCRIPT_NAME.
     */
    public static function base(string $path = ''): string
    {
        static $prefix = null;
        if ($prefix === null) {
            // Normalise Windows backslashes to forward slashes
            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
            $dir = dirname($scriptName);
            // dirname('/index.php') returns '/' — avoid producing '//'
            $prefix = ($dir === '/' || $dir === '\\') ? '' : rtrim($dir, '/');
        }
        if ($path === '') {
            return $prefix === '' ? '/' : $prefix . '/';
        }
        return $prefix . '/' . ltrim($path, '/');
    }

    /**
     * Shortcut for asset paths (CSS, JS, images).
     * e.g.  Helper::asset('css/admin.css')  →  /ems/public/assets/css/admin.css
     */
    public static function asset(string $path): string
    {
        return self::base('assets/' . ltrim($path, '/'));
    }
}
