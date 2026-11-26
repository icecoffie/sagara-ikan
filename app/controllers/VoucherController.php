<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class VoucherController extends Controller
{
    public function index(): void
    {
        require_permission('voucher.view');
        $q = trim((string) input('q', ''));
        $total = Voucher::searchCount($q);
        $p = paginate($total, 10);
        $items = Voucher::search($q, $p['perPage'], $p['offset']);
        $this->view('voucher/index', compact('items', 'p', 'q'), 'Voucher');
    }

    public function create(): void
    {
        require_permission('voucher.create');
        $this->view('voucher/form', ['item' => [], 'errors' => [], 'mode' => 'create'], 'Tambah Voucher');
    }

    public function store(): void
    {
        require_permission('voucher.create');
        $this->guardPost();
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $this->view('voucher/form', ['item' => $data, 'errors' => $errors, 'mode' => 'create'], 'Tambah Voucher');
            return;
        }
        Voucher::create($data);
        log_activity('voucher.create', 'Menambah voucher: ' . $data['code']);
        flash('success', 'Voucher berhasil ditambahkan.');
        redirect('voucher');
    }

    public function edit($id): void
    {
        require_permission('voucher.update');
        $item = Voucher::find($this->intId($id)) ?? abort(404);
        $this->view('voucher/form', ['item' => $item, 'errors' => [], 'mode' => 'edit'], 'Ubah Voucher');
    }

    public function update($id): void
    {
        require_permission('voucher.update');
        $this->guardPost();
        $id = $this->intId($id);
        Voucher::find($id) ?? abort(404);
        $data = $this->collect();
        $errors = $this->validate($data, $id);
        if ($errors) {
            $data['id'] = $id;
            $this->view('voucher/form', ['item' => $data, 'errors' => $errors, 'mode' => 'edit'], 'Ubah Voucher');
            return;
        }
        Voucher::update($id, $data);
        log_activity('voucher.update', 'Mengubah voucher: ' . $data['code']);
        flash('success', 'Voucher berhasil diperbarui.');
        redirect('voucher');
    }

    public function delete($id): void
    {
        require_permission('voucher.delete');
        $this->guardPost();
        $id = $this->intId($id);
        $item = Voucher::find($id) ?? abort(404);
        Voucher::delete($id);
        log_activity('voucher.delete', 'Menghapus voucher: ' . $item['code']);
        flash('success', 'Voucher berhasil dihapus.');
        redirect('voucher');
    }

    private function collect(): array
    {
        $type = post('type') === 'percent' ? 'percent' : 'amount';
        return [
            'code'        => strtoupper(trim((string) post('code'))),
            'description' => trim((string) post('description')),
            'type'        => $type,
            'value'       => (float) str_replace(['.', ','], ['', '.'], (string) post('value')),
            'min_total'   => (float) str_replace(['.', ','], ['', '.'], (string) post('min_total')),
            'is_active'   => post('is_active') ? 1 : 0,
        ];
    }

    private function validate(array $data, ?int $exceptId = null): array
    {
        $errors = [];
        if ($data['code'] === '') {
            $errors['code'] = 'Kode voucher wajib diisi.';
        } elseif (Voucher::codeExists($data['code'], $exceptId)) {
            $errors['code'] = 'Kode voucher sudah dipakai.';
        }
        if ($data['value'] <= 0) {
            $errors['value'] = 'Nilai voucher harus lebih dari 0.';
        }
        if ($data['type'] === 'percent' && $data['value'] > 100) {
            $errors['value'] = 'Persentase maksimal 100.';
        }
        return $errors;
    }
}
