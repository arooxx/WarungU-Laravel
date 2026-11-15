@extends('layouts.app')
@section('title','Edit Kategori')
@section('page-title')
<i class="fas fa-edit me-2" style="color:var(--primary);"></i>Edit Kategori
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Edit: <span style="color:var(--primary);">{{ $kategori->nama_kategori }}</span></div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('kategori.update', $kategori->id_kategori) }}">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Nama Kategori *</label>
                        <input type="text" name="nama_kategori" class="form-control"
                               value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="border-radius:8px;padding:8px 22px;">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
