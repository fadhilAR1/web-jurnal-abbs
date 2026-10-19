@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $siswa->nama_siswa)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Detail Siswa</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <p>{{ $siswa->nama_siswa }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIS</label>
                        <p>{{ $siswa->nis ?? '-' }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <p>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kelas</label>
                        <p>{{ $siswa->kelas?->nama_kelas ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.master-siswa.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
