<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$orderBadge = ['open' => 'amber', 'paid' => 'green', 'cancelled' => 'red'];
$resBadge = ['pending' => 'amber', 'confirmed' => 'green', 'cancelled' => 'red'];
$u = Auth::user();
?>
<p style="color:var(--clr-muted);margin-bottom:20px;margin-top:-6px;">
  Halo <b style="color:var(--clr-secondary)"><?= e($u['name']) ?></b>,
  ringkasan operasional <?= e(Config::get('app_name')) ?> hari ini.
</p>

<div class="cards" style="margin-bottom:24px;">
  <?php if (can('menu.view') || Auth::can('menu.view')): ?>
  <div class="card stat-card">
    <div class="icon"><ion-icon name="fast-food-outline"></ion-icon></div>
    <div><div class="label">Total Menu</div><div class="value"><?= $stats['menu_total'] ?></div>
      <div class="label"><?= $stats['menu_active'] ?> tersedia</div></div>
  </div>
  <?php endif; ?>

  <?php if (can('table.view')): ?>
  <div class="card stat-card">
    <div class="icon navy"><ion-icon name="restaurant-outline"></ion-icon></div>
    <div><div class="label">Meja Terisi</div>
      <div class="value"><?= $stats['tables_busy'] ?><span style="font-size:16px;color:var(--clr-muted)">/<?= $stats['tables_total'] ?></span></div>
      <div class="label">meja sedang digunakan</div></div>
  </div>
  <?php endif; ?>

  <?php if (can('order.view')): ?>
  <div class="card stat-card">
    <div class="icon amber"><ion-icon name="receipt-outline"></ion-icon></div>
    <div><div class="label">Pesanan Hari Ini</div><div class="value"><?= $stats['orders_today'] ?></div>
      <div class="label">transaksi dibuat</div></div>
  </div>
  <?php endif; ?>

  <?php if (can('report.view') || can('order.pay')): ?>
  <div class="card stat-card">
    <div class="icon green"><ion-icon name="cash-outline"></ion-icon></div>
    <div><div class="label">Omzet Hari Ini</div><div class="value" style="font-size:22px;"><?= rupiah($stats['revenue_today']) ?></div>
      <div class="label">dari pesanan lunas</div></div>
  </div>
  <?php endif; ?>
</div>

<?php if (can('report.view')): ?>
<div class="panel">
  <div class="panel-head"><h2>Omzet 7 Hari Terakhir</h2>
    <?php if (can('report.view')): ?><a href="<?= url('report') ?>" class="btn secondary sm"><ion-icon name="bar-chart-outline"></ion-icon> Laporan</a><?php endif; ?>
  </div>
  <div class="panel-body"><canvas id="revChart" height="90"></canvas></div>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:<?= (can('order.view') && can('reservation.view')) ? '1.3fr 1fr' : '1fr' ?>;gap:20px;align-items:start;">
  <?php if (can('order.view')): ?>
  <div class="panel">
    <div class="panel-head"><h2>Pesanan Terbaru</h2><a href="<?= url('order') ?>" class="btn secondary sm">Semua</a></div>
    <div class="panel-body" style="padding:0;">
      <div class="table-wrap"><table class="data">
        <thead><tr><th>No. Pesanan</th><th>Meja</th><th>Status</th><th>Total</th><th>Waktu</th></tr></thead>
        <tbody>
          <?php if ($recentOrders): foreach ($recentOrders as $o): ?>
            <tr>
              <td><a href="<?= url('order/show/' . $o['id']) ?>"><?= e($o['order_number']) ?></a></td>
              <td><?= e($o['table_number'] ?? '-') ?></td>
              <td><span class="badge <?= $orderBadge[$o['status']] ?? 'gray' ?>"><?= e(ucfirst($o['status'])) ?></span></td>
              <td><?= rupiah($o['total']) ?></td>
              <td><?= e(date('d M H:i', strtotime($o['created_at']))) ?></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="empty">Belum ada pesanan.</td></tr>
          <?php endif; ?>
        </tbody>
      </table></div>
    </div>
  </div>
  <?php endif; ?>

  <?php if (can('reservation.view')): ?>
  <div class="panel">
    <div class="panel-head"><h2>Reservasi Mendatang</h2><a href="<?= url('reservation') ?>" class="btn secondary sm">Semua</a></div>
    <div class="panel-body" style="padding:0;">
      <div class="table-wrap"><table class="data">
        <thead><tr><th>Tamu</th><th>Meja</th><th>Waktu</th><th>Status</th></tr></thead>
        <tbody>
          <?php if ($upcoming): foreach ($upcoming as $r): ?>
            <tr>
              <td><?= e($r['customer_name']) ?><div class="label" style="font-size:12px;color:var(--clr-muted)"><?= (int)$r['party_size'] ?> orang</div></td>
              <td><?= e($r['table_number'] ?? '-') ?></td>
              <td><?= e(date('d M H:i', strtotime($r['reservation_time']))) ?></td>
              <td><span class="badge <?= $resBadge[$r['status']] ?? 'gray' ?>"><?= e(ucfirst($r['status'])) ?></span></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="4" class="empty">Tidak ada reservasi mendatang.</td></tr>
          <?php endif; ?>
        </tbody>
      </table></div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (can('report.view')): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  var ctx = document.getElementById('revChart');
  if (!ctx) return;
  var data = <?= json_encode($revenue) ?>;
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: data.labels,
      datasets: [{
        label: 'Omzet (Rp)',
        data: data.values,
        borderColor: '#2aa7ff',
        backgroundColor: 'rgba(42,167,255,0.15)',
        fill: true, tension: 0.35, pointRadius: 4, pointBackgroundColor: '#1b86d6', borderWidth: 3
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: true,
      plugins: { legend: { display: false },
        tooltip: { callbacks: { label: function(c){ return 'Rp' + c.parsed.y.toLocaleString('id-ID'); } } } },
      scales: { y: { beginAtZero: true, ticks: { callback: function(v){ return 'Rp' + (v/1000) + 'k'; } } } }
    }
  });
})();
</script>
<?php endif; ?>
