<?php

namespace Models;

use Helpers\Database;
use Helpers\Helper;

class Event
{
    public static function all(array $filters = [], int $limit = 100, int $offset = 0): array
    {
        $db     = Database::getInstance();
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'e.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(e.title LIKE ? OR e.theme LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT e.*,
                       a.name AS created_by_name,
                       (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id AND r.status = 'confirmed') AS reg_count
                FROM events e
                LEFT JOIN admins a ON a.id = e.created_by
                WHERE " . implode(' AND ', $where) . "
                ORDER BY e.start_date DESC
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        return $db->fetchAll($sql, $params);
    }

    public static function count(array $filters = []): int
    {
        $db     = Database::getInstance();
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(title LIKE ? OR theme LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        return (int)$db->fetchColumn(
            'SELECT COUNT(*) FROM events WHERE ' . implode(' AND ', $where),
            $params
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::getInstance()->fetchOne(
            'SELECT e.*, a.name AS created_by_name FROM events e
             LEFT JOIN admins a ON a.id = e.created_by
             WHERE e.id = ?',
            [$id]
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::getInstance()->fetchOne(
            'SELECT * FROM events WHERE slug = ?',
            [$slug]
        );
    }

    public static function published(): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT e.*,
                    (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id AND r.status='confirmed') AS reg_count
             FROM events e
             WHERE e.status = 'published'
             ORDER BY e.start_date ASC"
        );
    }

    public static function create(array $data): string
    {
        // Ensure slug is unique
        $base = Helper::slugify($data['slug'] ?? $data['title']);
        $slug = $base;
        $db   = Database::getInstance();
        $i    = 1;
        while ($db->fetchOne('SELECT id FROM events WHERE slug = ?', [$slug])) {
            $slug = $base . '-' . $i++;
        }
        $data['slug'] = $slug;

        return $db->insert('events', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->update('events', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::getInstance()->delete('events', 'id = ?', [$id]);
    }

    public static function stats(): array
    {
        $db = Database::getInstance();
        return [
            'total'            => (int)$db->fetchColumn('SELECT COUNT(*) FROM events'),
            'active'           => (int)$db->fetchColumn("SELECT COUNT(*) FROM events WHERE status = 'published'"),
            'total_regs'       => (int)$db->fetchColumn("SELECT COUNT(*) FROM registrations WHERE status = 'confirmed'"),
            'checked_in'       => (int)$db->fetchColumn('SELECT COUNT(*) FROM checkins'),
            'recent_regs'      => $db->fetchAll(
                "SELECT r.*, CONCAT(a.first_name,' ',a.last_name) AS attendee_name,
                        a.email, e.title AS event_title
                 FROM registrations r
                 JOIN attendees a  ON a.id = r.attendee_id
                 JOIN events e     ON e.id = r.event_id
                 ORDER BY r.registered_at DESC LIMIT 10"
            ),
        ];
    }

    // Speakers
    public static function getSpeakers(int $eventId): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT * FROM event_speakers WHERE event_id = ? ORDER BY sort_order, id',
            [$eventId]
        );
    }

    public static function addSpeaker(array $data): string
    {
        return Database::getInstance()->insert('event_speakers', $data);
    }

    public static function deleteSpeaker(int $id): void
    {
        Database::getInstance()->delete('event_speakers', 'id = ?', [$id]);
    }

    // Schedule
    public static function getSchedule(int $eventId): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT s.*, sp.name AS speaker_name FROM event_schedule s
             LEFT JOIN event_speakers sp ON sp.id = s.speaker_id
             WHERE s.event_id = ?
             ORDER BY s.day, s.start_time',
            [$eventId]
        );
    }

    public static function addScheduleItem(array $data): string
    {
        return Database::getInstance()->insert('event_schedule', $data);
    }

    public static function deleteScheduleItem(int $id): void
    {
        Database::getInstance()->delete('event_schedule', 'id = ?', [$id]);
    }

    /** Check if registration is currently open. */
    public static function isRegistrationOpen(array $event): bool
    {
        if ($event['status'] !== 'published') return false;
        if ($event['registration_status'] !== 'open') return false;

        $now = time();
        if (!empty($event['registration_open']) && $now < strtotime($event['registration_open'])) return false;
        if (!empty($event['registration_close']) && $now > strtotime($event['registration_close'])) return false;

        // Check capacity
        if (!empty($event['capacity'])) {
            $count = (int)Database::getInstance()->fetchColumn(
                "SELECT COUNT(*) FROM registrations WHERE event_id = ? AND status = 'confirmed'",
                [$event['id']]
            );
            if ($count >= $event['capacity']) return false;
        }

        return true;
    }
}
