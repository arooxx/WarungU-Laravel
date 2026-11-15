@extends('layouts.app')
@section('title','Manajemen Kategori')
@section('page-title')
<i class="fas fa-tags me-2" style="color:var(--primary);"></i>Manajemen Kategori
@endsection

@section('content')

@php
$msgMap = ['tambah'=>'Kategori berhasil ditambahkan','edit'=>'Kategori berhasil diupdate','hapus'=>'Kategori berhasil dihapus'];
// Ikon + warna per nama kategori — identik dengan native
$ikonKat = [
    'Minuman'      => ['fa-coffee',          '#3498db'],
    'Makanan'      => ['fa-utensils',        '#e67e22'],
    'Snack'        => ['fa-cookie',          '#f39c12'],
    'Sembako'      => ['fa-shopping-basket', '#27ae60'],
    'Rokok'        => ['fa-smoking',         '#7f8c8d'],
    'Obat'         => ['fa-pills',           '#e74c3c'],
    'Minuman Soda' => ['fa-glass-whiskey',   '#9b59b6'],
    'Makanan Beku' => ['fa-snowflake',       '#3498db'],
];
@endphp

@if(session('msg') && isset($msgMap[session('msg')]))
    <div class="alert alert-success alert-auto py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
        <i class="fas fa-check-circle me-1"></i>{{ $msgMap[session('msg')] }}
    </div>
@endif

<div class="row g-3">
    {{-- Form tambah --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus me-1"></i>Tambah Kategori
            </div>
            <div class="card-body">
                @if($error)
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $error }}
                    </div>
                @endif

                <form method="POST" action="{{ route('kategori.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Nama Kategori *</label>
                        <input type="text" name="nama_kategori" class="form-control"
                               placeholder="mis: Minuman, Snack, Obat..." required>
                        <small style="color:#7a809a;font-size:.75rem;">Nama kategori tidak boleh duplikat.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:8px;">
                        <i class="fas fa-plus me-1"></i>Tambah Kategori
                    </button>
                </form>
            </div>
        </div>

        {{-- Info card — identik dengan native --}}
        <div class="card mt-3">
            <div class="card-body py-3" style="font-size:.82rem;color:#7a809a;">
                <i class="fas fa-info-circle me-1" style="color:var(--primary);"></i>
                Kategori digunakan untuk mengelompokkan barang di kasir dan laporan.<br><br>
                @if(auth()->user()->isPemilik())
                    <i class="fas fa-trash me-1" style="color:var(--danger);"></i>
                    Kategori yang masih memiliki barang tidak dapat dihapus.
                @else
                    <i class="fas fa-lock me-1"></i>
                    Hanya pemilik yang dapat mengedit atau menghapus kategori.
                @endif
            </div>
        </div>
    </div>

    {{-- Daftar kategori --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-1"></i>Daftar Kategori</span>
                <span class="badge bg-secondary" style="border-radius:6px;">{{ $kategori->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($kategori->count())
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kategori</th>
                                <th class="text-center">Jumlah Barang</th>
                                @if(auth()->user()->isPemilik())
                                    <th class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategori as $i => $k)
                                @php
                                    $ik = $ikonKat[$k->nama_kategori] ?? ['fa-tag', '#6C63FF'];
                                @endphp
                                <tr>
                                    <td style="color:#7a809a;">{{ $i + 1 }}</td>
                                    <td>
                                        {{-- Ikon identik dengan native --}}
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <div style="width:32px;height:32px;border-radius:8px;background:{{ $ik[1] }}22;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="fas {{ $ik[0] }}" style="color:{{ $ik[1] }};font-size:.85rem;"></i>
                                            </div>
                                            <span style="font-weight:600;">{{ $k->nama_kategori }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($k->jml_barang > 0)
                                            <span style="background:#e8e6ff;color:#6C63FF;padding:2px 10px;border-radius:6px;font-size:.8rem;font-weight:600;">
                                                {{ $k->jml_barang }} barang
                                            </span>
                                        @else
                                            <span style="background:#f0f2f8;color:#7a809a;padding:2px 10px;border-radius:6px;font-size:.8rem;">
                                                Kosong
                                            </span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->isPemilik())
                                        <td class="text-center">
                                            <a href="{{ route('kategori.edit', $k->id_kategori) }}"
                                               style="background:#e8e6ff;color:#6C63FF;padding:3px 10px;border-radius:7px;font-size:.75rem;font-weight:600;">
                                                Edit
                                            </a>
                                            @if($k->jml_barang == 0)
                                                <a href="#"
                                                   onclick="hapusKategori('{{ route('kategori.destroy', $k->id_kategori) }}','{{ addslashes($k->nama_kategori) }}')"
                                                   style="background:#fff0f0;color:var(--danger);padding:3px 10px;border-radius:7px;font-size:.75rem;font-weight:600;margin-left:4px;">
                                                    Hapus
                                                </a>
                                                <form id="form-kat-{{ $k->id_kategori }}" method="POST"
                                                      action="{{ route('kategori.destroy', $k->id_kategori) }}" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @else
                                                <span style="background:#f0f2f8;color:#c0c5d8;padding:3px 10px;border-radius:7px;font-size:.75rem;margin-left:4px;"
                                                      title="Tidak bisa dihapus, masih ada barang">
                                                    Hapus
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="page-empty">
                        <i class="fas fa-tags d-block"></i>Belum ada kategori
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function hapusKategori(action, nama) {
    var id = action.split('/').slice(-1)[0];
    if (confirm('Yakin hapus kategori "' + nama + '"?')) {
        document.getElementById('form-kat-' + id).submit();
    }
}
</script>
@endpush
