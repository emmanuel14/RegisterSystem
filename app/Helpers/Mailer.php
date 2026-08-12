<?php

namespace Helpers;

use Models\Setting;

/**
 * Mailer – Thin wrapper around PHPMailer for sending transactional emails.
 */
class Mailer
{
    /**
     * Send an email.
     *
     * @param  string      $to        Recipient email
     * @param  string      $toName    Recipient name
     * @param  string      $subject   Email subject
     * @param  string      $htmlBody  HTML body
     * @param  string|null $textBody  Plain-text fallback (auto-stripped if null)
     * @return bool
     */
    public static function send(
        string  $to,
        string  $toName,
        string  $subject,
        string  $htmlBody,
        ?string $textBody = null,
        ?string $qrImagePath = null
    ): bool {
        $settings = Setting::all();

        if (empty($settings['emails_enabled']) || !$settings['emails_enabled']) {
            return true; // Silently skip when disabled
        }

        $phpMailerPath = ROOT_PATH . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        if (!file_exists($phpMailerPath)) {
            self::logError('PHPMailer not found at ' . $phpMailerPath);
            return false;
        }

        require_once $phpMailerPath;
        require_once ROOT_PATH . '/vendor/phpmailer/phpmailer/src/SMTP.php';
        require_once ROOT_PATH . '/vendor/phpmailer/phpmailer/src/Exception.php';

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = $settings['smtp_host'] ?? 'localhost';
            $mail->Port       = (int)($settings['smtp_port'] ?? 587);
            $mail->Username   = $settings['smtp_user'] ?? '';
            $mail->Password   = $settings['smtp_pass'] ?? '';
            $mail->SMTPAuth   = !empty($settings['smtp_user']);
            $mail->SMTPSecure = $settings['smtp_encryption'] ?? 'tls';

            $mail->setFrom($settings['smtp_from_email'] ?? 'noreply@ems.local', $settings['smtp_from_name'] ?? 'EMS');
            $mail->addAddress($to, $toName);
            $mail->addReplyTo($settings['smtp_from_email'] ?? 'noreply@ems.local', $settings['smtp_from_name'] ?? 'EMS');

            if (!empty($qrImagePath) && file_exists($qrImagePath)) {
                $mail->addAttachment($qrImagePath, basename($qrImagePath));
            }

            $mail->CharSet  = 'UTF-8';
            $mail->Subject  = $subject;
            $mail->isHTML(true);
            $mail->Body     = $htmlBody;
            $mail->AltBody  = $textBody ?? strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (\Exception $e) {
            self::logError($e->getMessage());
            return false;
        }
    }

    /**
     * Send a registration confirmation email.
     */
    public static function sendConfirmation(array $registration, string $qrImagePath): bool
    {
        $settings = Setting::all();
        $orgName  = $settings['church_name'] ?? 'Event Management System';
        $subject  = $settings['reg_email_subject'] ?? 'Your Registration Confirmation';

        ob_start();
        require VIEW_PATH . '/emails/confirmation.php';
        $html = ob_get_clean();

        return self::send(
            $registration['email'],
            $registration['first_name'] . ' ' . $registration['last_name'],
            $subject,
            $html,
            null,
            $qrImagePath
        );
    }

    private static function logError(string $message): void
    {
        $log = STORAGE_PATH . '/logs/mailer.log';
        @file_put_contents($log, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
    }
}
