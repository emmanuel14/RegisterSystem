<?php

namespace Models;

use Helpers\Database;

class Announcement
{
    public static function published(int $limit = 10): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT a.*, e.title AS event_title, e.slug AS event_slug
             FROM announcements a
             LEFT JOIN events e ON e.id = a.event_id
             WHERE a.is_published = 1
             ORDER BY a.published_at DESC, a.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    public static function search(string $query, int $limit = 20): array
    {
        $like = '%' . $query . '%';
        return Database::getInstance()->fetchAll(
            "SELECT a.*, e.title AS event_title
             FROM announcements a
             LEFT JOIN events e ON e.id = a.event_id
             WHERE a.is_published = 1 AND (a.title LIKE ? OR a.content LIKE ?)
             ORDER BY a.published_at DESC LIMIT ?",
            [$like, $like, $limit]
        );
    }

    public static function all(int $limit = 50): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT a.*, e.title AS event_title FROM announcements a
             LEFT JOIN events e ON e.id = a.event_id
             ORDER BY a.created_at DESC LIMIT ?',
            [$limit]
        );
    }
}
