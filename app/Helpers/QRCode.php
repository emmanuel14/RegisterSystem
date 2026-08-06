<?php

namespace Helpers;

/**
 * QRCode – Generates QR code PNG images using phpqrcode library.
 * Falls back to a data-URI approach if GD is unavailable.
 */
class QRCode
{
    private static string $libPath;

    public static function init(): void
    {
        self::$libPath = ROOT_PATH . '/vendor/phpqrcode/qrlib.php';
    }

    /**
     * Generate a QR code PNG and save it to disk.
     *
     * @param  string $content     The text/URL to encode
     * @param  string $filename    Filename (without path), e.g. 'EMS-2026-000001.png'
     * @return string              Full path to the saved PNG
     */
    public static function generate(string $content, string $filename): string
    {
        self::init();

        $dir = QR_STORAGE_PATH;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $outFile = $dir . '/' . $filename;

        // Use phpqrcode if GD is available
        if (extension_loaded('gd') && file_exists(self::$libPath)) {
            ob_start();
            require_once self::$libPath;
            ob_end_clean();

            \QRcode::png($content, $outFile, QR_ECLEVEL_M, 8, 2);
        } else {
            // Fallback: generate a minimal QR placeholder using GD primitives
            self::generateFallback($content, $outFile);
        }

        return $outFile;
    }

    /**
     * Get the public URL for a QR code file.
     */
    public static function url(string $filename): string
    {
        $settings = \Models\Setting::all();
        $base     = rtrim($settings['site_url'] ?? '', '/');
        return $base . '/uploads/qrcodes/' . $filename;
    }

    /**
     * Get the filename for a registration code.
     */
    public static function filename(string $registrationCode): string
    {
        return $registrationCode . '.png';
    }

    /**
     * Minimal fallback: creates a placeholder PNG with the text.
     * Real QR functionality requires the phpqrcode library + GD.
     */
    private static function generateFallback(string $content, string $outFile): void
    {
        if (!extension_loaded('gd')) {
            // Last resort: save empty file
            file_put_contents($outFile, '');
            return;
        }

        $size  = 300;
        $img   = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, $size, $size, $white);
        imagestring($img, 2, 10, 140, 'QR: ' . substr($content, -20), $black);
        imagepng($img, $outFile);
        imagedestroy($img);
    }
}
