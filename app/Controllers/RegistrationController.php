<?php

namespace Controllers;

use Helpers\Helper;
use Helpers\Session;
use Helpers\QRCode;
use Helpers\Mailer;
use Middleware\AuthMiddleware;
use Models\Registration;
use Models\Event;

class RegistrationController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::requireAuth();

        $filters = [
            'event_id'   => $this->get('event_id', ''),
            'gender'     => $this->get('gender', ''),
            'checked_in' => $this->get('checked_in', ''),
            'date'       => $this->get('date', ''),
            'search'     => $this->get('search', ''),
        ];
        $page    = max(1, (int)$this->get('page', 1));
        $perPage = DEFAULT_PER_PAGE;
        $total   = Registration::count($filters);
        $paging  = Helper::paginate($total, $page, $perPage);
        $regs    = Registration::all($filters, $perPage, $paging['offset']);
        $events  = Event::all([], 500, 0);

        $this->view('admin/registrations/index', compact('regs', 'paging', 'filters', 'total', 'events'));
    }

    public function show(int $id): void
    {
        AuthMiddleware::requireAuth();

        $reg = Registration::findById($id);
        if (!$reg) $this->redirectWith('/admin/registrations', 'error', 'Registration not found.');

        $qrFile = QRCode::filename($reg['registration_code']);
        $qrUrl  = QRCode::url($qrFile);

        $this->view('admin/registrations/show', compact('reg', 'qrUrl'));
    }

    public function edit(int $id): void
    {
        AuthMiddleware::requireRole('admin');

        $reg = Registration::findById($id);
        if (!$reg) $this->redirectWith('/admin/registrations', 'error', 'Registration not found.');

        $this->view('admin/registrations/edit', compact('reg'));
    }

    public function update(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::validateCsrf();

        $reg = Registration::findById($id);
        if (!$reg) $this->redirectWith('/admin/registrations', 'error', 'Not found.');

        // Update attendee data
        $attendeeData = [
            'first_name'              => $this->post('first_name'),
            'last_name'               => $this->post('last_name'),
            'email'                   => filter_var($this->post('email'), FILTER_SANITIZE_EMAIL),
            'phone'                   => $this->post('phone'),
            'gender'                  => $this->post('gender'),
            'church_name'             => $this->post('church_name'),
            'state'                   => $this->post('state'),
            'city'                    => $this->post('city'),
            'address'                 => $this->post('address'),
            'emergency_contact_name'  => $this->post('emergency_contact_name'),
            'emergency_contact_phone' => $this->post('emergency_contact_phone'),
        ];

        $this->db->update('attendees', $attendeeData, 'id = ?', [$reg['attendee_id']]);
        Registration::update($id, ['status' => $this->post('status', 'confirmed')]);

        $this->redirectWith('/admin/registrations/' . $id, 'success', 'Registration updated.');
    }

    public function delete(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::validateCsrf();

        $reg = Registration::findById($id);
        if (!$reg) Helper::json(['success' => false, 'message' => 'Not found.'], 404);

        // Remove QR code file
        $qrPath = QR_STORAGE_PATH . '/' . QRCode::filename($reg['registration_code']);
        if (file_exists($qrPath)) @unlink($qrPath);

        Registration::delete($id);
        Helper::json(['success' => true, 'message' => 'Registration deleted.']);
    }

    public function exportCsv(): void
    {
        AuthMiddleware::requireAuth();

        $filters = [
            'event_id' => $this->get('event_id', ''),
        ];

        $data = Registration::exportData($filters);

        $filename = 'registrations_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        if (!empty($data)) {
            fputcsv($out, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($out, $row);
            }
        }
        fclose($out);
        exit;
    }

    public function print(int $id): void
    {
        AuthMiddleware::requireAuth();

        $reg    = Registration::findById($id);
        if (!$reg) die('Not found');

        $qrUrl  = QRCode::url(QRCode::filename($reg['registration_code']));
        $this->view('admin/registrations/print', compact('reg', 'qrUrl'), '');
    }

    // ── Check-In API ────────────────────────────────────────────────────────

    public function checkinPage(): void
    {
        AuthMiddleware::requireAuth();
        $events = Event::all(['status' => 'published'], 100, 0);
        $this->view('admin/checkin/index', compact('events'));
    }

    public function lookupQr(): void
    {
        AuthMiddleware::requireAuth();

        $code = Helper::sanitizeString($this->post('code', ''));
        if (!$code) Helper::json(['success' => false, 'message' => 'No code provided.'], 400);

        // Strip URL prefix if scanner picked up a full URL
        if (str_contains($code, '/checkin/')) {
            $code = basename(parse_url($code, PHP_URL_PATH));
        }

        $reg = Registration::findByCode($code);
        if (!$reg) {
            Helper::json(['success' => false, 'message' => 'Registration not found for code: ' . htmlspecialchars($code)]);
        }

        Helper::json([
            'success'      => true,
            'registration' => [
                'id'               => $reg['id'],
                'registration_code'=> $reg['registration_code'],
                'attendee_name'    => $reg['attendee_name'],
                'email'            => $reg['email'],
                'phone'            => $reg['phone'],
                'church_name'      => $reg['church_name'],
                'event_title'      => $reg['event_title'],
                'status'           => $reg['status'],
                'checked_in'       => !empty($reg['checkin_id']),
                'checked_in_at'    => $reg['checked_in_at'] ?? null,
            ],
        ]);
    }

    public function checkin(int $id): void
    {
        AuthMiddleware::requireAuth();
        AuthMiddleware::validateCsrf();

        $reg = Registration::findById($id);
        if (!$reg) Helper::json(['success' => false, 'message' => 'Not found.'], 404);

        if (!empty($reg['checked_in_at'])) {
            Helper::json([
                'success'       => false,
                'already_in'    => true,
                'message'       => 'Already checked in at ' . Helper::formatDateTime($reg['checked_in_at']),
            ]);
        }

        $adminId = Session::getAdmin()['id'];
        Registration::checkIn($id, $adminId);

        Helper::json(['success' => true, 'message' => 'Checked in successfully!', 'checked_in_at' => date('Y-m-d H:i:s')]);
    }

    public function checkinStats(int $id): void
    {
        AuthMiddleware::requireAuth();

        $event = Event::findById($id);
        if (!$event) {
            Helper::json(['success' => false], 404);
        }

        $db = \Helpers\Database::getInstance();
        $total = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM registrations WHERE event_id = ? AND status = 'confirmed'",
            [$id]
        );
        $checkedIn = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM checkins c
             JOIN registrations r ON r.id = c.registration_id
             WHERE r.event_id = ?",
            [$id]
        );

        Helper::json([
            'success'    => true,
            'total'      => $total,
            'checked_in' => $checkedIn,
            'remaining'  => max(0, $total - $checkedIn),
        ]);
    }
}
