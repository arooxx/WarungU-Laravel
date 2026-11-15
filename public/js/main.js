// assets/js/main.js

// ===== TOAST =====
function showToast(pesan, tipe = 'success') {
    var container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    var icons = { success: '✓', danger: '✕', warning: '⚠' };
    var div = document.createElement('div');
    div.className = 'toast-item ' + tipe;
    div.innerHTML = '<div class="toast-icon">' + (icons[tipe] || '•') + '</div>' + pesan;
    container.appendChild(div);

    setTimeout(function () {
        div.style.transition = 'all .3s ease';
        div.style.opacity = '0';
        div.style.transform = 'translateX(40px)';
        setTimeout(function () { div.remove(); }, 320);
    }, 3000);
}

// ===== AUTO CLOSE ALERT =====
setTimeout(function () {
    document.querySelectorAll('.alert-auto').forEach(function (el) {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(function () { el.remove(); }, 420);
    });
}, 3500);

// ===== KONFIRMASI HAPUS =====
function konfirmasiHapus(url, nama) {
    if (confirm('Yakin ingin menghapus "' + nama + '"?\nData yang dihapus tidak bisa dikembalikan.')) {
        window.location.href = url;
    }
}
