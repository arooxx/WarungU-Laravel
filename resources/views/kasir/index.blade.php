<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir — WarungU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div id="toast-container"></div>

{{-- Navbar kasir --}}
<div class="kasir-navbar">
    <div class="nav-brand">
        <div class="logo-circle"><i class="fas fa-store"></i></div>
        <span class="brand-name">WarungU</span>
        @if($infoWarung)
            <span class="warung-name">— {{ $infoWarung->nama_warung }}</span>
        @endif
    </div>
    <div class="d-flex gap-2 align-items-center">
        @if(auth()->user()->isPemilik() && $daftarWarungList->count() > 1)
            <div class="d-flex align-items-center gap-1">
                <i class="fas fa-store" style="color:#7a809a;font-size:.8rem;"></i>
                <select id="pilihWarung" class="form-select form-select-sm"
                        style="width:auto;border-radius:8px;font-size:.8rem;border-color:#e4e7f0;min-width:160px;"
                        onchange="gantiWarung(this.value)">
                    @foreach($daftarWarungList as $w)
                        <option value="{{ $w->id_warung }}" {{ $w->id_warung == $idWarung ? 'selected' : '' }}>
                            {{ $w->nama_warung }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        <span class="text-muted d-none d-md-inline" style="font-size:.8rem;">
            <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->nama }}
        </span>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.8rem;">
            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
        </a>
        <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger" style="border-radius:8px;font-size:.8rem;">Logout</a>
    </div>
</div>

<div class="kasir-wrap">

    {{-- Kiri: Produk --}}
    <div class="kasir-products">
        <div class="kasir-search-area">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" style="background:#f8f9fc;"><i class="fas fa-search" style="color:#7a809a;font-size:.8rem;"></i></span>
                        <input type="text" id="searchBarang" class="form-control"
                               placeholder="Cari nama barang..." style="font-size:.85rem;">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filterKategori" class="form-select form-select-sm" style="font-size:.83rem;">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" style="background:#f8f9fc;"><i class="fas fa-barcode" style="color:#7a809a;font-size:.8rem;"></i></span>
                        <input type="text" id="inputBarcode" class="form-control"
                               placeholder="Ketik barcode + Enter" style="font-size:.83rem;" autofocus>
                    </div>
                </div>
            </div>
        </div>

        <div class="product-grid" id="productGrid">
            @foreach($daftarBarang as $b)
                @php
                    $icons = ['Minuman'=>'fa-coffee','Makanan'=>'fa-utensils','Snack'=>'fa-cookie','Sembako'=>'fa-shopping-basket','Rokok'=>'fa-smoking','Obat'=>'fa-pills'];
                    $ic = $icons[$b->kategori->nama_kategori ?? ''] ?? 'fa-box';
                @endphp
                <div class="product-card"
                     data-id="{{ $b->id_barang }}"
                     data-nama="{{ strtolower($b->nama_barang) }}"
                     data-barcode="{{ $b->barcode }}"
                     data-kategori="{{ $b->id_kategori }}"
                     data-bisa-satuan="{{ $b->bisa_satuan }}"
                     onclick="klikProduk(this)">

                    @if($b->bisa_satuan === 'ya')
                        <div class="satuan-badge">Bisa Satuan</div>
                    @endif

                    <div class="prod-icon">
                        <i class="fas {{ $ic }}"></i>
                    </div>
                    <div class="prod-name">{{ $b->nama_barang }}</div>
                    <div class="prod-price">Rp {{ number_format($b->harga_jual,0,',','.') }}</div>
                    @if($b->bisa_satuan === 'ya')
                        <div class="prod-price-sat">Rp {{ number_format($b->harga_satuan,0,',','.') }}/{{ $b->satuan }}</div>
                    @endif
                    <div class="prod-stok">Stok: {{ $b->stok }} {{ $b->satuan }}</div>
                </div>
            @endforeach
        </div>

        @if(!$daftarBarang->count())
            <div class="page-empty mt-4">
                <i class="fas fa-box-open d-block"></i>
                Belum ada barang tersedia
                <div class="mt-2"><a href="{{ route('barang.create') }}" style="color:var(--primary);">Tambah barang →</a></div>
            </div>
        @endif
    </div>

    {{-- Kanan: Keranjang --}}
    <div class="kasir-cart">
        <div class="cart-header">
            <span><i class="fas fa-shopping-cart me-2" style="color:var(--primary);"></i>Keranjang</span>
            <button class="btn btn-sm btn-outline-danger py-0 px-2" style="border-radius:7px;font-size:.75rem;" onclick="resetKeranjang()">
                <i class="fas fa-trash me-1"></i>Kosongkan
            </button>
        </div>

        <div class="cart-items" id="cartItems">
            <div id="emptyMsg" class="text-center py-5" style="color:#c0c5d8;">
                <i class="fas fa-shopping-cart fa-2x mb-3 d-block"></i>
                <span style="font-size:.85rem;">Keranjang masih kosong</span>
            </div>
        </div>

        <div class="cart-footer">
            <div class="cart-total-row">
                <span style="color:#7a809a;">Total Item</span>
                <span id="totalItem" style="font-weight:600;">0</span>
            </div>
            <div class="cart-total-row main">
                <span>Total Bayar</span>
                <span id="totalHarga">Rp 0</span>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="mb-2 mt-2">
                <label style="font-size:.78rem;font-weight:600;color:#7a809a;text-transform:uppercase;letter-spacing:.04em;">Metode Pembayaran</label>
                <div class="d-flex gap-2 mt-1">
                    <button type="button" id="btnCash" onclick="pilihMetode('cash')"
                        class="btn btn-sm flex-fill metode-btn active-metode"
                        style="border-radius:8px;font-size:.82rem;border:2px solid #6C63FF;color:#6C63FF;background:#e8e6ff;">
                        <i class="fas fa-money-bill-wave me-1"></i>Cash
                    </button>
                    <button type="button" id="btnQris" onclick="pilihMetode('qris')"
                        class="btn btn-sm flex-fill metode-btn"
                        style="border-radius:8px;font-size:.82rem;border:2px solid #e4e7f0;color:#7a809a;background:#fff;">
                        <i class="fas fa-qrcode me-1"></i>QRIS
                    </button>
                </div>
                <input type="hidden" id="metodePembayaran" value="cash">
            </div>

            <div class="mb-2 mt-2" id="wrapBayar">
                <label style="font-size:.78rem;font-weight:600;color:#7a809a;text-transform:uppercase;letter-spacing:.04em;">Uang Bayar</label>
                <input type="number" id="inputBayar" class="form-control form-control-sm mt-1"
                       placeholder="0" oninput="hitungKembalian()"
                       style="font-size:.9rem;font-weight:600;border-radius:8px;">
            </div>

            <div class="kembalian-box" id="wrapKembalian">
                <span style="color:#7a809a;font-size:.82rem;">Kembalian</span>
                <strong id="nilaiKembalian">Rp 0</strong>
            </div>

            <div id="infoQris" style="display:none;background:#e0f7f3;border-radius:8px;padding:8px 12px;margin-bottom:8px;font-size:.8rem;color:#00897b;">
                <i class="fas fa-info-circle me-1"></i>
                Pembayaran QRIS — tidak perlu uang kembalian.<br>Arahkan pelanggan ke kode QR kasir.
            </div>

            <button class="btn-checkout" id="btnBayar" onclick="prosesTransaksi()" disabled>
                <i class="fas fa-check-circle me-2"></i>Bayar Sekarang
            </button>
        </div>
    </div>
</div>

{{-- Modal Tipe Penjualan --}}
<div class="modal fade" id="modalTipePenjualan" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header" style="border-bottom:1px solid #e4e7f0;padding:14px 18px;">
                <div>
                    <h6 class="modal-title mb-0" id="modalTipeJudul" style="font-weight:700;"></h6>
                    <small id="modalTipeStok" style="color:#7a809a;"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <p style="font-size:.82rem;color:#7a809a;margin-bottom:12px;">Pilih cara penjualan:</p>
                <div class="tipe-option" id="optPack" onclick="pilihTipe('pack')">
                    <div class="to-title"><i class="fas fa-box me-2" style="color:#6C63FF;"></i>Per Pack / Bungkus</div>
                    <div class="to-desc" id="descPack"></div>
                    <div class="to-price" id="pricePack"></div>
                </div>
                <div class="tipe-option" id="optSatuan" onclick="pilihTipe('satuan')">
                    <div class="to-title"><i class="fas fa-circle me-2" style="color:#00b894;"></i>Per Satuan (Ketengan)</div>
                    <div class="to-desc" id="descSatuan"></div>
                    <div class="to-price" id="priceSatuan" style="color:#00b894;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Struk --}}
<div class="modal fade" id="modalStruk" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header py-2" style="border-bottom:1px solid #e4e7f0;">
                <h6 class="modal-title fw-bold"><i class="fas fa-receipt me-2" style="color:var(--primary);"></i>Struk Pembelian</h6>
            </div>
            <div class="modal-body" id="isiStruk" style="font-family:'Courier New',monospace;font-size:.78rem;"></div>
            <div class="modal-footer py-2" style="border-top:1px solid #e4e7f0;">
                <button class="btn btn-sm btn-primary" style="border-radius:8px;" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Cetak
                </button>
                <button class="btn btn-sm btn-secondary" style="border-radius:8px;" data-bs-dismiss="modal" onclick="location.reload()">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
var daftarBarang  = @json($daftarBarang);
var keranjang     = [];
var produkDipilih = null;
var modalTipe, modalStruk;

document.addEventListener('DOMContentLoaded', function () {
    modalTipe  = new bootstrap.Modal(document.getElementById('modalTipePenjualan'));
    modalStruk = new bootstrap.Modal(document.getElementById('modalStruk'));
});

function pilihMetode(metode) {
    document.getElementById('metodePembayaran').value = metode;
    var btnCash = document.getElementById('btnCash');
    var btnQris = document.getElementById('btnQris');
    var wrapBayar     = document.getElementById('wrapBayar');
    var wrapKembalian = document.getElementById('wrapKembalian');
    var infoQris      = document.getElementById('infoQris');

    if (metode === 'qris') {
        btnQris.style.border = '2px solid #00b894'; btnQris.style.color = '#00b894'; btnQris.style.background = '#e0f7f3';
        btnCash.style.border = '2px solid #e4e7f0'; btnCash.style.color = '#7a809a'; btnCash.style.background = '#fff';
        wrapBayar.style.display = 'none'; wrapKembalian.style.display = 'none'; infoQris.style.display = 'block';
        document.getElementById('inputBayar').value = keranjang.reduce((s,i) => s+i.subtotal, 0);
    } else {
        btnCash.style.border = '2px solid #6C63FF'; btnCash.style.color = '#6C63FF'; btnCash.style.background = '#e8e6ff';
        btnQris.style.border = '2px solid #e4e7f0'; btnQris.style.color = '#7a809a'; btnQris.style.background = '#fff';
        wrapBayar.style.display = ''; wrapKembalian.style.display = ''; infoQris.style.display = 'none';
        document.getElementById('inputBayar').value = '';
    }
    hitungKembalian();
}

function klikProduk(el) {
    var id = parseInt(el.dataset.id);
    var barang = daftarBarang.find(b => b.id_barang == id);
    if (!barang) return;
    if (barang.bisa_satuan === 'ya') {
        produkDipilih = barang;
        document.getElementById('modalTipeJudul').textContent = barang.nama_barang;
        document.getElementById('modalTipeStok').textContent  = 'Stok: ' + barang.stok + ' ' + barang.satuan;
        document.getElementById('descPack').textContent    = 'Isi: ' + barang.isi_per_pack + ' ' + barang.satuan + ' per bungkus';
        document.getElementById('pricePack').textContent   = 'Rp ' + parseInt(barang.harga_jual).toLocaleString('id-ID') + ' / bungkus';
        document.getElementById('descSatuan').textContent  = 'Jual per ' + barang.satuan + ' (ketengan)';
        document.getElementById('priceSatuan').textContent = 'Rp ' + parseInt(barang.harga_satuan).toLocaleString('id-ID') + ' / ' + barang.satuan;
        modalTipe.show();
    } else {
        tambahKeKeranjang(barang, 'pack');
    }
}

function pilihTipe(tipe) {
    if (!produkDipilih) return;
    modalTipe.hide();
    tambahKeKeranjang(produkDipilih, tipe);
    produkDipilih = null;
}

function tambahKeKeranjang(barang, tipe) {
    var harga      = tipe === 'satuan' ? parseInt(barang.harga_satuan) : parseInt(barang.harga_jual);
    var unitPerQty = tipe === 'satuan' ? 1 : parseInt(barang.isi_per_pack);
    var key        = barang.id_barang + '_' + tipe;
    var idx        = keranjang.findIndex(i => i.key == key);
    var stokTerpakai = keranjang.filter(i => i.id == barang.id_barang).reduce((s,i) => s + i.qty * i.unitPerQty, 0);

    if (idx >= 0) {
        if (stokTerpakai + unitPerQty > parseInt(barang.stok)) { showToast('Stok tidak cukup!','danger'); return; }
        keranjang[idx].qty++;
        keranjang[idx].subtotal = keranjang[idx].qty * harga;
    } else {
        if (stokTerpakai + unitPerQty > parseInt(barang.stok)) { showToast('Stok tidak cukup!','danger'); return; }
        keranjang.push({ key, id: barang.id_barang, nama: barang.nama_barang, tipe, harga, qty: 1, subtotal: harga, unitPerQty, satuan: barang.satuan });
    }
    renderKeranjang();
    playBeep();
}

function renderKeranjang() {
    var el = document.getElementById('cartItems');
    if (keranjang.length === 0) {
        el.innerHTML = '<div id="emptyMsg" class="text-center py-5" style="color:#c0c5d8;"><i class="fas fa-shopping-cart fa-2x mb-3 d-block"></i><span style="font-size:.85rem;">Keranjang masih kosong</span></div>';
        document.getElementById('totalItem').textContent = '0';
        document.getElementById('totalHarga').textContent = 'Rp 0';
        document.getElementById('btnBayar').disabled = true;
        hitungKembalian(); return;
    }
    var html = '', totalItem = 0, totalHarga = 0;
    keranjang.forEach(function(item, idx) {
        totalItem += item.qty; totalHarga += item.subtotal;
        var tipeBadge = item.tipe === 'satuan'
            ? '<span style="background:#e0f7f3;color:#00b894;font-size:.62rem;padding:1px 6px;border-radius:5px;font-weight:600;">satuan</span>'
            : '<span style="background:#e8e6ff;color:#6C63FF;font-size:.62rem;padding:1px 6px;border-radius:5px;font-weight:600;">pack</span>';
        html += '<div class="cart-item">';
        html += '<div class="ci-name"><span>' + item.nama + ' ' + tipeBadge + '</span>';
        html += '<span style="cursor:pointer;color:#FF5E57;font-size:.8rem;" onclick="hapusItem('+idx+')"><i class="fas fa-times"></i></span></div>';
        html += '<div class="ci-ctrl"><div class="d-flex align-items-center gap-2">';
        html += '<button class="ci-qty-btn" onclick="ubahQty('+idx+',-1)">−</button>';
        html += '<input type="number" value="' + item.qty + '" min="1" onchange="setQty('+idx+', this.value)" onclick="this.select()" style="width:52px;text-align:center;border:1.5px solid #e4e7f0;border-radius:7px;font-size:.88rem;font-weight:700;padding:2px 4px;color:#1e2438;">';
        html += '<button class="ci-qty-btn" onclick="ubahQty('+idx+',1)">+</button>';
        html += '</div><span style="font-size:.85rem;color:var(--primary);font-weight:700;">Rp ' + item.subtotal.toLocaleString('id-ID') + '</span></div></div>';
    });
    el.innerHTML = html;
    document.getElementById('totalItem').textContent  = totalItem;
    document.getElementById('totalHarga').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
    document.getElementById('btnBayar').disabled = false;
    if (document.getElementById('metodePembayaran').value === 'qris') {
        document.getElementById('inputBayar').value = totalHarga;
    }
    hitungKembalian();
}

function getStokTerpakai(id_barang, excludeIdx) {
    return keranjang.reduce(function(s, item, i) {
        if (i === excludeIdx || item.id != id_barang) return s;
        return s + (item.qty * item.unitPerQty);
    }, 0);
}

function ubahQty(idx, d) {
    var item   = keranjang[idx];
    var newQty = item.qty + d;
    if (newQty <= 0) { keranjang.splice(idx, 1); renderKeranjang(); return; }

    var barang = daftarBarang.find(b => b.id_barang == item.id);
    if (barang) {
        var totalUnit = getStokTerpakai(item.id, idx) + (newQty * item.unitPerQty);
        if (totalUnit > parseInt(barang.stok)) {
            showToast('Stok "' + barang.nama_barang + '" tidak cukup! Tersisa: ' + barang.stok + ' ' + barang.satuan, 'danger');
            return;
        }
    }
    keranjang[idx].qty     = newQty;
    keranjang[idx].subtotal = newQty * item.harga;
    renderKeranjang();
}

function setQty(idx, val) {
    var newQty = parseInt(val);
    if (isNaN(newQty) || newQty < 1) newQty = 1;

    var item   = keranjang[idx];
    var barang = daftarBarang.find(b => b.id_barang == item.id);
    if (barang) {
        var stokLain  = getStokTerpakai(item.id, idx);
        var totalUnit = stokLain + (newQty * item.unitPerQty);
        if (totalUnit > parseInt(barang.stok)) {
            var maxQty = Math.floor((parseInt(barang.stok) - stokLain) / item.unitPerQty);
            if (maxQty < 1) maxQty = 1;
            showToast('Stok "' + barang.nama_barang + '" hanya ' + barang.stok + ' ' + barang.satuan + '. Qty dikembalikan ke ' + maxQty + '.', 'danger');
            newQty = maxQty;
        }
    }
    keranjang[idx].qty     = newQty;
    keranjang[idx].subtotal = newQty * item.harga;
    renderKeranjang();
}

function hapusItem(idx)   { keranjang.splice(idx,1); renderKeranjang(); }
function resetKeranjang() { if (keranjang.length && confirm('Kosongkan keranjang?')) { keranjang=[]; renderKeranjang(); document.getElementById('inputBayar').value=''; } }
function hitungKembalian() {
    var total = keranjang.reduce((s,i) => s+i.subtotal, 0);
    var bayar = parseInt(document.getElementById('inputBayar').value) || 0;
    var el    = document.getElementById('nilaiKembalian');
    if (el) { el.textContent = 'Rp ' + Math.max(0, bayar-total).toLocaleString('id-ID'); el.style.color = (bayar<total && bayar>0) ? '#FF5E57' : '#6C63FF'; }
}

function prosesTransaksi() {
    if (!keranjang.length) { showToast('Keranjang masih kosong!','danger'); return; }

    var total  = keranjang.reduce((s,i) => s+i.subtotal, 0);
    var metode = document.getElementById('metodePembayaran').value;

    // Validasi stok client-side
    for (var ci = 0; ci < keranjang.length; ci++) {
        var cItem   = keranjang[ci];
        var cBarang = daftarBarang.find(b => b.id_barang == cItem.id);
        if (cBarang) {
            var unitDibutuhkan = keranjang.reduce(function(s, x) {
                return x.id == cItem.id ? s + (x.qty * x.unitPerQty) : s;
            }, 0);
            if (unitDibutuhkan > parseInt(cBarang.stok)) {
                showToast('Stok "' + cItem.nama + '" tidak cukup! Tersedia: ' + cBarang.stok + ' ' + cBarang.satuan, 'danger');
                return;
            }
        }
    }

    var bayar, kembalian;
    if (metode === 'qris') { bayar = total; kembalian = 0; }
    else {
        bayar = parseInt(document.getElementById('inputBayar').value) || 0;
        if (bayar < total) { showToast('Uang bayar kurang dari total!','danger'); return; }
        kembalian = bayar - total;
    }

    var btn = document.getElementById('btnBayar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    var snapshot = JSON.parse(JSON.stringify(keranjang));

    fetch('{{ route("kasir.proses") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ keranjang: keranjang, total: total, bayar: bayar, kembalian: kembalian, metode_pembayaran: metode })
    })
    .then(r => r.json())
    .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Bayar Sekarang';
        if (res.status === 'ok') {
            keranjang = []; renderKeranjang();
            document.getElementById('inputBayar').value = '';
            tampilkanStruk(res.id_transaksi, total, bayar, kembalian, metode, snapshot);
        } else {
            showToast('❌ ' + (res.pesan || 'Transaksi gagal'), 'danger');
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Bayar Sekarang';
        showToast('Koneksi error! Coba lagi.','danger');
    });
}

function tampilkanStruk(id, total, bayar, kembalian, metode, items) {
    var tgl = new Date().toLocaleString('id-ID');
    var wn  = '{{ addslashes($infoWarung->nama_warung ?? "WarungU") }}';
    var metodeLbl = metode === 'qris'
        ? '<span style="background:#e0f7f3;color:#00b894;padding:1px 6px;border-radius:4px;font-size:.75rem;font-family:sans-serif;">QRIS</span>'
        : '<span style="background:#e8e6ff;color:#6C63FF;padding:1px 6px;border-radius:4px;font-size:.75rem;font-family:sans-serif;">Cash</span>';
    var html = '<div style="text-align:center;padding:10px 0 6px;"><strong style="font-size:.9rem;">' + wn + '</strong><br>';
    html += '<small>No: #' + id + '</small><br><small>' + tgl + '</small></div>';
    html += '<hr style="border-style:dashed;margin:6px 0;">';
    items.forEach(function(item) {
        html += item.nama + ' <span style="color:#6C63FF;font-size:.75rem;">['+item.tipe+']</span><br>';
        html += '  ' + item.qty + ' × Rp ' + item.harga.toLocaleString('id-ID') + ' = Rp ' + item.subtotal.toLocaleString('id-ID') + '<br>';
    });
    html += '<hr style="border-style:dashed;margin:6px 0;">';
    html += 'Total     : Rp ' + total.toLocaleString('id-ID') + '<br>';
    html += 'Metode    : ' + metodeLbl + '<br>';
    if (metode === 'cash') {
        html += 'Bayar     : Rp ' + bayar.toLocaleString('id-ID') + '<br>';
        html += 'Kembalian : Rp ' + kembalian.toLocaleString('id-ID') + '<br>';
    }
    html += '<hr style="border-style:dashed;margin:6px 0;"><div style="text-align:center;"><small>Terima kasih! 🙏</small></div>';
    document.getElementById('isiStruk').innerHTML = html;
    modalStruk.show();
}

function filterProduk() {
    var kata = document.getElementById('searchBarang').value.toLowerCase();
    var kat  = document.getElementById('filterKategori').value;
    document.querySelectorAll('.product-card').forEach(function(el) {
        el.style.display = (el.dataset.nama.includes(kata) && (!kat || el.dataset.kategori === kat)) ? '' : 'none';
    });
}
document.getElementById('searchBarang').addEventListener('input', filterProduk);
document.getElementById('filterKategori').addEventListener('change', filterProduk);

document.getElementById('inputBarcode').addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    var bc = this.value.trim();
    var barang = daftarBarang.find(b => b.barcode == bc);
    if (barang) {
        this.value = '';
        if (barang.bisa_satuan === 'ya') klikProduk(document.querySelector('[data-id="'+barang.id_barang+'"]'));
        else tambahKeKeranjang(barang, 'pack');
    } else { showToast('Barcode tidak ditemukan: ' + bc, 'danger'); }
});

function playBeep() {}

function gantiWarung(id_warung) {
    if (keranjang.length > 0) {
        if (!confirm('Keranjang ada ' + keranjang.length + ' item.\nGanti warung akan mengosongkan keranjang. Lanjutkan?')) {
            document.getElementById('pilihWarung').value = {{ $idWarung }};
            return;
        }
    }
    window.location.href = '{{ route("kasir.index") }}?warung=' + id_warung;
}
</script>
</body>
</html>
