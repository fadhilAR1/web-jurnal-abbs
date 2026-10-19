@extends('layouts.app')

@section('title', 'Master Data Kelas')

@push('styles')
<style>
    /* Action Buttons - Responsive */
    .action-cell {
        padding: 0.3rem !important;
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
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle; /* Menyelaraskan teks ke tengah secara vertikal */
    }

    /* Penyesuaian khusus untuk layar HP */
    @media (max-width: 768px) {
        .action-buttons .btn {
            flex: 1 1 calc(50% - 5px); /* Membuat tombol menjadi 2 kolom / bentuk grid 2x2 */
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"></i> Kelola Data Kelas</h2>
                <p class="text-muted small mb-0">Kelola data kelas dan siswanya</p>
            </div>
            <div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.master-kelas.export') }}" class="btn btn-outline-success me-2">
                        Unduh Daftar Kelas
                    </a>
                @endif
                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addKelasModal">
                    Tambah Kelas
                </button>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Daftar Kelas</h5>
            </div>
            <div class="card-body">
                @if($kelas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-fixed">
                            <thead>
                                <tr>
                                    <th style="width: 10%;" class="col-no">No</th>
                                    <th style="width: 50%;" class="col-name">Nama Kelas</th>
                                    <th style="width: 40%;" class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelas as $index => $k)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $k->nama_kelas }}</strong></td>
                                        <td class="action-cell">
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.master-kelas.show', $k) }}" class="btn btn-sm btn-outline-info">
                                                    Lihat
                                                </a>
                                                <a href="{{ route('admin.master-kelas.students', $k->id) }}" class="btn btn-sm btn-outline-secondary" title="Kelola Siswa">
                                                    Siswa
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editKelasModal"
                                                    data-kelas-id="{{ $k->id }}"
                                                    data-update-url="{{ route('admin.master-kelas.update', $k) }}"
                                                    data-nama-kelas="{{ $k->nama_kelas }}"
                                                    data-tingkat="{{ $k->tingkat }}"
                                                    data-jurusan="{{ $k->jurusan }}"
                                                    data-program="{{ $k->program }}"
                                                    data-kapasitas="{{ $k->kapasitas }}">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-delete-url="{{ route('admin.master-kelas.destroy', $k) }}"
                                                        onclick="confirmDelete(this)">
                                                    Hapus
                                                </button>
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
                                    <th style="width: 10%;" class="col-no">No</th>
                                    <th style="width: 50%;" class="col-name">Nama Kelas</th>
                                    <th style="width: 40%;" class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada data kelas</small>
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

<div class="modal fade" id="addKelasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.master-kelas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addNamaKelas" class="form-label">Nama Kelas *</label>
                                <input type="text" class="form-control" id="addNamaKelas" name="nama_kelas" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addTingkat" class="form-label">Tingkat *</label>
                                <select class="form-select" id="addTingkat" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    @foreach($tingkatList as $tingkat)
                                        <option value="{{ $tingkat }}">{{ $tingkat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addJurusan" class="form-label">Jurusan *</label>
                                <input type="text" class="form-control" id="addJurusan" name="jurusan" value="IPA" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="addProgram" class="form-label">Program *</label>
                                <select class="form-select" id="addProgram" name="program" required>
                                    <option value="">Pilih Program</option>
                                    @foreach($programList as $p)
                                        <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="addKapasitas" class="form-label">Kapasitas Siswa</label>
                                <input type="number" class="form-control" id="addKapasitas" name="kapasitas" value="32" min="1" max="40">
                            </div>
                        </div>
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

<div class="modal fade" id="editKelasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editKelasForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editNamaKelas" class="form-label">Nama Kelas *</label>
                                <input type="text" class="form-control" id="editNamaKelas" name="nama_kelas" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editTingkat" class="form-label">Tingkat *</label>
                                <select class="form-select" id="editTingkat" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    @foreach($tingkatList as $tingkat)
                                        <option value="{{ $tingkat }}">{{ $tingkat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editJurusan" class="form-label">Jurusan *</label>
                                <input type="text" class="form-control" id="editJurusan" name="jurusan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editProgram" class="form-label">Program *</label>
                                <select class="form-select" id="editProgram" name="program" required>
                                    <option value="">Pilih Program</option>
                                    @foreach($programList as $p)
                                        <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="editKapasitas" class="form-label">Kapasitas Siswa</label>
                                <input type="number" class="form-control" id="editKapasitas" name="kapasitas" min="1" max="40">
                            </div>
                        </div>
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
    const editKelasModal = document.getElementById('editKelasModal');
    editKelasModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const kelasId = button.dataset.kelasId;
        const namaKelas = button.dataset.namaKelas;
        const tingkat = button.dataset.tingkat;
        const jurusan = button.dataset.jurusan;
        const program = button.dataset.program;
        const kapasitas = button.dataset.kapasitas;

        const form = document.getElementById('editKelasForm');
        if (!kelasId) {
            console.error('editKelasModal: missing kelasId', button);
            return; // don't set action if id is missing
        }
        // Prefer explicit update URL from the triggering button to avoid host/port mismatch
        const updateUrl = button.dataset.updateUrl;
        if (updateUrl) {
            form.action = updateUrl;
            console.log('editKelasForm action (from data-update-url) ->', updateUrl);
        } else {
            const actionUrl = window.location.origin + '/admin/master-kelas/' + encodeURIComponent(kelasId);
            console.log('editKelasForm action (fallback) ->', actionUrl);
            form.action = actionUrl;
        }

        document.getElementById('editNamaKelas').value = namaKelas;
        document.getElementById('editTingkat').value = tingkat;
        document.getElementById('editJurusan').value = jurusan;
        document.getElementById('editProgram').value = program;
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
                <p>Apakah Anda yakin ingin menghapus kelas ini?</p>
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