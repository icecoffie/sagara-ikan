<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class CategoryController extends Controller
{
    public function index(): void
    {
        require_permission('category.view');
        $q = trim((string) input('q', ''));
        $total = Category::searchCount($q);
        $p = paginate($total, 10);
        $items = Category::search($q, $p['perPage'], $p['offset']);
        $this->view('category/index', compact('items', 'p', 'q'), 'Kategori');
    }

    public function create(): void
    {
        require_permission('category.create');
        $this->view('category/form', ['item' => [], 'errors' => [], 'mode' => 'create'], 'Tambah Kategori');
    }

    public function store(): void
    {
        require_permission('category.create');
        $this->guardPost();
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $this->view('category/form', ['item' => $data, 'errors' => $errors, 'mode' => 'create'], 'Tambah Kategori');
            return;
        }
        $data['slug'] = $this->uniqueSlug($data['name']);
        $id = Category::create($data);
        log_activity('category.create', 'Menambah kategori: ' . $data['name']);
        flash('success', 'Kategori berhasil ditambahkan.');
        redirect('category');
    }

    public function edit($id): void
    {
        require_permission('category.update');
        $item = Category::find($this->intId($id)) ?? abort(404);
        $this->view('category/form', ['item' => $item, 'errors' => [], 'mode' => 'edit'], 'Ubah Kategori');
    }

    public function update($id): void
    {
        require_permission('category.update');
        $this->guardPost();
        $id = $this->intId($id);
        $item = Category::find($id) ?? abort(404);
        $data = $this->collect();
        $errors = $this->validate($data);
        if ($errors) {
            $data['id'] = $id;
            $this->view('category/form', ['item' => $data, 'errors' => $errors, 'mode' => 'edit'], 'Ubah Kategori');
            return;
        }
        $data['slug'] = $this->uniqueSlug($data['name'], $id);
        Category::update($id, $data);
        log_activity('category.update', 'Mengubah kategori: ' . $data['name']);
        flash('success', 'Kategori berhasil diperbarui.');
        redirect('category');
    }

    public function delete($id): void
    {
        require_permission('category.delete');
        $this->guardPost();
        $id = $this->intId($id);
        $item = Category::find($id) ?? abort(404);
        $used = (int) Database::scalar('SELECT COUNT(*) FROM menu_items WHERE category_id = ?', [$id]);
        if ($used > 0) {
            flash('error', "Kategori tidak bisa dihapus karena masih dipakai $used menu.");
            redirect('category');
        }
        Category::delete($id);
        log_activity('category.delete', 'Menghapus kategori: ' . $item['name']);
        flash('success', 'Kategori berhasil dihapus.');
        redirect('category');
    }

    private function collect(): array
    {
        return [
            'name'        => trim((string) post('name')),
            'description' => trim((string) post('description')),
            'is_active'   => post('is_active') ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Nama kategori wajib diisi.';
        } elseif (mb_strlen($data['name']) > 100) {
            $errors['name'] = 'Nama maksimal 100 karakter.';
        }
        return $errors;
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = slugify($name);
        $slug = $base;
        $i = 2;
        while (Category::slugExists($slug, $exceptId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
