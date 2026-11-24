<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class OrderController extends Controller
{
    public function index(): void
    {
        require_permission('order.view');
        $filters = [
            'q'      => trim((string) input('q', '')),
            'status' => input('status', ''),
            'date'   => input('date', ''),
        ];
        $total = Order::searchCount($filters);
        $p = paginate($total, 10);
        $items = Order::search($filters, $p['perPage'], $p['offset']);
        $this->view('order/index', compact('items', 'p', 'filters'), 'Pesanan');
    }

    public function create(): void
    {
        require_permission('order.create');
        $tables = DiningTable::options();
        $this->view('order/create', compact('tables'), 'Pesanan Baru');
    }

    public function store(): void
    {
        require_permission('order.create');
        $this->guardPost();
        $tableId = post('table_id') ? (int) post('table_id') : null;
        $id = Order::create([
            'order_number' => order_number(),
            'table_id'     => $tableId,
            'user_id'      => Auth::id(),
            'status'       => 'open',
            'total'        => 0,
        ]);
        if ($tableId) {
            Database::run("UPDATE dining_tables SET status='occupied' WHERE id=?", [$tableId]);
        }
        log_activity('order.create', 'Membuat pesanan baru');
        flash('success', 'Pesanan dibuat. Silakan tambahkan item.');
        redirect('order/show/' . $id);
    }

    public function show($id): void
    {
        require_permission('order.view');
        $order = Order::withDetails($this->intId($id)) ?? abort(404);
        $menu = MenuItem::available();
        $this->view('order/show', compact('order', 'menu'), 'Pesanan ' . $order['order_number']);
    }

    public function addItem($id): void
    {
        require_permission('order.update');
        $this->guardPost();
        $id = $this->intId($id);
        $order = Order::find($id) ?? abort(404);
        $this->ensureOpen($order);

        $menuId = (int) post('menu_item_id');
        $qty = max(1, (int) post('qty', 1));
        $menu = MenuItem::find($menuId);
        if (!$menu) {
            flash('error', 'Menu tidak ditemukan.');
            redirect('order/show/' . $id);
        }

        $existing = Database::first('SELECT * FROM order_items WHERE order_id=? AND menu_item_id=? LIMIT 1', [$id, $menuId]);
        if ($existing) {
            $newQty = (int) $existing['qty'] + $qty;
            OrderItem::update((int) $existing['id'], [
                'qty' => $newQty, 'subtotal' => $newQty * (float) $existing['price'],
            ]);
        } else {
            OrderItem::create([
                'order_id'     => $id,
                'menu_item_id' => $menuId,
                'item_name'    => $menu['name'],
                'qty'          => $qty,
                'price'        => $menu['price'],
                'subtotal'     => $qty * (float) $menu['price'],
            ]);
        }
        Order::recalculate($id);
        redirect('order/show/' . $id);
    }

    public function removeItem($itemId): void
    {
        require_permission('order.update');
        $this->guardPost();
        $itemId = $this->intId($itemId);
        $item = OrderItem::find($itemId) ?? abort(404);
        $order = Order::find((int) $item['order_id']) ?? abort(404);
        $this->ensureOpen($order);
        OrderItem::delete($itemId);
        Order::recalculate((int) $order['id']);
        redirect('order/show/' . $order['id']);
    }

    public function pay($id): void
    {
        require_permission('order.pay');
        $this->guardPost();
        $id = $this->intId($id);
        $order = Order::find($id) ?? abort(404);
        $this->ensureOpen($order);
        $total = Order::recalculate($id);
        if ($total <= 0) {
            flash('error', 'Tidak dapat membayar pesanan kosong.');
            redirect('order/show/' . $id);
        }
        Database::run("UPDATE orders SET status='paid', paid_at=NOW() WHERE id=?", [$id]);
        if ($order['table_id']) {
            Database::run("UPDATE dining_tables SET status='available' WHERE id=?", [$order['table_id']]);
        }
        log_activity('order.pay', 'Pembayaran pesanan ' . $order['order_number']);
        flash('success', 'Pembayaran berhasil. Struk siap dicetak.');
        redirect('order/receipt/' . $id);
    }

    public function cancel($id): void
    {
        require_permission('order.update');
        $this->guardPost();
        $id = $this->intId($id);
        $order = Order::find($id) ?? abort(404);
        $this->ensureOpen($order);
        Database::run("UPDATE orders SET status='cancelled' WHERE id=?", [$id]);
        if ($order['table_id']) {
            Database::run("UPDATE dining_tables SET status='available' WHERE id=?", [$order['table_id']]);
        }
        log_activity('order.update', 'Membatalkan pesanan ' . $order['order_number']);
        flash('success', 'Pesanan dibatalkan.');
        redirect('order/show/' . $id);
    }

    public function delete($id): void
    {
        require_permission('order.delete');
        $this->guardPost();
        $id = $this->intId($id);
        $order = Order::find($id) ?? abort(404);
        Order::delete($id);
        log_activity('order.delete', 'Menghapus pesanan ' . $order['order_number']);
        flash('success', 'Pesanan dihapus.');
        redirect('order');
    }

    public function receipt($id): void
    {
        require_permission('order.view');
        $order = Order::withDetails($this->intId($id)) ?? abort(404);
        $this->raw('order/receipt', ['order' => $order, 'appName' => Config::get('app_name', 'Sagara Resto')]);
    }

    private function ensureOpen(array $order): void
    {
        if ($order['status'] !== 'open') {
            flash('error', 'Pesanan ini sudah ' . ($order['status'] === 'paid' ? 'dibayar' : 'dibatalkan') . ' dan tidak bisa diubah.');
            redirect('order/show/' . $order['id']);
        }
    }
}
