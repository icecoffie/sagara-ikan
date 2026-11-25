<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class MenuController extends Controller
{
    private array $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

    public function index(): void
    {
        require_permission('menu.view');
        $filters = [
            'q'           => trim((string) input('q', '')),
            'category_id' => input('category_id', ''),
            'available'   => input('available', ''),
        ];
        $total = MenuItem::searchCount($filters);
        $p = paginate($total, 10);
        $items = MenuItem::search($filters, $p['perPage'], $p['offset']);
        $categories = Category::options();
        $this->view('menu/index', compact('items', 'p', 'filters', 'categories'), 'Menu');
    }

    public function create(): void
    {
        require_permission('menu.create');
        $this->view('menu/form', ['item' => [], 'errors' => [], 'mode' => 'create', 'categories' => Category::options()], 'Tambah Menu');
    }

    public function store(): void
    {
        require_permission('menu.create');
        $this->guardPost();
        $data = $this->collect();
        $errors = $this->validate($data, true);
        $image = $this->handleUpload($errors);
        if ($errors) {
            $this->view('menu/form', ['item' => $data, 'errors' => $errors, 'mode' => 'create', 'categories' => Category::options()], 'Tambah Menu');
            return;
        }
        $data['image'] = $image;
        MenuItem::create($data);
        log_activity('menu.create', 'Menambah menu: ' . $data['name']);
        flash('success', 'Menu berhasil ditambahkan.');
        redirect('menus');
    }

    public function edit($id): void
    {
        require_permission('menu.update');
        $item = MenuItem::find($this->intId($id)) ?? abort(404);
        $this->view('menu/form', ['item' => $item, 'errors' => [], 'mode' => 'edit', 'categories' => Category::options()], 'Ubah Menu');
    }

    public function update($id): void
    {
        require_permission('menu.update');
        $this->guardPost();
        $id = $this->intId($id);
        $item = MenuItem::find($id) ?? abort(404);
        $data = $this->collect();
        $errors = $this->validate($data, false);
        $newImage = $this->handleUpload($errors);
        if ($errors) {
            $data['id'] = $id;
            $data['image'] = $item['image'];
            $this->view('menu/form', ['item' => $data, 'errors' => $errors, 'mode' => 'edit', 'categories' => Category::options()], 'Ubah Menu');
            return;
        }
        if ($newImage !== null) {
            $this->deleteImage($item['image']);
            $data['image'] = $newImage;
        }
        MenuItem::update($id, $data);
        log_activity('menu.update', 'Mengubah menu: ' . $data['name']);
        flash('success', 'Menu berhasil diperbarui.');
        redirect('menus');
    }

    public function delete($id): void
    {
        require_permission('menu.delete');
        $this->guardPost();
        $id = $this->intId($id);
        $item = MenuItem::find($id) ?? abort(404);
        $used = (int) Database::scalar('SELECT COUNT(*) FROM order_items WHERE menu_item_id = ?', [$id]);
        if ($used > 0) {
            Database::run('UPDATE menu_items SET is_available = 0 WHERE id = ?', [$id]);
            log_activity('menu.update', 'Menonaktifkan menu (dipakai pesanan): ' . $item['name']);
            flash('info', 'Menu dipakai pada pesanan, jadi dinonaktifkan alih-alih dihapus.');
            redirect('menus');
        }
        $this->deleteImage($item['image']);
        MenuItem::delete($id);
        log_activity('menu.delete', 'Menghapus menu: ' . $item['name']);
        flash('success', 'Menu berhasil dihapus.');
        redirect('menus');
    }

    private function collect(): array
    {
        return [
            'name'         => trim((string) post('name')),
            'category_id'  => (int) post('category_id'),
            'price'        => (float) str_replace(['.', ','], ['', '.'], (string) post('price')),
            'description'  => trim((string) post('description')),
            'is_available' => post('is_available') ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Nama menu wajib diisi.';
        }
        if (!$data['category_id'] || !Category::find($data['category_id'])) {
            $errors['category_id'] = 'Kategori wajib dipilih.';
        }
        if ($data['price'] < 0) {
            $errors['price'] = 'Harga tidak boleh negatif.';
        }
        return $errors;
    }

    private function handleUpload(array &$errors): ?string
    {
        if (empty($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        $f = $_FILES['image'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $errors['image'] = 'Gagal mengunggah gambar.';
            return null;
        }
        if ($f['size'] > 3 * 1024 * 1024) {
            $errors['image'] = 'Ukuran gambar maksimal 3MB.';
            return null;
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        if (!isset($this->allowed[$mime])) {
            $errors['image'] = 'Format gambar harus JPG, PNG, WEBP, atau GIF.';
            return null;
        }
        $dir = ROOT_PATH . '/public_html/' . Config::get('upload_dir', 'assets/uploads');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $name = 'menu_' . date('YmdHis') . '_' . bin2hex(random_bytes(3)) . '.' . $this->allowed[$mime];
        if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) {
            $errors['image'] = 'Gambar tidak dapat disimpan.';
            return null;
        }
        return $name;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }
        $path = ROOT_PATH . '/public_html/' . Config::get('upload_dir', 'assets/uploads') . '/' . $image;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
