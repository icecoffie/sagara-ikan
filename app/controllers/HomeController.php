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
            $withBg = array_filter(MenuItem::publicMenu(), fn($m) => has_bg_image($m['image']));
            $menu = $this->featured(array_values($withBg), 6);
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

    private function featured(array $all, int $limit): array
    {
        $byCat = [];
        foreach ($all as $m) {
            $byCat[$m['category_slug']][] = $m;
        }
        $featured = [];
        $round = 0;
        while (count($featured) < $limit) {
            $added = false;
            foreach ($byCat as $list) {
                if (isset($list[$round])) {
                    $featured[] = $list[$round];
                    $added = true;
                    if (count($featured) >= $limit) {
                        break;
                    }
                }
            }
            if (!$added) {
                break;
            }
            $round++;
        }
        return $featured;
    }
}
