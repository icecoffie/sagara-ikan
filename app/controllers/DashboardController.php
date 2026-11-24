<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class DashboardController extends Controller
{
    public function index(): void
    {
        require_login();

        $stats = [
            'menu_total'   => (int) Database::scalar('SELECT COUNT(*) FROM menu_items'),
            'menu_active'  => (int) Database::scalar('SELECT COUNT(*) FROM menu_items WHERE is_available = 1'),
            'tables_total' => (int) Database::scalar('SELECT COUNT(*) FROM dining_tables'),
            'tables_busy'  => (int) Database::scalar("SELECT COUNT(*) FROM dining_tables WHERE status = 'occupied'"),
            'orders_today' => (int) Database::scalar('SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()'),
            'revenue_today'=> (float) Database::scalar("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='paid' AND DATE(paid_at) = CURDATE()"),
            'res_pending'  => (int) Database::scalar("SELECT COUNT(*) FROM reservations WHERE status = 'pending'"),
        ];

        $revenue = $this->revenueSeries();

        $recentOrders = can('order.view') ? Database::all(
            "SELECT o.*, t.table_number
               FROM orders o
               LEFT JOIN dining_tables t ON t.id = o.table_id
              ORDER BY o.created_at DESC LIMIT 6"
        ) : [];

        $upcoming = can('reservation.view') ? Database::all(
            "SELECT r.*, t.table_number
               FROM reservations r
               LEFT JOIN dining_tables t ON t.id = r.table_id
              WHERE r.status <> 'cancelled' AND r.reservation_time >= NOW()
              ORDER BY r.reservation_time ASC LIMIT 5"
        ) : [];

        $this->view('dashboard/index', [
            'stats'        => $stats,
            'revenue'      => $revenue,
            'recentOrders' => $recentOrders,
            'upcoming'     => $upcoming,
        ], 'Dashboard');
    }

    public function revenueData(): void
    {
        require_login();
        $this->json($this->revenueSeries());
    }

    private function revenueSeries(): array
    {
        $rows = Database::all(
            "SELECT DATE(paid_at) d, SUM(total) t
               FROM orders
              WHERE status = 'paid' AND paid_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
              GROUP BY DATE(paid_at)"
        );
        $map = [];
        foreach ($rows as $r) {
            $map[$r['d']] = (float) $r['t'];
        }
        $labels = [];
        $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i day"));
            $labels[] = date('d M', strtotime($day));
            $values[] = $map[$day] ?? 0;
        }
        return ['labels' => $labels, 'values' => $values];
    }
}
