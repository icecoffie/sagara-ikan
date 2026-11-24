<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class MenuItem extends Model
{
    protected static string $table = 'menu_items';
    protected static array $fillable = [
        'name', 'category_id', 'price', 'description', 'image', 'is_available',
    ];

    public static function publicMenu(): array
    {
        return Database::all(
            'SELECT m.*, c.name AS category_name, c.slug AS category_slug
               FROM menu_items m
               JOIN categories c ON c.id = m.category_id
              WHERE m.is_available = 1 AND c.is_active = 1
              ORDER BY c.name ASC, m.name ASC'
        );
    }

    public static function available(): array
    {
        return Database::all(
            'SELECT m.*, c.name AS category_name
               FROM menu_items m
               JOIN categories c ON c.id = m.category_id
              WHERE m.is_available = 1
              ORDER BY c.name ASC, m.name ASC'
        );
    }

    public static function findWithCategory(int $id): ?array
    {
        return Database::first(
            'SELECT m.*, c.name AS category_name
               FROM menu_items m
               JOIN categories c ON c.id = m.category_id
              WHERE m.id = ? LIMIT 1',
            [$id]
        );
    }

    public static function search(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = self::buildWhere($filters);
        $params[] = $limit;
        $params[] = $offset;
        return Database::all(
            'SELECT m.*, c.name AS category_name
               FROM menu_items m
               JOIN categories c ON c.id = m.category_id'
            . $where .
            ' ORDER BY m.created_at DESC LIMIT ? OFFSET ?',
            $params
        );
    }

    public static function searchCount(array $filters): int
    {
        [$where, $params] = self::buildWhere($filters);
        return (int) Database::scalar(
            'SELECT COUNT(*) FROM menu_items m
               JOIN categories c ON c.id = m.category_id' . $where,
            $params
        );
    }

    private static function buildWhere(array $filters): array
    {
        $clauses = [];
        $params = [];
        if (!empty($filters['q'])) {
            $clauses[] = '(m.name LIKE ? OR m.description LIKE ?)';
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['category_id'])) {
            $clauses[] = 'm.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }
        if (isset($filters['available']) && $filters['available'] !== '') {
            $clauses[] = 'm.is_available = ?';
            $params[] = (int) $filters['available'];
        }
        $where = $clauses ? ' WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }
}
