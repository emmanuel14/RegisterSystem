<?php

namespace Controllers;

use Middleware\AuthMiddleware;
use Models\Event;
use Models\Registration;
use Helpers\Helper;

class ReportController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::requireAuth();

        $eventId = (int)$this->get('event_id', 0);
        $events  = Event::all([], 500, 0);

        // Stats for selected event (or all)
        $db = $this->db;

        $where  = $eventId ? 'r.event_id = ' . $eventId : '1=1';

        $totalRegs   = (int)$db->fetchColumn("SELECT COUNT(*) FROM registrations r WHERE {$where} AND r.status='confirmed'");
        $checkedIn   = (int)$db->fetchColumn("SELECT COUNT(c.id) FROM checkins c JOIN registrations r ON r.id=c.registration_id WHERE {$where}");
        $noShows     = $totalRegs - $checkedIn;
        $attendancePct = $totalRegs > 0 ? round(($checkedIn / $totalRegs) * 100, 1) : 0;

        // Daily registrations (last 30 days)
        $daily = $db->fetchAll(
            "SELECT DATE(r.registered_at) AS day, COUNT(*) AS total
             FROM registrations r WHERE {$where} AND r.status='confirmed'
             AND r.registered_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY DATE(r.registered_at) ORDER BY day",
            $eventId ? [$eventId] : []
        );

        // Gender breakdown
        $genders = $db->fetchAll(
            "SELECT a.gender, COUNT(*) AS total
             FROM registrations r JOIN attendees a ON a.id=r.attendee_id
             WHERE {$where} AND r.status='confirmed'
             GROUP BY a.gender",
            []
        );

        // Top churches
        $churches = $db->fetchAll(
            "SELECT a.church_name, COUNT(*) AS total
             FROM registrations r JOIN attendees a ON a.id=r.attendee_id
             WHERE {$where} AND r.status='confirmed'
             GROUP BY a.church_name ORDER BY total DESC LIMIT 10",
            []
        );

        // Top states
        $states = $db->fetchAll(
            "SELECT a.state, COUNT(*) AS total
             FROM registrations r JOIN attendees a ON a.id=r.attendee_id
             WHERE {$where} AND r.status='confirmed'
             GROUP BY a.state ORDER BY total DESC LIMIT 10",
            []
        );

        $this->view('admin/reports/index', compact(
            'events', 'eventId', 'totalRegs', 'checkedIn', 'noShows',
            'attendancePct', 'daily', 'genders', 'churches', 'states'
        ));
    }
}
