<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class ShopController extends Controller
{
    public function index(): void
    {
        $categories = Category::active();
        $activeSlug = isset($_GET['kategori']) ? (string) $_GET['kategori'] : 'all';
        $items = array_values(array_filter(MenuItem::available(), fn($m) => has_bg_image($m['image'])));
        View::renderPublic('public/menu', compact('categories', 'items', 'activeSlug'), 'Menu & Pesan');
    }

    public function item($id): void
    {
        $id = $this->intId($id);
        $item = MenuItem::findWithCategory($id);
        if (!$item || !$item['is_available']) {
            abort(404);
        }
        $related = array_values(array_filter(Database::all(
            "SELECT m.*, c.name AS category_name FROM menu_items m
             JOIN categories c ON c.id = m.category_id
             WHERE m.category_id = ? AND m.id <> ? AND m.is_available = 1
             ORDER BY RAND()",
            [$item['category_id'], $id]
        ), fn($m) => has_bg_image($m['image'])));
        $related = array_slice($related, 0, 3);
        View::renderPublic('public/item', compact('item', 'related'), $item['name']);
    }
}
