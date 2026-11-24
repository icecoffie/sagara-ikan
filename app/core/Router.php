<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

/**
 * Convention-based router.
 *
 * URL shape (via PATH_INFO): /index.php/<resource>/<action>/<id>
 *   - resource maps to a Controller class (e.g. "menu" -> MenuController)
 *   - action maps to a method (default "index")
 *   - id (if present) is passed as the method argument
 *
 * A few fixed routes (home, login, logout, dashboard) are mapped explicitly.
 */
class Router
{
    /** resource slug => Controller class */
    private array $map = [
        ''            => ['HomeController', 'index'],
        'home'        => ['HomeController', 'index'],
        'login'       => ['AuthController', 'login'],
        'logout'      => ['AuthController', 'logout'],
        'dashboard'   => ['DashboardController', 'index'],
        'revenue-data'=> ['DashboardController', 'revenueData'],
    ];

    /** resource slug => Controller class (action-based CRUD controllers) */
    private array $resources = [
        'category'    => 'CategoryController',
        'menu'        => 'MenuController',
        'table'       => 'TableController',
        'reservation' => 'ReservationController',
        'order'       => 'OrderController',
        'user'        => 'UserController',
        'role'        => 'RoleController',
        'report'      => 'ReportController',
        'log'         => 'LogController',
    ];

    public function dispatch(): void
    {
        $path = $this->currentPath();
        $segments = $path === '' ? [] : explode('/', $path);

        $resource = $segments[0] ?? '';

        // Fixed single routes.
        if (array_key_exists($resource, $this->map) && count($segments) <= 1) {
            [$class, $method] = $this->map[$resource];
            $this->call($class, $method, []);
            return;
        }

        // Resource/action CRUD routes.
        if (isset($this->resources[$resource])) {
            $class  = $this->resources[$resource];
            $action = $segments[1] ?? 'index';
            $id     = $segments[2] ?? null;
            $args   = $id !== null ? [$id] : [];

            // Guard against calling non-public/internal methods.
            $reserved = ['__construct', 'dispatch'];
            if (in_array($action, $reserved, true) || !method_exists($class, $action)) {
                abort(404);
            }
            $this->call($class, $action, $args);
            return;
        }

        abort(404);
    }

    private function call(string $class, string $method, array $args): void
    {
        if (!class_exists($class) || !method_exists($class, $method)) {
            abort(404);
        }
        $controller = new $class();
        $controller->$method(...$args);
    }

    /** Extract the route path from PATH_INFO or ?r= fallback. */
    private function currentPath(): string
    {
        $path = $_SERVER['PATH_INFO'] ?? '';
        if ($path === '' && isset($_GET['r'])) {
            $path = '/' . $_GET['r'];
        }
        return trim($path, '/');
    }
}
