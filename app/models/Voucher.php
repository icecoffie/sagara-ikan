<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Voucher extends Model
{
    protected static string $table = 'vouchers';
    protected static array $fillable = ['code', 'description', 'type', 'value', 'min_total', 'is_active'];

    public static function findActive(string $code): ?array
    {
        return Database::first(
            'SELECT * FROM vouchers WHERE code = ? AND is_active = 1 LIMIT 1',
            [strtoupper(trim($code))]
        );
    }

    public static function codeExists(string $code, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM vouchers WHERE code = ?';
        $params = [$code];
        if ($exceptId) { $sql .= ' AND id <> ?'; $params[] = $exceptId; }
        return (int) Database::scalar($sql, $params) > 0;
    }

    public static function discountFor(array $v, float $subtotal): float
    {
        if ($subtotal < (float) $v['min_total']) {
            return 0.0;
        }
        if ($v['type'] === 'percent') {
            return round($subtotal * (float) $v['value'] / 100);
        }
        return min((float) $v['value'], $subtotal);
    }

    public static function search(string $q, int $limit, int $offset): array
    {
        $like = '%' . $q . '%';
        return Database::all(
            'SELECT * FROM vouchers WHERE code LIKE ? OR description LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$like, $like, $limit, $offset]
        );
    }

    public static function searchCount(string $q): int
    {
        $like = '%' . $q . '%';
        return (int) Database::scalar('SELECT COUNT(*) FROM vouchers WHERE code LIKE ? OR description LIKE ?', [$like, $like]);
    }
}
