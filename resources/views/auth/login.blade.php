<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — WarungU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <div style="width:56px;height:56px;background:linear-gradient(135deg,#6C63FF,#9b5de5);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 8px 20px rgba(108,99,255,.3);">
                <i class="fas fa-store" style="color:#fff;font-size:1.4rem;"></i>
            </div>
            <div class="brand-title">WarungU</div>
            <p style="color:#7a809a;font-size:.83rem;margin-top:4px;">Masuk ke akun Anda</p>
        </div>

        @if($errors->has('login'))
            <div class="alert alert-danger py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first('login') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
                <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" style="font-weight:600;font-size:.84rem;">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user" style="font-size:.8rem;"></i></span>
                    <input type="text" name="username" class="form-control"
                           value="{{ old('username') }}"
                           placeholder="Masukkan username" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" style="font-weight:600;font-size:.84rem;">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock" style="font-size:.8rem;"></i></span>
                    <input type="password" name="password" class="form-control"
                           placeholder="Masukkan password" required>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center gap-2" style="font-size:.83rem;">
                <input type="checkbox" name="remember_me" id="remember_me" style="width:16px;height:16px;">
                <label for="remember_me" style="color:#7a809a;margin:0;cursor:pointer;">Ingat saya selama 30 hari</label>
            </div>
            <button type="submit" class="btn btn-primary w-100" style="border-radius:8px;padding:10px;font-weight:700;">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
        </form>

        <div class="auth-divider">atau</div>
        <div class="text-center" style="font-size:.83rem;color:#7a809a;">
            Hubungi admin untuk membuat akun.
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
