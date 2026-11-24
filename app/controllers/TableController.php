<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class TableController extends Controller
{
    private array $locations = ['indoor', 'outdoor'];
    private array $statuses = ['available', 'occupied', 'reserved'];

    public function index(): void
    {
        require_permission('table.view');
        $filters = [
            'q'        => trim((string) input('q', '')),
            'status'   => input('status', ''),
            'location' => input('location', ''),
        ];
        $total = DiningTable::searchCount($filters);
        $p = paginate($total, 10);
        $items = DiningTable::search($filters, $p['perPage'], $p['offset']);
        $this->view('table/index', compact('items', 'p', 'filters'), 'Meja');
    }

    public function create(): void
    {
        require_permission('table.create');
        $this->view('table/form', ['item' => [], 'errors' => [], 'mode' => 'create'], 'Tambah Meja');
    }

    public function store(): void
    {
        require_permission('table.create');
        $this->guardPost();
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $this->view('table/form', ['item' => $data, 'errors' => $errors, 'mode' => 'create'], 'Tambah Meja');
            return;
        }
        DiningTable::create($data);
        log_activity('table.create', 'Menambah meja: ' . $data['table_number']);
        flash('success', 'Meja berhasil ditambahkan.');
        redirect('table');
    }

    public function edit($id): void
    {
        require_permission('table.update');
        $item = DiningTable::find($this->intId($id)) ?? abort(404);
        $this->view('table/form', ['item' => $item, 'errors' => [], 'mode' => 'edit'], 'Ubah Meja');
    }

    public function update($id): void
    {
        require_permission('table.update');
        $this->guardPost();
        $id = $this->intId($id);
        DiningTable::find($id) ?? abort(404);
        $data = $this->collect();
        $errors = $this->validate($data, $id);
        if ($errors) {
            $data['id'] = $id;
            $this->view('table/form', ['item' => $data, 'errors' => $errors, 'mode' => 'edit'], 'Ubah Meja');
            return;
        }
        DiningTable::update($id, $data);
        log_activity('table.update', 'Mengubah meja: ' . $data['table_number']);
        flash('success', 'Meja berhasil diperbarui.');
        redirect('table');
    }

    public function delete($id): void
    {
        require_permission('table.delete');
        $this->guardPost();
        $id = $this->intId($id);
        $item = DiningTable::find($id) ?? abort(404);
        DiningTable::delete($id);
        log_activity('table.delete', 'Menghapus meja: ' . $item['table_number']);
        flash('success', 'Meja berhasil dihapus.');
        redirect('table');
    }

    private function collect(): array
    {
        $location = post('location');
        $status = post('status');
        return [
            'table_number' => trim((string) post('table_number')),
            'capacity'     => max(1, (int) post('capacity')),
            'location'     => in_array($location, $this->locations, true) ? $location : 'indoor',
            'status'       => in_array($status, $this->statuses, true) ? $status : 'available',
        ];
    }

    private function validate(array $data, ?int $exceptId = null): array
    {
        $errors = [];
        if ($data['table_number'] === '') {
            $errors['table_number'] = 'Nomor meja wajib diisi.';
        } elseif (DiningTable::numberExists($data['table_number'], $exceptId)) {
            $errors['table_number'] = 'Nomor meja sudah digunakan.';
        }
        return $errors;
    }
}
