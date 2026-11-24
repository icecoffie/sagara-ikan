<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class ReportController extends Controller
{
    public function index(): void
    {
        require_permission('report.view');
        [$from, $to] = $this->range();
        $data = $this->report($from, $to);
        $this->view('report/index', array_merge($data, compact('from', 'to')), 'Laporan Penjualan');
    }

    public function export(): void
    {
        require_permission('report.export');
        [$from, $to] = $this->range();
        $rows = $this->paidOrders($from, $to);

        $filename = 'laporan-penjualan_' . $from . '_sd_' . $to . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fprintf($out, "\xEF\xBB\xBF");
        fputcsv($out, ['No. Pesanan', 'Tanggal Bayar', 'Meja', 'Kasir', 'Jumlah Item', 'Total (Rp)']);
        $grand = 0;
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['order_number'],
                date('Y-m-d H:i', strtotime($r['paid_at'])),
                $r['table_number'] ?? '-',
                $r['cashier'] ?? '-',
                $r['item_count'],
                $r['total'],
            ]);
            $grand += (float) $r['total'];
        }
        fputcsv($out, []);
        fputcsv($out, ['', '', '', '', 'TOTAL', $grand]);
        fclose($out);

        log_activity('report.export', "Export CSV laporan $from s/d $to");
        exit;
    }

    public function pdf(): void
    {
        require_permission('report.export');
        $autoload = ROOT_PATH . '/vendor/autoload.php';
        if (is_file($autoload)) {
            require_once $autoload;
        }
        if (!class_exists('Dompdf\\Dompdf')) {
            flash('info', 'Export PDF memerlukan dompdf. Jalankan: composer require dompdf/dompdf. Sementara gunakan CSV atau tombol cetak.');
            redirect('report?' . http_build_query($_GET));
        }
        [$from, $to] = $this->range();
        $data = $this->report($from, $to);
        $html = View::capture('report/pdf', array_merge($data, compact('from', 'to')));
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        log_activity('report.export', "Export PDF laporan $from s/d $to");
        $dompdf->stream('laporan-penjualan.pdf', ['Attachment' => true]);
        exit;
    }

    private function range(): array
    {
        $from = (string) input('from', date('Y-m-d', strtotime('-6 days')));
        $to   = (string) input('to', date('Y-m-d'));
        if (strtotime($from) === false) { $from = date('Y-m-d', strtotime('-6 days')); }
        if (strtotime($to) === false) { $to = date('Y-m-d'); }
        if (strtotime($from) > strtotime($to)) { [$from, $to] = [$to, $from]; }
        return [$from, $to];
    }

    private function paidOrders(string $from, string $to): array
    {
        return Database::all(
            "SELECT o.*, t.table_number, u.name AS cashier,
                    (SELECT COALESCE(SUM(qty),0) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
               FROM orders o
               LEFT JOIN dining_tables t ON t.id = o.table_id
               LEFT JOIN users u ON u.id = o.user_id
              WHERE o.status = 'paid' AND DATE(o.paid_at) BETWEEN ? AND ?
              ORDER BY o.paid_at ASC",
            [$from, $to]
        );
    }

    private function report(string $from, string $to): array
    {
        $orders = $this->paidOrders($from, $to);
        $summary = [
            'count'   => count($orders),
            'revenue' => array_sum(array_map(fn($o) => (float) $o['total'], $orders)),
            'items'   => array_sum(array_map(fn($o) => (int) $o['item_count'], $orders)),
        ];
        $summary['avg'] = $summary['count'] ? $summary['revenue'] / $summary['count'] : 0;

        $daily = Database::all(
            "SELECT DATE(paid_at) d, COUNT(*) c, SUM(total) t
               FROM orders WHERE status='paid' AND DATE(paid_at) BETWEEN ? AND ?
              GROUP BY DATE(paid_at) ORDER BY d ASC",
            [$from, $to]
        );
        $topItems = Database::all(
            "SELECT oi.item_name, SUM(oi.qty) qty, SUM(oi.subtotal) revenue
               FROM order_items oi JOIN orders o ON o.id = oi.order_id
              WHERE o.status='paid' AND DATE(o.paid_at) BETWEEN ? AND ?
              GROUP BY oi.item_name ORDER BY qty DESC LIMIT 10",
            [$from, $to]
        );
        return compact('orders', 'summary', 'daily', 'topItems');
    }
}
