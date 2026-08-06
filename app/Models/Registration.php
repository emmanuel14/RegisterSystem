<?php

namespace Models;

use Helpers\Database;
use Helpers\Helper;
use Helpers\Session;

class Registration
{
    public static function findByCode(string $code): ?array
    {
        return Database::getInstance()->fetchOne(
            "SELECT r.*,
                    CONCAT(a.first_name,' ',a.last_name) AS attendee_name,
                    a.first_name, a.last_name, a.email, a.phone, a.gender,
                    a.church_name, a.state, a.city,
                    e.title AS event_title, e.venue, e.start_date, e.end_date, e.slug AS event_slug,
                    c.id AS checkin_id, c.checked_in_at,
                    ad.name AS checked_in_by_name
             FROM registrations r
             JOIN attendees a    ON a.id = r.attendee_id
             JOIN events e       ON e.id = r.event_id
             LEFT JOIN checkins c ON c.registration_id = r.id
             LEFT JOIN admins ad  ON ad.id = c.checked_in_by
             WHERE r.registration_code = ?",
            [$code]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::getInstance()->fetchOne(
            "SELECT r.*,
                    CONCAT(a.first_name,' ',a.last_name) AS attendee_name,
                    a.first_name, a.last_name, a.email, a.phone, a.gender,
                    a.date_of_birth, a.church_name, a.state, a.city, a.address,
                    a.emergency_contact_name, a.emergency_contact_phone,
                    e.title AS event_title, e.slug AS event_slug, e.venue, e.start_date,
                    c.checked_in_at, c.method AS checkin_method
             FROM registrations r
             JOIN attendees a    ON a.id = r.attendee_id
             JOIN events e       ON e.id = r.event_id
             LEFT JOIN checkins c ON c.registration_id = r.id
             WHERE r.id = ?",
            [$id]
        );
    }

    public static function all(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $db     = Database::getInstance();
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['event_id'])) {
            $where[]  = 'r.event_id = ?';
            $params[] = (int)$filters['event_id'];
        }
        if (!empty($filters['gender'])) {
            $where[]  = 'a.gender = ?';
            $params[] = $filters['gender'];
        }
        if (!empty($filters['church'])) {
            $where[]  = 'a.church_name LIKE ?';
            $params[] = '%' . $filters['church'] . '%';
        }
        if (isset($filters['checked_in']) && $filters['checked_in'] !== '') {
            if ($filters['checked_in'] == '1') {
                $where[] = 'c.id IS NOT NULL';
            } else {
                $where[] = 'c.id IS NULL';
            }
        }
        if (!empty($filters['date'])) {
            $where[]  = 'DATE(r.registered_at) = ?';
            $params[] = $filters['date'];
        }
        if (!empty($filters['search'])) {
            $where[]  = "(a.first_name LIKE ? OR a.last_name LIKE ? OR a.email LIKE ? OR r.registration_code LIKE ?)";
            $s        = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$s, $s, $s, $s]);
        }

        $sql = "SELECT r.id, r.registration_code, r.status, r.registered_at,
                       a.first_name, a.last_name, a.email, a.phone, a.gender, a.church_name, a.state,
                       e.title AS event_title, e.slug AS event_slug,
                       c.checked_in_at
                FROM registrations r
                JOIN attendees a    ON a.id = r.attendee_id
                JOIN events e       ON e.id = r.event_id
                LEFT JOIN checkins c ON c.registration_id = r.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY r.registered_at DESC
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

        if (!empty($filters['event_id'])) {
            $where[]  = 'r.event_id = ?';
            $params[] = (int)$filters['event_id'];
        }
        if (isset($filters['checked_in']) && $filters['checked_in'] !== '') {
            if ($filters['checked_in'] == '1') $where[] = 'c.id IS NOT NULL';
            else $where[] = 'c.id IS NULL';
        }
        if (!empty($filters['search'])) {
            $where[]  = "(a.first_name LIKE ? OR a.last_name LIKE ? OR a.email LIKE ?)";
            $s        = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$s, $s, $s]);
        }

        return (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM registrations r
             JOIN attendees a ON a.id = r.attendee_id
             JOIN events e ON e.id = r.event_id
             LEFT JOIN checkins c ON c.registration_id = r.id
             WHERE " . implode(' AND ', $where),
            $params
        );
    }

    /**
     * Create a registration. Returns ['code' => ..., 'id' => ...]
     */
    public static function create(array $attendeeData, int $eventId): array
    {
        $db = Database::getInstance();

        return $db->transaction(function (Database $db) use ($attendeeData, $eventId) {
            // Upsert attendee (by email)
            $attendee = $db->fetchOne(
                'SELECT id FROM attendees WHERE email = ?',
                [$attendeeData['email']]
            );

            if ($attendee) {
                $attendeeId = $attendee['id'];
                unset($attendeeData['member_id']);
                $db->update('attendees', $attendeeData, 'id = ?', [$attendeeId]);
            } else {
                $attendeeData['member_id'] = self::generateMemberId();
                $attendeeId = $db->insert('attendees', $attendeeData);
            }

            // Ensure existing attendees have a member ID
            $existingMemberId = $db->fetchColumn('SELECT member_id FROM attendees WHERE id = ?', [$attendeeId]);
            if (empty($existingMemberId)) {
                $db->update('attendees', ['member_id' => self::generateMemberId()], 'id = ?', [$attendeeId]);
            }

            // Check for duplicate registration
            $dup = $db->fetchOne(
                "SELECT id FROM registrations WHERE event_id = ? AND attendee_id = ? AND status != 'cancelled'",
                [$eventId, $attendeeId]
            );
            if ($dup) {
                throw new \RuntimeException('DUPLICATE');
            }

            // Generate registration code
            $settings = \Models\Setting::all();
            $prefix   = $settings['reg_code_prefix'] ?? REG_CODE_PREFIX;
            $year     = date('Y');
            $code     = Helper::generateRegistrationCode($prefix, $year);

            $regId = $db->insert('registrations', [
                'registration_code' => $code,
                'event_id'          => $eventId,
                'attendee_id'       => $attendeeId,
                'status'            => 'confirmed',
            ]);

            return ['code' => $code, 'id' => $regId, 'attendee_id' => $attendeeId];
        });
    }

    public static function update(int $id, array $data): void
    {
        Database::getInstance()->update('registrations', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::getInstance()->delete('registrations', 'id = ?', [$id]);
    }

    public static function getAttendeeProfile(int $attendeeId): ?array
    {
        return Database::getInstance()->fetchOne(
            'SELECT * FROM attendees WHERE id = ?',
            [$attendeeId]
        );
    }

    public static function byAttendee(int $attendeeId): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT r.*, e.title AS event_title, e.slug AS event_slug, e.start_date, e.end_date,
                    e.venue, c.checked_in_at
             FROM registrations r
             JOIN events e ON e.id = r.event_id
             LEFT JOIN checkins c ON c.registration_id = r.id
             WHERE r.attendee_id = ? AND r.status = 'confirmed'
             ORDER BY e.start_date DESC",
            [$attendeeId]
        );
    }

    public static function upcomingByAttendee(int $attendeeId): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT r.*, e.title AS event_title, e.slug AS event_slug, e.start_date, e.venue
             FROM registrations r
             JOIN events e ON e.id = r.event_id
             WHERE r.attendee_id = ? AND r.status = 'confirmed' AND e.start_date >= NOW()
             ORDER BY e.start_date ASC",
            [$attendeeId]
        );
    }

    private static function generateMemberId(): string
    {
        $db   = Database::getInstance();
        $year = date('Y');
        $last = $db->fetchColumn(
            "SELECT member_id FROM attendees WHERE member_id LIKE ? ORDER BY id DESC LIMIT 1",
            ["CH-{$year}-%"]
        );
        $seq = 1;
        if ($last) {
            $seq = (int)substr($last, -6) + 1;
        }
        return sprintf('CH-%s-%06d', $year, $seq);
    }

    // ── Check-in ────────────────────────────────────────────────────────────

    public static function checkIn(int $registrationId, ?int $adminId = null): bool
    {
        $db = Database::getInstance();

        // Already checked in?
        $existing = $db->fetchOne('SELECT id FROM checkins WHERE registration_id = ?', [$registrationId]);
        if ($existing) return false;

        $db->insert('checkins', [
            'registration_id' => $registrationId,
            'checked_in_at'   => date('Y-m-d H:i:s'),
            'checked_in_by'   => $adminId,
            'method'          => 'qr',
        ]);
        return true;
    }

    // ── Reports ─────────────────────────────────────────────────────────────

    public static function dailyReport(int $eventId = 0): array
    {
        $params = [];
        $where  = "WHERE r.status = 'confirmed'";
        if ($eventId) {
            $where   .= ' AND r.event_id = ?';
            $params[] = $eventId;
        }

        return Database::getInstance()->fetchAll(
            "SELECT DATE(r.registered_at) AS reg_date, COUNT(*) AS total
             FROM registrations r {$where}
             GROUP BY DATE(r.registered_at)
             ORDER BY reg_date DESC
             LIMIT 30",
            $params
        );
    }

    public static function exportData(array $filters = []): array
    {
        // Same as all() but no limit
        $db     = Database::getInstance();
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['event_id'])) {
            $where[]  = 'r.event_id = ?';
            $params[] = (int)$filters['event_id'];
        }

        return $db->fetchAll(
            "SELECT r.registration_code, r.status, r.registered_at,
                    a.first_name, a.last_name, a.email, a.phone, a.gender,
                    a.date_of_birth, a.church_name, a.state, a.city, a.address,
                    a.emergency_contact_name, a.emergency_contact_phone,
                    e.title AS event_title,
                    CASE WHEN c.id IS NOT NULL THEN 'Yes' ELSE 'No' END AS checked_in,
                    c.checked_in_at
             FROM registrations r
             JOIN attendees a    ON a.id = r.attendee_id
             JOIN events e       ON e.id = r.event_id
             LEFT JOIN checkins c ON c.registration_id = r.id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY r.registered_at",
            $params
        );
    }
}
