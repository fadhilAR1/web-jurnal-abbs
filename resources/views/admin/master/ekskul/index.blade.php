@extends('layouts.app')

@section('title', 'Master Ekstrakurikuler')

@push('styles')
<style>
    /* Base table & Action Buttons styles */
    .action-cell {
        padding: 0.3rem !important;
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap; /* Mengizinkan tombol turun baris */
        justify-content: flex-start;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .action-cell form {
        display: inline;
        margin: 0;
    }

    .table-fixed {
        table-layout: fixed;
        width: 100%;
    }

    .table-fixed td, .table-fixed th {
        word-wrap: break-word;
        vertical-align: middle;
    }

    /* Memastikan tombol header teksnya pas di tengah */
    .header-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* =========================================
       Penyesuaian Khusus Layar HP (Mobile)
       ========================================= */
    @media (max-width: 768px) {
        /* Memperbaiki Header: Judul & Tombol Bertumpuk agar lega */
        .header-container {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 15px;
        }
        
        .header-actions {
            display: flex;
            width: 100%;
            gap: 8px;
        }

        .header-actions .btn {
            flex: 1; /* Tombol membagi lebar 50:50 */
            font-size: 0.75rem !important;
            padding: 0.4rem 0.2rem !important;
            white-space: normal; /* Membiarkan teks turun baris jika sangat panjang */
            line-height: 1.2;
        }

        /* Memperbaiki Tabel dan Tombol Aksi */
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.8rem !important;
            padding: 0.4rem 0.2rem !important; 
        }

        /* Grid 2x2 untuk 4 tombol aksi */
        .action-buttons .btn {
            flex: 1 1 calc(50% - 5px); 
            font-size: 0.7rem !important;
            padding: 0.25rem 0.2rem !important;
            margin: 0;
        }

        /* Mengatur proporsi lebar tabel di HP */
        .table-fixed th:nth-child(1) { width: 12% !important; } /* No */
        .table-fixed th:nth-child(2) { width: 38% !important; } /* Nama Ekskul */
        .table-fixed th:nth-child(3) { width: 50% !important; } /* Aksi (Lebar agar 2x2 pas) */
    }

    /* =========================================
       Penyesuaian Khusus Tablet
       ========================================= */
    @media (min-width: 769px) and (max-width: 1024px) {
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.85rem;
            padding: 0.5rem 0.3rem !important;
        }

        .action-buttons .btn {
            flex: 1 1 calc(50% - 5px); /* Grid 2x2 juga di tablet */
            padding: 0.2rem 0.4rem !important;
            font-size: 0.75rem !important;
        }

        .table-fixed th:nth-child(1) { width: 8% !important; }
        .table-fixed th:nth-child(2) { width: 42% !important; }
        .table-fixed th:nth-child(3) { width: 50% !important; }

        .header-container { flex-direction: column !important; align-items: flex-start !important; }
        .header-actions { display: flex; gap: 0.5rem; margin-top: 0.5rem; width: 100%; }
        .header-actions .btn { flex: 1; white-space: normal; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 header-container">
            <div>
                <h2 class="mb-1"></i> Kelola Ekstrakurikuler</h2>
                <p class="text-muted small mb-0">Kelola kegiatan ekstrakurikuler dan peserta</p>
            </div>
            <div class="header-actions">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.master-ekskul.export') }}" class="btn btn-outline-success">
                        Unduh Ekstrakurikuler
                    </a>
                @endif
                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addEkskulModal">
                    Tambah Ekstrakurikuler
                </button>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Daftar Ekstrakurikuler</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th style="width: 10%;" class="col-no">No</th>
                                <th style="width: 50%;" class="col-name">Nama Ekskul</th>
                                <th style="width: 40%;" class="col-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ekskuls as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $item->nama_ekskul }}</strong></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.master-ekskul.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                                Lihat
                                            </a>
                                            <a href="{{ route('admin.master-ekskul.participants', $item->id) }}" class="btn btn-sm btn-outline-secondary" title="Kelola Peserta">
                                                Peserta
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editEkskulModal"
                                                data-ekskul-id="{{ $item->id }}"
                                                data-update-url="{{ route('admin.master-ekskul.update', $item) }}"
                                                data-nama-ekskul="{{ $item->nama_ekskul }}"
                                                data-kapasitas="{{ $item->kapasitas }}">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-delete-url="{{ route('admin.master-ekskul.destroy', $item->id) }}"
                                                    onclick="confirmDelete(this)">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada data ekstrakurikuler</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEkskulModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.master-ekskul.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Ekstrakurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addNamaEkskul" class="form-label">Nama Ekskul *</label>
                        <input type="text" class="form-control" id="addNamaEkskul" name="nama_ekskul" required>
                    </div>
                    <div class="mb-3">
                        <label for="addKapasitas" class="form-label">Kapasitas Peserta</label>
                        <input type="number" min="1" class="form-control" id="addKapasitas" name="kapasitas" value="30">
                        <div class="form-text">Isi 0 atau kosong untuk tanpa batas.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editEkskulModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editEkskulForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Ekstrakurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNamaEkskul" class="form-label">Nama Ekskul *</label>
                        <input type="text" class="form-control" id="editNamaEkskul" name="nama_ekskul" required>
                    </div>
                    <div class="mb-3">
                        <label for="editKapasitas" class="form-label">Kapasitas Peserta</label>
                        <input type="number" min="1" class="form-control" id="editKapasitas" name="kapasitas">
                        <div class="form-text">Isi 0 atau kosong untuk tanpa batas.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(button) {
        const deleteUrl = button.getAttribute('data-delete-url');
        const form = document.getElementById('deleteForm');
        form.action = deleteUrl;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

document.addEventListener('DOMContentLoaded', function() {
    const editEkskulModal = document.getElementById('editEkskulModal');
    editEkskulModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const ekskulId = button.dataset.ekskulId;
        const namaEkskul = button.dataset.namaEkskul;
        const kapasitas = button.dataset.kapasitas;

        const form = document.getElementById('editEkskulForm');
        if (!ekskulId) {
            console.error('editEkskulModal: missing ekskulId', button);
            return;
        }
        const updateUrl = button.dataset.updateUrl;
        if (updateUrl) {
            form.action = updateUrl;
            console.log('editEkskulForm action (from data-update-url) ->', updateUrl);
        } else {
            const actionUrl = window.location.origin + '/admin/master-ekskul/' + encodeURIComponent(ekskulId);
            console.log('editEkskulForm action (fallback) ->', actionUrl);
            form.action = actionUrl;
        }

        document.getElementById('editNamaEkskul').value = namaEkskul;
        document.getElementById('editKapasitas').value = kapasitas;
    });
});
</script>
@endpush

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus ekstrakurikuler ini?</p>
                <p class="text-muted mb-0"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection