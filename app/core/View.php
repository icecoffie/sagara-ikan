<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class View
{

    public static function render(string $view, array $data = [], string $title = ''): void
    {
        $content = self::capture($view, $data);
        $pageTitle = $title;
        require APP_PATH . '/views/layouts/app.php';
    }

    public static function raw(string $view, array $data = []): void
    {
        echo self::capture($view, $data);
    }

    public static function capture(string $view, array $data = []): string
    {
        $file = APP_PATH . '/views/' . $view . '.php';
        if (!is_file($file)) {
            http_response_code(500);
            exit('View not found: ' . htmlspecialchars($view));
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
