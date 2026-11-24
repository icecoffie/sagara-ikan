<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="' . self::KEY . '" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    public static function check(?string $token): bool
    {
        return !empty($_SESSION[self::KEY])
            && is_string($token)
            && hash_equals($_SESSION[self::KEY], $token);
    }

    public static function verifyPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        if (!self::check($_POST[self::KEY] ?? null)) {
            http_response_code(419);
            exit('Invalid or expired CSRF token. Please reload the page and try again.');
        }
    }
}
