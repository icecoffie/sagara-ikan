<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Role extends Model
{
    protected static string $table = 'roles';
    protected static array $fillable = ['name', 'slug', 'description'];

    public static function withCounts(): array
    {
        return Database::all(
            "SELECT r.*,
                (SELECT COUNT(*) FROM users u WHERE u.role_id = r.id) AS user_count,
                (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) AS perm_count
             FROM roles r ORDER BY r.id ASC");
    }

    public static function permissionIds(int $roleId): array
    {
        $rows = Database::all('SELECT permission_id FROM role_permissions WHERE role_id = ?', [$roleId]);
        return array_map('intval', array_column($rows, 'permission_id'));
    }

    public static function syncPermissions(int $roleId, array $permissionIds): void
    {
        Database::run('DELETE FROM role_permissions WHERE role_id = ?', [$roleId]);
        $stmt = Database::conn()->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)');
        foreach ($permissionIds as $pid) {
            $stmt->execute([$roleId, (int) $pid]);
        }
    }

    public static function options(): array
    {
        return Database::all('SELECT id, name FROM roles ORDER BY name ASC');
    }
}
