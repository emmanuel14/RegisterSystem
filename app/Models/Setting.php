<?php

namespace Models;

use Helpers\Database;

class Setting
{
    private static ?array $cache = null;

    /** Get all settings as key => value map. */
    public static function all(): array
    {
        if (self::$cache !== null) return self::$cache;

        $rows = Database::getInstance()->fetchAll('SELECT `key`, `value` FROM settings');
        self::$cache = array_column($rows, 'value', 'key');
        return self::$cache;
    }

    /** Get a single setting value. */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();
        return $all[$key] ?? $default;
    }

    /** Update or insert a setting. */
    public static function set(string $key, string $value): void
    {
        $db  = Database::getInstance();
        $row = $db->fetchOne('SELECT id FROM settings WHERE `key` = ?', [$key]);

        if ($row) {
            $db->update('settings', ['value' => $value], '`key` = ?', [$key]);
        } else {
            $db->insert('settings', ['key' => $key, 'value' => $value, 'label' => $key]);
        }

        self::$cache = null; // Invalidate cache
    }

    /** Bulk save from a form submission. */
    public static function saveMany(array $data): void
    {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }

    /** Get settings grouped by their group field. */
    public static function grouped(): array
    {
        $rows   = Database::getInstance()->fetchAll(
            'SELECT `key`, `value`, `type`, `group`, `label` FROM settings ORDER BY `group`, id'
        );
        $groups = [];
        foreach ($rows as $row) {
            $groups[$row['group']][] = $row;
        }
        return $groups;
    }
}
