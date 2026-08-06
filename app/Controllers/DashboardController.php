<?php

namespace Controllers;

use Middleware\AuthMiddleware;
use Models\Event;
use Models\Registration;
use Models\Setting;

class DashboardController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::requireAuth();
        $stats = Event::stats();
        $this->view('admin/dashboard/index', compact('stats'));
    }
}
