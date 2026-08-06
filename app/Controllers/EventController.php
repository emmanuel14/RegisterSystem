<?php

namespace Controllers;

use Helpers\Helper;
use Helpers\Session;
use Middleware\AuthMiddleware;
use Models\Event;

class EventController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::requireAuth();

        $filters = [
            'status' => $this->get('status', ''),
            'search' => $this->get('search', ''),
        ];
        $page    = max(1, (int)$this->get('page', 1));
        $perPage = DEFAULT_PER_PAGE;

        $total  = Event::count($filters);
        $paging = Helper::paginate($total, $page, $perPage);
        $events = Event::all($filters, $perPage, $paging['offset']);

        $this->view('admin/events/index', compact('events', 'paging', 'filters', 'total'));
    }

    public function create(): void
    {
        AuthMiddleware::requireRole('admin');
        $this->view('admin/events/form', ['event' => null, 'speakers' => [], 'schedule' => []]);
    }

    public function store(): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::validateCsrf();

        $data = $this->collectEventData();
        $errors = $this->validateEvent($data);

        if ($errors) {
            Session::flash('error', implode('<br>', $errors));
            Helper::redirect('/admin/events/create');
        }

        // Handle banner upload
        if (!empty($_FILES['banner_image']['name'])) {
            $upload = Helper::uploadFile($_FILES['banner_image'], BANNER_STORAGE_PATH);
            if ($upload['success']) {
                $data['banner_image'] = $upload['path'];
            } else {
                Session::flash('error', $upload['error']);
                Helper::redirect('/admin/events/create');
            }
        }

        $data['created_by'] = Session::getAdmin()['id'];
        $id = Event::create($data);

        // Handle speakers JSON input
        $this->saveSpeakers((int)$id);
        $this->saveSchedule((int)$id);

        $this->redirectWith('/admin/events', 'success', 'Event created successfully.');
    }

    public function edit(int $id): void
    {
        AuthMiddleware::requireRole('admin');

        $event = Event::findById($id);
        if (!$event) $this->redirectWith('/admin/events', 'error', 'Event not found.');

        $speakers = Event::getSpeakers($id);
        $schedule = Event::getSchedule($id);

        $this->view('admin/events/form', compact('event', 'speakers', 'schedule'));
    }

    public function update(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::validateCsrf();

        $event = Event::findById($id);
        if (!$event) $this->redirectWith('/admin/events', 'error', 'Event not found.');

        $data   = $this->collectEventData();
        $errors = $this->validateEvent($data);

        if ($errors) {
            Session::flash('error', implode('<br>', $errors));
            Helper::redirect('/admin/events/' . $id . '/edit');
        }

        // Handle banner upload
        if (!empty($_FILES['banner_image']['name'])) {
            $upload = Helper::uploadFile($_FILES['banner_image'], BANNER_STORAGE_PATH);
            if ($upload['success']) {
                // Delete old banner
                if ($event['banner_image']) {
                    @unlink(BANNER_STORAGE_PATH . '/' . $event['banner_image']);
                }
                $data['banner_image'] = $upload['path'];
            }
        } else {
            unset($data['banner_image']); // Keep existing
        }

        Event::update($id, $data);

        $this->redirectWith('/admin/events', 'success', 'Event updated successfully.');
    }

    public function delete(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::validateCsrf();

        $event = Event::findById($id);
        if (!$event) {
            Helper::json(['success' => false, 'message' => 'Event not found.'], 404);
        }

        // Check if registrations exist
        $count = $this->db->count('registrations', 'event_id = ?', [$id]);
        if ($count > 0) {
            Helper::json(['success' => false, 'message' => "Cannot delete — {$count} registration(s) exist for this event."]);
        }

        // Delete banner
        if ($event['banner_image']) {
            @unlink(BANNER_STORAGE_PATH . '/' . $event['banner_image']);
        }

        Event::delete($id);
        Helper::json(['success' => true, 'message' => 'Event deleted.']);
    }

    public function toggleStatus(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::validateCsrf();

        $event = Event::findById($id);
        if (!$event) Helper::json(['success' => false, 'message' => 'Not found.'], 404);

        $newStatus = $event['status'] === 'published' ? 'draft' : 'published';
        Event::update($id, ['status' => $newStatus]);

        Helper::json(['success' => true, 'status' => $newStatus, 'message' => 'Status updated.']);
    }

    public function toggleRegistration(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        AuthMiddleware::validateCsrf();

        $event = Event::findById($id);
        if (!$event) Helper::json(['success' => false], 404);

        $new = $event['registration_status'] === 'open' ? 'closed' : 'open';
        Event::update($id, ['registration_status' => $new]);

        Helper::json(['success' => true, 'registration_status' => $new]);
    }

    public function show(int $id): void
    {
        AuthMiddleware::requireAuth();

        $event    = Event::findById($id);
        if (!$event) $this->redirectWith('/admin/events', 'error', 'Event not found.');

        $speakers = Event::getSpeakers($id);
        $schedule = Event::getSchedule($id);
        $stats    = [
            'total'      => $this->db->count('registrations', "event_id = ? AND status = 'confirmed'", [$id]),
            'checked_in' => $this->db->fetchColumn(
                'SELECT COUNT(*) FROM checkins c JOIN registrations r ON r.id = c.registration_id WHERE r.event_id = ?',
                [$id]
            ),
        ];

        $this->view('admin/events/show', compact('event', 'speakers', 'schedule', 'stats'));
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function collectEventData(): array
    {
        return [
            'title'               => $this->post('title'),
            'theme'               => $this->post('theme'),
            'slug'                => $this->post('slug') ?: Helper::slugify($this->post('title', '')),
            'description'         => $_POST['description'] ?? '', // Allow HTML
            'venue'               => $this->post('venue'),
            'venue_address'       => $this->post('venue_address'),
            'city'                => $this->post('city'),
            'state'               => $this->post('state'),
            'country'             => $this->post('country', 'Nigeria'),
            'start_date'          => $this->post('start_date'),
            'end_date'            => $this->post('end_date'),
            'registration_open'   => $this->post('registration_open') ?: null,
            'registration_close'  => $this->post('registration_close') ?: null,
            'capacity'            => $this->post('capacity') ? (int)$this->post('capacity') : null,
            'status'              => $this->post('status', 'draft'),
            'registration_status' => $this->post('registration_status', 'open'),
            'is_featured'         => isset($_POST['is_featured']) ? 1 : 0,
            'allow_walk_in'       => isset($_POST['allow_walk_in']) ? 1 : 0,
        ];
    }

    private function validateEvent(array $data): array
    {
        $errors = [];
        if (empty($data['title']))      $errors[] = 'Event title is required.';
        if (empty($data['start_date'])) $errors[] = 'Start date is required.';
        if (empty($data['end_date']))   $errors[] = 'End date is required.';
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $errors[] = 'End date cannot be before start date.';
            }
        }
        return $errors;
    }

    private function saveSpeakers(int $eventId): void
    {
        // Delete existing and re-insert from JSON payload
        $json = $_POST['speakers_json'] ?? '';
        if (!$json) return;

        $speakers = json_decode($json, true);
        if (!is_array($speakers)) return;

        $this->db->delete('event_speakers', 'event_id = ?', [$eventId]);

        foreach ($speakers as $i => $s) {
            if (empty($s['name'])) continue;
            Event::addSpeaker([
                'event_id'   => $eventId,
                'name'       => Helper::sanitizeString($s['name']),
                'title'      => Helper::sanitizeString($s['title'] ?? ''),
                'bio'        => Helper::sanitizeString($s['bio'] ?? ''),
                'sort_order' => $i,
            ]);
        }
    }

    private function saveSchedule(int $eventId): void
    {
        $json = $_POST['schedule_json'] ?? '';
        if (!$json) return;

        $items = json_decode($json, true);
        if (!is_array($items)) return;

        $this->db->delete('event_schedule', 'event_id = ?', [$eventId]);

        foreach ($items as $i => $item) {
            if (empty($item['title'])) continue;
            Event::addScheduleItem([
                'event_id'   => $eventId,
                'day'        => $item['day'],
                'start_time' => $item['start_time'],
                'end_time'   => $item['end_time'] ?? null,
                'title'      => Helper::sanitizeString($item['title']),
                'description'=> Helper::sanitizeString($item['description'] ?? ''),
                'sort_order' => $i,
            ]);
        }
    }
}
