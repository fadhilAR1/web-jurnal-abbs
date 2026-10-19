@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="row">
    <div class="col-12 col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Edit Siswa</h2>
                <p class="text-muted small mb-0">Perbarui informasi siswa</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.master-siswa.update', $siswa->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap *</label>
                        <input name="nama_siswa" class="form-control" value="{{ old('nama_siswa', $siswa->nama_siswa) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ $siswa->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $siswa->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}">
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.master-siswa.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
