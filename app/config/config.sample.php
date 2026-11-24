<?php
/**
 * Sagara Resto — application configuration (SAMPLE).
 *
 * Copy this file to `config.php` in the same folder and fill in your values.
 * `config.php` is git-ignored so your real credentials never get committed.
 */

if (!defined('SAGARA_APP')) {
    http_response_code(403);
    exit('Forbidden');
}

return [
    // ---- Database (MySQL) ----
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'sagara_resto',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // ---- Application ----
    // Base URL WITHOUT trailing slash. Leave empty to auto-detect from the
    // request (recommended for most cPanel setups). Example: '/resto'
    'base_url' => '',

    // Human-friendly app name shown in the UI.
    'app_name' => 'Sagara Resto',

    // Timezone used for all date/time handling.
    'timezone' => 'Asia/Jakarta',

    // Where uploaded menu images are stored (relative to public_html).
    'upload_dir' => 'assets/uploads',

    // Show PHP errors on screen? Keep false in production.
    'debug' => false,
];
