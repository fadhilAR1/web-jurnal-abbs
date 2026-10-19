@extends('layouts.app')

@section('title', 'Detail Jurnal Ekstrakurikuler')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail Jurnal Ekstrakurikuler</h2>
            <a href="{{ route('jurnal.ekskul') }}" class="btn btn-secondary">
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
                        <label class="fw-bold">Ekstrakurikuler:</label>
                        <p>{{ $jurnal->ekskul->nama_ekskul }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Jam Pelaksanaan:</label>
                        <p>
                            @if($jurnal->jam_mulai && $jurnal->jam_selesai)
                                {{ \Carbon\Carbon::parse($jurnal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jurnal->jam_selesai)->format('H:i') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Materi Kegiatan:</label>
                        <p>{{ $jurnal->materi }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Catatan:</label>
                        <p>{{ $jurnal->catatan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Foto Kegiatan -->
        @php
            $foto_kegiatan = $jurnal->foto_kegiatan;
            if (is_string($foto_kegiatan)) {
                $foto_kegiatan = json_decode($foto_kegiatan, true) ?: [];
            }
            $foto_kegiatan = is_array($foto_kegiatan) ? $foto_kegiatan : [];
        @endphp

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Foto Kegiatan</h5>
            </div>
            <div class="card-body">
                @php
                    $fotoKegiatan = $jurnal->foto_kegiatan;
                    if (is_string($fotoKegiatan)) {
                        $fotoKegiatan = json_decode($fotoKegiatan, true) ?? [];
                    }
                    $fotoKegiatan = is_array($fotoKegiatan) ? $fotoKegiatan : [];
                @endphp
                @if(count($fotoKegiatan) > 0)
                    <div class="row">
                        @foreach($fotoKegiatan as $foto)
                            @php
                                // Normalize path: replace escaped backslashes
                                $foto = str_replace('\\', '/', $foto);
                                // Use /file/ route to serve photos (avoids symlink issues on Windows)
                                $fotoUrl = url('/file/' . $foto);
                            @endphp
                            <div class="col-md-3 mb-3">
                                <img src="{{ $fotoUrl }}" 
                                     class="img-fluid rounded foto-thumb" 
                                     alt="Foto Kegiatan" 
                                     data-full="{{ $fotoUrl }}"
                                     style="cursor:pointer; max-height:180px; object-fit:cover; width:100%;" 
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%25%22 height=%22180%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22100%25%22 height=%22180%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3EFile not found%3C/text%3E%3C/svg%3E'; this.style.minHeight='180px';">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Tidak ada foto yang diunggah</p>
                @endif
            </div>
        </div>

        <!-- Modal untuk melihat foto lebih besar -->
        <div class="modal fade" id="fotoModalShow" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-body position-relative p-0">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                        <img id="fotoModalShowImg" src="" class="img-fluid w-100" alt="Foto Kegiatan">
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const thumbs = Array.from(document.querySelectorAll('.foto-thumb'));
            if (thumbs.length === 0) return;

            const modalEl = document.getElementById('fotoModalShow');
            const modalImg = document.getElementById('fotoModalShowImg');
            let photos = thumbs.map(t => t.dataset.full);

            thumbs.forEach((thumb, idx) => {
                thumb.addEventListener('click', () => {
                    modalImg.src = photos[idx];
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });
            });
        });
        </script>
        @endpush

        <!-- Absensi Summary Card -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Ringkasan Kehadiran</h5>
            </div>
            <div class="card-body">
                @php
                    $absensiData = $jurnal->absensi;
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
                                case 'hadir': $hadir++; break;
                                case 'izin': $izin++; break;
                                case 'sakit': $sakit++; break;
                                case 'alpha': $alpha++; break;
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

                @if(is_array($absensiData) && count($absensiData) > 0)
                    <div class="mt-4">
                        <h6>Detail Kehadiran Peserta:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Peserta</th>
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
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($item['status'] ?? '-') }}</span>
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
