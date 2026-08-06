<?php
require 'config/config.php';
require 'app/Helpers/Database.php';

use Helpers\Database;

$db = Database::getInstance();

// The correct hash for password "password"
$correctHash = '$2y$12$8BX5.BcYlYXWMrG.q/O0puaoxo7YHmtaZLus7Eci3NLfrfeGx//1i';

// Update the admin account using the update method
try {
    $affected = $db->update('admins', ['password' => $correctHash], 'email = ?', ['admin@ems.local']);
    
    // Verify it was updated
    $admin = $db->fetchOne(
        'SELECT email, password FROM admins WHERE email = ?',
        ['admin@ems.local']
    );
    
    if ($admin && $admin['password'] === $correctHash) {
        echo "✓ Password hash updated successfully!\n";
        echo "Email: admin@ems.local\n";
        echo "Password: password\n";
        echo "Hash verified: " . strlen($admin['password']) . " characters\n";
    } else {
        echo "✗ Failed to update password hash\n";
        if ($admin) {
            echo "Current hash: " . $admin['password'] . "\n";
            echo "Expected: " . $correctHash . "\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    var_dump($e);
}
?>
