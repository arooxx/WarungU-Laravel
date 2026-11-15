@extends('layouts.app')
@section('title','Manajemen Warung')
@section('page-title')
<i class="fas fa-store-alt me-2" style="color:var(--primary);"></i>Manajemen Warung
@endsection

@section('content')
@php $msgMap = ['tambah'=>'Warung ditambahkan','edit'=>'Data diupdate','hapus'=>'Warung dihapus']; @endphp
@if(session('msg') && isset($msgMap[session('msg')]))
    <div class="alert alert-success alert-auto py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
        <i class="fas fa-check-circle me-1"></i>{{ $msgMap[session('msg')] }}
    </div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Tambah Warung Baru</div>
            <div class="card-body">
                @if($error)
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">{{ $error }}</div>
                @endif
                <form method="POST" action="{{ route('warung.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label" style="font-weight:600;font-size:.84rem;">Nama Warung *</label>
                        <input type="text" name="nama_warung" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-weight:600;font-size:.84rem;">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:.84rem;">No HP</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:8px;">
                        <i class="fas fa-plus me-1"></i>Tambah Warung
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Daftar Warung</span>
                <span class="badge bg-secondary" style="border-radius:6px;">{{ count($warung) }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>#</th><th>Nama Warung</th><th>Alamat</th><th>No HP</th><th class="text-center">Pegawai</th><th class="text-center">Barang</th><th class="text-center">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach($warung as $i => $w)
                            <tr>
                                <td style="color:#7B857F;">{{ $i+1 }}</td>
                                <td style="font-weight:600;">{{ $w->nama_warung }}</td>
                                <td><small style="color:#7B857F;">{{ $w->alamat ?? '—' }}</small></td>
                                <td>{{ $w->no_hp ?? '—' }}</td>
                                <td class="text-center"><span style="background:#DDEFE4;color:#1F7A4D;padding:2px 9px;border-radius:6px;font-size:.78rem;font-weight:600;">{{ $w->jml_pegawai }}</span></td>
                                <td class="text-center"><span style="background:#E3F4EA;color:#2E9E68;padding:2px 9px;border-radius:6px;font-size:.78rem;font-weight:600;">{{ $w->jml_barang }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('warung.edit', $w->id_warung) }}" style="background:#DDEFE4;color:#1F7A4D;padding:3px 10px;border-radius:7px;font-size:.75rem;font-weight:600;">Edit</a>
                                    <a href="#" onclick="hapusWarung('{{ route('warung.destroy', $w->id_warung) }}','{{ addslashes($w->nama_warung) }}')"
                                       style="background:#fff0f0;color:var(--danger);padding:3px 10px;border-radius:7px;font-size:.75rem;font-weight:600;margin-left:4px;">Hapus</a>
                                    <form id="form-warung-{{ $w->id_warung }}" method="POST" action="{{ route('warung.destroy', $w->id_warung) }}" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function hapusWarung(action, nama) {
    var id = action.split('/').slice(-1)[0];
    if (confirm('Yakin hapus warung "' + nama + '"?\nSemua data barang di warung ini juga akan terhapus!')) {
        document.getElementById('form-warung-' + id).submit();
    }
}
</script>
@endpush
