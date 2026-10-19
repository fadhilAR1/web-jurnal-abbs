@extends('layouts.app')

@section('title', 'Detail Jurnal Kegiatan')

@section('content')
@php
    $dokumentasi = is_array($jurnal->dokumentasi) ? $jurnal->dokumentasi : (is_string($jurnal->dokumentasi) ? json_decode($jurnal->dokumentasi, true) : []);
    $peserta = is_array($jurnal->peserta) ? $jurnal->peserta : (is_string($jurnal->peserta) ? json_decode($jurnal->peserta, true) : []);
@endphp
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail Jurnal Kegiatan</h2>
            <a href="{{ route('jurnal.kegiatan') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

        <!-- Detail Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $jurnal->nama_kegiatan }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Tanggal:</label>
                        <p>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Penulis:</label>
                        <p>{{ $jurnal->guru->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Jenis Kegiatan:</label>
                        <p><span class="badge bg-info">{{ ucfirst($jurnal->jenis_kegiatan) }}</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Lokasi:</label>
                        <p>{{ $jurnal->lokasi ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Waktu:</label>
                        <p>{{ \Carbon\Carbon::parse($jurnal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jurnal->waktu_selesai)->format('H:i') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Peserta:</label>
                        <p>
                            Siswa: {{ $peserta['siswa'] ?? 0 }}<br>
                            Guru: {{ $peserta['guru'] ?? 0 }}
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Deskripsi Kegiatan:</label>
                        <p>{{ $jurnal->deskripsi }}</p>
                    </div>
                    @if($jurnal->catatan)
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Catatan:</label>
                        <p>{{ $jurnal->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Foto Kegiatan Card -->
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
                            $fotoUrl = null;
                            if ($foto) {
                                // Normalize path: convert backslashes to forward slashes
                                $foto = str_replace('\\', '/', $foto);
                                
                                // If it's already a full URL, use it
                                if (filter_var($foto, FILTER_VALIDATE_URL)) {
                                    $fotoUrl = $foto;
                                } else {
                                    // Use /file/ route to serve photos (avoids symlink issues on Windows)
                                    $fotoUrl = url('/file/' . $foto);
                                }
                            }
                        @endphp
                        <div class="col-md-3 mb-3">
                            @if($fotoUrl)
                                <img src="{{ $fotoUrl }}" 
                                     class="img-fluid rounded foto-thumb" 
                                     alt="Foto Kegiatan" 
                                     data-full="{{ $fotoUrl }}"
                                     style="cursor:pointer; max-height:180px; object-fit:cover; width:100%;" 
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%25%22 height=%22180%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22100%25%22 height=%22180%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3EFile not found%3C/text%3E%3C/svg%3E'; this.style.minHeight='180px';">
                            @else
                                <div class="border rounded p-3 text-muted small text-center" style="height:180px; display:flex; align-items:center; justify-content:center;"><span>File tidak ditemukan:<br>{{ substr($foto, -30) }}</span></div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @else
                    <p class="text-muted">Tidak ada foto yang diunggah</p>
                @endif
            </div>
        </div>

        <!-- Dokumentasi Lainnya Card -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Dokumentasi Lainnya</h5>
            </div>
            <div class="card-body">
                @if(count($dokumentasi) > 0)
                <div class="list-group">
                    @foreach($dokumentasi as $doc)
                        @php
                            $docPath = $doc['path'] ?? null;
                            $exists = $docPath && Storage::disk('public')->exists($docPath);
                            $docName = $doc['nama'] ?? ($doc['original_name'] ?? basename($docPath ?? 'file'));
                        @endphp

                        @if($exists)
                            <a href="{{ route('jurnal.kegiatan.download', ['jurnal' => $jurnal->id, 'index' => $loop->index]) }}" class="list-group-item list-group-item-action" download>
                                <i class="bi bi-file-earmark"></i> {{ $docName }}
                            </a>
                        @else
                            <div class="list-group-item">
                                <i class="bi bi-file-earmark"></i> {{ $docName }} <span class="text-muted">(file tidak ditemukan)</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                @else
                    <p class="text-muted">Tidak ada dokumentasi yang diunggah</p>
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
@endsection