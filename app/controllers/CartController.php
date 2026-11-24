<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class CartController extends Controller
{
    public function index(): void
    {
        $cart = cart_items();
        View::renderPublic('public/cart', $cart, 'Keranjang');
    }

    public function add($id): void
    {
        $this->guardPost();
        $id = (int) $id;
        $menu = MenuItem::find($id);
        if (!$menu || !$menu['is_available']) {
            flash('error', 'Menu tidak tersedia.');
            redirect('');
        }
        cart_add($id, (int) post('qty', 1));
        flash('success', e($menu['name']) . ' ditambahkan ke keranjang.');
        $back = (string) post('back', '');
        redirect($back === 'cart' ? 'cart' : '');
    }

    public function update(): void
    {
        $this->guardPost();
        foreach ((array) post('qty', []) as $mid => $qty) {
            cart_set((int) $mid, (int) $qty);
        }
        flash('success', 'Keranjang diperbarui.');
        redirect('cart');
    }

    public function remove($id): void
    {
        $this->guardPost();
        cart_remove((int) $id);
        flash('info', 'Item dihapus dari keranjang.');
        redirect('cart');
    }

    public function clear(): void
    {
        $this->guardPost();
        cart_clear();
        redirect('cart');
    }

    public function checkout(): void
    {
        $cart = cart_items();
        if (!$cart['items']) {
            flash('error', 'Keranjang masih kosong.');
            redirect('cart');
        }
        View::renderPublic('public/checkout', array_merge($cart, [
            'tables' => DiningTable::options(),
            'errors' => [],
            'old'    => [],
        ]), 'Checkout');
    }

    public function place(): void
    {
        $this->guardPost();
        $cart = cart_items();
        if (!$cart['items']) {
            flash('error', 'Keranjang masih kosong.');
            redirect('cart');
        }

        $data = [
            'customer_name'  => trim((string) post('customer_name')),
            'customer_phone' => trim((string) post('customer_phone')),
            'order_type'     => post('order_type') === 'takeaway' ? 'takeaway' : 'dine_in',
            'table_id'       => post('table_id') ? (int) post('table_id') : null,
            'notes'          => trim((string) post('notes')),
        ];

        $errors = [];
        if ($data['customer_name'] === '') {
            $errors['customer_name'] = 'Nama wajib diisi.';
        }
        if ($data['order_type'] === 'dine_in' && !$data['table_id']) {
            $errors['table_id'] = 'Pilih nomor meja untuk makan di tempat.';
        }
        if ($data['order_type'] === 'takeaway') {
            $data['table_id'] = null;
        }
        if ($errors) {
            View::renderPublic('public/checkout', array_merge($cart, [
                'tables' => DiningTable::options(),
                'errors' => $errors,
                'old'    => $data,
            ]), 'Checkout');
            return;
        }

        $number = order_number();
        $orderId = Order::create([
            'order_number'   => $number,
            'table_id'       => $data['table_id'],
            'user_id'        => null,
            'source'         => 'customer',
            'order_type'     => $data['order_type'],
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'status'         => 'open',
            'total'          => $cart['total'],
        ]);
        foreach ($cart['items'] as $it) {
            OrderItem::create([
                'order_id'     => $orderId,
                'menu_item_id' => $it['id'],
                'item_name'    => $it['name'],
                'qty'          => $it['qty'],
                'price'        => $it['price'],
                'subtotal'     => $it['subtotal'],
            ]);
        }
        Order::recalculate($orderId);
        if ($data['table_id']) {
            Database::run("UPDATE dining_tables SET status='occupied' WHERE id=?", [$data['table_id']]);
        }
        log_activity('order.create', 'Pesanan pelanggan ' . $number . ' (' . $data['customer_name'] . ')');
        cart_clear();
        $_SESSION['last_order'] = $number;
        redirect('cart/confirm/' . $number);
    }

    public function confirm($number): void
    {
        $order = $this->findByNumber((string) $number);
        if (!$order) {
            abort(404);
        }
        View::renderPublic('public/confirm', ['order' => $order], 'Pesanan Diterima');
    }

    public function track(): void
    {
        $order = null;
        $searched = false;
        if ($this->isPost()) {
            $this->guardPost();
            $searched = true;
            $order = $this->findByNumber(trim((string) post('order_number')));
        } elseif (isset($_GET['order_number'])) {
            $searched = true;
            $order = $this->findByNumber(trim((string) $_GET['order_number']));
        }
        View::renderPublic('public/track', compact('order', 'searched'), 'Lacak Pesanan');
    }

    private function findByNumber(string $number): ?array
    {
        if ($number === '') {
            return null;
        }
        $order = Database::first(
            "SELECT o.*, t.table_number FROM orders o
             LEFT JOIN dining_tables t ON t.id = o.table_id
             WHERE o.order_number = ? LIMIT 1",
            [$number]
        );
        if (!$order) {
            return null;
        }
        $order['items'] = Database::all('SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC', [$order['id']]);
        return $order;
    }
}
