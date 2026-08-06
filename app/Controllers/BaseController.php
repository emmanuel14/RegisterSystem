<?php

namespace Controllers;

use Helpers\Database;
use Helpers\Session;
use Helpers\Helper;
use Models\Setting;

/**
 * BaseController – Shared functionality for all controllers.
 */
abstract class BaseController
{
    protected Database $db;
    protected array    $viewData = [];

    public function __construct()
    {
        $this->db = Database::getInstance();

        // Load global settings into view data
        $this->viewData['settings'] = Setting::all();
        $this->viewData['admin']    = Session::getAdmin();
        $this->viewData['csrf']     = Session::getCsrfToken();
        $this->viewData['flash']    = [
            'success' => Session::getFlash('success'),
            'error'   => Session::getFlash('error'),
            'warning' => Session::getFlash('warning'),
            'info'    => Session::getFlash('info'),
        ];
    }

    /**
     * Render a view file with data.
     * Views are in views/ directory.
     *
     * @param string $view  e.g. 'admin/dashboard/index'
     * @param array  $data  Additional data to pass
     */
    protected function view(string $view, array $data = [], string $layout = 'admin'): void
    {
        $data = array_merge($this->viewData, $data);
        extract($data, EXTR_SKIP);

        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("View not found: {$viewFile}");
        }

        // Capture content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render layout
        $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';
        if ($layout && file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /** Render without a layout. */
    protected function viewRaw(string $view, array $data = []): void
    {
        $this->view($view, $data, '');
    }

    /** Convenience: redirect with flash. */
    protected function redirectWith(string $url, string $type, string $message): never
    {
        Session::flash($type, $message);
        Helper::redirect($url);
    }

    /** Is this an AJAX/fetch request? */
    protected function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    /** Get a POST value, sanitized. */
    protected function post(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $default;
        if (is_string($value)) {
            return trim(strip_tags($value));
        }
        return $value;
    }

    /** Get a GET value, sanitized. */
    protected function get(string $key, mixed $default = null): mixed
    {
        $value = $_GET[$key] ?? $default;
        if (is_string($value)) {
            return trim(strip_tags($value));
        }
        return $value;
    }
}
