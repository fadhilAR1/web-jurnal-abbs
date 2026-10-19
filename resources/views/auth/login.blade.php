@extends('layouts.public')

@section('title', 'Login')

@push('styles')
<style>
    html, body {
        height: 100vh;
        overflow: hidden;
    }
    /* Mobile: make form labels and checkbox label align left */
    @media (max-width: 576px) {
        .card-body .form-label,
        .card-body .form-check-label {
            text-align: left !important;
        }
        .card-body .form-check {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center" style="min-height: calc(100vh - 200px); display: flex; align-items: center;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg modern-login-card border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/Logo_SMA_ABBS.png') }}" alt="Logo" class="login-logo-center mb-3" onerror="this.style.display='none'">
                    <h5 class="fw-bold text-secondary mb-1">Sistem Monitoring Jurnal</h5>
                </div>

                <hr class="my-4">

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Login Gagal!</strong>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">Username</label>
                        <input type="text" class="form-control form-control-lg @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username') }}" required autofocus
                               placeholder="Masukkan username Anda">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" name="password" required
                                   placeholder="Masukkan password Anda">
                            <button type="button" class="position-absolute end-0 top-50 translate-middle-y" 
                                    id="togglePassword" style="z-index: 10; background: none; border: none; color: #6c757d; padding: 0.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const icon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
</script>
@endsection
