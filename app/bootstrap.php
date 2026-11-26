<?php

if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

define('APP_PATH', __DIR__);
define('ROOT_PATH', dirname(__DIR__));
// Web root (where index.php + assets live). Flattened: = ROOT_PATH; public_html layout: = ROOT_PATH/public_html.
define('PUBLIC_PATH', defined('FRONT_PATH') ? FRONT_PATH : ROOT_PATH);

spl_autoload_register(function (string $class): void {
    foreach (['core', 'controllers', 'models'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

Config::load();
date_default_timezone_set(Config::get('timezone', 'Asia/Jakarta'));

if (Config::get('debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

require APP_PATH . '/core/helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

(new Router())->dispatch();
