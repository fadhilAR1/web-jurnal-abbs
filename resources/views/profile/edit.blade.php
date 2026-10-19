@extends('layouts.app')

@section('title', 'Akun')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Pengaturan Akun</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nama Lengkap</th>
                        <td><strong>{{ auth()->user()->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><code>{{ auth()->user()->username }}</code></td>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span id="passwordDisplay">••••••••••</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="togglePasswordBtn" onclick="togglePassword()">
                                    Lihat
                                </button>
                            </div>
                            <input type="hidden" id="passwordValue" value="{{ auth()->user()->password }}">
                        </td>
                    </tr>
                </table>
                <hr>

                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let passwordVisible = false;

function togglePassword() {
    const passwordDisplay = document.getElementById('passwordDisplay');
    const passwordValue = document.getElementById('passwordValue').value;
    const toggleBtn = document.getElementById('togglePasswordBtn');

    if (passwordVisible) {
        passwordDisplay.textContent = '••••••••••';
        toggleBtn.innerHTML = 'Lihat';
        passwordVisible = false;
    } else {
        passwordDisplay.textContent = passwordValue;
        toggleBtn.innerHTML = 'Sembunyikan';
        passwordVisible = true;
    }
}
</script>
@endsection
