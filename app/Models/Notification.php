<?php

namespace Models;

use Helpers\Database;

class Notification
{
    public static function create(array $data): string
    {
        return Database::getInstance()->insert('notifications', $data);
    }

    public static function forAdmin(int $limit = 10): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT * FROM notifications
             WHERE recipient_type IN ('admin', 'all')
             ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public static function unreadAdminCount(): int
    {
        return (int)Database::getInstance()->fetchColumn(
            "SELECT COUNT(*) FROM notifications
             WHERE recipient_type IN ('admin', 'all') AND is_read = 0"
        );
    }

    public static function forAttendee(int $attendeeId, int $limit = 20): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT * FROM notifications
             WHERE (recipient_type = 'all' OR (recipient_type = 'attendee' AND recipient_id = ?))
             ORDER BY created_at DESC LIMIT ?",
            [$attendeeId, $limit]
        );
    }

    public static function markRead(int $id): void
    {
        Database::getInstance()->update('notifications', ['is_read' => 1], 'id = ?', [$id]);
    }

    public static function notifyRegistrationConfirmed(int $attendeeId, string $eventTitle, string $link): void
    {
        self::create([
            'type'           => 'registration_confirmed',
            'title'          => 'Registration Confirmed',
            'message'        => "Your registration for {$eventTitle} was confirmed.",
            'recipient_type' => 'attendee',
            'recipient_id'   => $attendeeId,
            'link'           => $link,
        ]);
    }
}
