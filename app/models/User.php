<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class User extends Model
{
    protected static string $table = 'users';
    protected static array $fillable = ['name', 'email', 'password', 'role_id', 'is_active'];

    public static function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $params = [$email];
        if ($exceptId) { $sql .= ' AND id <> ?'; $params[] = $exceptId; }
        return (int) Database::scalar($sql, $params) > 0;
    }

    public static function search(array $f, int $limit, int $offset): array
    {
        [$where, $params] = self::where($f);
        $params[] = $limit; $params[] = $offset;
        return Database::all(
            "SELECT u.*, r.name AS role_name FROM users u
             JOIN roles r ON r.id = u.role_id$where
             ORDER BY u.name ASC LIMIT ? OFFSET ?", $params);
    }

    public static function searchCount(array $f): int
    {
        [$where, $params] = self::where($f);
        return (int) Database::scalar("SELECT COUNT(*) FROM users u$where", $params);
    }

    private static function where(array $f): array
    {
        $c = []; $p = [];
        if (!empty($f['q'])) { $c[] = '(u.name LIKE ? OR u.email LIKE ?)'; $p[] = '%' . $f['q'] . '%'; $p[] = '%' . $f['q'] . '%'; }
        if (!empty($f['role_id'])) { $c[] = 'u.role_id = ?'; $p[] = (int) $f['role_id']; }
        return [$c ? ' WHERE ' . implode(' AND ', $c) : '', $p];
    }
}
