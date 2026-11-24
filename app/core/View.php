<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

/**
 * Minimal view renderer. Views are plain PHP files in app/views.
 * The "app" layout wraps authenticated pages with the sidebar/topbar.
 */
class View
{
    /** Render a view inside the admin app layout. */
    public static function render(string $view, array $data = [], string $title = ''): void
    {
        $content = self::capture($view, $data);
        $pageTitle = $title;
        require APP_PATH . '/views/layouts/app.php';
    }

    /** Render a view with no layout (public pages, print pages). */
    public static function raw(string $view, array $data = []): void
    {
        echo self::capture($view, $data);
    }

    /** Render a view and return its HTML as a string. */
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
