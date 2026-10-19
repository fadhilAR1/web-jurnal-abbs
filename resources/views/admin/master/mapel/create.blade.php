@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Mata Pelajaran</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.master-mapel.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nama_mapel" class="form-label"><strong>Nama Mapel</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_mapel') is-invalid @enderror" 
                               id="nama_mapel" name="nama_mapel" placeholder="Contoh: Matematika"
                               value="{{ old('nama_mapel') }}" required>
                        @error('nama_mapel')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="program" class="form-label"><strong>Program</strong> <span class="text-danger">*</span></label>
                        <select class="form-select @error('program') is-invalid @enderror" 
                                id="program" name="program" required>
                            <option value="">-- Pilih Program --</option>
                            <option value="Cambridge" {{ old('program') == 'Cambridge' ? 'selected' : '' }}>CAMBRIDGE</option>
                            <option value="ICT" {{ old('program') == 'ICT' ? 'selected' : '' }}>ICT</option>
                            <option value="Tahfidz" {{ old('program') == 'Tahfidz' ? 'selected' : '' }}>TAHFIDZ</option>
                        </select>
                        @error('program')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenjang" class="form-label"><strong>Jenjang</strong> <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenjang') is-invalid @enderror" 
                                id="jenjang" name="jenjang" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="X" {{ old('jenjang') == 'X' ? 'selected' : '' }}>Kelas X</option>
                            <option value="XI" {{ old('jenjang') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
                            <option value="XII" {{ old('jenjang') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
                        </select>
                        @error('jenjang')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kode_mapel" class="form-label"><strong>Kode Mapel</strong> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_mapel') is-invalid @enderror" 
                               id="kode_mapel" name="kode_mapel" placeholder="Format: PROGRAM-MAPEL-JENJANG"
                               value="{{ old('kode_mapel') }}" required>
                        <small class="text-muted d-block mt-2">
                            <strong>Format:</strong> SINGKAT_PROGRAM-SINGKAT_MAPEL-JENJANG<br>
                            <strong>Contoh:</strong> CAM-MAT-X (Cambridge-Matematika-Kelas X)<br>
                            <strong>Singkatan Program:</strong> CAM (Cambridge), ICT, TAH (Tahfidz)<br>
                            <strong>Jenjang:</strong> X, XI, XII
                        </small>
                        @error('kode_mapel')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.master-mapel.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Mata Pelajaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
