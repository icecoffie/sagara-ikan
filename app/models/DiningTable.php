<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class DiningTable extends Model
{
    protected static string $table = 'dining_tables';
    protected static array $fillable = ['table_number', 'capacity', 'location', 'status'];

    public static function options(): array
    {
        return Database::all('SELECT id, table_number, capacity, status FROM dining_tables ORDER BY table_number ASC');
    }

    public static function numberExists(string $number, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM dining_tables WHERE table_number = ?';
        $params = [$number];
        if ($exceptId) { $sql .= ' AND id <> ?'; $params[] = $exceptId; }
        return (int) Database::scalar($sql, $params) > 0;
    }

    public static function search(array $f, int $limit, int $offset): array
    {
        [$where, $params] = self::where($f);
        $params[] = $limit; $params[] = $offset;
        return Database::all("SELECT * FROM dining_tables$where ORDER BY table_number ASC LIMIT ? OFFSET ?", $params);
    }

    public static function searchCount(array $f): int
    {
        [$where, $params] = self::where($f);
        return (int) Database::scalar("SELECT COUNT(*) FROM dining_tables$where", $params);
    }

    private static function where(array $f): array
    {
        $c = []; $p = [];
        if (!empty($f['q'])) { $c[] = 'table_number LIKE ?'; $p[] = '%' . $f['q'] . '%'; }
        if (!empty($f['status'])) { $c[] = 'status = ?'; $p[] = $f['status']; }
        if (!empty($f['location'])) { $c[] = 'location = ?'; $p[] = $f['location']; }
        return [$c ? ' WHERE ' . implode(' AND ', $c) : '', $p];
    }
}
