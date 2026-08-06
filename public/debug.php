<?php
/**
 * EMS Debug Page — DELETE THIS FILE after fixing issues.
 * Visit: http://localhost/ems/public/debug.php
 */
require_once dirname(__DIR__) . '/config/config.php';

$scriptName  = $_SERVER['SCRIPT_NAME'] ?? 'N/A';
$normalized  = str_replace('\\', '/', $scriptName);
$dir         = dirname($normalized);
$prefix      = ($dir === '/' || $dir === '\\') ? '' : rtrim($dir, '/');
$cssPath     = $prefix . '/assets/css/admin.css';
$loginHref   = $prefix . '/admin/login';

echo "<pre style='font-family:monospace;font-size:14px;padding:20px'>";
echo "<b>EMS Path Debug</b>\n\n";
echo "SCRIPT_NAME raw:     " . htmlspecialchars($scriptName) . "\n";
echo "Normalized:          " . htmlspecialchars($normalized) . "\n";
echo "dirname():           " . htmlspecialchars($dir) . "\n";
echo "Prefix (base):       '" . htmlspecialchars($prefix) . "'\n\n";
echo "CSS link would be:   " . htmlspecialchars($cssPath) . "\n";
echo "Login href:          " . htmlspecialchars($loginHref) . "\n\n";

// Test if the CSS file physically exists
$cssFile = PUBLIC_PATH . '/assets/css/admin.css';
echo "CSS file exists at:  " . htmlspecialchars($cssFile) . " → " . (file_exists($cssFile) ? "✅ YES" : "❌ NO") . "\n\n";

// DB test
try {
    $db = \Helpers\Database::getInstance();
    $row = $db->fetchOne("SELECT COUNT(*) as c FROM settings");
    echo "DB settings rows:    " . $row['c'] . " \n";
} catch (Exception $e) {
    echo "DB error:            " . htmlspecialchars($e->getMessage()) . " ❌\n";
}

echo "\n<b>All _SERVER keys relevant to paths:</b>\n";
foreach (['SCRIPT_NAME','SCRIPT_FILENAME','PHP_SELF','REQUEST_URI','DOCUMENT_ROOT'] as $k) {
    echo str_pad($k, 22) . ($_SERVER[$k] ?? 'N/A') . "\n";
}
echo "</pre>";

// Show the actual CSS link tag so you can click it
echo "<p>Try clicking this CSS link to see if it loads:</p>";
echo "<a href='{$cssPath}'>{$cssPath}</a>";
