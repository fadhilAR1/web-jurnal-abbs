@extends('layouts.app')

@section('title', 'Monitoring Jurnal Kegiatan')

@push('styles')
<style>
    .table-fixed {
        table-layout: fixed;
        width: 100%;
    }

    .table-fixed td, .table-fixed th {
        word-wrap: break-word;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .text-ellipsis {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
        max-width: 100%;
    }

    /* Action Buttons - Single Line */
    .action-cell {
        white-space: nowrap;
        padding: 0.3rem !important;
    }

    .action-buttons {
        display: flex;
        gap: 3px;
        flex-wrap: nowrap;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Monitoring Jurnal Kegiatan</h2>
            <div class="btn-group">
                <a href="{{ route('admin.monitoring.kbm') }}" class="btn btn-outline-primary">
                    <i class="bi bi-journal-text"></i> KBM
                </a>
                <a href="{{ route('admin.monitoring.ekskul') }}" class="btn btn-outline-primary">
                    <i class="bi bi-people"></i> Ekskul
                </a>
                <a href="{{ route('admin.monitoring.piket') }}" class="btn btn-outline-primary">
                    <i class="bi bi-clipboard-check"></i> Piket
                </a>
                <a href="{{ route('admin.monitoring.kegiatan') }}" class="btn btn-outline-primary active">
                    <i class="bi bi-calendar-event"></i> Kegiatan
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th style="width: 20%;">Guru</th>
                                <th style="width: 35%;">Judul Kegiatan</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 12%;">Status</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurnals as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="text-ellipsis">{{ $item->user->name ?? '-' }}</span></td>
                                    <td><span class="text-ellipsis" title="{{ $item->judul_kegiatan ?? '' }}">{{ $item->judul_kegiatan ?? '-' }}</span></td>
                                    <td><span class="text-ellipsis">{{ $item->tanggal->format('d/m/Y') ?? '-' }}</span></td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                                Lihat
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
