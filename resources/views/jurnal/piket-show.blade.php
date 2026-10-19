@extends('layouts.app')

@section('title', 'Detail Jurnal Piket')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail Jurnal Piket</h2>
            <a href="{{ route('jurnal.piket') }}" class="btn btn-secondary">
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
                        <p>{{ $jurnal->tanggal->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Hari:</label>
                        <p>{{ $jurnal->hari ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Guru Piket:</label>
                        <p>{{ $jurnal->guru->name ?? '-' }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Catatan:</label>
                        <p>{{ $jurnal->catatan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guru Tidak Hadir Card -->
        @php
            $guruTidakHadir = is_array($jurnal->guru_tidak_hadir) ? $jurnal->guru_tidak_hadir : (is_string($jurnal->guru_tidak_hadir) ? json_decode($jurnal->guru_tidak_hadir, true) : []);
            $guruTidakHadir = $guruTidakHadir ?: [];
        @endphp
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Guru Tidak Hadir</h5>
            </div>
            <div class="card-body">
                @if(count($guruTidakHadir) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 45%;">Nama</th>
                                    <th style="width: 45%;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guruTidakHadir as $g)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $g['nama'] ?? $g }}</td>
                                        <td>{{ $g['catatan'] ?? ($g['alasan'] ?? '-') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Tidak ada guru yang tidak hadir</p>
                @endif
            </div>
        </div>

        <!-- Tamu Sekolah Card -->
        @php
            $tamu = is_array($jurnal->tamu_sekolah) ? $jurnal->tamu_sekolah : (is_string($jurnal->tamu_sekolah) ? json_decode($jurnal->tamu_sekolah, true) : []);
            $tamu = $tamu ?: [];
        @endphp
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Tamu Sekolah</h5>
            </div>
            <div class="card-body">
                @if(count($tamu) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 45%;">Nama</th>
                                    <th style="width: 45%;">Tujuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tamu as $t)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $t['nama'] ?? $t }}</td>
                                        <td>{{ $t['tujuan'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Tidak ada tamu sekolah</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Include edit modal and scripts so Edit works on show page --}}
@include('jurnal._piket-edit-modal')
@include('jurnal._piket-scripts')
@endsection