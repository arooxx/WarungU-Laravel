@extends('layouts.app')
@section('title','Data Penjualan')
@section('page-title')
<i class="fas fa-receipt me-2" style="color:var(--primary);"></i>Data Penjualan
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card-new">
            <div class="sc-icon" style="background:#1F7A4D;"><i class="fas fa-wallet"></i></div>
            <div class="sc-body"><div class="sc-val">Rp {{ number_format($totalPeriode,0,',','.') }}</div><div class="sc-lbl">Total Periode</div></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-new">
            <div class="sc-icon" style="background:#2E9E68;"><i class="fas fa-receipt"></i></div>
            <div class="sc-body"><div class="sc-val">{{ $transaksi->count() }}</div><div class="sc-lbl">Jumlah Transaksi</div></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-new">
            <div class="sc-icon" style="background:#14573A;"><i class="fas fa-money-bill-wave"></i></div>
            <div class="sc-body"><div class="sc-val">Rp {{ number_format($totalCash,0,',','.') }}</div><div class="sc-lbl">Cash ({{ $jmlCash }} transaksi)</div></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-new">
            <div class="sc-icon" style="background:#3FA974;"><i class="fas fa-qrcode"></i></div>
            <div class="sc-body"><div class="sc-val">Rp {{ number_format($totalQris,0,',','.') }}</div><div class="sc-lbl">QRIS ({{ $jmlQris }} transaksi)</div></div>
        </div>
    </div>
</div>

@include('partials.filter_warung')

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <input type="hidden" name="warung" value="{{ $semuaWarung ? '0' : $idWarungAktif }}">
            <div class="d-flex align-items-center gap-1">
                <label style="font-size:.82rem;color:#7B857F;white-space:nowrap;">Dari:</label>
                <input type="date" name="dari" class="form-control form-control-sm" style="width:140px;border-radius:8px;" value="{{ $dari }}">
            </div>
            <div class="d-flex align-items-center gap-1">
                <label style="font-size:.82rem;color:#7B857F;white-space:nowrap;">Sampai:</label>
                <input type="date" name="sampai" class="form-control form-control-sm" style="width:140px;border-radius:8px;" value="{{ $sampai }}">
            </div>
            <div class="d-flex align-items-center gap-1">
                <label style="font-size:.82rem;color:#7B857F;white-space:nowrap;">Metode:</label>
                <select name="metode" class="form-select form-select-sm" style="width:110px;border-radius:8px;font-size:.82rem;">
                    <option value="">Semua</option>
                    <option value="cash" {{ $filterMetode==='cash' ? 'selected':'' }}>Cash</option>
                    <option value="qris" {{ $filterMetode==='qris' ? 'selected':'' }}>QRIS</option>
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;"><i class="fas fa-filter me-1"></i>Filter</button>
            <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Riwayat Transaksi <small style="color:#7B857F;font-weight:400;font-size:.82rem;">{{ $dari }} — {{ $sampai }}</small></span>
        <span class="badge bg-secondary" style="border-radius:6px;">{{ $transaksi->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.83rem;">
                <thead>
                    <tr>
                        <th>#ID</th><th>Waktu</th>
                        @if($semuaWarung)<th>Warung</th>@endif
                        <th>Kasir</th><th>Metode</th>
                        <th class="text-center">Item</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Bayar</th>
                        <th class="text-end">Kembalian</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $t)
                        <tr>
                            <td><span class="badge-id">#{{ $t->id_transaksi }}</span></td>
                            <td>{{ $t->tanggal->format('d/m/Y H:i') }}</td>
                            @if($semuaWarung)
                                <td><span style="background:#EEF1EE;color:#7B857F;padding:2px 8px;border-radius:6px;font-size:.75rem;">{{ $t->warung->nama_warung ?? '-' }}</span></td>
                            @endif
                            <td>{{ $t->user->nama ?? '-' }}</td>
                            <td>
                                @if($t->metode_pembayaran === 'qris')
                                    <span style="background:#D3EEDF;color:#1B7A5A;padding:2px 8px;border-radius:6px;font-size:.75rem;font-weight:600;"><i class="fas fa-qrcode me-1"></i>QRIS</span>
                                @else
                                    <span style="background:#14573A;color:#fff;padding:2px 8px;border-radius:6px;font-size:.75rem;font-weight:600;"><i class="fas fa-money-bill-wave me-1"></i>Cash</span>
                                @endif
                            </td>
                            <td class="text-center"><span style="background:#EEF1EE;color:#7B857F;padding:2px 8px;border-radius:6px;font-size:.78rem;">{{ $t->jml_item }} item</span></td>
                            <td class="text-end fw-bold" style="color:var(--primary);">Rp {{ number_format($t->total,0,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($t->bayar,0,',','.') }}</td>
                            <td class="text-end" style="color:#2E9E68;">Rp {{ number_format($t->kembalian,0,',','.') }}</td>
                            <td>
                                <a href="{{ route('penjualan.detail', $t->id_transaksi) }}"
                                   style="background:#DDEFE4;color:#1F7A4D;padding:3px 10px;border-radius:7px;font-size:.75rem;font-weight:600;">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $semuaWarung ? 10 : 9 }}" class="text-center py-5">
                            <i class="fas fa-receipt fa-2x mb-2 d-block" style="color:#BFC8C2;"></i>
                            <span style="color:#7B857F;">Tidak ada transaksi pada periode ini</span>
                        </td></tr>
                    @endforelse
                </tbody>
                @if($transaksi->count())
                    <tfoot>
                        <tr style="background:#F7F9F7;">
                            <td colspan="{{ $semuaWarung ? 6 : 5 }}" style="font-weight:700;">Total</td>
                            <td class="text-end fw-bold" style="color:var(--primary);">Rp {{ number_format($totalPeriode,0,',','.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
