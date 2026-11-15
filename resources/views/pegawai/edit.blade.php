@extends('layouts.app')
@section('title','Edit Pegawai')
@section('page-title')
<i class="fas fa-edit me-2" style="color:var(--primary);"></i>Edit Pegawai
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Edit: <span style="color:var(--primary);">{{ $pegawai->nama }}</span></div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('pegawai.update', $pegawai->id_user) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $pegawai->nama) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $pegawai->username) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                        <small style="color:#7B857F;font-size:.75rem;">Minimal 6 karakter jika diisi</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Warung</label>
                        <select name="id_warung" class="form-select">
                            @foreach($warungList as $w)
                                <option value="{{ $w->id_warung }}" {{ $pegawai->id_warung == $w->id_warung ? 'selected' : '' }}>
                                    {{ $w->nama_warung }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="border-radius:8px;padding:8px 22px;">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
