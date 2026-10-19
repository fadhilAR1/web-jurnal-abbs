@extends('layouts.app')

@section('title', 'Kelola Siswa Kelas: ' . $kelas->nama_kelas)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Siswa: {{ $kelas->nama_kelas }}</h3>
            <a href="{{ route('admin.master-kelas.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.master-kelas.students.store', $kelas->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="siswa_id">Tambah Siswa</label>
                        <input type="search" id="searchSiswa" class="form-control mb-2" placeholder="Cari siswa...">
                        <select name="siswa_id[]" id="siswa_id" class="form-select" multiple size="8">
                            @forelse($availableSiswa as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_siswa }} @if($s->kelas) ({{ $s->kelas->nama_kelas }}) @endif</option>
                            @empty
                                <option disabled>Tidak ada siswa tersedia</option>
                            @endforelse
                        </select>
                        <div class="form-text">Pilih satu atau lebih siswa untuk ditambahkan ke kelas ini.</div>
                        @error('siswa_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <strong>Jumlah Siswa Saat Ini:</strong> {{ $students->count() }}
                    </div>

                    <div>
                        <button class="btn btn-primary">Tambah Siswa</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Daftar Siswa Saat Ini</div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th style="width:10%;" class="col-no">No</th>
                            <th style="width:50%;" class="col-name">Nama</th>
                            <th style="width:10%;">Jenis Kelamin</th>
                            <th style="width:30%;" class="col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama_siswa ?? '-' }}</td>
                                <td>{{ $p->jenis_kelamin ?? '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="sweetConfirmDelete('{{ route('admin.master-kelas.students.destroy', [$kelas->id, $p->id]) }}', 'Hapus siswa dari kelas?')">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada siswa untuk kelas ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sweetConfirmDelete(url, message = 'Apakah Anda yakin ingin menghapus item ini?') {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: message + '<br><small>Tindakan ini tidak dapat dibatalkan.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.style.display = 'none';
            const token = document.createElement('input');
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);
            const method = document.createElement('input');
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const search = document.getElementById('searchSiswa');
    const select = document.getElementById('siswa_id');
    if (search && select) {
        search.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            for (const option of select.options) {
                const text = option.text.toLowerCase();
                option.style.display = text.includes(q) ? '' : 'none';
            }
        });
    }
});
</script>
@endpush

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage">Apakah Anda yakin ingin menghapus item ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection