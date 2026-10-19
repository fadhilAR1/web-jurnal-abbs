@extends('layouts.app')

@section('title', $kelas->nama_kelas)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">{{ $kelas->nama_kelas }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Kelas</label>
                        <p>{{ $kelas->nama_kelas }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tingkat / Program</label>
                        <p>{{ $kelas->tingkat ?? '-' }} / {{ $kelas->program ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jurusan</label>
                        <p>{{ $kelas->jurusan ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kapasitas</label>
                        <p>{{ $kelas->kapasitas !== null ? $kelas->kapasitas : '-' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.master-kelas.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

                <hr>

                <h5>Daftar Siswa</h5>
                @if($students->count() > 0)
                    <ul class="list-group mt-2">
                        @foreach($students as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $s->nama_siswa }}</strong>
                                    <div class="text-muted">{{ $s->jenis_kelamin ?? '' }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum ada siswa yang terdaftar di kelas ini.</p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection