<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class ReservationController extends Controller
{
    private array $statuses = ['pending', 'confirmed', 'cancelled'];

    public function index(): void
    {
        require_permission('reservation.view');
        $filters = ['q' => trim((string) input('q', '')), 'status' => input('status', '')];
        $total = Reservation::searchCount($filters);
        $p = paginate($total, 10);
        $items = Reservation::search($filters, $p['perPage'], $p['offset']);
        $this->view('reservation/index', compact('items', 'p', 'filters'), 'Reservasi');
    }

    public function create(): void
    {
        require_permission('reservation.create');
        $this->view('reservation/form', ['item' => [], 'errors' => [], 'mode' => 'create', 'tables' => DiningTable::options()], 'Tambah Reservasi');
    }

    public function store(): void
    {
        require_permission('reservation.create');
        $this->guardPost();
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $this->view('reservation/form', ['item' => $data, 'errors' => $errors, 'mode' => 'create', 'tables' => DiningTable::options()], 'Tambah Reservasi');
            return;
        }
        Reservation::create($data);
        log_activity('reservation.create', 'Reservasi untuk ' . $data['customer_name']);
        flash('success', 'Reservasi berhasil ditambahkan.');
        redirect('reservation');
    }

    public function edit($id): void
    {
        require_permission('reservation.update');
        $item = Reservation::find($this->intId($id)) ?? abort(404);
        $item['reservation_time'] = date('Y-m-d\TH:i', strtotime($item['reservation_time']));
        $this->view('reservation/form', ['item' => $item, 'errors' => [], 'mode' => 'edit', 'tables' => DiningTable::options()], 'Ubah Reservasi');
    }

    public function update($id): void
    {
        require_permission('reservation.update');
        $this->guardPost();
        $id = $this->intId($id);
        Reservation::find($id) ?? abort(404);
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $data['id'] = $id;
            $this->view('reservation/form', ['item' => $data, 'errors' => $errors, 'mode' => 'edit', 'tables' => DiningTable::options()], 'Ubah Reservasi');
            return;
        }
        Reservation::update($id, $data);
        log_activity('reservation.update', 'Mengubah reservasi: ' . $data['customer_name']);
        flash('success', 'Reservasi berhasil diperbarui.');
        redirect('reservation');
    }

    public function delete($id): void
    {
        require_permission('reservation.delete');
        $this->guardPost();
        $id = $this->intId($id);
        $item = Reservation::find($id) ?? abort(404);
        Reservation::delete($id);
        log_activity('reservation.delete', 'Menghapus reservasi: ' . $item['customer_name']);
        flash('success', 'Reservasi berhasil dihapus.');
        redirect('reservation');
    }

    private function collect(): array
    {
        $status = post('status');
        $tableId = post('table_id');
        $time = post('reservation_time');
        return [
            'customer_name'    => trim((string) post('customer_name')),
            'phone'            => trim((string) post('phone')),
            'table_id'         => $tableId ? (int) $tableId : null,
            'reservation_time' => $time ? date('Y-m-d H:i:s', strtotime($time)) : '',
            'party_size'       => max(1, (int) post('party_size')),
            'status'           => in_array($status, $this->statuses, true) ? $status : 'pending',
            'notes'            => trim((string) post('notes')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['customer_name'] === '') {
            $errors['customer_name'] = 'Nama tamu wajib diisi.';
        }
        if (empty($data['reservation_time']) || strtotime($data['reservation_time']) === false) {
            $errors['reservation_time'] = 'Waktu reservasi tidak valid.';
        }
        return $errors;
    }
}
