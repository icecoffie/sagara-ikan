<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class UserController extends Controller
{
    public function index(): void
    {
        require_permission('user.view');
        $filters = ['q' => trim((string) input('q', '')), 'role_id' => input('role_id', '')];
        $total = User::searchCount($filters);
        $p = paginate($total, 10);
        $items = User::search($filters, $p['perPage'], $p['offset']);
        $roles = Role::options();
        $this->view('user/index', compact('items', 'p', 'filters', 'roles'), 'Pengguna');
    }

    public function create(): void
    {
        require_permission('user.create');
        $this->view('user/form', ['item' => [], 'errors' => [], 'mode' => 'create', 'roles' => Role::options()], 'Tambah Pengguna');
    }

    public function store(): void
    {
        require_permission('user.create');
        $this->guardPost();
        $data = $this->collect();
        $errors = $this->validate($data, true);
        if ($errors) {
            $this->view('user/form', ['item' => $data, 'errors' => $errors, 'mode' => 'create', 'roles' => Role::options()], 'Tambah Pengguna');
            return;
        }
        User::create([
            'name' => $data['name'], 'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id' => $data['role_id'], 'is_active' => $data['is_active'],
        ]);
        log_activity('user.create', 'Menambah pengguna: ' . $data['email']);
        flash('success', 'Pengguna berhasil ditambahkan.');
        redirect('user');
    }

    public function edit($id): void
    {
        require_permission('user.update');
        $item = User::find($this->intId($id)) ?? abort(404);
        unset($item['password']);
        $this->view('user/form', ['item' => $item, 'errors' => [], 'mode' => 'edit', 'roles' => Role::options()], 'Ubah Pengguna');
    }

    public function update($id): void
    {
        require_permission('user.update');
        $this->guardPost();
        $id = $this->intId($id);
        $user = User::find($id) ?? abort(404);
        $data = $this->collect();
        $errors = $this->validate($data, false, $id);
        if ($errors) {
            $data['id'] = $id;
            $this->view('user/form', ['item' => $data, 'errors' => $errors, 'mode' => 'edit', 'roles' => Role::options()], 'Ubah Pengguna');
            return;
        }
        $update = [
            'name' => $data['name'], 'email' => $data['email'],
            'role_id' => $data['role_id'], 'is_active' => $data['is_active'],
        ];
        if ($data['password'] !== '') {
            $update['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        User::update($id, $update);
        if ($id === Auth::id()) {
            Auth::refreshPermissions();
        }
        log_activity('user.update', 'Mengubah pengguna: ' . $data['email']);
        flash('success', 'Pengguna berhasil diperbarui.');
        redirect('user');
    }

    public function delete($id): void
    {
        require_permission('user.delete');
        $this->guardPost();
        $id = $this->intId($id);
        $user = User::find($id) ?? abort(404);
        if ($id === Auth::id()) {
            flash('error', 'Anda tidak dapat menghapus akun sendiri.');
            redirect('user');
        }
        $admins = (int) Database::scalar("SELECT COUNT(*) FROM users WHERE role_id = (SELECT id FROM roles WHERE slug='admin')");
        if ((int) $user['role_id'] === (int) Database::scalar("SELECT id FROM roles WHERE slug='admin'") && $admins <= 1) {
            flash('error', 'Tidak dapat menghapus satu-satunya admin.');
            redirect('user');
        }
        User::delete($id);
        log_activity('user.delete', 'Menghapus pengguna: ' . $user['email']);
        flash('success', 'Pengguna berhasil dihapus.');
        redirect('user');
    }

    private function collect(): array
    {
        return [
            'name'      => trim((string) post('name')),
            'email'     => trim((string) post('email')),
            'password'  => (string) post('password'),
            'role_id'   => (int) post('role_id'),
            'is_active' => post('is_active') ? 1 : 0,
        ];
    }

    private function validate(array $data, bool $isNew, ?int $exceptId = null): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Nama wajib diisi.';
        }
        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email tidak valid.';
        } elseif (User::emailExists($data['email'], $exceptId)) {
            $errors['email'] = 'Email sudah terdaftar.';
        }
        if (!$data['role_id'] || !Role::find($data['role_id'])) {
            $errors['role_id'] = 'Role wajib dipilih.';
        }
        if ($isNew && strlen($data['password']) < 6) {
            $errors['password'] = 'Kata sandi minimal 6 karakter.';
        } elseif (!$isNew && $data['password'] !== '' && strlen($data['password']) < 6) {
            $errors['password'] = 'Kata sandi minimal 6 karakter.';
        }
        return $errors;
    }
}
