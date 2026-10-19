@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="mb-1">Dashboard</h2>
                <p class="text-muted mb-0">Selamat datang, <strong>{{ Auth::user()->name }}</strong></p>
            </div>
            <div>
                <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.95rem;">
                    {{ now()->format('d F Y') }}
                </span>
            </div>
        </div>

        @if(Auth::user()->isGuru())
            <!-- Guru Dashboard Summary Cards -->
            <div class="row mb-5 jurnal-grid-row">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card summary-tile bg-white border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">
                                        Jurnal KBM
                                    </p>
                                    <h3 class="mb-2 text-primary">{{ $jurnalKBMCount ?? 0 }}</h3>
                                    <small class="text-muted">Hari ini</small>
                                </div>
                            </div>
                            <hr class="my-3">
                            <a href="{{ route('jurnal.kbm') }}" class="btn btn-sm btn-outline-primary action-btn">
                                Lihat
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card summary-tile bg-white border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">
                                        Jurnal Ekskul
                                    </p>
                                    <h3 class="mb-2 text-success">{{ $jurnalEkskulCount ?? 0 }}</h3>
                                    <small class="text-muted">Hari ini</small>
                                </div>
                            </div>
                            <hr class="my-3">
                            <a href="{{ route('jurnal.ekskul') }}" class="btn btn-sm btn-outline-success action-btn">
                                Lihat
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card summary-tile bg-white border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">
                                        Jurnal Piket
                                    </p>
                                    <h3 class="mb-2 text-warning">{{ $jurnalPiketCount ?? 0 }}</h3>
                                    <small class="text-muted">Hari ini</small>
                                </div>
                            </div>
                            <hr class="my-3">
                            <a href="{{ route('jurnal.piket') }}" class="btn btn-sm btn-outline-warning action-btn">
                                Lihat
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card summary-tile bg-white border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">
                                        Jurnal Kegiatan
                                    </p>
                                    <h3 class="mb-2 text-info">{{ $jurnalKegiatanCount ?? 0 }}</h3>
                                    <small class="text-muted">Hari ini</small>
                                </div>
                            </div>
                            <hr class="my-3">
                            <a href="{{ route('jurnal.kegiatan') }}" class="btn btn-sm btn-outline-info action-btn">
                                Lihat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentActivities as $activity)
                                @php $isEdited = $activity->updated_at->gt($activity->created_at); @endphp
                                <div class="list-group-item px-0 py-3 border-bottom-subtle">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                @if($activity instanceof \App\Models\JurnalKBM)
                                                    <i class="bi bi-journal-text text-primary"></i> Jurnal KBM
                                                @elseif($activity instanceof \App\Models\JurnalEkskul)
                                                    <i class="bi bi-people text-success"></i> Jurnal Ekskul
                                                @elseif($activity instanceof \App\Models\JurnalPiket)
                                                    <i class="bi bi-shield-check text-warning"></i> Jurnal Piket
                                                @elseif($activity instanceof \App\Models\JurnalKegiatan)
                                                    <i class="bi bi-bookmark text-info"></i> Jurnal Kegiatan
                                                @endif
                                                <span class="badge bg-secondary ms-2">{{ $activity->guru?->name ?? 'Tidak diketahui' }}</span>
                                                @if($isEdited)
                                                    <span class="badge bg-warning text-dark ms-2">Diedit</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1 text-muted">{{ $activity->materi ?? $activity->kegiatan ?? $activity->nama_kegiatan ?? 'Aktivitas jurnal' }}</p>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                {{ $activity->tanggal instanceof \Carbon\Carbon ? $activity->tanggal->format('d/m/Y') : \Carbon\Carbon::parse($activity->tanggal)->format('d/m/Y') }}
                                                <span class="ms-2"><i class="bi bi-clock"></i> {{ $activity->updated_at->diffForHumans() }}</span>
                                            </small>
                                        </div>
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
        @elseif(Auth::user()->isAdmin())
            <!-- Admin Dashboard -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5></i> Kelola Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <p>Kelola pengguna sistem dan persetujuan akun guru.</p>
                            <a href="{{ route('admin.users') }}" class="btn btn-primary">Kelola Pengguna</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5></i> Kelola Kelas</h5>
                        </div>
                        <div class="card-body">
                            <p>Kelola data kelas dan siswanya.</p>
                            <a href="{{ route('admin.master-kelas.index') }}" class="btn btn-primary">Kelola Kelas</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5></i> Kelola Ekstrakurikuler</h5>
                        </div>
                        <div class="card-body">
                            <p>Kelola kegiatan ekstrakurikuler dan pesertanya.</p>
                            <a href="{{ route('admin.master-ekskul.index') }}" class="btn btn-primary">Kelola Ekstrakurikuler</a>
                            </div>
                        </div>
                    </div>

            <!-- Admin Dashboard Summary Cards -->
                                    <div class="row mb-5 jurnal-grid-row">
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <div class="card summary-tile bg-white border-0">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <p class="text-muted small mb-2">
                                                                Jurnal KBM
                                                            </p>
                                                            <h3 class="mb-2 text-primary">{{ $jurnalKBMCount ?? 0 }}</h3>
                                                            <small class="text-muted">Hari ini</small>
                                                        </div>
                                                    </div>
                                                    <hr class="my-3">
                                                    <a href="{{ route('jurnal.kbm') }}" class="btn btn-sm btn-outline-primary action-btn">
                                                        Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <div class="card summary-tile bg-white border-0">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <p class="text-muted small mb-2">
                                                                Jurnal Ekskul
                                                            </p>
                                                            <h3 class="mb-2 text-success">{{ $jurnalEkskulCount ?? 0 }}</h3>
                                                            <small class="text-muted">Hari ini</small>
                                                        </div>
                                                    </div>
                                                    <hr class="my-3">
                                                    <a href="{{ route('jurnal.ekskul') }}" class="btn btn-sm btn-outline-success action-btn">
                                                        Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <div class="card summary-tile bg-white border-0">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <p class="text-muted small mb-2">
                                                                Jurnal Piket
                                                            </p>
                                                            <h3 class="mb-2 text-warning">{{ $jurnalPiketCount ?? 0 }}</h3>
                                                            <small class="text-muted">Hari ini</small>
                                                        </div>
                                                    </div>
                                                    <hr class="my-3">
                                                    <a href="{{ route('jurnal.piket') }}" class="btn btn-sm btn-outline-warning action-btn">
                                                        Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <div class="card summary-tile bg-white border-0">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <p class="text-muted small mb-2">
                                                                Jurnal Kegiatan
                                                            </p>
                                                            <h3 class="mb-2 text-info">{{ $jurnalKegiatanCount ?? 0 }}</h3>
                                                            <small class="text-muted">Hari ini</small>
                                                        </div>
                                                    </div>
                                                    <hr class="my-3">
                                                    <a href="{{ route('jurnal.kegiatan') }}" class="btn btn-sm btn-outline-info action-btn">
                                                        Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Activities for Admin -->
                                    <div class="card border-0">
                                        <div class="card-header bg-white border-bottom">
                                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
                                        </div>
                                        <div class="card-body">
                                            @if($recentActivities->count() > 0)
                                                <div class="list-group list-group-flush">
                                                    @foreach($recentActivities as $activity)
                                                        @php $isEdited = $activity->updated_at->gt($activity->created_at); @endphp
                                                        <div class="list-group-item px-0 py-3 border-bottom-subtle">
                                                            <div class="d-flex w-100 justify-content-between align-items-start">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1">
                                                                        @if($activity instanceof \App\Models\JurnalKBM)
                                                                            <i class="bi bi-journal-text text-primary"></i> Jurnal KBM
                                                                        @elseif($activity instanceof \App\Models\JurnalEkskul)
                                                                            <i class="bi bi-people text-success"></i> Jurnal Ekskul
                                                                        @elseif($activity instanceof \App\Models\JurnalPiket)
                                                                            <i class="bi bi-shield-check text-warning"></i> Jurnal Piket
                                                                        @elseif($activity instanceof \App\Models\JurnalKegiatan)
                                                                            <i class="bi bi-bookmark text-info"></i> Jurnal Kegiatan
                                                                        @endif
                                                                        <span class="badge bg-secondary ms-2">{{ $activity->guru?->name ?? 'Tidak diketahui' }}</span>
                                                                        @if($isEdited)
                                                                            <span class="badge bg-warning text-dark ms-2">Diedit</span>
                                                                        @endif
                                                                    </h6>
                                                                    <p class="mb-1 text-muted">{{ $activity->materi ?? $activity->kegiatan ?? $activity->nama_kegiatan ?? 'Aktivitas jurnal' }}</p>
                                                                    <small class="text-muted">
                                                                        <i class="bi bi-calendar"></i> 
                                                                        {{ $activity->tanggal instanceof \Carbon\Carbon ? $activity->tanggal->format('d/m/Y') : \Carbon\Carbon::parse($activity->tanggal)->format('d/m/Y') }}
                                                                        <span class="ms-2"><i class="bi bi-clock"></i> {{ $activity->updated_at->diffForHumans() }}</span>
                                                                    </small>
                                                                </div>
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
        @else
            <!-- Pending Approval -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">Menunggu Persetujuan</h4>
                            <p>Akun Anda sedang menunggu persetujuan dari administrator.</p>
                            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection