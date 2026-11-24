<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

abstract class Model
{
    protected static string $table = '';
    protected static array $fillable = [];

    public static function table(): string
    {
        return static::$table;
    }

    public static function find(int $id): ?array
    {
        return Database::first(
            'SELECT * FROM ' . static::$table . ' WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        return Database::all('SELECT * FROM ' . static::$table . ' ORDER BY ' . $orderBy);
    }

    public static function count(): int
    {
        return (int) Database::scalar('SELECT COUNT(*) FROM ' . static::$table);
    }

    public static function create(array $data): int
    {
        $data = static::only($data);
        $cols = array_keys($data);
        $place = implode(', ', array_fill(0, count($cols), '?'));
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(', ', $cols)
            . ') VALUES (' . $place . ')';
        Database::run($sql, array_values($data));
        return (int) Database::lastId();
    }

    public static function update(int $id, array $data): void
    {
        $data = static::only($data);
        if (empty($data)) {
            return;
        }
        $set = implode(', ', array_map(fn($c) => "$c = ?", array_keys($data)));
        $params = array_values($data);
        $params[] = $id;
        Database::run('UPDATE ' . static::$table . " SET $set WHERE id = ?", $params);
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM ' . static::$table . ' WHERE id = ?', [$id]);
    }

    protected static function only(array $data): array
    {
        if (empty(static::$fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip(static::$fillable));
    }
}
