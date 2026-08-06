<?php

/**
 * EMS – Front Controller
 * All HTTP requests pass through this file.
 */

declare(strict_types=1);

// ── Bootstrap ──────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/routes/Router.php';

use Helpers\Session;

// Start session
Session::start();

// ── Route ──────────────────────────────────────────────────────────────────
$router = new Router();
require ROOT_PATH . '/routes/web.php';
$router->dispatch();
