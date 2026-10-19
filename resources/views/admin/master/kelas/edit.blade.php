@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Kelas</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.master-kelas.update', $kelas->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nama_kelas" class="form-label">Nama Kelas *</label>
                        <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" 
                               id="nama_kelas" name="nama_kelas" 
                               value="{{ old('nama_kelas', $kelas->nama_kelas) }}" 
                               placeholder="Contoh: X-A, XI-B" required>
                        @error('nama_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: [Tingkat]-[Rombel]</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tingkat" class="form-label">Tingkat *</label>
                        <select class="form-select @error('tingkat') is-invalid @enderror" 
                                id="tingkat" name="tingkat" required>
                            <option value="">Pilih Tingkat</option>
                            @foreach($tingkatList as $tingkat)
                                <option value="{{ $tingkat }}" {{ old('tingkat', $kelas->tingkat) == $tingkat ? 'selected' : '' }}>
                                    {{ $tingkat }}
                                </option>
                            @endforeach
                        </select>
                        @error('tingkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan *</label>
                        <input type="text" class="form-control @error('jurusan') is-invalid @enderror" 
                               id="jurusan" name="jurusan" 
                               value="{{ old('jurusan', $kelas->jurusan ?? 'IPA') }}" 
                               placeholder="Contoh: IPA, IPS" required>
                        @error('jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                        <div class="mb-3">
                            <label for="program" class="form-label">Program <span class="text-danger">*</span></label>
                            <select class="form-select @error('program') is-invalid @enderror" id="program" name="program" required>
                                <option value="">Pilih Program</option>
                                @foreach($programList as $p)
                                    <option value="{{ $p }}" {{ old('program', $kelas->program) == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('program')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas Siswa</label>
                        <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" 
                               id="kapasitas" name="kapasitas" 
                               value="{{ old('kapasitas', $kelas->kapasitas) }}" 
                               placeholder="Contoh: 36" min="1" max="40">
                        @error('kapasitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maksimal 40 siswa per kelas</small>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.master-kelas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
