<?php

namespace Models;

use Helpers\Database;

class Testimonial
{
    public static function approved(int $limit = 6): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public static function create(array $data): string
    {
        return Database::getInstance()->insert('testimonials', $data);
    }
}
