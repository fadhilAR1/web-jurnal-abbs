@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Edit Jurnal KBM</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('jurnal.kbm') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <form action="{{ route('jurnal.kbm.update', $jurnal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                               id="tanggal" name="tanggal" value="{{ old('tanggal', $jurnal->tanggal->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select class="form-select @error('kelas_id') is-invalid @enderror" 
                                id="kelasSelect" name="kelas_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id', $jurnal->kelas_id) == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="mapel_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('mapel_id') is-invalid @enderror" 
                                id="mapel_id" name="mapel_id" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach ($mapel as $m)
                                <option value="{{ $m->id }}" {{ old('mapel_id', $jurnal->mapel_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        @error('mapel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" 
                               id="jam_mulai" name="jam_mulai" 
                               value="{{ old('jam_mulai', $jurnal->jam_mulai ? substr($jurnal->jam_mulai, 0, 5) : '') }}" 
                               required>
                        @error('jam_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" 
                               id="jam_selesai" name="jam_selesai" 
                               value="{{ old('jam_selesai', $jurnal->jam_selesai ? substr($jurnal->jam_selesai, 0, 5) : '') }}" 
                               required>
                        @error('jam_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="materi" class="form-label">Materi Pembelajaran <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('materi') is-invalid @enderror" 
                              id="materi" name="materi" rows="3" required>{{ old('materi', $jurnal->materi) }}</textarea>
                    @error('materi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control @error('catatan') is-invalid @enderror" 
                              id="catatan" name="catatan" rows="2">{{ old('catatan', $jurnal->catatan) }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Absensi Section -->
                <div class="mb-4">
                    <label class="form-label"><strong>Absensi Siswa</strong></label>
                    <div id="siswaContainer" class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">Nama Siswa</th>
                                    <th style="width: 15%;" class="text-center">Hadir</th>
                                    <th style="width: 15%;" class="text-center">Izin</th>
                                    <th style="width: 15%;" class="text-center">Sakit</th>
                                    <th style="width: 15%;" class="text-center">Alpha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Siswa akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('jurnal.kbm') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const kelasSelect = document.getElementById('kelasSelect');
    const siswaContainer = document.getElementById('siswaContainer');
    const currentAbsensi = @json($jurnal->absensi ?? []);

    // Parse absensi data
    let absensiMap = {};
    if (Array.isArray(currentAbsensi)) {
        currentAbsensi.forEach(item => {
            absensiMap[item.nama] = item.status;
        });
    } else if (typeof currentAbsensi === 'string') {
        try {
            const parsed = JSON.parse(currentAbsensi);
            if (Array.isArray(parsed)) {
                parsed.forEach(item => {
                    absensiMap[item.nama] = item.status;
                });
            }
        } catch (e) {
            console.error('Error parsing absensi:', e);
        }
    }

    // Function to load students
    async function loadStudents() {
        const kelasId = kelasSelect.value;
        if (!kelasId) {
            siswaContainer.querySelector('tbody').innerHTML = 
                '<tr><td colspan="5" class="text-center text-muted">Pilih kelas terlebih dahulu</td></tr>';
            return;
        }

        try {
            const response = await fetch(`{{ url('/jurnal/get-siswa') }}/${kelasId}`);
            const siswa = await response.json();

            if (siswa.length === 0) {
                siswaContainer.querySelector('tbody').innerHTML = 
                    '<tr><td colspan="5" class="text-center text-muted">Tidak ada siswa di kelas ini</td></tr>';
            } else {
                let html = '';
                let index = 0;
                siswa.forEach(s => {
                    const status = absensiMap[s.nama_siswa] || 'Hadir';
                    html += `
                        <tr>
                            <td>
                                ${s.nama_siswa}
                                <input type="hidden" name="absensi[${index}][nama]" value="${s.nama_siswa}">
                            </td>
                            <td class="text-center">
                                <input type="radio" class="form-check-input" name="absensi[${index}][status]" value="Hadir" 
                                       ${status === 'Hadir' ? 'checked' : ''} style="cursor: pointer;">
                            </td>
                            <td class="text-center">
                                <input type="radio" class="form-check-input" name="absensi[${index}][status]" value="Izin" 
                                       ${status === 'Izin' ? 'checked' : ''} style="cursor: pointer;">
                            </td>
                            <td class="text-center">
                                <input type="radio" class="form-check-input" name="absensi[${index}][status]" value="Sakit" 
                                       ${status === 'Sakit' ? 'checked' : ''} style="cursor: pointer;">
                            </td>
                            <td class="text-center">
                                <input type="radio" class="form-check-input" name="absensi[${index}][status]" value="Alpha" 
                                       ${status === 'Alpha' ? 'checked' : ''} style="cursor: pointer;">
                            </td>
                        </tr>
                    `;
                    index++;
                });
                siswaContainer.querySelector('tbody').innerHTML = html;
            }
        } catch (error) {
            console.error('Error loading students:', error);
            siswaContainer.querySelector('tbody').innerHTML = 
                '<tr><td colspan="5" class="text-center text-danger">Error loading students</td></tr>';
        }
    }

    // Function to load mapel for the selected class
    async function loadMapels() {
        const kelasId = kelasSelect.value;
        const mapelSelect = document.getElementById('mapel_id');
        if (!kelasId) {
            mapelSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
            return;
        }

        try {
            const res = await fetch(`{{ url('/jurnal/get-mapel') }}/${kelasId}`);
            const mapels = await res.json();
            mapelSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
            mapels.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.nama_mapel;
                mapelSelect.appendChild(opt);
            });
            // If current jurnal has selected mapel, set it
            const currentMapel = {{ json_encode(old('mapel_id', $jurnal->mapel_id ?? null)) }};
            if (currentMapel) {
                mapelSelect.value = currentMapel;
            }
        } catch (e) {
            console.error('Error loading mapels:', e);
            mapelSelect.innerHTML = '<option value="">Error memuat mapel</option>';
        }
    }

    // Trigger loadStudents and loadMapels on page load
    loadStudents();
    loadMapels();

    // Trigger loadStudents and loadMapels when class changes
    kelasSelect.addEventListener('change', function() {
        loadStudents();
        loadMapels();
    });
});
</script>
@endsection
