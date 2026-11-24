<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $configured = Config::get('base_url');
    if (!empty($configured)) {
        return $base = rtrim($configured, '/');
    }

    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = str_replace('\\', '/', dirname($script));
    return $base = ($dir === '/' ? '' : rtrim($dir, '/'));
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return base_url() . '/index.php' . ($path === '' ? '' : '/' . $path);
}

function asset(string $path): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function rupiah($amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function require_login(): void
{
    if (!Auth::check()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('login');
    }
}

function require_permission(string $permission): void
{
    require_login();
    if (!Auth::can($permission)) {
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}

function can(string $permission): bool
{
    return Auth::can($permission);
}

function abort(int $code, string $message = ''): void
{
    http_response_code($code);
    $messages = [
        403 => 'Akses Ditolak',
        404 => 'Halaman Tidak Ditemukan',
        500 => 'Terjadi Kesalahan',
    ];
    $title = $messages[$code] ?? 'Error';
    if (Auth::check()) {
        View::render('errors/error', compact('code', 'title', 'message'), $title);
    } else {
        View::raw('errors/error_bare', compact('code', 'title', 'message'));
    }
    exit;
}

function flash(?string $type = null, ?string $message = null)
{
    if ($type === null) {
        $all = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $all;
    }
    $_SESSION['_flash'][$type] = $message;
}

function flash_input(array $input): void
{
    $_SESSION['_old'] = $input;
}

function old(string $key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function paginate(int $total, int $perPage = 10, string $pageParam = 'page'): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $page  = max(1, min($pages, (int) ($_GET[$pageParam] ?? 1)));
    return [
        'page'    => $page,
        'pages'   => $pages,
        'perPage' => $perPage,
        'offset'  => ($page - 1) * $perPage,
        'total'   => $total,
    ];
}

function pagination_links(array $p, string $route): string
{
    if ($p['pages'] <= 1) {
        return '';
    }
    $query = $_GET;
    $make = function ($page) use ($query, $route) {
        $query['page'] = $page;
        return url($route) . '?' . http_build_query($query);
    };
    $html = '<nav class="pagination">';
    $html .= '<a class="page ' . ($p['page'] <= 1 ? 'disabled' : '') . '" href="'
        . e($make(max(1, $p['page'] - 1))) . '">&laquo;</a>';
    $start = max(1, $p['page'] - 2);
    $end = min($p['pages'], $p['page'] + 2);
    for ($i = $start; $i <= $end; $i++) {
        $html .= '<a class="page ' . ($i === $p['page'] ? 'active' : '') . '" href="'
            . e($make($i)) . '">' . $i . '</a>';
    }
    $html .= '<a class="page ' . ($p['page'] >= $p['pages'] ? 'disabled' : '') . '" href="'
        . e($make(min($p['pages'], $p['page'] + 1))) . '">&raquo;</a>';
    $html .= '</nav>';
    return $html;
}

function log_activity(string $action, ?string $detail = null): void
{
    try {
        Database::run(
            'INSERT INTO activity_logs (user_id, action, detail, ip_address, created_at)
             VALUES (?, ?, ?, ?, NOW())',
            [Auth::id(), $action, $detail, $_SERVER['REMOTE_ADDR'] ?? null]
        );
    } catch (Throwable $e) {

    }
}

function input(string $key, $default = null)
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function post(string $key, $default = null)
{
    return $_POST[$key] ?? $default;
}

function query_with(array $overrides = []): string
{
    return http_build_query(array_merge($_GET, $overrides));
}

function flash_errors(array $errors): void
{
    $_SESSION['_errors'] = $errors;
}

function errors(): array
{
    $e = $_SESSION['_errors'] ?? [];
    unset($_SESSION['_errors']);
    return $e;
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item';
}

function order_number(): string
{
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}

function menu_image_url(?string $image): string
{
    if ($image) {
        $base = ROOT_PATH . '/public_html/';
        if (is_file($base . 'assets/uploads/' . $image)) {
            return asset('assets/uploads/' . $image);
        }
        if (is_file($base . 'assets/' . $image)) {
            return asset('assets/' . $image);
        }
    }
    return asset('assets/all.png');
}
