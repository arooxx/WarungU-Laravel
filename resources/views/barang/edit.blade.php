@extends('layouts.app')
@section('title','Edit Barang')
@section('page-title')
<i class="fas fa-edit me-2" style="color:var(--primary);"></i>Edit Barang
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-edit" style="color:var(--primary);"></i>
                Edit: <span style="color:var(--primary);">{{ $barang->nama_barang }}</span>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('barang.update', $barang->id_barang) }}">
                    @csrf @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Nama Barang *</label>
                            <input type="text" name="nama_barang" class="form-control" required value="{{ old('nama_barang', $barang->nama_barang) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">— Pilih —</option>
                                @foreach($kategoriList as $k)
                                    <option value="{{ $k->id_kategori }}" {{ old('id_kategori', $barang->id_kategori) == $k->id_kategori ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Barcode</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode" style="font-size:.85rem;"></i></span>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $barang->barcode) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Harga Beli</label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size:.83rem;">Rp</span>
                                <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli', $barang->harga_beli) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Harga Jual (Pack) *</label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size:.83rem;">Rp</span>
                                <input type="number" name="harga_jual" class="form-control" required value="{{ old('harga_jual', $barang->harga_jual) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 p-3 rounded-3" style="background:#F7F9F7;border:1.5px solid var(--border);">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="bisa_satuan" id="cekSatuan"
                                   {{ old('bisa_satuan', $barang->bisa_satuan) === 'ya' ? 'checked' : '' }}
                                   onchange="toggleSatuan(this.checked)">
                            <label class="form-check-label fw-semibold" for="cekSatuan" style="font-size:.87rem;">
                                <i class="fas fa-circle me-1" style="color:#2E9E68;font-size:.7rem;"></i>
                                Bisa dijual per satuan / ketengan
                            </label>
                        </div>
                        @php $isSatuan = old('bisa_satuan', $barang->bisa_satuan) === 'ya'; @endphp

                        {{-- FIX: Satu set field, bukan dua set — dikontrol JS enable/disable --}}
                        <div class="row g-2">
                            <div class="col-md-4" id="wrapIsiPack" style="display:{{ $isSatuan ? 'block' : 'none' }};">
                                <label style="font-size:.8rem;font-weight:600;color:#7B857F;">Isi per Pack</label>
                                <input type="number" id="fldIsiPerPack" name="isi_per_pack"
                                       class="form-control form-control-sm" min="1"
                                       value="{{ old('isi_per_pack', $barang->isi_per_pack) }}"
                                       {{ !$isSatuan ? 'disabled' : '' }}>
                                <div style="font-size:.72rem;color:#7B857F;margin-top:3px;">1 bungkus = ? satuan</div>
                            </div>
                            <div class="col-md-4">
                                <label style="font-size:.8rem;font-weight:600;color:#7B857F;" id="labelSatuan">
                                    {{ $isSatuan ? 'Satuan Terkecil' : 'Satuan' }}
                                </label>
                                <input type="text" name="satuan" id="fldSatuan"
                                       class="form-control form-control-sm"
                                       value="{{ old('satuan', $barang->satuan) }}"
                                       placeholder="mis: pcs, batang, botol...">
                            </div>
                            <div class="col-md-4" id="wrapHargaSatuan" style="display:{{ $isSatuan ? 'block' : 'none' }};">
                                <label style="font-size:.8rem;font-weight:600;color:#7B857F;">Harga per Satuan</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" id="fldHargaSatuan" name="harga_satuan"
                                           class="form-control" min="0"
                                           value="{{ old('harga_satuan', $barang->harga_satuan) }}"
                                           {{ !$isSatuan ? 'disabled' : '' }}>
                                </div>
                            </div>
                            {{-- Hidden defaults untuk saat tidak satuan --}}
                            <input type="hidden" id="fldIsiPerPackHidden" name="isi_per_pack" value="1" {{ $isSatuan ? 'disabled' : '' }}>
                            <input type="hidden" id="fldHargaSatuanHidden" name="harga_satuan" value="0" {{ $isSatuan ? 'disabled' : '' }}>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">Stok <small style="color:#7B857F;">(satuan terkecil)</small></label>
                            <input type="number" name="stok" class="form-control" min="0" value="{{ old('stok', $barang->stok) }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="border-radius:8px;padding:8px 22px;">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSatuan(cek) {
    document.getElementById('wrapIsiPack').style.display     = cek ? 'block' : 'none';
    document.getElementById('wrapHargaSatuan').style.display = cek ? 'block' : 'none';
    document.getElementById('labelSatuan').textContent       = cek ? 'Satuan Terkecil' : 'Satuan';

    document.getElementById('fldIsiPerPack').disabled        = !cek;
    document.getElementById('fldHargaSatuan').disabled       = !cek;
    document.getElementById('fldIsiPerPackHidden').disabled  = cek;
    document.getElementById('fldHargaSatuanHidden').disabled = cek;
}
window.addEventListener('DOMContentLoaded', function() {
    toggleSatuan(document.getElementById('cekSatuan').checked);
});
</script>
@endpush
