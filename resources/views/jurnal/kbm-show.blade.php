@extends('layouts.app')

@section('title', 'Detail Jurnal KBM')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail Jurnal KBM</h2>
            <a href="{{ route('jurnal.kbm') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

        <!-- Detail Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Informasi Jurnal</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Tanggal:</label>
                        <p>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Pengajar:</label>
                        <p>{{ $jurnal->guru?->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Kelas:</label>
                        <p>{{ $jurnal->kelas->nama_kelas }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Mata Pelajaran:</label>
                        <p>{{ $jurnal->mapel->nama_mapel }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Jam Pelajaran:</label>
                        <p>{{ \Carbon\Carbon::parse($jurnal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jurnal->jam_selesai)->format('H:i') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Durasi:</label>
                        <p>
                            {{ \Carbon\Carbon::parse($jurnal->jam_mulai)->diffInMinutes(\Carbon\Carbon::parse($jurnal->jam_selesai)) }} menit
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Materi Pembelajaran:</label>
                        <p>{{ $jurnal->materi }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Catatan:</label>
                        <p>{{ $jurnal->catatan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absensi Summary Card -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Ringkasan Absensi</h5>
            </div>
            <div class="card-body">
                @php
                    $absensiData = $jurnal->absensi;
                    
                    // Decode JSON if it's a string
                    if (is_string($absensiData)) {
                        $absensiData = json_decode($absensiData, true) ?? [];
                    }
                    
                    $hadir = 0;
                    $izin = 0;
                    $sakit = 0;
                    $alpha = 0;
                    
                    if (is_array($absensiData)) {
                        foreach ($absensiData as $item) {
                            $status = strtolower($item['status'] ?? '');
                            switch ($status) {
                                case 'hadir':
                                    $hadir++;
                                    break;
                                case 'izin':
                                    $izin++;
                                    break;
                                case 'sakit':
                                    $sakit++;
                                    break;
                                case 'alpha':
                                    $alpha++;
                                    break;
                            }
                        }
                    }
                @endphp
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-success">{{ $hadir }}</h4>
                            <p class="mb-0">Hadir</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-warning">{{ $izin }}</h4>
                            <p class="mb-0">Izin</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-info">{{ $sakit }}</h4>
                            <p class="mb-0">Sakit</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-danger">{{ $alpha }}</h4>
                            <p class="mb-0">Alpha</p>
                        </div>
                    </div>
                </div>

                <!-- Detail Absensi Table -->
                @if(is_array($absensiData) && count($absensiData) > 0)
                    <div class="mt-4">
                        <h6>Detail Kehadiran Siswa:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($absensiData as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['nama'] ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $status = strtolower($item['status'] ?? '');
                                                    $badgeClass = match($status) {
                                                        'hadir' => 'bg-success',
                                                        'izin' => 'bg-warning',
                                                        'sakit' => 'bg-info',
                                                        'alpha' => 'bg-danger',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($item['status'] ?? '-') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Tidak ada data absensi</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
