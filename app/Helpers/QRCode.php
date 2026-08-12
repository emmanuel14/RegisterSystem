<?php

namespace Helpers;

use chillerlan\QRCode\QRCode as QRCodeLib;
use chillerlan\QRCode\QROptions;

/**
 * QRCode – Generates proper QR code PNG images for registration check-ins.
 */
class QRCode
{
    public static function generate(string $content, string $filename): string
    {
        $dir = QR_STORAGE_PATH;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $outFile = $dir . '/' . $filename;

        if (!extension_loaded('gd')) {
            file_put_contents($outFile, '');
            return $outFile;
        }

        $options = new QROptions([
            'outputType' => QRCodeLib::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCodeLib::ECC_L,
            'moduleSize' => 8,
            'margin'     => 2,
        ]);

        $qrCode = new QRCodeLib($options);
        $png = $qrCode->generate($content);
        file_put_contents($outFile, $png);

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

}
