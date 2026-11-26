<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Order extends Model
{
    protected static string $table = 'orders';
    protected static array $fillable = [
        'order_number', 'table_id', 'user_id', 'source', 'order_type',
        'customer_name', 'customer_phone', 'status', 'kitchen_status',
        'subtotal', 'discount', 'tax', 'service_charge', 'voucher_code',
        'total', 'paid_at',
    ];

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

    /** Recompute subtotal, discount (incl. voucher), tax, service, and total. */
    public static function recalculate(int $id): float
    {
        $subtotal = (float) Database::scalar('SELECT COALESCE(SUM(subtotal),0) FROM order_items WHERE order_id = ?', [$id]);
        $order = self::find($id);
        $discount = (float) ($order['discount'] ?? 0);

        if (!empty($order['voucher_code'])) {
            $v = Voucher::findActive($order['voucher_code']);
            $discount = $v ? Voucher::discountFor($v, $subtotal) : 0.0;
        }
        $discount = min($discount, $subtotal);

        $base = max(0, $subtotal - $discount);
        $taxPct = (float) Config::get('tax_percent', 0);
        $svcPct = (float) Config::get('service_percent', 0);
        $tax = round($base * $taxPct / 100);
        $service = round($base * $svcPct / 100);
        $total = $base + $tax + $service;

        Database::run(
            'UPDATE orders SET subtotal=?, discount=?, tax=?, service_charge=?, total=? WHERE id=?',
            [$subtotal, $discount, $tax, $service, $total, $id]
        );
        return $total;
    }

    /** Apply a voucher code. Returns [ok(bool), message(string)]. */
    public static function applyVoucher(int $id, string $code): array
    {
        $subtotal = (float) Database::scalar('SELECT COALESCE(SUM(subtotal),0) FROM order_items WHERE order_id = ?', [$id]);
        $v = Voucher::findActive($code);
        if (!$v) {
            return [false, 'Kode voucher tidak valid atau tidak aktif.'];
        }
        if ($subtotal < (float) $v['min_total']) {
            return [false, 'Minimal belanja ' . rupiah($v['min_total']) . ' untuk voucher ini.'];
        }
        Database::run('UPDATE orders SET voucher_code=?, discount=? WHERE id=?',
            [$v['code'], Voucher::discountFor($v, $subtotal), $id]);
        self::recalculate($id);
        return [true, 'Voucher ' . $v['code'] . ' diterapkan.'];
    }

    public static function setManualDiscount(int $id, float $amount): void
    {
        Database::run('UPDATE orders SET voucher_code=NULL, discount=? WHERE id=?', [max(0, $amount), $id]);
        self::recalculate($id);
    }

    public static function clearDiscount(int $id): void
    {
        Database::run('UPDATE orders SET voucher_code=NULL, discount=0 WHERE id=?', [$id]);
        self::recalculate($id);
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
