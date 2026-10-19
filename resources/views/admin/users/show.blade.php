@extends('layouts.app')

@section('title', 'Detail Pengguna - ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Detail Pengguna</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <p>{{ $user->username }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <p><code class="text-break">{{ $user->password }}</code></p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
