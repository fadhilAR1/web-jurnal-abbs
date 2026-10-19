@extends('layouts.app')

@section('title', 'Kelola Siswa')

@push('styles')
<style>
    .action-cell { padding: 0.3rem !important; }
    .action-buttons { display:flex; gap:5px; flex-wrap:wrap; }
    .action-buttons .btn { padding:0.25rem 0.5rem; font-size:0.75rem; }
    .table-fixed { table-layout: fixed; width:100%; }
    .table-fixed td, .table-fixed th { word-wrap:break-word; overflow:hidden; text-overflow:ellipsis; vertical-align:middle; }
    @media (max-width:768px) {
        .action-buttons .btn { flex:1 1 calc(50% - 5px); }
        .table-fixed th:nth-child(1) { width: 15% !important; }
        .table-fixed th:nth-child(2) { width: 45% !important; }
        .table-fixed th:nth-child(3) { width: 40% !important; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"> Kelola Siswa</h2>
                <p class="text-muted small mb-0">Kelola daftar siswa</p>
            </div>
            <div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.master-siswa.export') }}" class="btn btn-outline-success me-2">Unduh Siswa</a>
                @endif
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSiswaModal">Tambah Siswa</button>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Daftar Siswa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th style="width:10%;" class="col-no">No</th>
                                <th style="width:50%;" class="col-name">Nama</th>
                                <th style="width:40%;" class="col-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $siswa)
                                <tr>
                                    <td>{{ $loop->iteration + ($siswas->currentPage()-1)*$siswas->perPage() }}</td>
                                    <td title="{{ $siswa->nama_siswa }}"><strong>{{ $siswa->nama_siswa }}</strong></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.master-siswa.show', $siswa->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">Lihat</a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" data-bs-target="#editSiswaModal"
                                                data-siswa-id="{{ $siswa->id }}"
                                                data-update-url="{{ route('admin.master-siswa.update', $siswa) }}"
                                                data-nama-siswa="{{ $siswa->nama_siswa }}"
                                                data-jenis-kelamin="{{ $siswa->jenis_kelamin }}"
                                                data-nis="{{ $siswa->nis }}"
                                                title="Edit">
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-delete-url="{{ route('admin.master-siswa.destroy', $siswa->id) }}"
                                                    onclick="confirmDelete(this)"
                                                    title="Hapus">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada data siswa</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">{{ $siswas->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addSiswaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.master-siswa.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Tambah Siswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Lengkap *</label><input name="nama_siswa" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-control"><option value="">-- Pilih --</option><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                    <div class="mb-3"><label class="form-label">NIS</label><input name="nis" class="form-control"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editSiswaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSiswaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header"><h5 class="modal-title">Edit Siswa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Lengkap *</label><input id="editNamaSiswa" name="nama_siswa" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Jenis Kelamin</label>
                        <select id="editJenisKelamin" name="jenis_kelamin" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">NIS</label><input id="editNis" name="nis" class="form-control"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus siswa ini?</p>
                <p class="text-muted mb-0"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
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
        const editSiswaModal = document.getElementById('editSiswaModal');
        editSiswaModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const siswaId = button.dataset.siswaId;
            const nama = button.dataset.namaSiswa;
            const jenis = button.dataset.jenisKelamin;
            const nis = button.dataset.nis;

            const form = document.getElementById('editSiswaForm');
            const updateUrl = button.dataset.updateUrl || (window.location.origin + '/admin/master-siswa/' + encodeURIComponent(siswaId));
            form.action = updateUrl;

            document.getElementById('editNamaSiswa').value = nama || '';
            document.getElementById('editJenisKelamin').value = jenis || '';
            document.getElementById('editNis').value = nis || '';
        });
    });
</script>
@endpush

@endsection
