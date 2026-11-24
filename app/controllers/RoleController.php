<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class RoleController extends Controller
{
    public function index(): void
    {
        require_permission('role.view');
        $roles = Role::withCounts();
        $this->view('role/index', compact('roles'), 'Role & Izin');
    }

    public function create(): void
    {
        require_permission('role.update');
        $this->view('role/form', [
            'item' => [], 'errors' => [], 'mode' => 'create',
            'grouped' => Permission::grouped(), 'assigned' => [],
        ], 'Tambah Role');
    }

    public function store(): void
    {
        require_permission('role.update');
        $this->guardPost();
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $this->view('role/form', [
                'item' => $data, 'errors' => $errors, 'mode' => 'create',
                'grouped' => Permission::grouped(), 'assigned' => array_map('intval', (array) post('permissions', [])),
            ], 'Tambah Role');
            return;
        }
        $slug = $this->uniqueSlug($data['name']);
        $id = Role::create(['name' => $data['name'], 'slug' => $slug, 'description' => $data['description']]);
        Role::syncPermissions($id, array_map('intval', (array) post('permissions', [])));
        log_activity('role.update', 'Membuat role: ' . $data['name']);
        flash('success', 'Role berhasil dibuat.');
        redirect('role');
    }

    public function edit($id): void
    {
        require_permission('role.view');
        $item = Role::find($this->intId($id)) ?? abort(404);
        $this->view('role/form', [
            'item' => $item, 'errors' => [], 'mode' => 'edit',
            'grouped' => Permission::grouped(), 'assigned' => Role::permissionIds((int) $item['id']),
        ], 'Ubah Role: ' . $item['name']);
    }

    public function update($id): void
    {
        require_permission('role.update');
        $this->guardPost();
        $id = $this->intId($id);
        $role = Role::find($id) ?? abort(404);
        $data = $this->collect();
        $errors = $this->validate($data, $id);
        if ($errors) {
            $data['id'] = $id; $data['slug'] = $role['slug'];
            $this->view('role/form', [
                'item' => $data, 'errors' => $errors, 'mode' => 'edit',
                'grouped' => Permission::grouped(), 'assigned' => array_map('intval', (array) post('permissions', [])),
            ], 'Ubah Role');
            return;
        }
        Role::update($id, ['name' => $data['name'], 'description' => $data['description']]);

        $perms = array_map('intval', (array) post('permissions', []));
        if ($role['slug'] === 'admin') {
            $perms = array_column(Permission::all('id ASC'), 'id');
            $perms = array_map('intval', $perms);
        }
        Role::syncPermissions($id, $perms);
        Auth::refreshPermissions();
        log_activity('role.update', 'Memperbarui izin role: ' . $data['name']);
        flash('success', 'Role & izin berhasil diperbarui.');
        redirect('role');
    }

    public function delete($id): void
    {
        require_permission('role.update');
        $this->guardPost();
        $id = $this->intId($id);
        $role = Role::find($id) ?? abort(404);
        if ($role['slug'] === 'admin') {
            flash('error', 'Role admin tidak dapat dihapus.');
            redirect('role');
        }
        $inUse = (int) Database::scalar('SELECT COUNT(*) FROM users WHERE role_id = ?', [$id]);
        if ($inUse > 0) {
            flash('error', "Role dipakai $inUse pengguna, tidak dapat dihapus.");
            redirect('role');
        }
        Role::delete($id);
        log_activity('role.update', 'Menghapus role: ' . $role['name']);
        flash('success', 'Role berhasil dihapus.');
        redirect('role');
    }

    private function collect(): array
    {
        return [
            'name'        => trim((string) post('name')),
            'description' => trim((string) post('description')),
        ];
    }

    private function validate(array $data, ?int $exceptId = null): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Nama role wajib diisi.';
        }
        return $errors;
    }

    private function uniqueSlug(string $name): string
    {
        $base = slugify($name); $slug = $base; $i = 2;
        while (Database::scalar('SELECT COUNT(*) FROM roles WHERE slug = ?', [$slug]) > 0) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
