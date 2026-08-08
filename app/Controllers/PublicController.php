<?php

namespace Controllers;

use Helpers\Helper;
use Helpers\Session;
use Helpers\QRCode;
use Helpers\Mailer;
use Models\Event;
use Models\Registration;
use Models\Setting;

class PublicController extends BaseController
{
    public function home(): void
    {
        $events = Event::published();
        $this->view('public/home', compact('events'), 'public');
    }

    public function eventDetail(string $slug): void
    {
        $event = Event::findBySlug($slug);
        if (!$event || $event['status'] !== 'published') {
            http_response_code(404);
            $this->view('public/404', [], 'public');
            return;
        }

        $speakers       = Event::getSpeakers($event['id']);
        $schedule       = Event::getSchedule($event['id']);
        $registrationOpen = Event::isRegistrationOpen($event);

        $this->view('public/event', compact('event', 'speakers', 'schedule', 'registrationOpen'), 'public');
    }

    public function registerForm(string $slug): void
    {
        $event = Event::findBySlug($slug);
        if (!$event || !Event::isRegistrationOpen($event)) {
            Session::flash('warning', 'Registration is not currently available for this event.');
            Helper::redirect('/events/' . $slug);
        }

        $this->view('public/register', compact('event'), 'public');
    }

    public function registerSubmit(string $slug): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/events/' . $slug . '/register');
        }

        // CSRF
        $csrfToken = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!Session::validateCsrfToken($csrfToken)) {
            Session::flash('error', 'Security token expired. Please try again.');
            Helper::redirect('/events/' . $slug . '/register');
        }

        $event = Event::findBySlug($slug);
        if (!$event || !Event::isRegistrationOpen($event)) {
            Session::flash('error', 'Registration is closed for this event.');
            Helper::redirect('/events/' . $slug);
        }

        // Collect & validate
        $attendeeData = [
            'first_name'              => Helper::sanitizeString($_POST['first_name'] ?? ''),
            'last_name'               => Helper::sanitizeString($_POST['last_name'] ?? ''),
            'email'                   => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'phone'                   => Helper::sanitizeString($_POST['phone'] ?? ''),
            'gender'                  => Helper::sanitizeString($_POST['gender'] ?? ''),
            'date_of_birth'           => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
            'church_name'             => Helper::sanitizeString($_POST['church_name'] ?? ''),
            'state'                   => Helper::sanitizeString($_POST['state'] ?? ''),
            'city'                    => Helper::sanitizeString($_POST['city'] ?? ''),
            'address'                 => Helper::sanitizeString($_POST['address'] ?? ''),
            'emergency_contact_name'  => Helper::sanitizeString($_POST['emergency_contact_name'] ?? '') ?: null,
            'emergency_contact_phone' => Helper::sanitizeString($_POST['emergency_contact_phone'] ?? '') ?: null,
        ];

        $errors = $this->validateRegistration($attendeeData);

        if ($errors) {
            Session::flash('error', implode('<br>', $errors));
            // Store form data so the user doesn't have to retype
            Session::set('reg_form_data', $attendeeData);
            Helper::redirect('/events/' . $slug . '/register');
        }

        try {
            $result = Registration::create($attendeeData, $event['id']);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE') {
                Session::flash('error', 'You are already registered for this event with that email address.');
            } else {
                Session::flash('error', 'Registration failed. Please try again.');
            }
            Helper::redirect('/events/' . $slug . '/register');
        }

        // Generate QR code
        $settings  = Setting::all();
        $siteUrl   = rtrim($settings['site_url'] ?? 'http://localhost', '/');
        $checkinUrl = $siteUrl . '/checkin/' . $result['code'];
        $qrFile    = QRCode::filename($result['code']);

        try {
            QRCode::generate($checkinUrl, $qrFile);
        } catch (\Throwable $e) {
            // QR generation failure is non-fatal
        }

        // Send confirmation email (non-blocking)
        $fullReg = Registration::findByCode($result['code']);
        if ($fullReg) {
            try {
                Mailer::sendConfirmation($fullReg, QR_STORAGE_PATH . '/' . $qrFile);
            } catch (\Throwable $e) {
                // Email failure should not block the user
            }
        }

        Helper::redirect('/registration/success/' . $result['code']);
    }

    public function success(string $code): void
    {
        $reg = Registration::findByCode($code);
        if (!$reg) {
            Session::flash('error', 'Registration not found.');
            Helper::redirect('/');
        }

        $qrFile = QRCode::filename($code);
        $qrUrl  = file_exists(QR_STORAGE_PATH . '/' . $qrFile)
            ? '/uploads/qrcodes/' . $qrFile
            : null;

        $this->view('public/success', compact('reg', 'qrUrl', 'code'), 'public');
    }

    public function downloadQr(string $code): void
    {
        $reg = Registration::findByCode($code);
        if (!$reg) die('Not found');

        $qrFile = QR_STORAGE_PATH . '/' . QRCode::filename($code);
        if (!file_exists($qrFile)) die('QR code not found');

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="QR-' . $code . '.png"');
        header('Content-Length: ' . filesize($qrFile));
        readfile($qrFile);
        exit;
    }

    public function checkinPublic(string $code): void
    {
        // Public check-in page for QR scans
        $reg = Registration::findByCode($code);
        $this->view('public/checkin', compact('reg', 'code'), 'public');
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['first_name'])) $errors[] = 'First name is required.';
        if (empty($data['last_name']))  $errors[] = 'Last name is required.';
        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!Helper::validateEmail($data['email'])) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (empty($data['phone'])) {
            $errors[] = 'Phone number is required.';
        } elseif (!Helper::validatePhone($data['phone'])) {
            $errors[] = 'Please enter a valid phone number.';
        }
        if (empty($data['gender'])) {
            $errors[] = 'Gender is required.';
        } elseif (!in_array($data['gender'], ['male', 'female', 'other', 'prefer_not_to_say'])) {
            $errors[] = 'Invalid gender value.';
        }
        if (empty($data['church_name'])) $errors[] = 'Church name is required.';
        if (empty($data['state']))       $errors[] = 'State is required.';
        if (empty($data['city']))        $errors[] = 'City is required.';

        return $errors;
    }
}
