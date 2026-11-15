<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — WarungU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <div style="width:56px;height:56px;background:linear-gradient(135deg,#6C63FF,#9b5de5);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 8px 20px rgba(108,99,255,.3);">
                <i class="fas fa-user-plus" style="color:#fff;font-size:1.3rem;"></i>
            </div>
            <div class="brand-title">WarungU</div>
            <p style="color:#7a809a;font-size:.83rem;margin-top:4px;">Buat akun pegawai baru</p>
        </div>

        @if($errors->has('register'))
            <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first('register') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" style="font-weight:600;font-size:.84rem;">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Nama lengkap" required>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-weight:600;font-size:.84rem;">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Username login" required>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-weight:600;font-size:.84rem;">Password <small style="color:#7a809a;">(min. 6 karakter)</small></label>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-weight:600;font-size:.84rem;">Warung</label>
                <select name="id_warung" class="form-select" required>
                    <option value="">Pilih warung...</option>
                    @foreach($warungList as $w)
                        <option value="{{ $w->id_warung }}" {{ old('id_warung') == $w->id_warung ? 'selected' : '' }}>
                            {{ $w->nama_warung }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100" style="border-radius:8px;padding:10px;font-weight:700;">
                <i class="fas fa-user-plus me-2"></i>Buat Akun
            </button>
        </form>

        <div class="auth-divider">atau</div>
        <div class="text-center" style="font-size:.83rem;color:#7a809a;">
            Sudah punya akun?
            <a href="{{ route('login') }}" style="color:#6C63FF;font-weight:600;">Login di sini</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
