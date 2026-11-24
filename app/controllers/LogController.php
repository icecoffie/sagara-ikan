<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class LogController extends Controller
{
    public function index(): void
    {
        require_permission('log.view');
        $q = trim((string) input('q', ''));
        $like = '%' . $q . '%';
        $where = $q !== '' ? ' WHERE l.action LIKE ? OR l.detail LIKE ? OR u.name LIKE ?' : '';
        $params = $q !== '' ? [$like, $like, $like] : [];

        $total = (int) Database::scalar(
            "SELECT COUNT(*) FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id" . $where,
            $params
        );
        $p = paginate($total, 20);
        $rowsParams = $params;
        $rowsParams[] = $p['perPage'];
        $rowsParams[] = $p['offset'];
        $items = Database::all(
            "SELECT l.*, u.name AS user_name, u.email
               FROM activity_logs l
               LEFT JOIN users u ON u.id = l.user_id" . $where .
            " ORDER BY l.created_at DESC LIMIT ? OFFSET ?",
            $rowsParams
        );
        $this->view('log/index', compact('items', 'p', 'q'), 'Log Aktivitas');
    }
}
