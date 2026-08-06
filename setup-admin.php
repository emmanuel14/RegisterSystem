<?php
require 'config/config.php';
require 'app/Helpers/Database.php';

use Helpers\Database;

$db = Database::getInstance();

// Hash the password
$hash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]);

// Remove old admin if exists
$db->exec('DELETE FROM admins WHERE email = ?', ['admin@ems.local']);

// Create new admin
$db->exec(
    'INSERT INTO admins (name, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)',
    ['System Administrator', 'admin@ems.local', $hash, 'superadmin', 1]
);

echo "✓ Admin account created successfully\n";
echo "Email: admin@ems.local\n";
echo "Password: password\n";
echo "\nYou can now log in at: http://localhost/ems/public/admin/login\n";
?>
