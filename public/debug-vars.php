<?php
echo "<h2>Server Variables Debug</h2>";
echo "<pre>";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$dir = dirname($scriptName);
$prefix = ($dir === '/' || $dir === '\\') ? '' : rtrim($dir, '/');

echo "\nCalculated prefix: '" . $prefix . "'\n";
echo "</pre>";
?>
