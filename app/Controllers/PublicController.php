<?php

namespace Controllers;

use Helpers\Helper;
use Helpers\Session;
use Helpers\QRCode;
use Helpers\Mailer;
use Helpers\Calendar;
use Helpers\Database;
use Models\Event;
use Models\Registration;
use Models\Setting;
use Models\Ministry;
use Models\Announcement;
use Models\Testimonial;
use Models\Gallery;
use Models\Notification;

class PublicController extends BaseController
{
    public function home(): void
    {
        $events        = Event::upcomingFeatured(3);
        $ministries    = $this->safeQuery(fn() => Ministry::allActive(), []);
        $testimonials  = $this->safeQuery(fn() => Testimonial::approved(6), []);
        $announcements = $this->safeQuery(fn() => Announcement::published(5), []);
        $gallery       = $this->safeQuery(fn() => Gallery::recent(8), []);

        $this->view('public/home', compact('events', 'ministries', 'testimonials', 'announcements', 'gallery'), 'public');
    }

    public function eventsList(): void
    {
        $events = Event::published();
        $this->view('public/events', compact('events'), 'public');
    }

    public function eventDetail(string $slug): void
    {
        $event = Event::findBySlug($slug);
        if (!$event || $event['status'] !== 'published') {
            http_response_code(404);
            $this->view('public/404', [], 'public');
            return;
        }

        $regCount = (int)Database::getInstance()->fetchColumn(
            "SELECT COUNT(*) FROM registrations WHERE event_id = ? AND status = 'confirmed'",
            [$event['id']]
        );
        $event['reg_count'] = $regCount;

        $speakers         = Event::getSpeakers($event['id']);
        $schedule         = Event::getSchedule($event['id']);
        $registrationOpen = Event::isRegistrationOpen($event);
        $gallery          = $this->safeQuery(fn() => Gallery::byEvent($event['id']), []);

        $this->view('public/event', compact('event', 'speakers', 'schedule', 'registrationOpen', 'gallery'), 'public');
    }

    public function ministries(): void
    {
        $ministries = $this->safeQuery(fn() => Ministry::allActive(), []);
        $this->view('public/ministries', compact('ministries'), 'public');
    }

    public function ministryDetail(string $slug): void
    {
        $ministry = $this->safeQuery(fn() => Ministry::findBySlug($slug));
        if (!$ministry) {
            http_response_code(404);
            $this->view('public/404', [], 'public');
            return;
        }
        $events = Event::published();
        $this->view('public/ministry', compact('ministry', 'events'), 'public');
    }

    public function search(): void
    {
        $query = Helper::sanitizeString($_GET['q'] ?? '');
        $results = ['events' => [], 'announcements' => [], 'ministries' => []];

        if (strlen($query) >= 2) {
            $results['events']        = Event::search($query);
            $results['announcements'] = $this->safeQuery(fn() => Announcement::search($query), []);
            $allMinistries            = $this->safeQuery(fn() => Ministry::allActive(), []);
            $results['ministries']    = array_values(array_filter($allMinistries, fn($m) =>
                stripos($m['name'], $query) !== false || stripos($m['description'] ?? '', $query) !== false
            ));
        }

        $this->view('public/search', compact('query', 'results'), 'public');
    }

    public function downloadCalendar(string $slug): void
    {
        $event = Event::findBySlug($slug);
        if (!$event) {
            http_response_code(404);
            exit;
        }
        $code = Helper::sanitizeString($_GET['code'] ?? '');
        $ics  = Calendar::generateIcs($event, $code);
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . Helper::slugify($event['title']) . '.ics"');
        echo $ics;
        exit;
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

        $settings   = Setting::all();
        $siteUrl    = rtrim($settings['site_url'] ?? 'http://localhost', '/');
        $checkinUrl = $siteUrl . Helper::base('checkin/' . $result['code']);
        $qrFile     = QRCode::filename($result['code']);

        try {
            QRCode::generate($checkinUrl, $qrFile);
        } catch (\Throwable) {}

        $fullReg = Registration::findByCode($result['code']);
        if ($fullReg) {
            try {
                Mailer::sendConfirmation($fullReg, QR_STORAGE_PATH . '/' . $qrFile);
            } catch (\Throwable) {}

            try {
                Notification::notifyRegistrationConfirmed(
                    (int)$result['attendee_id'],
                    $event['title'],
                    Helper::base('registration/success/' . $result['code'])
                );
            } catch (\Throwable) {}
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

        $event = Event::findBySlug($reg['event_slug']);
        $qrFile = QRCode::filename($code);
        $qrUrl  = file_exists(QR_STORAGE_PATH . '/' . $qrFile)
            ? Helper::base('uploads/qrcodes/' . $qrFile)
            : null;

        $googleCalUrl = $event ? Calendar::googleUrl($event) : '';
        $icsUrl       = $event ? Helper::base('events/' . $reg['event_slug'] . '/calendar.ics?code=' . urlencode($code)) : '';

        $this->view('public/success', compact('reg', 'qrUrl', 'code', 'googleCalUrl', 'icsUrl', 'event'), 'public');
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

    /** Gracefully handle missing v2 tables before migration is run. */
    private function safeQuery(callable $fn, mixed $default = null): mixed
    {
        try {
            return $fn();
        } catch (\Throwable) {
            return $default;
        }
    }
}
