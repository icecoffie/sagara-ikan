<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

abstract class Controller
{

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function guardPost(): void
    {
        Csrf::verifyPost();
    }

    protected function view(string $view, array $data = [], string $title = ''): void
    {
        View::render($view, $data, $title);
    }

    protected function raw(string $view, array $data = []): void
    {
        View::raw($view, $data);
    }

    protected function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function intId($id): int
    {
        if (!ctype_digit((string) $id)) {
            abort(404);
        }
        return (int) $id;
    }
}
