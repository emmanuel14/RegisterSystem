<?php

namespace Models;

use Helpers\Database;

class Gallery
{
    public static function byEvent(int $eventId): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT * FROM event_gallery WHERE event_id = ? ORDER BY sort_order, id',
            [$eventId]
        );
    }

    public static function recent(int $limit = 12): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT g.*, e.title AS event_title, e.slug AS event_slug
             FROM event_gallery g
             JOIN events e ON e.id = g.event_id
             WHERE g.type = 'photo' AND g.file_path IS NOT NULL
             ORDER BY g.created_at DESC LIMIT ?",
            [$limit]
        );
    }
}
