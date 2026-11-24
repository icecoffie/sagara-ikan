<?php
/**
 * Application bootstrap. Included by public_html/index.php after SAGARA_APP
 * is defined. Sets up paths, autoloading, config, session, then dispatches.
 */

if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

// --- Paths ---
define('APP_PATH', __DIR__);                 // .../app
define('ROOT_PATH', dirname(__DIR__));       // project root (parent of app)

// --- Autoloader for core, controllers, models ---
spl_autoload_register(function (string $class): void {
    foreach (['core', 'controllers', 'models'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

// --- Config + runtime settings ---
Config::load();
date_default_timezone_set(Config::get('timezone', 'Asia/Jakarta'));

if (Config::get('debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

// --- Helpers (plain functions, not autoloaded) ---
require APP_PATH . '/core/helpers.php';

// --- Session ---
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// --- Dispatch ---
(new Router())->dispatch();
