@extends('layouts.app')
@section('title','Dashboard')
@section('page-title')
    <i class="fas fa-tachometer-alt"></i> Dashboard — {{ $namaWarungFilter }}
@endsection

@section('content')

@if(request('error'))
    <div class="alert alert-warning py-2 mb-3" style="font-size:.83rem;border-radius:10px;">
        <i class="fas fa-exclamation-triangle me-1"></i>Akses ditolak. Halaman ini khusus pemilik.
    </div>
@endif

{{-- Filter warung --}}
@include('partials.filter_warung')

{{-- Stat cards -- identik dengan native --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card-new">
            <div class="sc-icon" style="background:linear-gradient(135deg,#6C63FF,#9b5de5);"><i class="fas fa-wallet"></i></div>
            <div class="sc-body">
                <div class="sc-val">Rp {{ number_format($pendapatanHari,0,',','.') }}</div>
                <div class="sc-lbl">Pendapatan Hari Ini</div>
            </div>
            <div class="sc-trend up"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card-new">
            <div class="sc-icon" style="background:linear-gradient(135deg,#00b894,#00cec9);"><i class="fas fa-chart-line"></i></div>
            <div class="sc-body">
                <div class="sc-val">Rp {{ number_format($pendapatanBulan,0,',','.') }}</div>
                <div class="sc-lbl">Pendapatan Bulan Ini</div>
            </div>
            <div class="sc-trend up"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card-new">
            <div class="sc-icon" style="background:linear-gradient(135deg,#f7b731,#fd9644);"><i class="fas fa-receipt"></i></div>
            <div class="sc-body">
                <div class="sc-val">{{ $totalTransaksi }}</div>
                <div class="sc-lbl">Transaksi Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card-new {{ $hampirHabis > 0 ? 'danger' : '' }}">
            <div class="sc-icon" style="background:linear-gradient(135deg,#e17055,#d63031);"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="sc-body">
                <div class="sc-val {{ $hampirHabis > 0 ? 'text-danger' : '' }}">{{ $hampirHabis }}</div>
                <div class="sc-lbl">Stok Hampir Habis</div>
            </div>
            @if($hampirHabis > 0)
                <a href="{{ route('barang.index', ['filter'=>'hampir_habis','warung'=>$warungParam]) }}" class="sc-trend warn">
                    <i class="fas fa-arrow-right"></i>
                </a>
            @endif
        </div>
    </div>
</div>

@if($hampirHabis > 0)
    <div class="alert-stok mb-4">
        <i class="fas fa-exclamation-triangle me-1" style="color:#f39c12;"></i>
        <strong>{{ $hampirHabis }} barang</strong> memiliki stok kurang dari {{ \App\Http\Controllers\BaseController::STOK_MIN }}.
        <a href="{{ route('barang.index', ['filter'=>'hampir_habis','warung'=>$warungParam]) }}"
           class="ms-2" style="color:var(--primary);font-weight:600;">Lihat detail →</a>
    </div>
@endif

<div class="row g-3 mb-3">
    {{-- Grafik 7 hari --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-area me-1" style="color:var(--primary);"></i>Grafik Penjualan 7 Hari</span>
                <a href="{{ route('laporan.index', ['warung'=>$warungParam]) }}"
                   class="btn btn-sm btn-outline-primary py-0" style="font-size:.75rem;">Laporan Lengkap</a>
            </div>
            <div class="card-body">
                <canvas id="grafikPenjualan" height="90"></canvas>
            </div>
        </div>
    </div>
    {{-- Pie metode pembayaran --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-credit-card me-1" style="color:#6C63FF;"></i>Metode Pembayaran Hari Ini</div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center gap-2" style="padding:14px;">
                @if($pieCash + $pieQris > 0)
                    <div style="display:flex;gap:12px;margin-bottom:4px;">
                        <div style="text-align:center;">
                            <div style="font-size:1.3rem;font-weight:800;color:#6C63FF;">{{ $pieCash }}</div>
                            <div style="font-size:.7rem;color:#7a809a;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Cash</div>
                        </div>
                        <div style="width:1px;background:#e4e7f0;"></div>
                        <div style="text-align:center;">
                            <div style="font-size:1.3rem;font-weight:800;color:#00b894;">{{ $pieQris }}</div>
                            <div style="font-size:.7rem;color:#7a809a;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">QRIS</div>
                        </div>
                    </div>
                    <div style="width:110px;height:110px;position:relative;flex-shrink:0;">
                        <canvas id="grafikMetode"></canvas>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                            <div style="font-size:.65rem;color:#7a809a;font-weight:600;line-height:1.2;">Total</div>
                            <div style="font-size:.9rem;font-weight:800;color:#2d3748;line-height:1.2;">{{ $pieCash + $pieQris }}</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3" style="font-size:.78rem;margin-top:2px;">
                        <span style="display:flex;align-items:center;gap:5px;"><span style="width:9px;height:9px;border-radius:50%;background:#6C63FF;display:inline-block;flex-shrink:0;"></span>Cash</span>
                        <span style="display:flex;align-items:center;gap:5px;"><span style="width:9px;height:9px;border-radius:50%;background:#00b894;display:inline-block;flex-shrink:0;"></span>QRIS</span>
                    </div>
                @else
                    <div class="text-center text-muted" style="font-size:.83rem;">
                        <i class="fas fa-chart-pie fa-2x mb-2 d-block" style="color:#c0c5d8;"></i>
                        Belum ada transaksi hari ini
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    {{-- Transaksi terbaru --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history me-1" style="color:var(--primary);"></i>Transaksi Terbaru</span>
                <a href="{{ route('penjualan.index', ['warung'=>$warungParam]) }}"
                   class="btn btn-sm btn-outline-primary py-0" style="font-size:.75rem;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead><tr><th>#</th><th>Waktu</th><th>Kasir</th><th>Metode</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        @forelse($transaksiBaru as $t)
                            <tr>
                                <td><span class="badge-id">#{{ $t->id_transaksi }}</span></td>
                                <td>{{ $t->tanggal->format('d/m H:i') }}</td>
                                <td>{{ $t->user->nama ?? '-' }}</td>
                                <td>
                                    @if($t->metode_pembayaran === 'qris')
                                        <span style="background:#e0f7f3;color:#00b894;padding:2px 7px;border-radius:5px;font-size:.72rem;font-weight:600;">QRIS</span>
                                    @else
                                        <span style="background:#e8e6ff;color:#6C63FF;padding:2px 7px;border-radius:5px;font-size:.72rem;font-weight:600;">Cash</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold" style="color:var(--primary);">Rp {{ number_format($t->total,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Stok kritis --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-box-open me-1" style="color:#e17055;"></i>Stok Kritis</div>
            <div class="card-body p-0">
                @if($listHampir->count())
                    @foreach($listHampir as $b)
                        <div class="stok-item">
                            <div class="stok-nama">{{ $b->nama_barang }}</div>
                            <div class="stok-badge {{ $b->stok <= 3 ? 'critical' : '' }}">{{ $b->stok }} {{ $b->satuan }}</div>
                        </div>
                    @endforeach
                    <div style="padding:8px 16px;border-top:1px solid var(--border);">
                        <a href="{{ route('barang.index', ['filter'=>'hampir_habis','warung'=>$warungParam]) }}"
                           style="font-size:.78rem;color:var(--primary);font-weight:600;">Lihat semua barang kritis →</a>
                    </div>
                @else
                    <div class="p-4 text-center text-muted" style="font-size:.83rem;">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#00b894;"></i>Semua stok aman
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Shortcut --}}
<div class="row g-3 mt-1">
    <div class="col-md-6">
        <a href="{{ route('kasir.index') }}" class="text-decoration-none">
            <div class="shortcut-card" style="border-color:var(--primary);">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#6C63FF,#9b5de5);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-cash-register" style="color:#fff;font-size:1.1rem;"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;color:var(--text);font-size:.9rem;">Buka Kasir</div>
                    <div style="font-size:.78rem;color:#7a809a;">Mulai transaksi penjualan</div>
                </div>
                <i class="fas fa-arrow-right" style="color:#c0c5d8;font-size:.85rem;"></i>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('barang.tambah-stok') }}" class="text-decoration-none">
            <div class="shortcut-card" style="border-color:#00b894;">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#00b894,#00cec9);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-plus-circle" style="color:#fff;font-size:1.1rem;"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;color:var(--text);font-size:.9rem;">Tambah Stok</div>
                    <div style="font-size:.78rem;color:#7a809a;">Input stok barang masuk</div>
                </div>
                <i class="fas fa-arrow-right" style="color:#c0c5d8;font-size:.85rem;"></i>
            </div>
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart Penjualan 7 Hari
    var ctxLine = document.getElementById('grafikPenjualan');
    if (ctxLine) {
        var ctx  = ctxLine.getContext('2d');
        var grad = ctx.createLinearGradient(0, 0, 0, 200);
        grad.addColorStop(0, 'rgba(108,99,255,0.35)');
        grad.addColorStop(1, 'rgba(108,99,255,0.0)');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelGrafik) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($valueGrafik) !!},
                    borderColor: '#6C63FF',
                    backgroundColor: grad,
                    fill: true, tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6C63FF',
                    pointBorderWidth: 2, pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' },
                         ticks: { callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'k', font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // Chart Metode Pembayaran
    var ctxPie = document.getElementById('grafikMetode');
    if (ctxPie) {
        new Chart(ctxPie.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Cash', 'QRIS'],
                datasets: [{ data: [{{ $pieCash }}, {{ $pieQris }}], backgroundColor: ['#6C63FF', '#00b894'], borderWidth: 0, hoverOffset: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true, cutout: '68%',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + ' transaksi' } } },
                animation: { duration: 600 }
            }
        });
    }
});
</script>
@endpush
