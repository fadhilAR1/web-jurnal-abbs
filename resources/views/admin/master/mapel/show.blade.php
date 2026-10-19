@extends('layouts.app')

@section('title', $mapel->nama_mapel)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">{{ $mapel->nama_mapel }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Mapel</label>
                        <p>{{ $mapel->nama_mapel }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kode Mapel</label>
                        <p>{{ $mapel->kode_mapel ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Program</label>
                        <p>{{ $mapel->program ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenjang</label>
                        <p>{{ $mapel->jenjang ? 'Kelas ' . $mapel->jenjang : '-' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.master-mapel.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection