@extends('layouts.app')
@section('title','Edit Warung')
@section('page-title')
<i class="fas fa-edit me-2" style="color:var(--primary);"></i>Edit Warung
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Edit: <span style="color:var(--primary);">{{ $warung->nama_warung }}</span></div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('warung.update', $warung->id_warung) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Nama Warung *</label>
                        <input type="text" name="nama_warung" class="form-control" value="{{ old('nama_warung', $warung->nama_warung) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $warung->alamat) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $warung->no_hp) }}">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="border-radius:8px;padding:8px 22px;">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('warung.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
