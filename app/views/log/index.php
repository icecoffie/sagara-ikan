<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$icon = function($a){
  if (str_contains($a,'login')||str_contains($a,'logout')) return 'log-in-outline';
  if (str_contains($a,'create')) return 'add-circle-outline';
  if (str_contains($a,'update')||str_contains($a,'pay')) return 'create-outline';
  if (str_contains($a,'delete')) return 'trash-outline';
  if (str_contains($a,'export')) return 'download-outline';
  return 'ellipse-outline';
};
?>
<div class="panel">
  <div class="panel-head">
    <h2>Log Aktivitas</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('log') ?>">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari aksi/detail/user...">
        <button class="btn secondary sm" type="submit"><ion-icon name="search-outline"></ion-icon></button>
      </form>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Detail</th><th>IP</th></tr></thead>
      <tbody>
        <?php if ($items): foreach ($items as $l): ?>
          <tr>
            <td><?= e(date('d M Y H:i:s', strtotime($l['created_at']))) ?></td>
            <td><?= e($l['user_name'] ?? 'Sistem') ?><?php if (!empty($l['email'])): ?><div style="font-size:12px;color:var(--clr-muted)"><?= e($l['email']) ?></div><?php endif; ?></td>
            <td><span class="badge gray"><ion-icon name="<?= $icon($l['action']) ?>" style="vertical-align:-2px"></ion-icon> <?= e($l['action']) ?></span></td>
            <td style="font-size:13px;"><?= e($l['detail'] ?? '-') ?></td>
            <td style="font-size:12px;color:var(--clr-muted)"><?= e($l['ip_address'] ?? '-') ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="empty">Belum ada aktivitas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?= pagination_links($p, 'log') ?>
