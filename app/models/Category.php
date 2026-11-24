<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Category extends Model
{
    protected static string $table = 'categories';
    protected static array $fillable = ['name', 'slug', 'description', 'is_active'];

    public static function active(): array
    {
        return Database::all(
            'SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC'
        );
    }

    public static function options(): array
    {
        return Database::all('SELECT id, name FROM categories ORDER BY name ASC');
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM categories WHERE slug = ?';
        $params = [$slug];
        if ($exceptId) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }
        return (int) Database::scalar($sql, $params) > 0;
    }

    public static function search(string $q, int $limit, int $offset): array
    {
        $like = '%' . $q . '%';
        return Database::all(
            'SELECT c.*, (SELECT COUNT(*) FROM menu_items m WHERE m.category_id = c.id) AS item_count
               FROM categories c
              WHERE c.name LIKE ? OR c.slug LIKE ?
              ORDER BY c.name ASC
              LIMIT ? OFFSET ?',
            [$like, $like, $limit, $offset]
        );
    }

    public static function searchCount(string $q): int
    {
        $like = '%' . $q . '%';
        return (int) Database::scalar(
            'SELECT COUNT(*) FROM categories WHERE name LIKE ? OR slug LIKE ?',
            [$like, $like]
        );
    }
}
