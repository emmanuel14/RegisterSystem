<?php

/** @var Router $router */

// ── Public ────────────────────────────────────────────────────────────────
$router->get('/',                                       'PublicController', 'home');
$router->get('/events',                                 'PublicController', 'home');
$router->get('/events/{slug}',                          'PublicController', 'eventDetail');
$router->get('/events/{slug}/register',                 'PublicController', 'registerForm');
$router->post('/events/{slug}/register',                'PublicController', 'registerSubmit');
$router->get('/registration/success/{code}',            'PublicController', 'success');
$router->get('/registration/download-qr/{code}',        'PublicController', 'downloadQr');
$router->get('/checkin/{code}',                         'PublicController', 'checkinPublic');

// ── Admin Auth ─────────────────────────────────────────────────────────────
$router->get('/admin/login',                            'AuthController',         'loginPage');
$router->post('/admin/login',                           'AuthController',         'login');
$router->get('/admin/logout',                           'AuthController',         'logout');

// ── Admin (protected) ──────────────────────────────────────────────────────
$router->get('/admin',                                  'DashboardController',    'index');
$router->get('/admin/dashboard',                        'DashboardController',    'index');

// Events
$router->get('/admin/events',                           'EventController',        'index');
$router->get('/admin/events/create',                    'EventController',        'create');
$router->post('/admin/events',                          'EventController',        'store');
$router->get('/admin/events/{id:int}',                  'EventController',        'show');
$router->get('/admin/events/{id:int}/edit',             'EventController',        'edit');
$router->post('/admin/events/{id:int}/update',          'EventController',        'update');
$router->post('/admin/events/{id:int}/delete',          'EventController',        'delete');
$router->post('/admin/events/{id:int}/toggle-status',   'EventController',        'toggleStatus');
$router->post('/admin/events/{id:int}/toggle-reg',      'EventController',        'toggleRegistration');

// Registrations
$router->get('/admin/registrations',                    'RegistrationController', 'index');
$router->get('/admin/registrations/export-csv',         'RegistrationController', 'exportCsv');
$router->get('/admin/registrations/{id:int}',           'RegistrationController', 'show');
$router->get('/admin/registrations/{id:int}/edit',      'RegistrationController', 'edit');
$router->post('/admin/registrations/{id:int}/update',   'RegistrationController', 'update');
$router->post('/admin/registrations/{id:int}/delete',   'RegistrationController', 'delete');
$router->get('/admin/registrations/{id:int}/print',     'RegistrationController', 'print');

// Check-in
$router->get('/admin/checkin',                          'RegistrationController', 'checkinPage');
$router->post('/admin/checkin/lookup',                  'RegistrationController', 'lookupQr');
$router->post('/admin/checkin/{id:int}',                'RegistrationController', 'checkin');

// Reports
$router->get('/admin/reports',                          'ReportController',       'index');

// Settings
$router->get('/admin/settings',                         'SettingsController',     'index');
$router->post('/admin/settings',                        'SettingsController',     'update');
