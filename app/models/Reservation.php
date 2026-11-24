<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Reservation extends Model
{
    protected static string $table = 'reservations';
    protected static array $fillable = ['customer_name', 'phone', 'table_id', 'reservation_time', 'party_size', 'status', 'notes'];

    public static function search(array $f, int $limit, int $offset): array
    {
        [$where, $params] = self::where($f);
        $params[] = $limit; $params[] = $offset;
        return Database::all(
            "SELECT r.*, t.table_number FROM reservations r
             LEFT JOIN dining_tables t ON t.id = r.table_id$where
             ORDER BY r.reservation_time DESC LIMIT ? OFFSET ?", $params);
    }

    public static function searchCount(array $f): int
    {
        [$where, $params] = self::where($f);
        return (int) Database::scalar("SELECT COUNT(*) FROM reservations r$where", $params);
    }

    private static function where(array $f): array
    {
        $c = []; $p = [];
        if (!empty($f['q'])) { $c[] = '(r.customer_name LIKE ? OR r.phone LIKE ?)'; $p[] = '%' . $f['q'] . '%'; $p[] = '%' . $f['q'] . '%'; }
        if (!empty($f['status'])) { $c[] = 'r.status = ?'; $p[] = $f['status']; }
        return [$c ? ' WHERE ' . implode(' AND ', $c) : '', $p];
    }
}
