<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<div class="panel">
  <div class="panel-head">
    <h2>Daftar Pengguna</h2>
    <div class="toolbar">
      <form method="get" action="<?= url('user') ?>">
        <input type="text" name="q" value="<?= e($filters['q']) ?>" placeholder="Cari nama/email...">
        <select name="role_id">
          <option value="">Semua Role</option>
          <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>" <?= (string)$filters['role_id']===(string)$r['id']?'selected':'' ?>><?= e($r['name']) ?></option><?php endforeach; ?>
        </select>
        <button class="btn secondary sm" type="submit"><ion-icon name="search-outline"></ion-icon></button>
      </form>
      <?php if (can('user.create')): ?><a href="<?= url('user/create') ?>" class="btn sm"><ion-icon name="person-add-outline"></ion-icon> Tambah</a><?php endif; ?>
    </div>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-wrap"><table class="data">
      <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th class="actions">Aksi</th></tr></thead>
      <tbody>
        <?php if ($items): foreach ($items as $u): ?>
          <tr>
            <td><b><?= e($u['name']) ?></b><?php if ($u['id']==Auth::id()): ?> <span class="badge blue">Anda</span><?php endif; ?></td>
            <td><?= e($u['email']) ?></td>
            <td><span class="badge gray"><?= e($u['role_name']) ?></span></td>
            <td><span class="badge <?= $u['is_active'] ? 'green' : 'red' ?>"><?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
            <td class="actions">
              <?php if (can('user.update')): ?><a class="btn secondary sm" href="<?= url('user/edit/' . $u['id']) ?>"><ion-icon name="create-outline"></ion-icon></a><?php endif; ?>
              <?php if (can('user.delete') && $u['id'] != Auth::id()): ?>
                <form method="post" action="<?= url('user/delete/' . $u['id']) ?>" style="display:inline" onsubmit="return confirm('Hapus pengguna ini?')"><?= Csrf::field() ?><button class="btn danger sm"><ion-icon name="trash-outline"></ion-icon></button></form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="empty">Tidak ada pengguna.</td></tr>
        <?php endif; ?>
      </tbody>
    </table></div>
  </div>
</div>
<?= pagination_links($p, 'user') ?>
