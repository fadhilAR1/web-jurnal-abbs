@extends('layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
<style>
    /* Ensure dashboard tiles render correctly on initial load */
    .summary-tile {
        border-radius: 12px;
        padding: 18px;
        background: white;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.12s ease;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .summary-tile:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.12s ease;
    }
    
    .card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    
    .action-btn {
        transition: transform 0.12s ease !important;
    }
    
    .action-btn:hover {
        transform: scale(1.03);
    }
    
    /* Ensure row and column layouts display properly */
    .row {
        display: flex !important;
        flex-wrap: wrap !important;
    }
    
    .col-md-6, .col-lg-3, .col-12 {
        display: block !important;
    }

    /* Responsive Management Boxes */
    .management-box-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: center;
        width: 100%;
    }

    @media (max-width: 767px) {
        .management-box {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .management-box-row {
            justify-content: center;
            padding: 0 1rem;
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .management-box {
            flex: 0 0 calc(50% - 0.75rem);
            max-width: calc(50% - 0.75rem);
        }
        
        .management-box-row {
            justify-content: space-around;
        }
    }

    @media (min-width: 992px) {
        .management-box {
            flex: 0 0 calc(25% - 0.75rem);
            max-width: calc(25% - 0.75rem);
            min-width: 200px;
        }

        .management-box-row {
            justify-content: flex-start;
            gap: 1.5rem;
        }
    }
    /* Ensure summary tiles (both Jurnal and Kelola) share same visual size on desktop */
    @media (min-width: 992px) {
        .summary-tile {
            min-height: 130px; /* shorter cards on desktop */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .summary-tile .card-body {
            padding: 14px;
        }
    }
    /* Make each management-box and its card stretch to equal height */
    .management-box {
        display: flex;
        align-items: stretch;
    }

    .management-box .card {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
    }

    .management-box .card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.75rem;
    }
    /* Limit the overall width of the 4-column block so it doesn't collide with following sections */
    .management-grid {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    /* Keep Kelola buttons aligned like jurnal "Lihat" buttons */
    .management-box .card .card-body .btn {
        align-self: flex-start;
    }
    /* Slightly reduce vertical spacing so Kelola cards sit closer under Jurnal without overlapping
       and ensure the whole grid doesn't collide with the Aktivitas Terbaru section */
    @media (min-width: 992px) {
        .management-grid {
            margin-bottom: 1rem; /* smaller gap before Aktivitas Terbaru */
        }

        .management-grid .summary-tile.mb-3 {
            margin-bottom: 0.5rem; /* bring Kelola card closer to Jurnal card */
        }

        .management-grid .card.h-100 {
            margin-top: 0; /* ensure no extra top spacing */
        }
    }

    /* Mobile-specific: shorter cards and tighter spacing to avoid overlap */
    @media (max-width: 767px) {
        .summary-tile {
            min-height: 92px;
            padding: 10px;
        }

        .summary-tile .card-body {
            padding: 10px;
        }

        .summary-tile h3 {
            margin-bottom: 0.25rem;
            font-size: 1.15rem;
        }

        .summary-tile p.small {
            margin-bottom: 0.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="mb-1">Dashboard Admin</h2>
                <p class="text-muted mb-0">Pantau semua aktivitas dan kelola sistem</p>
            </div>
            <div>
                <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.95rem;">
                    {{ now()->format('d F Y') }}
                </span>
            </div>
        </div>
        
        <!-- Jurnal row (appear first on mobile) -->
        <div class="management-grid">
        <div class="row mb-3">
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card summary-tile bg-white border-0 jurnal-card">
                    <div class="card-body">
                        <p class="text-muted small mb-2">Jurnal KBM</p>
                        <h3 class="mb-2 text-primary">{{ $stats['jurnalKBMCount'] ?? 0 }}</h3>
                        <small class="text-muted">Hari ini</small>
                        <hr class="my-2">
                        <a href="{{ route('admin.monitoring.kbm') }}" class="btn btn-sm btn-outline-primary action-btn">Lihat</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card summary-tile bg-white border-0 jurnal-card">
                    <div class="card-body">
                        <p class="text-muted small mb-2">Jurnal Ekskul</p>
                        <h3 class="mb-2 text-success">{{ $stats['jurnalEkskulCount'] ?? 0 }}</h3>
                        <small class="text-muted">Hari ini</small>
                        <hr class="my-2">
                        <a href="{{ route('admin.monitoring.ekskul') }}" class="btn btn-sm btn-outline-success action-btn">Lihat</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card summary-tile bg-white border-0 jurnal-card">
                    <div class="card-body">
                        <p class="text-muted small mb-2">Jurnal Piket</p>
                        <h3 class="mb-2 text-warning">{{ $stats['jurnalPiketCount'] ?? 0 }}</h3>
                        <small class="text-muted">Hari ini</small>
                        <hr class="my-2">
                        <a href="{{ route('admin.monitoring.piket') }}" class="btn btn-sm btn-outline-warning action-btn">Lihat</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card summary-tile bg-white border-0 jurnal-card">
                    <div class="card-body">
                        <p class="text-muted small mb-2">Jurnal Kegiatan</p>
                        <h3 class="mb-2 text-info">{{ $stats['jurnalKegiatanCount'] ?? 0 }}</h3>
                        <small class="text-muted">Hari ini</small>
                        <hr class="my-2">
                        <a href="{{ route('admin.monitoring.kegiatan') }}" class="btn btn-sm btn-outline-info action-btn">Lihat</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kelola row (appear after jurnal on mobile) -->
        <div class="row mb-5">
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card h-100 border-0 summary-tile kelola-card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-2">Kelola Pengguna</h5>
                        <p class="card-text text-muted small mb-3">Kelola pengguna sistem dan akun guru.</p>
                        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary action-btn">Buka</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card h-100 border-0 summary-tile kelola-card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-2">Kelola Siswa</h5>
                        <p class="card-text text-muted small mb-3">Kelola data siswa, daftar dan detail.</p>
                        <a href="{{ route('admin.master-siswa.index') }}" class="btn btn-sm btn-outline-secondary action-btn">Buka</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card h-100 border-0 summary-tile kelola-card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-2">Kelola Kelas</h5>
                        <p class="card-text text-muted small mb-3">Kelola data kelas dan siswa tiap kelas.</p>
                        <a href="{{ route('admin.master-kelas.index') }}" class="btn btn-sm btn-outline-success action-btn">Buka</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card h-100 border-0 summary-tile kelola-card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-2">Kelola Mapel</h5>
                        <p class="card-text text-muted small mb-3">Kelola mata pelajaran dan program.</p>
                        <a href="{{ route('admin.master-mapel.index') }}" class="btn btn-sm btn-outline-info action-btn">Buka</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card h-100 border-0 summary-tile kelola-card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-2">Kelola Ekskul</h5>
                        <p class="card-text text-muted small mb-3">Kelola kegiatan ekstrakurikuler dan peserta.</p>
                        <a href="{{ route('admin.master-ekskul.index') }}" class="btn btn-sm btn-outline-warning action-btn">Buka</a>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
                    </div>
                    <div class="card-body">
                        @if($recentActivities->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentActivities as $activity)
                                    @php $isEdited = $activity->updated_at->gt($activity->created_at); @endphp
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    @if($activity instanceof \App\Models\JurnalKBM)
                                                        <i class="bi bi-journal-text text-primary"></i> Jurnal KBM
                                                    @elseif($activity instanceof \App\Models\JurnalEkskul)
                                                        <i class="bi bi-people text-success"></i> Jurnal Ekstrakurikuler
                                                    @elseif($activity instanceof \App\Models\JurnalPiket)
                                                        <i class="bi bi-shield-check text-warning"></i> Jurnal Piket
                                                    @elseif($activity instanceof \App\Models\JurnalKegiatan)
                                                        <i class="bi bi-bookmark text-info"></i> Jurnal Kegiatan
                                                    @endif
                                                    <span class="badge bg-secondary ms-2">{{ $activity->guru?->name ?? 'Tidak diketahui' }}</span>
                                                    @if($isEdited)
                                                        <span class="badge bg-warning text-dark ms-2">Telah diedit</span>
                                                    @endif
                                                </h6>
                                                <p class="mb-1">{{ $activity->materi ?? $activity->kegiatan ?? $activity->nama_kegiatan ?? 'Aktivitas jurnal' }}</p>
                                                <small class="text-muted">{{ $activity->tanggal instanceof \Carbon\Carbon ? $activity->tanggal->format('d/m/Y') : \Carbon\Carbon::parse($activity->tanggal)->format('d/m/Y') }}</small>
                                            </div>
                                            <small class="text-muted ms-2">{{ $activity->updated_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 2.5rem;"></i>
                                <p class="text-muted mt-3 mb-0">Belum ada aktivitas dalam 1 minggu terakhir</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</div>
@endsection