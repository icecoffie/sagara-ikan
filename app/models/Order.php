<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Order extends Model
{
    protected static string $table = 'orders';
    protected static array $fillable = ['order_number', 'table_id', 'user_id', 'source', 'order_type', 'customer_name', 'customer_phone', 'status', 'total', 'paid_at'];

    public static function search(array $f, int $limit, int $offset): array
    {
        [$where, $params] = self::where($f);
        $params[] = $limit; $params[] = $offset;
        return Database::all(
            "SELECT o.*, t.table_number, u.name AS cashier
             FROM orders o
             LEFT JOIN dining_tables t ON t.id = o.table_id
             LEFT JOIN users u ON u.id = o.user_id$where
             ORDER BY o.created_at DESC LIMIT ? OFFSET ?", $params);
    }

    public static function searchCount(array $f): int
    {
        [$where, $params] = self::where($f);
        return (int) Database::scalar("SELECT COUNT(*) FROM orders o$where", $params);
    }

    public static function withDetails(int $id): ?array
    {
        $order = Database::first(
            "SELECT o.*, t.table_number, u.name AS cashier
             FROM orders o
             LEFT JOIN dining_tables t ON t.id = o.table_id
             LEFT JOIN users u ON u.id = o.user_id
             WHERE o.id = ? LIMIT 1", [$id]);
        if (!$order) { return null; }
        $order['items'] = Database::all('SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC', [$id]);
        return $order;
    }

    public static function recalculate(int $id): float
    {
        $total = (float) Database::scalar('SELECT COALESCE(SUM(subtotal),0) FROM order_items WHERE order_id = ?', [$id]);
        Database::run('UPDATE orders SET total = ? WHERE id = ?', [$total, $id]);
        return $total;
    }

    private static function where(array $f): array
    {
        $c = []; $p = [];
        if (!empty($f['q'])) { $c[] = 'o.order_number LIKE ?'; $p[] = '%' . $f['q'] . '%'; }
        if (!empty($f['status'])) { $c[] = 'o.status = ?'; $p[] = $f['status']; }
        if (!empty($f['date'])) { $c[] = 'DATE(o.created_at) = ?'; $p[] = $f['date']; }
        return [$c ? ' WHERE ' . implode(' AND ', $c) : '', $p];
    }
}
