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
            $menu = MenuItem::publicMenu();
        } catch (Throwable $e) {

        }
        $this->raw('public/landing', [
            'categories' => $categories,
            'menu'       => $menu,
            'appName'    => Config::get('app_name', 'Sagara Resto'),
        ]);
    }
}
