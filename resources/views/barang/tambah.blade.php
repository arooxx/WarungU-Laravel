@extends('layouts.app')
@section('title','Tambah Barang')

@section('page-title')
<i class="fas fa-plus me-2" style="color:var(--primary);"></i>Tambah Barang
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-box" style="color:var(--primary);"></i> Form Tambah Barang
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('barang.store') }}">
                    @csrf
                    <input type="hidden" name="id_warung" value="{{ $idWarungSimpan }}">

                    @if(auth()->user()->isPemilik() && $semuaWarung)
                        <div class="alert alert-warning py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Anda sedang lihat "Semua Warung". Pilih warung spesifik terlebih dahulu di menu
                            <a href="{{ route('barang.index') }}" style="color:var(--primary);font-weight:600;">Barang</a>.
                        </div>
                        <div class="text-center py-3">
                            <a href="{{ route('barang.index') }}" class="btn btn-primary" style="border-radius:8px;">
                                <i class="fas fa-arrow-left me-1"></i>Kembali & Pilih Warung Dulu
                            </a>
                        </div>
                    @else

                    @if(auth()->user()->isPemilik() && !$semuaWarung)
                        @php $namaW = $warungList->firstWhere('id_warung', $idWarungSimpan)?->nama_warung ?? ''; @endphp
                        <div class="mb-3 p-2 rounded-3 d-flex align-items-center gap-2"
                             style="background:#f0eeff;border:1px solid #d8d4ff;font-size:.83rem;">
                            <i class="fas fa-store" style="color:#6C63FF;"></i>
                            Barang akan ditambahkan ke: <strong style="color:#6C63FF;">{{ $namaW }}</strong>
                        </div>
                    @endif

                    {{-- Baris 1: nama + kategori --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Nama Barang <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" required
                                   value="{{ old('nama_barang') }}" placeholder="Nama barang">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">— Pilih —</option>
                                @foreach($kategoriList as $k)
                                    <option value="{{ $k->id_kategori }}" {{ old('id_kategori') == $k->id_kategori ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Baris 2: barcode --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Barcode</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode" style="font-size:.85rem;"></i></span>
                            <input type="text" name="barcode" id="fldBarcode" class="form-control"
                                   value="{{ old('barcode') }}" placeholder="Scan atau ketik manual">
                            <button type="button" class="btn btn-outline-secondary" id="btnGenBarcode" style="font-size:.82rem;">
                                <i class="fas fa-magic me-1"></i>Generate
                            </button>
                        </div>
                    </div>

                    {{-- Baris 3: harga --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Harga Beli</label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size:.83rem;">Rp</span>
                                <input type="number" name="harga_beli" class="form-control" min="0" value="{{ old('harga_beli', 0) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Harga Jual (Pack) <span style="color:var(--danger);">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size:.83rem;">Rp</span>
                                <input type="number" name="harga_jual" class="form-control" min="0" required value="{{ old('harga_jual', 0) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Baris 4: Satuan & ketengan --}}
                    <div class="mb-3 p-3 rounded-3" style="background:#fafbff;border:1.5px solid var(--border);">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="cekSatuan"
                                   {{ old('bisa_satuan') ? 'checked' : '' }}>
                            <input type="hidden" name="bisa_satuan" id="hiddenBisaSatuan" value="{{ old('bisa_satuan') ? 'ya' : 'tidak' }}">
                            <label class="form-check-label fw-semibold" for="cekSatuan" style="font-size:.87rem;">
                                <i class="fas fa-circle me-1" style="color:#00b894;font-size:.7rem;"></i>
                                Bisa dijual per satuan / ketengan
                            </label>
                        </div>

                        {{-- Box satuan: muncul saat diceklis --}}
                        <div id="boxSatuan" style="display:{{ old('bisa_satuan') ? 'block' : 'none' }};">
                            <div class="row g-2 mt-1">
                                <div class="col-md-4">
                                    <label style="font-size:.8rem;font-weight:600;color:#7a809a;">Isi per Pack</label>
                                    <input type="number" name="isi_per_pack" id="fldIsiPerPack"
                                           class="form-control form-control-sm" min="1"
                                           value="{{ old('isi_per_pack', 12) }}" placeholder="mis: 12">
                                    <div style="font-size:.72rem;color:#7a809a;margin-top:3px;">1 bungkus = ? satuan</div>
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size:.8rem;font-weight:600;color:#7a809a;">Satuan Terkecil</label>
                                    <input type="text" name="satuan_kecil" id="fldSatuanKecil"
                                           class="form-control form-control-sm"
                                           value="{{ old('satuan', 'batang') }}"
                                           placeholder="mis: batang, biji, sachet...">
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size:.8rem;font-weight:600;color:#7a809a;">Harga per Satuan</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga_satuan" id="fldHargaSatuan"
                                               class="form-control" min="0" value="{{ old('harga_satuan', 0) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Box satuan biasa: muncul saat tidak diceklis --}}
                        <div id="boxSatuanBiasa" style="display:{{ old('bisa_satuan') ? 'none' : 'block' }};">
                            <div class="row g-2 mt-1">
                                <div class="col-md-4">
                                    <label style="font-size:.8rem;font-weight:600;color:#7a809a;">Satuan</label>
                                    <input type="text" name="satuan_biasa" id="fldSatuanBiasa"
                                           class="form-control form-control-sm"
                                           value="{{ old('satuan', 'pcs') }}"
                                           placeholder="mis: pcs, botol, bungkus...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 5: stok --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Stok Awal <small style="color:#7a809a;">(satuan terkecil)</small></label>
                            <input type="number" name="stok" class="form-control" min="0" value="{{ old('stok', 0) }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="border-radius:8px;padding:8px 22px;">
                            <i class="fas fa-save me-1"></i>Simpan Barang
                        </button>
                        <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Batal</a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// FIX: Semua fungsi di top-level, tidak nested
function toggleSatuan(cek) {
    var boxSatuan      = document.getElementById('boxSatuan');
    var boxSatuanBiasa = document.getElementById('boxSatuanBiasa');
    var hiddenBisa     = document.getElementById('hiddenBisaSatuan');

    if (cek) {
        boxSatuan.style.display      = 'block';
        boxSatuanBiasa.style.display = 'none';
        hiddenBisa.value             = 'ya';
    } else {
        boxSatuan.style.display      = 'none';
        boxSatuanBiasa.style.display = 'block';
        hiddenBisa.value             = 'tidak';
    }
}

// FIX: generate barcode pakai addEventListener, tidak inline onclick
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox toggle
    var cek = document.getElementById('cekSatuan');
    if (cek) {
        cek.addEventListener('change', function() {
            toggleSatuan(this.checked);
        });
        // Init saat load
        toggleSatuan(cek.checked);
    }

    // Generate barcode
    var btnGen = document.getElementById('btnGenBarcode');
    if (btnGen) {
        btnGen.addEventListener('click', function() {
            document.getElementById('fldBarcode').value = Date.now().toString().slice(-10);
        });
    }
});
</script>
@endpush
