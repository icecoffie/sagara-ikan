<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class Permission extends Model
{
    protected static string $table = 'permissions';

    public static function grouped(): array
    {
        $rows = Database::all('SELECT * FROM permissions ORDER BY resource ASC, id ASC');
        $out = [];
        foreach ($rows as $r) { $out[$r['resource']][] = $r; }
        return $out;
    }
}
