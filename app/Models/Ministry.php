<?php

namespace Models;

use Helpers\Database;

class Ministry
{
    public static function allActive(): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT * FROM ministries WHERE is_active = 1 ORDER BY sort_order, name'
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::getInstance()->fetchOne(
            'SELECT * FROM ministries WHERE slug = ? AND is_active = 1',
            [$slug]
        );
    }

    public static function all(): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT * FROM ministries ORDER BY sort_order, name'
        );
    }
}
