@extends('layouts.app')
@section('title','Detail Transaksi #'.$transaksi->id_transaksi)
@section('page-title')
<i class="fas fa-receipt me-2" style="color:var(--primary);"></i>Detail Transaksi #{{ $transaksi->id_transaksi }}
@endsection

@push('styles')
<style>@media print{.sidebar,.topbar,.no-print{display:none!important;}.main-content{margin-left:0!important;padding:0!important;}}</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-invoice me-1"></i>Struk Pembelian</span>
                <button class="btn btn-sm btn-outline-primary no-print" style="border-radius:8px;font-size:.78rem;" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Cetak
                </button>
            </div>
            <div class="card-body" style="font-family:'Courier New',monospace;font-size:.8rem;">
                <div class="text-center mb-3" style="padding-bottom:10px;border-bottom:1px dashed #e4e7f0;">
                    <strong style="font-size:.95rem;">{{ $transaksi->warung->nama_warung ?? 'WarungU' }}</strong><br>
                    <span style="color:#7a809a;">Struk Pembelian</span><br>
                    <span style="color:#7a809a;">No: #{{ $transaksi->id_transaksi }} | {{ $transaksi->tanggal->format('d/m/Y H:i') }}</span><br>
                    <span style="color:#7a809a;">Kasir: {{ $transaksi->user->nama ?? '-' }}</span><br>
                    <span style="font-family:sans-serif;">
                        @if($transaksi->metode_pembayaran === 'qris')
                            <span style="background:#e0f7f3;color:#00b894;padding:2px 8px;border-radius:5px;font-size:.75rem;font-weight:600;">
                                <i class="fas fa-qrcode"></i> QRIS
                            </span>
                        @else
                            <span style="background:#e8e6ff;color:#6C63FF;padding:2px 8px;border-radius:5px;font-size:.75rem;font-weight:600;">
                                <i class="fas fa-money-bill-wave"></i> Cash
                            </span>
                        @endif
                    </span>
                </div>

                <table style="width:100%;border-collapse:collapse;">
                    <tbody>
                        @foreach($transaksi->detail as $d)
                            <tr>
                                <td style="padding:3px 0;">
                                    {{ $d->barang->nama_barang ?? '-' }}
                                    <span style="background:{{ $d->tipe_penjualan==='satuan' ? '#e0f7f3;color:#00b894' : '#e8e6ff;color:#6C63FF' }};font-size:.65rem;padding:1px 5px;border-radius:4px;font-family:sans-serif;">
                                        {{ $d->tipe_penjualan }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 0 6px;color:#7a809a;font-size:.75rem;">
                                    &nbsp;&nbsp;{{ $d->qty }} × Rp {{ number_format($d->harga,0,',','.') }} = <strong>Rp {{ number_format($d->subtotal,0,',','.') }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="border-top:1px dashed #e4e7f0;padding-top:10px;margin-top:4px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="color:#7a809a;">Total</span><strong>Rp {{ number_format($transaksi->total,0,',','.') }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="color:#7a809a;">Metode</span>
                        <span style="font-family:sans-serif;font-size:.78rem;font-weight:700;color:{{ $transaksi->metode_pembayaran==='qris' ? '#00b894' : '#6C63FF' }};">
                            {{ strtoupper($transaksi->metode_pembayaran ?? 'cash') }}
                        </span>
                    </div>
                    @if($transaksi->metode_pembayaran !== 'qris')
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span style="color:#7a809a;">Bayar</span><span>Rp {{ number_format($transaksi->bayar,0,',','.') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="color:#7a809a;">Kembalian</span>
                            <span style="color:#00b894;font-weight:700;">Rp {{ number_format($transaksi->kembalian,0,',','.') }}</span>
                        </div>
                    @endif
                </div>

                <div style="border-top:1px dashed #e4e7f0;padding-top:8px;margin-top:8px;text-align:center;color:#7a809a;font-size:.75rem;">
                    Terima kasih sudah berbelanja! 🙏
                </div>
            </div>
        </div>
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary btn-sm mt-2 no-print" style="border-radius:8px;">← Kembali</a>
    </div>
</div>
@endsection
