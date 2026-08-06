<?php

namespace Controllers;

use Middleware\AuthMiddleware;
use Models\Setting;
use Helpers\Helper;

class SettingsController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::requireRole('superadmin');
        $groups = Setting::grouped();
        $this->view('admin/settings/index', compact('groups'));
    }

    public function update(): void
    {
        AuthMiddleware::requireRole('superadmin');
        AuthMiddleware::validateCsrf();

        $data = $_POST;
        unset($data[CSRF_TOKEN_NAME], $data['_method']);

        // Sanitize
        $clean = [];
        foreach ($data as $key => $value) {
            $clean[Helper::sanitizeString($key)] = is_string($value) ? strip_tags($value) : $value;
        }

        Setting::saveMany($clean);
        $this->redirectWith('/admin/settings', 'success', 'Settings saved successfully.');
    }
}
