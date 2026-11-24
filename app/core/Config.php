<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

/**
 * Loads and exposes configuration from app/config/config.php.
 */
class Config
{
    private static array $data = [];
    private static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }
        $file = APP_PATH . '/config/config.php';
        if (!is_file($file)) {
            http_response_code(500);
            exit('Missing app/config/config.php. Copy config.sample.php to config.php and set your DB credentials.');
        }
        self::$data = require $file;
        self::$loaded = true;
    }

    public static function get(string $key, $default = null)
    {
        self::load();
        return self::$data[$key] ?? $default;
    }
}
