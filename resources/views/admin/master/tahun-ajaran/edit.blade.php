@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Tahun Ajaran</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.master-tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran *</label>
                        <input type="text" class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                               id="tahun_ajaran" name="tahun_ajaran" 
                               value="{{ old('tahun_ajaran', $tahunAjaran->tahun_ajaran) }}" placeholder="Contoh: 2024/2025" required>
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $tahunAjaran->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.master-tahun-ajaran.index') }}" class="btn btn-secondary">
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
