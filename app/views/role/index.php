<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="panel">
  <div class="panel-head">
    <h2>Role &amp; Izin</h2>
    <?php if (can('role.update')): ?><a href="<?= url('role/create') ?>" class="btn sm"><ion-icon name="add-outline"></ion-icon> Tambah Role</a><?php endif; ?>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>Role</th><th>Deskripsi</th><th>Pengguna</th><th>Jumlah Izin</th><th class="actions">Aksi</th></tr></thead>
      <tbody>
        <?php foreach ($roles as $r): ?>
          <tr>
            <td><b><?= e($r['name']) ?></b> <code style="font-size:11px;color:var(--clr-muted)"><?= e($r['slug']) ?></code></td>
            <td style="color:var(--clr-muted);font-size:13px;"><?= e($r['description']) ?></td>
            <td><?= (int)$r['user_count'] ?></td>
            <td><span class="badge blue"><?= $r['slug']==='admin' ? 'Semua' : (int)$r['perm_count'] ?></span></td>
            <td class="actions">
              <a class="btn secondary sm" href="<?= url('role/edit/' . $r['id']) ?>"><ion-icon name="options-outline"></ion-icon> Kelola Izin</a>
              <?php if (can('role.update') && $r['slug'] !== 'admin' && (int)$r['user_count'] === 0): ?>
                <form method="post" action="<?= url('role/delete/' . $r['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus role ini?')"><?= Csrf::field() ?><button class="btn danger sm"><ion-icon name="trash-outline"></ion-icon></button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
  </div>
</div>
<p style="color:var(--clr-muted);font-size:13px;">Centang izin per role lalu simpan untuk mengatur hak akses. Role <b>admin</b> selalu memiliki seluruh izin.</p>
