<?php

namespace Models;

use Helpers\Database;

class Admin
{
    public static function findByEmail(string $email): ?array
    {
        return Database::getInstance()->fetchOne(
            'SELECT * FROM admins WHERE email = ? LIMIT 1',
            [$email]
        );
    }

    public static function findById(int $id): ?array
    {
        return Database::getInstance()->fetchOne(
            'SELECT id, name, email, role, avatar, is_active, last_login, created_at FROM admins WHERE id = ?',
            [$id]
        );
    }

    public static function all(): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT id, name, email, role, is_active, last_login, created_at FROM admins ORDER BY name'
        );
    }

    public static function updateLastLogin(int $id): void
    {
        Database::getInstance()->update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
    }

    public static function create(array $data): string
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return Database::getInstance()->insert('admins', $data);
    }
}
