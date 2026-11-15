@extends('layouts.app')
@section('title','Manajemen Pegawai')
@section('page-title')
<i class="fas fa-users me-2" style="color:var(--primary);"></i>Manajemen Pegawai
@endsection

@section('content')
@php
    $msgMap = ['tambah'=>'Pegawai berhasil ditambahkan','edit'=>'Data pegawai diupdate','hapus'=>'Pegawai berhasil dihapus'];
@endphp
@if(session('msg') && isset($msgMap[session('msg')]))
    <div class="alert alert-success alert-auto py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
        <i class="fas fa-check-circle me-1"></i>{{ $msgMap[session('msg')] }}
    </div>
@endif

@include('partials.filter_warung')

<div class="row g-3">
    {{-- Form tambah --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Tambah Pegawai Baru</div>
            <div class="card-body">
                @if($error)
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">{{ $error }}</div>
                @endif
                <form method="POST" action="{{ route('pegawai.store') }}">
                    @csrf
                    <input type="hidden" name="action" value="tambah">
                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Password <small style="color:#7a809a;">(min 6)</small></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Warung</label>
                        <select name="id_warung" class="form-select">
                            @foreach($warungList as $w)
                                <option value="{{ $w->id_warung }}"
                                    {{ (!$semuaWarung && $w->id_warung == $idWarungAktif) ? 'selected' : '' }}>
                                    {{ $w->nama_warung }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:8px;">
                        <i class="fas fa-plus me-1"></i>Tambah Pegawai
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel pegawai --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Daftar Pegawai</span>
                <span class="badge bg-secondary" style="border-radius:6px;">{{ $pegawai->count() }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>#</th><th>Nama</th><th>Username</th><th>Warung</th><th class="text-center">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pegawai as $i => $p)
                            <tr>
                                <td style="color:#7a809a;">{{ $i+1 }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="width:30px;height:30px;background:linear-gradient(135deg,#6C63FF,#9b5de5);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0;">
                                            {{ strtoupper(substr($p->nama,0,1)) }}
                                        </div>
                                        <span style="font-weight:600;">{{ $p->nama }}</span>
                                    </div>
                                </td>
                                <td><code style="background:#f0eeff;color:#6C63FF;padding:2px 7px;border-radius:5px;font-size:.78rem;">{{ $p->username }}</code></td>
                                <td>
                                    <span style="background:{{ $p->id_warung == auth()->user()->id_warung ? '#e8e6ff;color:#6C63FF' : '#f0f2f8;color:#7a809a' }};padding:2px 8px;border-radius:6px;font-size:.78rem;">
                                        {{ $p->warung->nama_warung ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pegawai.edit', $p->id_user) }}"
                                       style="background:#e8e6ff;color:#6C63FF;padding:3px 10px;border-radius:7px;font-size:.75rem;font-weight:600;">Edit</a>
                                    <a href="#" onclick="hapusPegawai('{{ route('pegawai.destroy', $p->id_user) }}','{{ addslashes($p->nama) }}')"
                                       style="background:#fff0f0;color:var(--danger);padding:3px 10px;border-radius:7px;font-size:.75rem;font-weight:600;margin-left:4px;">Hapus</a>
                                    <form id="form-pegawai-{{ $p->id_user }}" method="POST" action="{{ route('pegawai.destroy', $p->id_user) }}" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5">
                                <i class="fas fa-users fa-2x mb-2 d-block" style="color:#c0c5d8;"></i>
                                <span style="color:#7a809a;">Belum ada pegawai{{ !$semuaWarung ? ' di warung ini' : '' }}</span>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function hapusPegawai(action, nama) {
    var id = action.split('/').slice(-1)[0];
    if (confirm('Yakin hapus pegawai "' + nama + '"?')) {
        document.getElementById('form-pegawai-' + id).submit();
    }
}
</script>
@endpush
