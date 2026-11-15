<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WarungU') — WarungU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* FIX: Hilangkan semua kedip-kedip dan animasi saat navigasi antar halaman */
        *, *::before, *::after {
            transition: none !important;
            animation-duration: 0s !important;
            animation-delay: 0s !important;
        }
        /* Izinkan hanya toast animasi */
        .toast-msg { animation-duration: .15s !important; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-6px);}to{opacity:1;} }
        /* Hilangkan flash putih saat load halaman */
        html { background: #EDEFEC; }
    </style>
    @stack('styles')
</head>
<body>
<div id="toast-container"></div>

{{-- SIDEBAR --}}
@php $initial = strtoupper(substr(auth()->user()->nama, 0, 1)); @endphp
<div class="sidebar">
    <div class="brand">
        <div class="logo-circle"><i class="fas fa-store"></i></div>
        <div>
            <div class="logo-text">WarungU</div>
            <div class="logo-sub">Kasir & Manajemen Stok</div>
        </div>
    </div>

    <nav>
        <a href="{{ route('kasir.index') }}"     class="{{ request()->routeIs('kasir.*')     ? 'active' : '' }}"><i class="fas fa-cash-register"></i> Kasir</a>
        <a href="{{ route('dashboard') }}"        class="{{ request()->routeIs('dashboard')   ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        @if(auth()->user()->isPemilik())
            <a href="{{ route('barang.index') }}"     class="{{ request()->routeIs('barang.*')    ? 'active' : '' }}"><i class="fas fa-box"></i> Barang</a>
            <a href="{{ route('kategori.index') }}"   class="{{ request()->routeIs('kategori.*')  ? 'active' : '' }}"><i class="fas fa-tags"></i> Kategori</a>
        @endif
        <a href="{{ route('penjualan.index') }}"  class="{{ request()->routeIs('penjualan.*') ? 'active' : '' }}"><i class="fas fa-receipt"></i> Penjualan</a>
        <a href="{{ route('laporan.index') }}"    class="{{ request()->routeIs('laporan.*')   ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Laporan</a>
        <a href="{{ route('profile.index') }}"    class="{{ request()->routeIs('profile.*')   ? 'active' : '' }}"><i class="fas fa-user-circle"></i> Profile</a>

        @if(auth()->user()->isPemilik())
            <div class="nav-section">Admin</div>
            <a href="{{ route('pegawai.index') }}" class="{{ request()->routeIs('pegawai.*') ? 'active' : '' }}"><i class="fas fa-users"></i> Pegawai</a>
            <a href="{{ route('warung.index') }}"  class="{{ request()->routeIs('warung.*')  ? 'active' : '' }}"><i class="fas fa-store-alt"></i> Warung</a>
        @endif
    </nav>

    <div class="user-box">
        <div class="user-avatar">{{ $initial }}</div>
        <div class="user-info-box">
            <div class="user-name">{{ auth()->user()->nama }}</div>
            <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="logout-btn" title="Logout" style="background:none;border:0;padding:0;cursor:pointer;">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </div>
</div>

{{-- TOPBAR — FIX: page-title sekarang ikut judul halaman masing-masing --}}
<div class="topbar">
    <div class="page-title">{!! $__env->yieldContent('page-title', '<i class="fas fa-tachometer-alt"></i> Dashboard') !!}</div>
    <div class="topbar-right"><i class="fas fa-calendar-alt me-1"></i>{{ now()->format('d M Y') }}</div>
</div>

{{-- Flash alerts --}}
@if(session('success'))
    <div class="flash-wrap">
        <div class="alert alert-success alert-auto py-2" style="font-size:.83rem;border-radius:10px;">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
        </div>
    </div>
@endif
@if(session('error_akses'))
    <div class="flash-wrap">
        <div class="alert alert-warning alert-auto py-2" style="font-size:.83rem;border-radius:10px;">
            <i class="fas fa-exclamation-triangle me-1"></i>{{ session('error_akses') }}
        </div>
    </div>
@endif
@if(session('error_msg'))
    <div class="flash-wrap">
        <div class="alert alert-danger alert-auto py-2" style="font-size:.83rem;border-radius:10px;">
            <i class="fas fa-times-circle me-1"></i>{{ session('error_msg') }}
        </div>
    </div>
@endif

{{-- Main content --}}
<div class="main-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
