<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Default root redirects to dashboard or login
$routes->get('/', 'Dashboard::index', ['filter' => 'auth']);

// Graceful redirect if accessed with 'surat/public' prefix under spark serve
$routes->get('surat/public', static function () {
    return redirect()->to(base_url('auth/login'));
});
$routes->get('surat/public/(:any)', static function ($subpath) {
    return redirect()->to(base_url($subpath));
});
$routes->post('surat/public/(:any)', static function ($subpath) {
    return redirect()->to(base_url($subpath));
});

// Public Verification & Tracking (No login required)
$routes->get('cek-surat/(:segment)', 'PublicSurat::verify/$1');
$routes->get('cek-surat/download/(:segment)', 'PublicSurat::download/$1');
$routes->get('cek-surat/view-file/(:segment)', 'PublicSurat::viewFile/$1');
$routes->get('verifikasi/(:segment)', 'PublicSurat::verify/$1');

// Authentication routes (Guest access)
$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
});

// Authenticated user routes (Admin & Pegawai)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // User Profile
    $routes->get('profile', 'Auth::profile');
    $routes->post('profile', 'Auth::profile');

    // Manajemen Surat
    $routes->group('surat', function ($routes) {
        $routes->get('', 'Surat::index');
        $routes->get('create', 'Surat::create');
        $routes->post('store', 'Surat::store');
        $routes->post('preview-ajax', 'Surat::previewAjax');
        $routes->get('show/(:num)', 'Surat::show/$1');
        $routes->get('edit/(:num)', 'Surat::edit/$1');
        $routes->post('update/(:num)', 'Surat::update/$1');
        $routes->post('upload-file/(:num)', 'Surat::uploadFile/$1');
        $routes->get('download-file/(:num)', 'Surat::downloadFile/$1');
        $routes->get('view-file/(:num)', 'Surat::viewFile/$1');
        $routes->get('cetak/(:num)', 'Surat::cetak/$1');

        // Admin-only surat actions
        $routes->post('batalkan/(:num)', 'Surat::batalkan/$1', ['filter' => 'admin']);
        $routes->post('delete-file/(:num)', 'Surat::deleteFile/$1', ['filter' => 'admin']);
        $routes->get('delete/(:num)', 'Surat::delete/$1', ['filter' => 'admin']);
    });

    // Master Kode Surat
    $routes->group('kode-surat', function ($routes) {
        $routes->get('', 'KodeSurat::index');
        $routes->post('store', 'KodeSurat::store');
        $routes->post('update/(:num)', 'KodeSurat::update/$1');
        $routes->get('delete/(:num)', 'KodeSurat::delete/$1', ['filter' => 'admin']);
    });

    // Reports
    $routes->group('reports', function ($routes) {
        $routes->get('', 'Reports::index');
        $routes->get('export-excel', 'Reports::exportExcel');
        $routes->get('export-pdf', 'Reports::exportPdf');
    });

    // Admin Only routes
    $routes->group('', ['filter' => 'admin'], function ($routes) {
        // User Management
        $routes->group('users', function ($routes) {
            $routes->get('', 'Users::index');
            $routes->post('store', 'Users::store');
            $routes->post('update/(:num)', 'Users::update/$1');
            $routes->get('toggle-status/(:num)', 'Users::toggleStatus/$1');
            $routes->get('delete/(:num)', 'Users::delete/$1');
        });

        // Settings
        $routes->group('settings', function ($routes) {
            $routes->get('', 'Settings::index');
            $routes->post('update', 'Settings::update');
        });

        // Activity Logs
        $routes->get('logs', 'ActivityLogs::index');
    });
});
