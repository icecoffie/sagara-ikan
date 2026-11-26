(function () {
  function toast(msg, type) {
    var t = document.createElement('div');
    t.className = 'toast ' + (type || 'success');
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function () { t.classList.add('hide'); }, 2600);
    setTimeout(function () { t.remove(); }, 3100);
  }
  function setBadge(n) {
    document.querySelectorAll('.cart-link').forEach(function (link) {
      var b = link.querySelector('.cart-badge');
      if (!b) { b = document.createElement('span'); b.className = 'cart-badge'; link.appendChild(b); }
      b.textContent = n;
      if (b.animate) b.animate([{ transform: 'scale(1.5)' }, { transform: 'scale(1)' }], { duration: 300 });
    });
  }
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (!f.classList || !f.classList.contains('order-form')) return;
    if (f.querySelector('input[name="back"]')) return; // item detail submits normally
    e.preventDefault();
    var btn = f.querySelector('button'); var old = btn.innerHTML;
    btn.disabled = true;
    var fd = new FormData(f);
    fetch(f.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d && d.ok) {
          setBadge(d.count);
          toast((d.name || 'Item') + ' ditambahkan ke keranjang');
          btn.innerHTML = '<ion-icon name="checkmark-outline"></ion-icon>';
        } else { toast('Gagal menambah item', 'error'); }
        setTimeout(function () { btn.innerHTML = old; btn.disabled = false; }, 1100);
      })
      .catch(function () { f.submit(); });
  });
})();
