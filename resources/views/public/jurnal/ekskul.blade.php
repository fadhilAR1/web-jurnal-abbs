@extends('layouts.public')

@section('title', 'Jurnal Ekstrakurikuler')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Jurnal Ekstrakurikuler</h2>
            <div class="btn-group">
                <a href="{{ route('public.jurnal.kbm') }}" class="btn btn-outline-primary">KBM</a>
                <a href="{{ route('public.jurnal.ekskul') }}" class="btn btn-outline-primary active">Ekskul</a>
                <a href="{{ route('public.jurnal.piket') }}" class="btn btn-outline-primary">Piket</a>
                <a href="{{ route('public.jurnal.kegiatan') }}" class="btn btn-outline-primary">Kegiatan</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Guru</th>
                                <th>Ekstrakurikuler</th>
                                <th>Tanggal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurnals as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->user->name ?? '-' }}</td>
                                    <td>{{ $item->ekskul->nama_ekskul ?? '-' }}</td>
                                    <td>{{ $item->tanggal->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ Str::limit($item->catatan, 50) ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($jurnals))
                    <div class="mt-3">
                        {{ $jurnals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
