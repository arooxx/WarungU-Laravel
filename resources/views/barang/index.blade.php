@extends('layouts.app')
@section('title','Manajemen Barang')
@section('page-title')
<i class="fas fa-box me-2" style="color:var(--primary);"></i>Manajemen Barang
@endsection

@section('content')

@php
    $msgMap = [
        'tambah'           => ['success', 'Barang berhasil ditambahkan.'],
        'edit'             => ['success', 'Barang berhasil diupdate.'],
        'hapus'            => ['success', 'Barang berhasil dihapus.'],
        'tidak_ditemukan'  => ['danger',  'Barang tidak ditemukan.'],
        'tidak_bisa_hapus' => ['warning', 'Barang tidak bisa dihapus karena sudah ada di riwayat transaksi. Kosongkan stok saja jika tidak ingin dijual lagi.'],
    ];
    $msgInfo = session('msg') ? ($msgMap[session('msg')] ?? null) : null;
@endphp

@if($msgInfo)
    <div class="alert alert-{{ $msgInfo[0] }} alert-auto py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
        <i class="fas fa-{{ $msgInfo[0]==='success' ? 'check-circle' : ($msgInfo[0]==='warning' ? 'exclamation-triangle' : 'times-circle') }} me-1"></i>
        {{ $msgInfo[1] }}
    </div>
@endif

@include('partials.filter_warung')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span>Daftar Barang</span>
            @if($filter === 'hampir_habis')
                <span class="badge" style="background:#fff0f0;color:var(--danger);border-radius:6px;font-size:.72rem;">Hampir Habis</span>
            @endif
            <span class="badge bg-secondary" style="border-radius:6px;font-size:.72rem;">{{ $barang->count() }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('barang.index', ['warung'=>$warungParam]) }}"
               class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;">Semua</a>
            <a href="{{ route('barang.index', ['filter'=>'hampir_habis','warung'=>$warungParam]) }}"
               class="btn btn-sm btn-outline-danger" style="border-radius:8px;font-size:.8rem;">
                <i class="fas fa-exclamation-triangle me-1"></i>Hampir Habis
            </a>
            @if(auth()->user()->isPemilik() && !$semuaWarung)
                <a href="{{ route('barang.create', ['warung'=>$warungParam]) }}"
                   class="btn btn-sm btn-primary" style="border-radius:8px;font-size:.8rem;">
                    <i class="fas fa-plus me-1"></i>Tambah Barang
                </a>
            @endif
        </div>
    </div>

    <div class="card-body p-0">
        <div class="p-3 border-bottom" style="background:#fafbff;">
            <input type="text" id="searchTabel" class="form-control form-control-sm"
                   placeholder="🔍 Cari nama barang, barcode, kategori..."
                   style="max-width:300px;border-radius:8px;">
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabelBarang">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Barcode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        @if($semuaWarung)<th>Warung</th>@endif
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-center">Isi/Pack</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Stok</th>
                        @if(auth()->user()->isPemilik())
                            <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($barang as $i => $b)
                        <tr>
                            <td style="color:#7a809a;">{{ $i+1 }}</td>
                            <td>
                                <code style="background:#f0eeff;color:#6C63FF;padding:2px 6px;border-radius:5px;font-size:.75rem;">
                                    {{ $b->barcode ?: '—' }}
                                </code>
                            </td>
                            <td style="font-weight:600;">{{ $b->nama_barang }}</td>
                            <td><span style="background:#f0f2f8;color:#7a809a;padding:2px 8px;border-radius:6px;font-size:.78rem;">{{ $b->kategori->nama_kategori ?? '—' }}</span></td>
                            @if($semuaWarung)
                                <td><span style="background:#e8e6ff;color:#6C63FF;padding:2px 8px;border-radius:6px;font-size:.75rem;">{{ $b->warung->nama_warung ?? '—' }}</span></td>
                            @endif
                            <td class="text-end fw-semibold">Rp {{ number_format($b->harga_jual,0,',','.') }}</td>
                            <td class="text-end">
                                @if($b->bisa_satuan === 'ya')
                                    <span style="color:#00b894;font-weight:600;">Rp {{ number_format($b->harga_satuan,0,',','.') }}</span>
                                @else
                                    <span style="color:#c0c5d8;">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $b->isi_per_pack }}</td>
                            <td class="text-center">
                                @if($b->bisa_satuan === 'ya')
                                    <span style="background:#e0f7f3;color:#00b894;padding:2px 8px;border-radius:6px;font-size:.75rem;font-weight:600;">✓ {{ $b->satuan }}</span>
                                @else
                                    <span style="color:#7a809a;font-size:.83rem;">{{ $b->satuan }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php $stokMin = \App\Http\Controllers\BaseController::STOK_MIN; @endphp
                                @if($b->stok < $stokMin)
                                    <span style="background:#fff0f0;color:var(--danger);padding:3px 9px;border-radius:6px;font-size:.78rem;font-weight:600;">{{ $b->stok }}</span>
                                @elseif($b->stok < $stokMin * 2)
                                    <span style="background:#fff9e6;color:#e67e22;padding:3px 9px;border-radius:6px;font-size:.78rem;font-weight:600;">{{ $b->stok }}</span>
                                @else
                                    <span style="background:#e0f7f3;color:#00b894;padding:3px 9px;border-radius:6px;font-size:.78rem;font-weight:600;">{{ $b->stok }}</span>
                                @endif
                            </td>
                            @if(auth()->user()->isPemilik())
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('barang.tambah-stok') }}?id={{ $b->id_barang }}"
                                       style="background:#e0f7f3;color:#00b894;padding:3px 8px;border-radius:7px;font-size:.73rem;">+Stok</a>
                                    <a href="{{ route('barang.edit', $b->id_barang) }}"
                                       style="background:#e8e6ff;color:#6C63FF;padding:3px 8px;border-radius:7px;font-size:.73rem;">Edit</a>
                                    <a href="#" onclick="konfirmasiHapusBlade('{{ route('barang.destroy', $b->id_barang) }}', '{{ addslashes($b->nama_barang) }}')"
                                       style="background:#fff0f0;color:var(--danger);padding:3px 8px;border-radius:7px;font-size:.73rem;">Hapus</a>
                                    <form id="hapus-{{ $b->id_barang }}" method="POST" action="{{ route('barang.destroy', $b->id_barang) }}" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $semuaWarung ? ($__env->yieldContent('') ? 12 : (auth()->user()->isPemilik() ? 11 : 10)) : (auth()->user()->isPemilik() ? 10 : 9) }}" class="text-center py-5">
                            <i class="fas fa-box-open fa-2x mb-2 d-block" style="color:#c0c5d8;"></i>
                            <span style="color:#7a809a;">Belum ada data barang</span>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchTabel').addEventListener('input', function () {
    var kata = this.value.toLowerCase();
    document.querySelectorAll('#tabelBarang tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().includes(kata) ? '' : 'none';
    });
});
function konfirmasiHapusBlade(actionUrl, nama) {
    if (confirm('Yakin ingin menghapus barang "' + nama + '"?\nTindakan ini tidak bisa dibatalkan!')) {
        var id = actionUrl.split('/').slice(-1)[0];
        document.getElementById('hapus-' + id).submit();
    }
}
</script>
@endpush
