@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-house-door"></i> Tambah Kelas</h2>
            <a href="{{ route('admin.master-kelas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Form Tambah Kelas</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.master-kelas.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_kelas" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" 
                                       id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas') }}" required>
                                @error('nama_kelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select @error('tingkat') is-invalid @enderror" 
                                        id="tingkat" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    @foreach($tingkatList as $tingkat)
                                        <option value="{{ $tingkat }}" {{ old('tingkat') == $tingkat ? 'selected' : '' }}>
                                            {{ $tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tingkat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('jurusan') is-invalid @enderror" 
                                       id="jurusan" name="jurusan" value="{{ old('jurusan', 'IPA') }}" required>
                                @error('jurusan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="program" class="form-label">Program <span class="text-danger">*</span></label>
                        <select class="form-select @error('program') is-invalid @enderror" 
                                id="program" name="program" required>
                            <option value="">Pilih Program</option>
                            @foreach($programList as $p)
                                <option value="{{ $p }}" {{ old('program') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('program')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas Siswa</label>
                        <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" 
                               id="kapasitas" name="kapasitas" value="{{ old('kapasitas', 32) }}" min="1" max="40">
                        @error('kapasitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection