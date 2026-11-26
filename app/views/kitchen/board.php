<?php if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$cols = [
  'queued'  => ['Antre', 'amber', 'cooking', 'Mulai Masak', 'flame-outline'],
  'cooking' => ['Dimasak', 'blue', 'ready', 'Tandai Siap', 'checkmark-done-outline'],
  'ready'   => ['Siap Disajikan', 'green', 'served', 'Sudah Disajikan', 'restaurant-outline'],
];
$typeLabel = ['dine_in' => 'Dine-in', 'takeaway' => 'Takeaway'];
?>
<p style="color:var(--clr-muted);margin:-6px 0 18px;">Papan dapur pesanan berjalan. Geser status tiap pesanan dari <b>Antre → Dimasak → Siap → Disajikan</b>.</p>
<div class="kboard">
  <?php foreach ($cols as $key => $c): [$title,$color,$next,$btn,$icon] = $c; ?>
    <div class="kcol">
      <div class="kcol-head <?= $color ?>"><span><?= e($title) ?></span><span class="kcount"><?= count($board[$key]) ?></span></div>
      <div class="kcol-body">
        <?php if ($board[$key]): foreach ($board[$key] as $o): ?>
          <div class="kcard">
            <div class="kcard-top">
              <b><?= e($o['order_number']) ?></b>
              <span class="badge <?= ($o['source']??'')==='customer'?'blue':'gray' ?>" style="font-size:11px;"><?= ($o['source']??'')==='customer'?'Online':'Staf' ?></span>
            </div>
            <div class="kcard-meta"><?= e($typeLabel[$o['order_type']] ?? '-') ?> · Meja <?= e($o['table_number'] ?? '-') ?><?= !empty($o['customer_name']) ? ' · '.e($o['customer_name']) : '' ?></div>
            <ul class="kitems">
              <?php foreach ($o['items'] as $it): ?><li><span class="q"><?= (int)$it['qty'] ?>×</span> <?= e($it['item_name']) ?></li><?php endforeach; ?>
              <?php if (!$o['items']): ?><li style="color:var(--clr-muted)">Belum ada item</li><?php endif; ?>
            </ul>
            <?php if (can('kitchen.update')): ?>
            <div class="kcard-actions">
              <?php if ($key !== 'queued'): ?>
                <?php $prev = $key === 'cooking' ? 'queued' : 'cooking'; ?>
                <form method="post" action="<?= url('kitchen/setStatus/' . $o['id']) ?>"><?= Csrf::field() ?><input type="hidden" name="kitchen_status" value="<?= $prev ?>"><button class="btn secondary sm" type="submit" title="Kembali"><ion-icon name="arrow-back-outline"></ion-icon></button></form>
              <?php endif; ?>
              <form method="post" action="<?= url('kitchen/setStatus/' . $o['id']) ?>" style="flex:1;"><?= Csrf::field() ?><input type="hidden" name="kitchen_status" value="<?= $next ?>"><button class="btn sm block" type="submit"><ion-icon name="<?= $icon ?>"></ion-icon> <?= e($btn) ?></button></form>
            </div>
            <?php endif; ?>
          </div>
        <?php endforeach; else: ?>
          <div class="kempty">Kosong</div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<script>setTimeout(function(){location.reload();}, 30000);</script>
