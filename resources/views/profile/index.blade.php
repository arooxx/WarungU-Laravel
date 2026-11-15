@extends('layouts.app')
@section('title','Profile Saya')
@section('page-title')
<i class="fas fa-user-circle me-2" style="color:var(--primary);"></i>Profile Saya
@endsection

@push('styles')
<style>
    .profile-hero {
        background: {{ auth()->user()->isPemilik() ? '#1F7A4D' : '#2E9E68' }};
        border-radius: 20px;
        padding: 36px 28px 80px;
        position: relative;
        overflow: hidden;
    }
    .profile-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }
    .profile-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: -30px;
        width: 240px; height: 240px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }
    .hero-avatar {
        width: 90px; height: 90px;
        background: rgba(255,255,255,.25);
        border: 4px solid rgba(255,255,255,.5);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem; font-weight: 800; color: #fff;
        margin: 0 auto 14px;
        position: relative; z-index: 1;
        box-shadow: 0 8px 24px rgba(0,0,0,.15);
    }
    .hero-name     { color:#fff; font-size:1.3rem; font-weight:700; text-align:center; position:relative; z-index:1; }
    .hero-username { color:rgba(255,255,255,.8); font-size:.84rem; text-align:center; position:relative; z-index:1; }
    .hero-badge {
        display:inline-block;
        background:rgba(255,255,255,.25); color:#fff;
        font-size:.72rem; font-weight:700;
        padding:3px 14px; border-radius:20px; margin-top:8px;
        letter-spacing:.05em; text-transform:uppercase;
        position:relative; z-index:1;
    }
    .profile-card-wrap {
        background:#fff; border-radius:20px;
        box-shadow:0 4px 24px rgba(31,122,77,.08);
        margin-top:-48px; padding:20px;
        position:relative; z-index:2;
    }
    .info-row {
        display:flex; align-items:center; gap:12px;
        padding:12px 0; border-bottom:1px dashed #EEF1EE; font-size:.85rem;
    }
    .info-row:last-child { border-bottom:none; }
    .info-icon {
        width:34px; height:34px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:.85rem; flex-shrink:0;
    }
    .info-label { color:#7B857F; font-size:.78rem; margin-bottom:1px; }
    .info-value { font-weight:700; color:#14211A; }
    .form-section-title {
        font-size:.72rem; font-weight:800; color:#7B857F;
        letter-spacing:.08em; text-transform:uppercase;
        display:flex; align-items:center; gap:8px; margin-bottom:18px;
    }
    .form-section-title::after { content:''; flex:1; height:1px; background:#E3E7E3; }
    .custom-input {
        border:2px solid #E3E7E3; border-radius:10px;
        padding:10px 14px; font-size:.88rem; transition:border-color .2s;
    }
    .custom-input:focus { border-color:#1F7A4D; box-shadow:0 0 0 3px rgba(31,122,77,.12); }
    .btn-save {
        background:#1F7A4D;
        color:#fff; border:none; border-radius:10px;
        padding:10px 24px; font-weight:700; font-size:.88rem;
    }
    .btn-save:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(31,122,77,.35); color:#fff; }
    .btn-pw {
        background:#E8920F;
        color:#fff; border:none; border-radius:10px;
        padding:10px 24px; font-weight:700; font-size:.88rem;
    }
    .btn-pw:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(243,156,18,.35); color:#fff; }
    .btn-un {
        background:#2E9E68;
        color:#fff; border:none; border-radius:10px;
        padding:10px 24px; font-weight:700; font-size:.88rem;
    }
    .btn-un:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(0,184,148,.35); color:#fff; }
    .pw-strength { height:4px; border-radius:2px; transition:all .3s; }
    .input-icon-wrap { position:relative; }
    .input-icon-wrap .form-icon { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#AEB8B2; font-size:.85rem; }
    .input-icon-wrap .custom-input { padding-left:36px; }
    .input-icon-wrap .toggle-pw { position:absolute; right:13px; top:50%; transform:translateY(-50%); color:#AEB8B2; cursor:pointer; font-size:.85rem; }
    .stats-chip { background:#F1F7F3; border-radius:12px; padding:10px 16px; text-align:center; flex:1; }
    .stats-chip .chip-val { font-size:1.2rem; font-weight:800; color:{{ auth()->user()->isPemilik() ? '#1F7A4D' : '#2E9E68' }}; }
    .stats-chip .chip-lbl { font-size:.72rem; color:#7B857F; margin-top:2px; }
</style>
@endpush

@section('content')
@php
    $u         = $user;
    $initial   = strtoupper(substr($u->nama, 0, 1));
    $isPemilik = $u->isPemilik();
    $roleColor = $isPemilik ? '#1F7A4D' : '#2E9E68';
@endphp

<div class="row g-4">

    {{-- ===== Kolom Kiri ===== --}}
    <div class="col-lg-4">
        <div class="profile-hero text-center">
            <div class="hero-avatar">{{ $initial }}</div>
            <div class="hero-name">{{ $u->nama }}</div>
            <div class="hero-username">&#64;{{ $u->username }}</div>
            <div>
                <span class="hero-badge">
                    <i class="fas fa-{{ $isPemilik ? 'crown' : 'user' }} me-1"></i>{{ ucfirst($u->role) }}
                </span>
            </div>
        </div>

        <div class="profile-card-wrap">
            <div class="d-flex gap-2 mb-4">
                <div class="stats-chip">
                    <div class="chip-val"><i class="fas fa-store"></i></div>
                    <div class="chip-lbl">{{ $u->warung->nama_warung ?? 'N/A' }}</div>
                </div>
                <div class="stats-chip">
                    <div class="chip-val"><i class="fas fa-{{ $isPemilik ? 'crown' : 'user-tag' }}"></i></div>
                    <div class="chip-lbl">{{ ucfirst($u->role) }}</div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-icon" style="background:#DDEFE4;">
                    <i class="fas fa-user" style="color:#1F7A4D;"></i>
                </div>
                <div>
                    <div class="info-label">Username</div>
                    <div class="info-value">&#64;{{ $u->username }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-icon" style="background:#E3F4EA;">
                    <i class="fas fa-store" style="color:#2E9E68;"></i>
                </div>
                <div>
                    <div class="info-label">Warung</div>
                    <div class="info-value">{{ $u->warung->nama_warung ?? '—' }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-icon" style="background:#fff0e0;">
                    <i class="fas fa-id-badge" style="color:#e67e22;"></i>
                </div>
                <div>
                    <div class="info-label">ID Akun</div>
                    <div class="info-value">#{{ $u->id_user }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-icon" style="background:{{ $isPemilik ? '#DDEFE4' : '#E3F4EA' }};">
                    <i class="fas fa-{{ $isPemilik ? 'crown' : 'user-check' }}" style="color:{{ $roleColor }};"></i>
                </div>
                <div>
                    <div class="info-label">Role</div>
                    <div class="info-value" style="color:{{ $roleColor }};">{{ ucfirst($u->role) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Kolom Kanan ===== --}}
    <div class="col-lg-8">

        {{-- Edit Nama --}}
        <div class="card-modern p-4 mb-4">
            <div class="form-section-title">
                <i class="fas fa-pen" style="color:#1F7A4D;"></i> Edit Nama
            </div>
            <form method="POST" action="{{ route('profile.nama') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:.84rem;">Nama Lengkap</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-user form-icon"></i>
                        <input type="text" name="nama" class="form-control custom-input"
                               value="{{ old('nama', $u->nama) }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Ubah Username --}}
        <div class="card-modern p-4 mb-4">
            <div class="form-section-title">
                <i class="fas fa-at" style="color:#2E9E68;"></i> Ubah Username
            </div>
            <div class="mb-3 p-3" style="background:#F3FAF6;border:1.5px dashed #2E9E68;border-radius:10px;font-size:.8rem;color:#00845e;">
                <i class="fas fa-info-circle me-1"></i>
                Username digunakan untuk login. Wajib memasukkan password untuk konfirmasi.
            </div>
            <form method="POST" action="{{ route('profile.username') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:.84rem;">Username Baru</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-at form-icon"></i>
                        <input type="text" name="username" class="form-control custom-input"
                               value="{{ old('username', $u->username) }}"
                               placeholder="Huruf, angka, underscore (3–30 karakter)"
                               pattern="[a-zA-Z0-9_]{3,30}" required
                               oninput="cekUsername(this.value)">
                    </div>
                    <div id="unLabel" style="font-size:.72rem;margin-top:4px;"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:.84rem;">Konfirmasi Password</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock form-icon"></i>
                        <input type="password" name="password_konfirm_un" id="pwKonfirmUn" class="form-control custom-input"
                               placeholder="Masukkan password Anda" required>
                        <i class="fas fa-eye toggle-pw" onclick="togglePw('pwKonfirmUn',this)"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-un">
                    <i class="fas fa-at me-2"></i>Simpan Username
                </button>
            </form>
        </div>

        {{-- Ganti Password --}}
        <div class="card-modern p-4">
            <div class="form-section-title">
                <i class="fas fa-lock" style="color:#e67e22;"></i> Keamanan Akun
            </div>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:.84rem;">Password Saat Ini</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock form-icon"></i>
                        <input type="password" name="password_lama" id="pwLama" class="form-control custom-input"
                               placeholder="Masukkan password saat ini" required>
                        <i class="fas fa-eye toggle-pw" onclick="togglePw('pwLama',this)"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600;font-size:.84rem;">Password Baru</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-key form-icon"></i>
                        <input type="password" name="password_baru" id="pwBaru" class="form-control custom-input"
                               placeholder="Minimal 6 karakter" required oninput="cekKekuatan(this.value)">
                        <i class="fas fa-eye toggle-pw" onclick="togglePw('pwBaru',this)"></i>
                    </div>
                    <div class="mt-2">
                        <div class="pw-strength" id="pwStrengthBar" style="background:#E3E7E3;width:0%;"></div>
                        <div id="pwStrengthLabel" style="font-size:.72rem;color:#7B857F;margin-top:4px;"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" style="font-weight:600;font-size:.84rem;">Konfirmasi Password Baru</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-check-circle form-icon"></i>
                        <input type="password" name="password_konfirm" id="pwKonfirm" class="form-control custom-input"
                               placeholder="Ulangi password baru" required oninput="cekMatch()">
                        <i class="fas fa-eye toggle-pw" onclick="togglePw('pwKonfirm',this)"></i>
                    </div>
                    <div id="matchLabel" style="font-size:.72rem;margin-top:4px;"></div>
                </div>
                <button type="submit" class="btn btn-pw">
                    <i class="fas fa-shield-alt me-2"></i>Perbarui Password
                </button>
            </form>
        </div>

    </div>{{-- /col-kanan --}}
</div>
@endsection

@push('scripts')
<script>
function cekUsername(val) {
    var lbl   = document.getElementById('unLabel');
    var valid = /^[a-zA-Z0-9_]{3,30}$/.test(val);
    if (!val) { lbl.textContent = ''; return; }
    lbl.textContent = valid ? '✓ Format username valid' : '✗ Hanya huruf, angka, underscore. Min 3, maks 30 karakter.';
    lbl.style.color = valid ? '#2E9E68' : '#FF5E57';
}
function togglePw(id, icon) {
    var input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}
function cekKekuatan(pw) {
    var bar = document.getElementById('pwStrengthBar');
    var lbl = document.getElementById('pwStrengthLabel');
    var s = 0;
    if (pw.length >= 6)  s++;
    if (pw.length >= 10) s++;
    if (/[A-Z]/.test(pw)) s++;
    if (/[0-9]/.test(pw)) s++;
    if (/[^A-Za-z0-9]/.test(pw)) s++;
    var c = [
        {w:'0%',  bg:'#E3E7E3', txt:''},
        {w:'25%', bg:'#FF5E57', txt:'Lemah'},
        {w:'50%', bg:'#f39c12', txt:'Sedang'},
        {w:'75%', bg:'#2E9E68', txt:'Kuat'},
        {w:'100%',bg:'#1F7A4D', txt:'Sangat Kuat'}
    ][Math.min(s, 4)];
    bar.style.width = c.w; bar.style.background = c.bg;
    lbl.textContent = c.txt; lbl.style.color = c.bg;
}
function cekMatch() {
    var baru    = document.getElementById('pwBaru').value;
    var konfirm = document.getElementById('pwKonfirm').value;
    var lbl     = document.getElementById('matchLabel');
    if (!konfirm) { lbl.textContent = ''; return; }
    lbl.textContent = baru === konfirm ? '✓ Password cocok' : '✗ Password tidak cocok';
    lbl.style.color = baru === konfirm ? '#2E9E68' : '#FF5E57';
}
</script>
@endpush
