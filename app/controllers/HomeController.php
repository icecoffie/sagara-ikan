<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class HomeController extends Controller
{
    public function index(): void
    {
        $categories = [];
        $menu = [];
        try {
            $categories = Category::active();
            $menu = array_values(array_filter(MenuItem::publicMenu(), fn($m) => has_bg_image($m['image'])));
        } catch (Throwable $e) {
        }
        $this->raw('public/landing', [
            'categories' => $categories,
            'menu'       => $menu,
            'appName'    => Config::get('app_name', 'Sagara Resto'),
            'flashes'    => flash(),
            'cartCount'  => cart_count(),
        ]);
    }
}
