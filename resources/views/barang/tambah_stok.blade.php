@extends('layouts.app')
@section('title','Tambah Stok')
@section('page-title')
<i class="fas fa-plus-circle me-2" style="color:var(--primary);"></i>Tambah Stok
@endsection

@section('content')

@if(session('msg') === 'stok_ok')
    <div class="alert alert-success alert-auto py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
        <i class="fas fa-check-circle me-1"></i>Stok berhasil ditambahkan!
    </div>
@endif

@include('partials.filter_warung')

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="fas fa-plus-circle me-1" style="color:var(--primary);"></i>Form Tambah Stok</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">{{ $errors->first() }}</div>
                @endif

                @if(auth()->user()->isPemilik())
                    @if($semuaWarung)
                        <div class="alert py-2 mb-3" style="font-size:.83rem;border-radius:10px;background:#e8f4fd;border:1px solid #bee3f8;color:#2b6cb0;">
                            <i class="fas fa-info-circle me-1"></i>
                            Menampilkan barang dari <strong>semua warung</strong>. Pilih warung spesifik di atas untuk filter.
                        </div>
                    @else
                        @php
                            $filterDaftarLocal = $daftarWarung ?? \App\Models\Warung::orderBy('nama_warung')->get();
                            $namaWarungAktif   = $filterDaftarLocal->firstWhere('id_warung', $idWarungAktif)?->nama_warung ?? '';
                        @endphp
                        <div class="mb-3 p-2 rounded-3 d-flex align-items-center gap-2"
                             style="background:#f0eeff;border:1px solid #d8d4ff;font-size:.83rem;">
                            <i class="fas fa-store" style="color:#6C63FF;"></i>
                            Filter barang: <strong style="color:#6C63FF;">{{ $namaWarungAktif }}</strong>
                        </div>
                    @endif
                @endif

                <form method="POST" action="{{ route('barang.proses-stok') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Pilih Barang *</label>
                        <select name="id_barang" class="form-select" required id="selectBarang" onchange="updateInfo(this)">
                            <option value="">— Pilih Barang —</option>
                            @php $prevWarung = ''; @endphp
                            @foreach($barangList as $b)
                                @if(auth()->user()->isPemilik())
                                    @if($prevWarung !== ($b->warung->nama_warung ?? ''))
                                        @if($prevWarung !== '') </optgroup> @endif
                                        @php $prevWarung = $b->warung->nama_warung ?? ''; @endphp
                                        <optgroup label="{{ $prevWarung }}">
                                    @endif
                                @endif
                                <option value="{{ $b->id_barang }}"
                                        data-stok="{{ $b->stok }}"
                                        data-satuan="{{ $b->satuan }}"
                                        data-isi="{{ $b->isi_per_pack }}"
                                        data-bisa="{{ $b->bisa_satuan }}"
                                        data-warung="{{ $b->warung->nama_warung ?? '' }}"
                                        {{ request('id') == $b->id_barang ? 'selected' : '' }}>
                                    {{ $b->nama_barang }}
                                    @if(auth()->user()->isPemilik() && $semuaWarung)
                                        ({{ $b->warung->nama_warung ?? '' }})
                                    @endif
                                    — Stok: {{ $b->stok }} {{ $b->satuan }}
                                </option>
                            @endforeach
                            @if(auth()->user()->isPemilik() && $prevWarung !== '') </optgroup> @endif
                        </select>
                    </div>

                    <div id="infoBox" class="mb-3 p-3 rounded-3" style="background:#f0eeff;border:1px solid #d8d4ff;display:none;">
                        <div style="font-size:.78rem;font-weight:700;color:#6C63FF;text-transform:uppercase;margin-bottom:6px;">Info Barang</div>
                        <div style="font-size:.83rem;display:flex;justify-content:space-between;">
                            <span style="color:#7a809a;">Stok saat ini</span>
                            <strong id="stokSaatIni">—</strong>
                        </div>
                        <div id="infoWarungBadge" style="margin-top:4px;"></div>
                        <div id="satuanInfo" style="font-size:.78rem;color:#7a809a;margin-top:4px;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            Jumlah Tambah * <span id="satuanLabel" style="font-weight:400;color:#7a809a;"></span>
                        </label>
                        <input type="number" name="jumlah" id="inputJumlah" class="form-control" min="1" required oninput="updateKonversi()">
                        <div id="konversiInfo" style="font-size:.78rem;color:#6C63FF;margin-top:4px;font-weight:600;"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="mis: Restock dari supplier">
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="border-radius:8px;">
                        <i class="fas fa-plus me-1"></i>Tambah Stok
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><i class="fas fa-history me-1" style="color:var(--primary);"></i>Riwayat Stok Masuk</div>
            <div class="card-body p-0">
                @php
                    $riwayat = \App\Models\StokMasuk::with(['barang.warung','user'])
                        ->when(!auth()->user()->isPemilik(),
                            fn($q) => $q->whereHas('barang', fn($q2) => $q2->where('id_warung', auth()->user()->id_warung))
                        )
                        ->when(auth()->user()->isPemilik() && !$semuaWarung,
                            fn($q) => $q->whereHas('barang', fn($q2) => $q2->where('id_warung', $idWarungAktif))
                        )
                        ->latest('tanggal')->limit(20)->get();
                @endphp
                @if($riwayat->count())
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th><th>Barang</th>
                                @if(auth()->user()->isPemilik())<th>Warung</th>@endif
                                <th class="text-center">Jumlah</th><th>Oleh</th><th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat as $r)
                                <tr>
                                    <td style="font-size:.78rem;color:#7a809a;white-space:nowrap;">{{ $r->tanggal->format('d/m H:i') }}</td>
                                    <td style="font-weight:600;font-size:.85rem;">{{ $r->barang->nama_barang ?? '—' }}</td>
                                    @if(auth()->user()->isPemilik())
                                        <td><span style="background:#e8e6ff;color:#6C63FF;padding:2px 7px;border-radius:6px;font-size:.75rem;">{{ $r->barang->warung->nama_warung ?? '—' }}</span></td>
                                    @endif
                                    <td class="text-center">
                                        <span style="background:#e0f7f3;color:#00b894;padding:3px 10px;border-radius:6px;font-size:.8rem;font-weight:700;">
                                            +{{ $r->jumlah }} {{ $r->barang->satuan ?? '' }}
                                        </span>
                                    </td>
                                    <td style="font-size:.82rem;">{{ $r->user->nama ?? '—' }}</td>
                                    <td style="font-size:.8rem;color:#7a809a;">{{ $r->keterangan ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-5 text-center text-muted" style="font-size:.85rem;">
                        <i class="fas fa-history fa-2x mb-2 d-block" style="color:#c0c5d8;"></i>Belum ada riwayat stok masuk
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateInfo(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt.value) { document.getElementById('infoBox').style.display = 'none'; return; }
    document.getElementById('infoBox').style.display = 'block';
    document.getElementById('stokSaatIni').textContent = opt.dataset.stok + ' ' + opt.dataset.satuan;
    document.getElementById('satuanLabel').textContent = '(' + opt.dataset.satuan + ')';
    document.getElementById('konversiInfo').textContent = '';
    if (opt.dataset.warung) {
        document.getElementById('infoWarungBadge').innerHTML =
            '<span style="background:#e8e6ff;color:#6C63FF;padding:2px 8px;border-radius:5px;font-size:.75rem;">' + opt.dataset.warung + '</span>';
    }
    document.getElementById('satuanInfo').textContent =
        (opt.dataset.bisa === 'ya' && parseInt(opt.dataset.isi) > 1)
        ? '1 pack = ' + opt.dataset.isi + ' ' + opt.dataset.satuan : '';
}
function updateKonversi() {
    var sel    = document.getElementById('selectBarang');
    var opt    = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    var jumlah = parseInt(document.getElementById('inputJumlah').value) || 0;
    var isi    = parseInt(opt.dataset.isi);
    if (opt.dataset.bisa === 'ya' && isi > 1 && jumlah > 0) {
        document.getElementById('konversiInfo').textContent =
            '= ' + (jumlah * isi) + ' ' + opt.dataset.satuan + ' ('+jumlah+' pack × '+isi+')';
    } else {
        document.getElementById('konversiInfo').textContent = '';
    }
}
window.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('selectBarang');
    if (sel && sel.value) updateInfo(sel);
});
</script>
@endpush
