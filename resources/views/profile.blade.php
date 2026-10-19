@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4>Profil Guru/Staff</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="mb-3">
                                @if(auth()->user()->biodata && auth()->user()->biodata->foto)
                                    @php
                                        $fotoUrl = null;
                                        $path = ltrim(auth()->user()->biodata->foto, '/');
                                        if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                                            $fotoUrl = asset('storage/' . $path);
                                        } else {
                                            $fotoUrl = asset('images/placeholder.png');
                                        }
                                    @endphp
                                    <img src="{{ $fotoUrl }}" 
                                         class="img-fluid rounded-circle" 
                                         style="width: 200px; height: 200px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                         style="width: 200px; height: 200px; margin: 0 auto;">
                                        <span style="font-size: 5rem; color: white;">👤</span>
                                    </div>
                                @endif
                            </div>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Upload foto profil (max 2MB)</small>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label>NIP (Nomor Induk Pegawai)</label>
                                <input type="text" name="nip" class="form-control" 
                                       value="{{ auth()->user()->biodata->nip ?? '' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label>Jabatan</label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="Guru Mapel" {{ (auth()->user()->biodata->jabatan ?? '') == 'Guru Mapel' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                                    <option value="Wali Kelas" {{ (auth()->user()->biodata->jabatan ?? '') == 'Wali Kelas' ? 'selected' : '' }}>Wali Kelas</option>
                                    <option value="Staff TU" {{ (auth()->user()->biodata->jabatan ?? '') == 'Staff TU' ? 'selected' : '' }}>Staff Tata Usaha</option>
                                    <option value="Kepala Sekolah" {{ (auth()->user()->biodata->jabatan ?? '') == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="Wakil Kepala Sekolah" {{ (auth()->user()->biodata->jabatan ?? '') == 'Wakil Kepala Sekolah' ? 'selected' : '' }}>Wakil Kepala Sekolah</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label>Nomor HP</label>
                                <input type="text" name="no_hp" class="form-control" 
                                       value="{{ auth()->user()->biodata->no_hp ?? '' }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3" required>{{ auth()->user()->biodata->alamat ?? '' }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Simpan Profil</button>
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection