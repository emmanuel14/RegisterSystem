<?php
require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/app/Helpers/Helper.php';

use Helpers\Helper;

echo "<h2>Helper::base() Test</h2>";
echo "<pre>";
echo "base(''): " . Helper::base('') . "\n";
echo "base('admin/events/create'): " . Helper::base('admin/events/create') . "\n";
echo "base('admin/events'): " . Helper::base('admin/events') . "\n";
echo "</pre>";

echo "<h3>Test Links</h3>";
echo "<a href=\"" . Helper::base('admin/events/create') . "\">Create Event</a><br>";
echo "<a href=\"" . Helper::base('admin/events') . "\">Events List</a><br>";
?>
