@extends('layouts.app')
@section('title','Laporan')
@section('page-title')
<i class="fas fa-chart-bar me-2" style="color:var(--primary);"></i>Laporan
@endsection

@push('styles')
<style>
@media print {
    .sidebar,.topbar,.no-print { display:none!important; }
    .main-content { margin-left:0!important;padding:0!important; }
    .card { box-shadow:none!important;border:none!important; }
}
</style>
@endpush

@section('content')

@include('partials.filter_warung')

{{-- Toolbar --}}
<div class="card mb-3 no-print">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <input type="hidden" name="warung" value="{{ $warungParam }}">
            <div class="d-flex gap-1 flex-wrap">
                @foreach($judulMap as $k => $v)
                    <button type="submit" name="jenis" value="{{ $k }}"
                            class="btn btn-sm {{ $jenis===$k ? 'btn-primary' : 'btn-outline-secondary' }}"
                            style="border-radius:8px;font-size:.78rem;">
                        {{ $v }}
                    </button>
                @endforeach
            </div>

            <div class="d-flex align-items-center gap-1 ms-2" id="filterTanggal"
                 style="display:{{ in_array($jenis,['penjualan','terlaris']) ? 'flex' : 'none' }}!important;">
                <label style="font-size:.8rem;color:#7a809a;">Dari:</label>
                <input type="date" name="dari" class="form-control form-control-sm" style="width:135px;border-radius:8px;" value="{{ $dari }}">
                <label style="font-size:.8rem;color:#7a809a;">—</label>
                <input type="date" name="sampai" class="form-control form-control-sm" style="width:135px;border-radius:8px;" value="{{ $sampai }}">
            </div>

            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('laporan.export', array_merge(request()->query(), ['jenis'=>$jenis,'dari'=>$dari,'sampai'=>$sampai,'warung'=>$warungParam])) }}"
                   class="btn btn-sm btn-outline-success" style="border-radius:8px;">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Cetak / PDF
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Isi laporan --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-table me-1" style="color:var(--primary);"></i>{{ $judulMap[$jenis] ?? $jenis }}
            @if(in_array($jenis, ['penjualan','terlaris']))
                <small style="color:#7a809a;font-weight:400;"> &nbsp;{{ $dari }} — {{ $sampai }}</small>
            @endif
        </span>
        @if($jenis === 'penjualan' && $totalLaporan > 0)
            <span style="font-size:.83rem;">Total: <strong style="color:var(--primary);">Rp {{ number_format($totalLaporan,0,',','.') }}</strong></span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">

            @if($jenis === 'penjualan')
                <table class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead>
                        <tr>
                            <th>#</th><th>Waktu</th>
                            @if($semuaWarung)<th>Warung</th>@endif
                            <th>Kasir</th><th>Metode</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Bayar</th>
                            <th class="text-end">Kembalian</th>
                            <th class="no-print"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $i => $t)
                            <tr>
                                <td style="color:#7a809a;">{{ $i+1 }}</td>
                                <td>{{ $t->tanggal->format('d/m/Y H:i') }}</td>
                                @if($semuaWarung)
                                    <td><span style="background:#f0f2f8;color:#7a809a;padding:2px 7px;border-radius:5px;font-size:.75rem;">{{ $t->warung->nama_warung ?? '-' }}</span></td>
                                @endif
                                <td>{{ $t->user->nama ?? '-' }}</td>
                                <td>
                                    @if($t->metode_pembayaran === 'qris')
                                        <span style="background:#e0f7f3;color:#00b894;padding:2px 7px;border-radius:5px;font-size:.73rem;font-weight:600;"><i class="fas fa-qrcode me-1"></i>QRIS</span>
                                    @else
                                        <span style="background:#e8e6ff;color:#6C63FF;padding:2px 7px;border-radius:5px;font-size:.73rem;font-weight:600;"><i class="fas fa-money-bill-wave me-1"></i>Cash</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold" style="color:var(--primary);">Rp {{ number_format($t->total,0,',','.') }}</td>
                                <td class="text-end">Rp {{ number_format($t->bayar,0,',','.') }}</td>
                                <td class="text-end" style="color:#00b894;">Rp {{ number_format($t->kembalian,0,',','.') }}</td>
                                <td class="no-print">
                                    <a href="{{ route('penjualan.detail', $t->id_transaksi) }}"
                                       style="background:#e8e6ff;color:#6C63FF;padding:3px 8px;border-radius:6px;font-size:.73rem;">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $semuaWarung ? 9 : 8 }}" class="text-center py-5">
                                <i class="fas fa-receipt fa-2x mb-2 d-block" style="color:#c0c5d8;"></i>
                                <span style="color:#7a809a;">Tidak ada data penjualan pada periode ini</span>
                            </td></tr>
                        @endforelse
                    </tbody>
                    @if($dataLaporan->count() && $totalLaporan > 0)
                        <tfoot>
                            <tr style="background:#fafbff;">
                                <td colspan="{{ $semuaWarung ? 5 : 4 }}" style="font-weight:700;">Total</td>
                                <td class="text-end fw-bold" style="color:var(--primary);">Rp {{ number_format($totalLaporan,0,',','.') }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>

            @elseif($jenis === 'terlaris')
                <table class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead>
                        <tr><th>#</th><th>Nama Barang</th>@if($semuaWarung)<th>Warung</th>@endif<th class="text-center">Qty Terjual</th><th class="text-end">Total Pendapatan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $i => $r)
                            <tr>
                                <td>
                                    @if($i < 3)
                                        <span style="width:22px;height:22px;background:{{ ['#FFD700','#C0C0C0','#CD7F32'][$i] }};border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:800;color:#fff;">{{ $i+1 }}</span>
                                    @else
                                        <span style="color:#7a809a;">{{ $i+1 }}</span>
                                    @endif
                                </td>
                                <td style="font-weight:600;">{{ $r->nama_barang }}</td>
                                @if($semuaWarung)<td><span style="background:#e8e6ff;color:#6C63FF;padding:2px 7px;border-radius:5px;font-size:.75rem;">{{ $r->nama_warung }}</span></td>@endif
                                <td class="text-center"><span style="background:#e0f7f3;color:#00b894;padding:3px 10px;border-radius:6px;font-size:.8rem;font-weight:700;">{{ number_format($r->total_qty) }}</span></td>
                                <td class="text-end fw-semibold" style="color:var(--primary);">Rp {{ number_format($r->total_pendapatan,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $semuaWarung ? 5 : 4 }}" class="text-center py-5 text-muted">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($jenis === 'stok' || $jenis === 'hampir_habis')
                <table class="table table-hover mb-0" style="font-size:.83rem;">
                    <thead>
                        <tr><th>#</th><th>Barcode</th><th>Nama Barang</th><th>Kategori</th>@if($semuaWarung)<th>Warung</th>@endif<th class="text-center">Stok</th><th>Satuan</th><th class="text-end">Harga Jual</th></tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $i => $b)
                            <tr>
                                <td style="color:#7a809a;">{{ $i+1 }}</td>
                                <td><code style="background:#f0eeff;color:#6C63FF;padding:2px 6px;border-radius:5px;font-size:.75rem;">{{ $b->barcode ?: '—' }}</code></td>
                                <td style="font-weight:600;">{{ $b->nama_barang }}</td>
                                <td><span style="background:#f0f2f8;color:#7a809a;padding:2px 7px;border-radius:5px;font-size:.75rem;">{{ $b->kategori->nama_kategori ?? '—' }}</span></td>
                                @if($semuaWarung)<td><span style="background:#e8e6ff;color:#6C63FF;padding:2px 7px;border-radius:5px;font-size:.75rem;">{{ $b->warung->nama_warung ?? '—' }}</span></td>@endif
                                @php $stokMin = \App\Http\Controllers\BaseController::STOK_MIN; @endphp
                                <td class="text-center">
                                    @if($b->stok < $stokMin)
                                        <span style="background:#fff0f0;color:var(--danger);padding:3px 9px;border-radius:6px;font-size:.8rem;font-weight:700;">{{ $b->stok }}</span>
                                    @elseif($b->stok < $stokMin * 2)
                                        <span style="background:#fff9e6;color:#e67e22;padding:3px 9px;border-radius:6px;font-size:.8rem;font-weight:700;">{{ $b->stok }}</span>
                                    @else
                                        <span style="background:#e0f7f3;color:#00b894;padding:3px 9px;border-radius:6px;font-size:.8rem;font-weight:700;">{{ $b->stok }}</span>
                                    @endif
                                </td>
                                <td>{{ $b->satuan }}</td>
                                <td class="text-end">Rp {{ number_format($b->harga_jual,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $semuaWarung ? 8 : 7 }}" class="text-center py-5 text-muted">
                                @if($jenis === 'hampir_habis')
                                    <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#00b894;"></i>Semua stok masih aman!
                                @else
                                    Tidak ada data barang
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif

        </div>
    </div>
</div>
@endsection
