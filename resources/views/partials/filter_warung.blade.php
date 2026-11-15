@php
    // Aman untuk semua halaman — jika $daftarWarung tidak dikirim dari controller, ambil sendiri
    $filterDaftar   = $daftarWarung   ?? \App\Models\Warung::orderBy('nama_warung')->get();
    $filterSemua    = $semuaWarung    ?? false;
    $filterIdAktif  = $idWarungAktif  ?? 0;
@endphp

@if(auth()->user()->isPemilik())
<div class="filter-warung-bar mb-3">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span style="font-size:.78rem;font-weight:600;color:#7a809a;white-space:nowrap;">
            <i class="fas fa-store me-1"></i>Warung:
        </span>
        @php
            $currentUrl = request()->url();
            $params     = request()->except('warung');
            $qs         = count($params) ? '&' . http_build_query($params) : '';
        @endphp
        <a href="{{ $currentUrl }}?warung=0{{ $qs }}"
           class="filter-warung-btn {{ $filterSemua ? 'active' : '' }}">
            <i class="fas fa-globe me-1"></i>Semua Warung
        </a>
        @foreach($filterDaftar as $w)
            <a href="{{ $currentUrl }}?warung={{ $w->id_warung }}{{ $qs }}"
               class="filter-warung-btn {{ (!$filterSemua && $filterIdAktif == $w->id_warung) ? 'active' : '' }}">
                <i class="fas fa-store me-1"></i>{{ $w->nama_warung }}
            </a>
        @endforeach
    </div>
</div>
@endif
