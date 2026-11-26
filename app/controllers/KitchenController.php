<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class KitchenController extends Controller
{
    private array $stages = ['queued', 'cooking', 'ready', 'served'];

    public function index(): void
    {
        require_permission('kitchen.view');
        $orders = Database::all(
            "SELECT o.*, t.table_number
               FROM orders o
               LEFT JOIN dining_tables t ON t.id = o.table_id
              WHERE o.status = 'open'
              ORDER BY o.created_at ASC"
        );
        foreach ($orders as &$o) {
            $o['items'] = Database::all('SELECT item_name, qty FROM order_items WHERE order_id = ? ORDER BY id ASC', [$o['id']]);
        }
        unset($o);
        $board = ['queued' => [], 'cooking' => [], 'ready' => []];
        foreach ($orders as $o) {
            $ks = $o['kitchen_status'];
            if (isset($board[$ks])) {
                $board[$ks][] = $o;
            }
        }
        $this->view('kitchen/board', compact('board'), 'Dapur');
    }

    public function setStatus($id): void
    {
        require_permission('kitchen.update');
        $this->guardPost();
        $id = $this->intId($id);
        $order = Order::find($id) ?? abort(404);
        $status = post('kitchen_status');
        if (!in_array($status, $this->stages, true)) {
            flash('error', 'Status dapur tidak valid.');
            redirect('kitchen');
        }
        Database::run('UPDATE orders SET kitchen_status = ? WHERE id = ?', [$status, $id]);
        log_activity('kitchen.update', 'Dapur ' . $order['order_number'] . ' -> ' . $status);
        flash('success', 'Status dapur ' . $order['order_number'] . ' diperbarui.');
        redirect('kitchen');
    }
}
