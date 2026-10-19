@extends('layouts.app')

@section('title', 'Kelola Pengguna')

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
        vertical-align: middle;
    }

    /* Penyesuaian khusus untuk layar HP */
    @media (max-width: 768px) {
        .table-responsive table td,
        .table-responsive table th {
            text-align: left !important;
        }

        /* Membagi tombol menjadi bentuk grid agar rapi dan tidak terlalu sempit */
        .action-buttons .btn {
            flex: 1 1 calc(50% - 5px); 
        }

        /* Mengatur ulang alokasi lebar tabel untuk mobile agar kolom aksi lebih lega */
        .table-fixed th:nth-child(1) { width: 15% !important; }
        .table-fixed th:nth-child(2) { width: 35% !important; }
        .table-fixed th:nth-child(3) { width: 50% !important; }
    }

    /* Penyesuaian Tablet */
    @media (min-width: 769px) and (max-width: 1024px) {
        .table-fixed th:nth-child(1) { width: 10% !important; }
        .table-fixed th:nth-child(2) { width: 45% !important; }
        .table-fixed th:nth-child(3) { width: 45% !important; }

        .d-flex.justify-content-between.align-items-center.mb-4 { flex-direction: column; align-items: flex-start; }
        .d-flex.justify-content-between.align-items-center.mb-4 > div:last-child { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
    }

    /* Empty state styling (centering icon + text on tablet & mobile) */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: rgba(0,0,0,0.55);
        padding: 2rem 1rem;
    }

    .empty-state .bi {
        font-size: 2.25rem;
        opacity: 0.28;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 1024px) {
        .empty-state { min-height: 180px; }
    }

    /* Ensure empty-state is centered inside table cells on tablet/mobile
       Override earlier rule that forces left alignment on small screens */
    .table-responsive td .empty-state,
    .table-responsive th .empty-state {
        width: 100%;
        text-align: center !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: rgba(0,0,0,0.55);
        padding: 1.5rem 0;
    }

    @media (max-width: 768px) {
        .table-responsive td .empty-state { min-height: 160px; }
    }

    /* Override left-align forced earlier for empty-state cell on small screens */
    @media (max-width: 768px) {
        .table-responsive td.text-center.text-muted.py-4,
        .table-responsive td.text-center {
            text-align: center !important;
            display: table-cell !important;
            vertical-align: middle !important;
        }
        .table-responsive td.text-center .bi {
            margin: 0 auto !important;
            display: block !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"></i> Kelola Pengguna</h2>
                <p class="text-muted small mb-0">Kelola daftar pengguna</p>
            </div>
            <div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.users.export') }}" class="btn btn-outline-success me-2">
                        Unduh Pengguna
                    </a>
                @endif
                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Tambah Pengguna
                </button>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Daftar Pengguna</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th style="width: 10%;" class="col-no">No</th>
                                <th style="width: 50%;" class="col-name">Nama</th>
                                <th style="width: 40%;" class="col-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    @php
                                        $parts = preg_split('/\s+/', trim($user->name));
                                        if(count($parts) >= 3) {
                                            $third = mb_substr($parts[2], 0, 1) . '...';
                                            $displayName = $parts[0] . ' ' . $parts[1] . ' ' . $third;
                                        } else {
                                            $displayName = $user->name;
                                        }
                                    @endphp
                                    <td class="user-name" title="{{ $user->name }}"><strong>{{ $displayName }}</strong></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.users.show', $user->id) }}" 
                                               class="btn btn-sm btn-outline-info"
                                               title="Lihat Detail">
                                                Lihat
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                data-user-id="{{ $user->id }}"
                                                data-update-url="{{ route('admin.users.update', $user) }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-username="{{ $user->username }}"
                                                title="Edit">
                                                Edit
                                            </button>
                                            @if($user->role != 'admin')
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-delete-url="{{ route('admin.users.destroy', $user->id) }}"
                                                        onclick="confirmDelete(this)"
                                                        title="Hapus">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada data pengguna</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addName" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="addName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="addUsername" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="addUsername" name="username" required>
                        <small class="form-text text-muted">Username untuk login (harus unik)</small>
                    </div>

                    <div class="mb-3">
                        <label for="addPassword" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="addPassword" name="password" required>
                        <small class="form-text text-muted">Minimal 8 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="addPasswordConfirmation" class="form-label">Konfirmasi Password *</label>
                        <input type="password" class="form-control" id="addPasswordConfirmation" name="password_confirmation" required>
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

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                        <small class="form-text text-muted">Username untuk login (harus unik)</small>
                    </div>

                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi</small>
                    </div>

                    <div class="mb-3">
                        <label for="editPasswordConfirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation">
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
    const editUserModal = document.getElementById('editUserModal');
    editUserModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const userId = button.dataset.userId;
        const userName = button.dataset.userName;
        const userUsername = button.dataset.userUsername;

        const form = document.getElementById('editUserForm');
        if (!userId) {
            console.error('editUserModal: missing userId', button);
            return;
        }
        const updateUrl = button.dataset.updateUrl;
        if (updateUrl) {
            form.action = updateUrl;
            console.log('editUserForm action (from data-update-url) ->', updateUrl);
        } else {
            const actionUrl = window.location.origin + '/admin/users/' + encodeURIComponent(userId);
            console.log('editUserForm action (fallback) ->', actionUrl);
            form.action = actionUrl;
        }

        document.getElementById('editName').value = userName;
        document.getElementById('editUsername').value = userUsername;
        document.getElementById('editPassword').value = '';
        document.getElementById('editPasswordConfirmation').value = '';
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
                <p>Apakah Anda yakin ingin menghapus pengguna ini?</p>
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