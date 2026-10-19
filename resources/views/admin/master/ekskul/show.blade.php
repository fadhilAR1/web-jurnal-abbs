@extends('layouts.app')

@section('title', $ekskul->nama_ekskul)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">{{ $ekskul->nama_ekskul }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Ekskul</label>
                        <p>{{ $ekskul->nama_ekskul }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kapasitas</label>
                        <p>{{ $ekskul->kapasitas !== null ? $ekskul->kapasitas : 'Tidak ditentukan' }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.master-ekskul.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

                <hr>

                <h5>Daftar Peserta</h5>
                @if($ekskul->participants && $ekskul->participants->count() > 0)
                    <ul class="list-group mt-2">
                        @foreach($ekskul->participants as $p)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $p->siswa->nama_siswa }}</strong>
                                    <div class="text-muted">{{ $p->siswa->jenis_kelamin ?? '' }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum ada peserta yang terdaftar di ekstrakurikuler ini (masih kosong / belum terdeteksi).</p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
