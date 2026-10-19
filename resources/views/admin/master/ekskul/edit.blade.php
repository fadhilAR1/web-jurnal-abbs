@extends('layouts.app')

@section('title', 'Edit Ekstrakurikuler')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Ekstrakurikuler</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.master-ekskul.update', $ekskul->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nama_ekskul" class="form-label">Nama Ekskul *</label>
                        <input type="text" class="form-control @error('nama_ekskul') is-invalid @enderror" 
                               id="nama_ekskul" name="nama_ekskul" 
                               value="{{ old('nama_ekskul', $ekskul->nama_ekskul) }}" required>
                        @error('nama_ekskul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas Peserta</label>
                        <input type="number" min="1" class="form-control @error('kapasitas') is-invalid @enderror" id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $ekskul->kapasitas ?? 30) }}">
                        <div class="form-text">Biarkan kosong atau isi 0 untuk tanpa batas.</div>
                        @error('kapasitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    

                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.master-ekskul.index') }}" class="btn btn-secondary">
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
