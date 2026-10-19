@extends('layouts.public')

@section('title', 'Permohonan Persetujuan')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-hourglass-split" style="font-size: 3rem; color: #ffc107;"></i>
                    <h3 class="mt-3">Permohonan Anda Sedang Diproses</h3>
                    <p class="text-muted mt-2">
                        Terima kasih telah mendaftar. Permohonan Anda sedang menunggu persetujuan dari administrator. 
                        Anda akan mendapatkan notifikasi via email ketika akun Anda disetujui.
                    </p>
                    <hr>
                    <p class="small text-muted">
                        Proses persetujuan biasanya memakan waktu 1-2 hari kerja.
                    </p>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary mt-3">
                            Kembali Ke Beranda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
