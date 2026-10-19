@extends('layouts.app')

@section('title', 'Master Mata Pelajaran')

@push('styles')
<style>
    /* Base table styles */
    .action-cell {
        padding: 0.3rem !important;
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: flex-start;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        text-align: center;
    }

    .action-cell form {
        display: block;
        margin: 0;
        width: 100%;
    }

    .table-fixed {
        table-layout: fixed;
        width: 100%;
    }

    .table-fixed td, .table-fixed th {
        word-wrap: break-word;
        vertical-align: middle;
    }

    /* =========================================
       Penyesuaian Khusus Layar HP (Mobile)
       ========================================= */
    @media (max-width: 768px) {
        /* 1. Mengecilkan font & padding keseluruhan tabel */
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.75rem !important; 
            padding: 0.4rem 0.2rem !important; 
        }

        /* 2. Mengecilkan badge agar tidak terpotong */
        .table-responsive .badge {
            font-size: 0.65rem !important;
            padding: 0.25rem 0.3rem !important;
            white-space: normal; /* Memungkinkan teks badge turun baris jika sangat panjang */
            display: inline-block;
            text-align: center;
        }

        /* 3. Menumpuk tombol Aksi ke bawah (Vertikal) agar hemat lebar */
        .action-buttons {
            flex-direction: column;
            gap: 4px;
        }

        .action-buttons .btn {
            width: 100%; /* Tombol menyesuaikan lebar kolom yang sudah dikecilkan */
            padding: 0.2rem 0 !important;
            font-size: 0.65rem !important;
            line-height: 1.2;
        }

        /* 4. Distribusi ulang lebar kolom agar proporsional */
        .table-fixed th:nth-child(1) { width: 8% !important; }  /* No */
        .table-fixed th:nth-child(2) { width: 28% !important; } /* Nama Mapel */
        .table-fixed th:nth-child(3) { width: 24% !important; } /* Program */
        .table-fixed th:nth-child(4) { width: 22% !important; } /* Jenjang */
        .table-fixed th:nth-child(5) { width: 18% !important; } /* Aksi (Sempit) */
    }

    /* =========================================
       Penyesuaian Khusus Tablet (iPad, dll)
       ========================================= */
    @media (min-width: 769px) and (max-width: 1024px) {
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.85rem;
            padding: 0.5rem 0.3rem !important;
        }
        
        .action-buttons {
            flex-wrap: wrap; /* Di tablet, tombol bisa grid 2x2 karena ruangnya lumayan */
        }

        .action-buttons .btn {
            padding: 0.2rem 0.4rem !important;
            font-size: 0.7rem !important;
            flex: 1 1 calc(50% - 5px);
        }

        .action-cell form { display: inline; width: auto; }
        .action-cell form button { width: 100%; }

        /* Proporsi lebar tablet */
        .table-fixed th:nth-child(1) { width: 6% !important; }
        .table-fixed th:nth-child(2) { width: 28% !important; }
        .table-fixed th:nth-child(3) { width: 21% !important; }
        .table-fixed th:nth-child(4) { width: 20% !important; }
        .table-fixed th:nth-child(5) { width: 25% !important; }
        
        .d-flex.justify-content-between.align-items-center.mb-4 { flex-direction: column; align-items: flex-start; }
        .d-flex.justify-content-between.align-items-center.mb-4 > div:last-child { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"></i> Kelola Mata Pelajaran</h2>
                <p class="text-muted small mb-0">Kelola daftar mata pelajaran yang tersedia di sekolah</p>
            </div>
            <div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.master-mapel.export') }}" class="btn btn-outline-success me-2">
                        Unduh Mapel
                    </a>
                @endif
                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addMapelModal">
                    Tambah Mapel
                </button>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Daftar Mapel</h5>
            </div>
            <div class="card-body">
                @if($mapels->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-fixed">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 30%;">Nama Mapel</th>
                                    <th style="width: 20%;">Program</th>
                                    <th style="width: 20%;">Jenjang</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mapels as $item)
                                    <tr>
                                        <td><strong>{{ $loop->iteration }}</strong></td>
                                        <td>
                                            <strong>{{ $item->nama_mapel }}</strong>
                                        </td>
                                        <td>
                                            @if($item->program)
                                                <span class="badge bg-info">{{ $item->program }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->jenjang)
                                                <span class="badge bg-success">Kelas {{ $item->jenjang }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="action-cell">
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.master-mapel.show', $item) }}" class="btn btn-sm btn-outline-info" title="Lihat">
                                                    Lihat
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editMapelModal"
                                                    data-mapel-id="{{ $item->id }}"
                                                    data-update-url="{{ route('admin.master-mapel.update', $item) }}"
                                                    data-nama-mapel="{{ $item->nama_mapel }}"
                                                    data-program="{{ $item->program }}"
                                                    data-jenjang="{{ $item->jenjang }}"
                                                    data-kode-mapel="{{ $item->kode_mapel }}"
                                                    title="Edit">
                                                    Edit
                                                </button>
                                                <form id="deleteForm-{{ $item->id }}" method="POST" action="{{ route('admin.master-mapel.destroy', $item->id) }}" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete('{{ route('admin.master-mapel.destroy', $item->id) }}')" 
                                                            title="Hapus">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-fixed">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 30%;">Nama Mapel</th>
                                    <th style="width: 20%;">Program</th>
                                    <th style="width: 20%;">Jenjang</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada data mapel</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus mata pelajaran ini?</p>
                <p class="text-muted mb-0"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="globalDeleteForm" method="POST" style="display:inline;">
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

@push('scripts')
<script>
    function confirmDelete(deleteUrl) {
        const form = document.getElementById('globalDeleteForm');
        form.action = deleteUrl;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const editMapelModal = document.getElementById('editMapelModal');
        if (editMapelModal) {
            editMapelModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const mapelId = button.dataset.mapelId;
                const namaMapel = button.dataset.namaMapel;
                const program = button.dataset.program;
                const jenjang = button.dataset.jenjang;
                const kodeMapel = button.dataset.kodeMapel;

                const form = document.getElementById('editMapelForm');
                if (!mapelId) {
                    console.error('editMapelModal: missing mapelId', button);
                    return; 
                }
                const updateUrl = button.dataset.updateUrl;
                if (updateUrl) {
                    form.action = updateUrl;
                } else {
                    const actionUrl = window.location.origin + '/admin/master-mapel/' + encodeURIComponent(mapelId);
                    form.action = actionUrl;
                }

                document.getElementById('editNamaMapel').value = namaMapel;
                document.getElementById('editProgram').value = program;
                document.getElementById('editJenjang').value = jenjang;
                document.getElementById('editKodeMapel').value = kodeMapel;
            });
        }
    });
</script>
@endpush

<div class="modal fade" id="addMapelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.master-mapel.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addNamaMapel" class="form-label"><strong>Nama Mapel</strong> *</label>
                        <input type="text" class="form-control" id="addNamaMapel" name="nama_mapel" placeholder="Contoh: Matematika" required>
                    </div>

                    <div class="mb-3">
                        <label for="addProgram" class="form-label"><strong>Program</strong> *</label>
                        <select class="form-select" id="addProgram" name="program" required>
                            <option value="">-- Pilih Program --</option>
                            <option value="Cambridge">CAMBRIDGE</option>
                            <option value="ICT">ICT</option>
                            <option value="Tahfidz">TAHFIDZ</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="addJenjang" class="form-label"><strong>Jenjang</strong> *</label>
                        <select class="form-select" id="addJenjang" name="jenjang" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="addKodeMapel" class="form-label"><strong>Kode Mapel</strong> *</label>
                        <input type="text" class="form-control" id="addKodeMapel" name="kode_mapel" placeholder="Format: PROGRAM-MAPEL-JENJANG" required>
                        <small class="text-muted d-block mt-2">
                            <strong>Format:</strong> SINGKAT_PROGRAM-SINGKAT_MAPEL-JENJANG<br>
                            <strong>Contoh:</strong> CAM-MAT-X
                        </small>
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

<div class="modal fade" id="editMapelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editMapelForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNamaMapel" class="form-label"><strong>Nama Mapel</strong> *</label>
                        <input type="text" class="form-control" id="editNamaMapel" name="nama_mapel" required>
                    </div>

                    <div class="mb-3">
                        <label for="editProgram" class="form-label"><strong>Program</strong> *</label>
                        <select class="form-select" id="editProgram" name="program" required>
                            <option value="">-- Pilih Program --</option>
                            <option value="Cambridge">CAMBRIDGE</option>
                            <option value="ICT">ICT</option>
                            <option value="Tahfidz">TAHFIDZ</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editJenjang" class="form-label"><strong>Jenjang</strong> *</label>
                        <select class="form-select" id="editJenjang" name="jenjang" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editKodeMapel" class="form-label"><strong>Kode Mapel</strong> *</label>
                        <input type="text" class="form-control" id="editKodeMapel" name="kode_mapel" required>
                        <small class="text-muted d-block mt-2">
                            <strong>Format:</strong> SINGKAT_PROGRAM-SINGKAT_MAPEL-JENJANG
                        </small>
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

@endsection