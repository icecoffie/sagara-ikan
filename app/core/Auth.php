<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

/**
 * Session-based authentication + permission resolution (RBAC).
 */
class Auth
{
    /** Attempt login. Returns the user row on success, null on failure. */
    public static function attempt(string $email, string $password): ?array
    {
        $user = Database::first(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug
               FROM users u
               JOIN roles r ON r.id = u.role_id
              WHERE u.email = ? AND u.is_active = 1
              LIMIT 1',
            [$email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        // Optional: upgrade legacy hashes.
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            Database::run('UPDATE users SET password = ? WHERE id = ?',
                [password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }

        self::startSession($user);
        return $user;
    }

    /** Persist the authenticated user + their permissions into the session. */
    public static function startSession(array $user): void
    {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['permissions'] = self::loadPermissions((int) $user['role_id']);
    }

    /** Refresh cached permissions (e.g. after an admin edits the role). */
    public static function refreshPermissions(): void
    {
        if (self::check()) {
            $_SESSION['permissions'] =
                self::loadPermissions((int) $_SESSION['user']['role_id']);
        }
    }

    private static function loadPermissions(int $roleId): array
    {
        $rows = Database::all(
            'SELECT p.slug
               FROM permissions p
               JOIN role_permissions rp ON rp.permission_id = p.id
              WHERE rp.role_id = ?',
            [$roleId]
        );
        return array_column($rows, 'slug');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']) ? (int) $_SESSION['user']['id'] : null;
    }

    /** Does the current user have a permission? Admins (role slug 'admin') pass all. */
    public static function can(string $permission): bool
    {
        if (!self::check()) {
            return false;
        }
        if (($_SESSION['user']['role_slug'] ?? '') === 'admin') {
            return true;
        }
        return in_array($permission, $_SESSION['permissions'] ?? [], true);
    }
}
