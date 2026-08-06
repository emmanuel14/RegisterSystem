<?php

namespace Controllers;

use Helpers\Session;
use Helpers\Helper;
use Middleware\AuthMiddleware;
use Models\Admin;

class AuthController extends BaseController
{
    public function loginPage(): void
    {
        if (Session::isLoggedIn()) {
            Helper::redirect('/admin/dashboard');
        }
        $this->view('admin/auth/login', [], 'auth');
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/admin/login');
        }

        AuthMiddleware::validateCsrf();

        $email    = filter_var($this->post('email', ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Basic validation
        $errors = [];
        if (empty($email))    $errors['email']    = 'Email is required.';
        if (empty($password)) $errors['password'] = 'Password is required.';

        if ($errors) {
            Session::flash('error', 'Please fill in all fields.');
            Helper::redirect('/admin/login');
        }

        $admin = Admin::findByEmail($email);

        if (!$admin || !password_verify($password, $admin['password'])) {
            // Artificial delay to throttle brute-force
            sleep(1);
            Session::flash('error', 'Invalid email or password.');
            Helper::redirect('/admin/login');
        }

        if (!$admin['is_active']) {
            Session::flash('error', 'Your account has been disabled. Contact the administrator.');
            Helper::redirect('/admin/login');
        }

        // Successful login
        session_regenerate_id(true);
        Session::setAdmin($admin);
        Admin::updateLastLogin($admin['id']);

        Helper::redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        Session::logout();
        Session::flash('success', 'You have been logged out.');
        Helper::redirect('/admin/login');
    }
}
