<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

/**
 * Base controller with shared helpers.
 */
abstract class Controller
{
    /** True when the current request is a POST. */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** Verify CSRF for POST requests (call before mutating state). */
    protected function guardPost(): void
    {
        Csrf::verifyPost();
    }

    /** Render inside the app layout. */
    protected function view(string $view, array $data = [], string $title = ''): void
    {
        View::render($view, $data, $title);
    }

    /** Render without a layout. */
    protected function raw(string $view, array $data = []): void
    {
        View::raw($view, $data);
    }

    /** Send a JSON response. */
    protected function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /** Read an integer id argument or 404. */
    protected function intId($id): int
    {
        if (!ctype_digit((string) $id)) {
            abort(404);
        }
        return (int) $id;
    }
}
