@extends('layouts.app')

@section('title', 'Jurnal KBM')

@push('styles')
<style>
    /* Base table styles */
    .table-fixed {
        table-layout: fixed;
        width: 100%;
    }

    .table-fixed td, .table-fixed th {
        word-wrap: break-word;
        vertical-align: middle;
    }

    /* Mengizinkan teks turun maksimal 2 baris sebelum dipotong elipsis */
    .text-ellipsis {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical;
        white-space: normal;
        max-width: 100%;
        font-size: inherit;
    }

    /* Form Input Separators */
    .form-group-separator {
        border-right: 1px solid #dee2e6;
        padding-right: 15px;
    }
    .form-group-separator:last-child {
        border-right: none;
    }

    /* Action Buttons */
    .action-cell {
        padding: 0.3rem !important;
        vertical-align: middle;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: flex-start;
    }
    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Header Alignment */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header-actions {
        display: flex;
        gap: 8px;
    }

    /* =========================================
       Penyesuaian Khusus Layar HP (Mobile)
       ========================================= */
    @media (max-width: 768px) {
        /* Header responsif */
        .header-container {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 15px;
        }
        .header-actions {
            width: 100%;
        }
        .header-actions .btn {
            flex: 1; /* Membagi rata lebar */
            font-size: 0.75rem !important;
            padding: 0.4rem 0.2rem !important;
            white-space: normal;
            line-height: 1.2;
        }

        /* Tabel (Font dikecilkan agar muat banyak kolom) */
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.7rem !important;
            padding: 0.4rem 0.2rem !important;
        }

        /* Menumpuk tombol aksi (vertikal) agar hemat ruang */
        .action-buttons {
            flex-direction: column;
            gap: 4px;
        }
        .action-buttons .btn {
            width: 100%;
            font-size: 0.65rem !important;
            padding: 0.2rem 0 !important;
        }

        /* Hapus border separator pada form modal di mobile */
        .form-group-separator {
            border-right: none;
            padding-right: 0;
        }

        /* Proporsi kolom untuk 6 kolom di HP */
        .table-fixed th:nth-child(1) { width: 8% !important; }  /* No */
        .table-fixed th:nth-child(2) { width: 17% !important; } /* Tgl */
        .table-fixed th:nth-child(3) { width: 15% !important; } /* Kls */
        .table-fixed th:nth-child(4) { width: 22% !important; } /* Guru */
        .table-fixed th:nth-child(5) { width: 20% !important; } /* Mapel */
        .table-fixed th:nth-child(6) { width: 18% !important; } /* Aksi (Sempit karena tombol ditumpuk) */
    }

    /* =========================================
       Penyesuaian Khusus Tablet
       ========================================= */
    @media (min-width: 769px) and (max-width: 1024px) {
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.8rem;
            padding: 0.5rem 0.3rem !important;
        }

        .action-buttons {
            flex-direction: column; /* Ditumpuk juga di tablet agar Mapel & Guru lega */
            gap: 4px;
        }
        .action-buttons .btn {
            width: 100%;
            font-size: 0.7rem !important;
            padding: 0.2rem 0 !important;
        }

        .table-fixed th:nth-child(1) { width: 6% !important; }
        .table-fixed th:nth-child(2) { width: 14% !important; }
        .table-fixed th:nth-child(3) { width: 12% !important; }
        .table-fixed th:nth-child(4) { width: 23% !important; }
        .table-fixed th:nth-child(5) { width: 25% !important; }
        .table-fixed th:nth-child(6) { width: 20% !important; }

        .header-container { flex-direction: column; align-items: flex-start; gap: 15px; }
        .header-actions { width: 100%; display: flex; }
        .header-actions .btn { flex: 1; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="header-container mb-4">
            <div>
                <h2 class="mb-1"></i> Jurnal Kegiatan Belajar Mengajar</h2>
                <p class="text-muted small mb-0">Catat dan pantau proses kegiatan belajar mengajar</p>
            </div>
            <div class="header-actions">
                @if(auth()->user()->isAdmin())
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success action-btn">
                        Unduh Jurnal
                    </a>
                @endif
                <button class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addKBMModal">
                    Tambah Jurnal
                </button>
            </div>
        </div>

        <div class="card mb-4 border-0">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Filter Jurnal</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('jurnal.kbm') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-500">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ request('tanggal') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-500">Program</label>
                            <select name="program" class="form-select">
                                <option value="">Semua Program</option>
                                @foreach($programList as $p)
                                    <option value="{{ $p }}" {{ request('program') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-500">Kelas</label>
                            <select name="kelas_id" class="form-select" id="filterKelasSelect">
                                <option value="">Semua Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" 
                                        {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 action-btn">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th style="width: 12%;">Tanggal</th>
                                <th style="width: 15%;">Kelas</th>
                                <th style="width: 20%;">Pengajar</th>
                                <th style="width: 23%;">Mata Pelajaran</th>
                                <th style="width: 22%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurnals as $jurnal)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="text-ellipsis">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/y') }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->kelas->nama_kelas }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->guru?->name ?? '-' }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->mapel->nama_mapel }}</span></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <a href="{{ route('jurnal.kbm.show', $jurnal->id) }}" 
                                               class="btn btn-sm btn-outline-info action-btn" title="Lihat Detail">
                                                Lihat
                                            </a>
                                            @if(auth()->user()->isAdmin() || auth()->user()->id == $jurnal->guru_id)
                                                <button type="button" class="btn btn-sm btn-outline-warning action-btn" 
                                                        onclick="loadEditKBMModal({{ $jurnal->id }})" title="Edit">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger action-btn"
                                                        data-delete-url="{{ route('jurnal.kbm.destroy', $jurnal->id) }}"
                                                        onclick="confirmDelete(this)" title="Hapus">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada jurnal KBM</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-3">
                        {{ $jurnals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addKBMModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('jurnal.kbm.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurnal KBM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Program *</label>
                            <select id="programModalSelect" name="program_modal" class="form-select" required>
                                <option value="">Pilih Program</option>
                                @foreach($programList as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Kelas *</label>
                            <select name="kelas_id" id="kelasSelect" class="form-select" required>
                                <option value="">Pilih Kelas</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-select" required>
                                <option value="">Pilih Mapel</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" step="60" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" step="60" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Materi Pembelajaran</label>
                            <textarea name="materi" class="form-control" rows="3" 
                                      placeholder="Deskripsi materi yang diajarkan..." required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan Khusus</label>
                            <textarea name="catatan" class="form-control" rows="2" 
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <h6 class="mt-2 border-bottom pb-2">Presensi Siswa</h6>
                            <div id="siswaContainer" class="table-responsive">
                                <p class="text-muted text-sm">Pilih kelas terlebih dahulu untuk menampilkan daftar siswa</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editKBMModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editKBMForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editKBMId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jurnal KBM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Tanggal</label>
                            <input type="date" id="editTanggal" name="tanggal" class="form-control" 
                                   max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Program</label>
                            <select id="editProgramModal" name="program_modal" class="form-select" required>
                                <option value="">Pilih Program</option>
                                @foreach($programList as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Kelas</label>
                            <select id="editKelasSelect" name="kelas_id" class="form-select" required>
                                <option value="">Pilih Kelas</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Mata Pelajaran</label>
                            <select id="editMapelSelect" name="mapel_id" class="form-select" required>
                                <option value="">Pilih Mapel</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" id="editJamMulai" name="jam_mulai" class="form-control" step="60" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" id="editJamSelesai" name="jam_selesai" class="form-control" step="60" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Materi Pembelajaran</label>
                            <textarea id="editMateri" name="materi" class="form-control" rows="3" 
                                      placeholder="Deskripsi materi yang diajarkan..." required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan Khusus</label>
                            <textarea id="editCatatan" name="catatan" class="form-control" rows="2" 
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <h6 class="mt-2 border-bottom pb-2">Presensi Siswa</h6>
                            <div id="editSiswaContainer" class="table-responsive">
                                <p class="text-muted text-sm">Loading data siswa...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus jurnal ini?</p>
                <p class="text-muted mb-0"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(button) {
    const deleteUrl = button.getAttribute('data-delete-url');
    const form = document.getElementById('deleteForm');
    form.action = deleteUrl;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function loadEditKBMModal(id) {
    fetch(`{{ url('/jurnal/kbm') }}/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.ok ? r.json() : Promise.reject('Failed to load jurnal'))
    .then(data => {
        document.getElementById('editKBMId').value = data.id;
        document.getElementById('editTanggal').value = data.tanggal;
        document.getElementById('editMateri').value = data.materi || '';
        document.getElementById('editCatatan').value = data.catatan || '';

        const jamMulaiVal = data.jam_mulai ? data.jam_mulai.substring(0,5) : '';
        const jamSelesaiVal = data.jam_selesai ? data.jam_selesai.substring(0,5) : '';
        document.getElementById('editJamMulai').value = jamMulaiVal;
        document.getElementById('editJamSelesai').value = jamSelesaiVal;

        const form = document.getElementById('editKBMForm');
        form.action = `{{ url('/jurnal/kbm') }}/${data.id}`;

        const program = data.program || '';
        const editProgram = document.getElementById('editProgramModal');
        const editKelas = document.getElementById('editKelasSelect');
        const editMapel = document.getElementById('editMapelSelect');

        function setKelasAndMapel() {
            editKelas.value = data.kelas_id;
            if (data.kelas_id) {
                fetch(`{{ url('/jurnal/get-mapel') }}/${data.kelas_id}`)
                    .then(r => r.ok ? r.json() : Promise.reject('Failed'))
                    .then(mapels => {
                        editMapel.innerHTML = '<option value="">Pilih Mapel</option>';
                        mapels.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.nama_mapel;
                            editMapel.appendChild(opt);
                        });
                        editMapel.value = data.mapel_id || '';
                        loadEditSiswa(data.kelas_id, data.absensi);
                    })
                    .catch(err => {
                        console.error('Error loading mapels for edit modal:', err);
                        editMapel.innerHTML = '<option value="">Error memuat mapel</option>';
                        loadEditSiswa(data.kelas_id, data.absensi);
                    });
            } else {
                editMapel.innerHTML = '<option value="">Pilih Mapel</option>';
                loadEditSiswa(data.kelas_id, data.absensi);
            }
        }

        if (program) {
            editProgram.value = program;
            fetch(`{{ url('/jurnal/get-kelas-by-program') }}/${encodeURIComponent(program)}`)
                .then(r => r.ok ? r.json() : Promise.reject('Failed'))
                .then(ks => {
                    editKelas.innerHTML = '<option value="">Pilih Kelas</option>';
                    ks.forEach(k => {
                        const opt = document.createElement('option');
                        opt.value = k.id;
                        opt.textContent = k.nama_kelas;
                        editKelas.appendChild(opt);
                    });
                    setKelasAndMapel();
                })
                .catch(err => {
                    console.error('Error loading kelas for edit modal:', err);
                    setKelasAndMapel();
                });
        } else {
            setKelasAndMapel();
        }

        new bootstrap.Modal(document.getElementById('editKBMModal')).show();
    })
    .catch(error => {
        alert('Gagal memuat data jurnal: ' + error);
        console.error('Error:', error);
    });
}

function loadEditSiswa(kelasId, existingAbsensi) {
    const siswaContainer = document.getElementById('editSiswaContainer');
    if (!kelasId) {
        siswaContainer.innerHTML = '<p class="text-muted">Pilih kelas terlebih dahulu</p>';
        return;
    }
    
    fetch(`{{ url('/jurnal/get-siswa') }}/${kelasId}`)
        .then(r => r.ok ? r.json() : Promise.reject('Failed'))
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                siswaContainer.innerHTML = '<p class="text-muted">Tidak ada siswa untuk kelas ini</p>';
            } else {
                let html = '<table class="table table-sm table-bordered">';
                html += '<thead class="table-light"><tr><th>Nama Siswa</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpha</th></tr></thead>';
                html += '<tbody>';
                
                const absensiMap = {};
                try {
                    let parsed = existingAbsensi;
                    if (typeof existingAbsensi === 'string' && existingAbsensi.trim() !== '') {
                        parsed = JSON.parse(existingAbsensi);
                    }
                    if (Array.isArray(parsed)) {
                        parsed.forEach(a => {
                            if (a && a.nama) {
                                absensiMap[String(a.nama).trim()] = a.status;
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error parsing existingAbsensi:', e);
                }
                
                data.forEach((siswa, index) => {
                    const key = String(siswa.nama_siswa || '').trim();
                    const status = absensiMap[key] || 'Hadir';
                    html += `
                    <tr>
                        <td><small>${siswa.nama_siswa}</small>
                            <input type="hidden" name="absensi[${index}][nama]" value="${siswa.nama_siswa}">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Hadir" class="form-check-input" ${status === 'Hadir' ? 'checked' : ''} style="cursor: pointer;">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Izin" class="form-check-input" ${status === 'Izin' ? 'checked' : ''} style="cursor: pointer;">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Sakit" class="form-check-input" ${status === 'Sakit' ? 'checked' : ''} style="cursor: pointer;">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Alpha" class="form-check-input" ${status === 'Alpha' ? 'checked' : ''} style="cursor: pointer;">
                        </td>
                    </tr>`;
                });
                html += '</tbody></table>';
                siswaContainer.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            siswaContainer.innerHTML = '<p class="text-danger">Error memuat siswa</p>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const editKelasSelect = document.getElementById('editKelasSelect');
    const editMapelSelect = document.getElementById('editMapelSelect');
    
    if (editKelasSelect && editMapelSelect) {
        editKelasSelect.addEventListener('change', function() {
            const kelasId = this.value;
            if (!kelasId) {
                editMapelSelect.innerHTML = '<option value="">Pilih Mapel</option>';
                return;
            }
            
            fetch(`{{ url('/jurnal/get-mapel') }}/${kelasId}`)
                .then(r => r.ok ? r.json() : Promise.reject('Failed'))
                .then(mapels => {
                    editMapelSelect.innerHTML = '<option value="">Pilih Mapel</option>';
                    mapels.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = m.nama_mapel;
                        editMapelSelect.appendChild(opt);
                    });
                })
                .catch(err => {
                    console.error('Error:', err);
                    editMapelSelect.innerHTML = '<option value="">Error memuat mapel</option>';
                });
            
            loadEditSiswa(kelasId, []);
        });
    }
    
    const editProgramModal = document.getElementById('editProgramModal');
    if (editProgramModal) {
        editProgramModal.addEventListener('change', function() {
            const p = this.value;
            if (!p) {
                editKelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
                return;
            }
            
            fetch(`{{ url('/jurnal/get-kelas-by-program') }}/${p}`)
                .then(r => r.ok ? r.json() : Promise.reject('fail'))
                .then(ks => {
                    editKelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
                    ks.forEach(k => {
                        const opt = document.createElement('option');
                        opt.value = k.id;
                        opt.textContent = k.nama_kelas;
                        editKelasSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching kelas for edit modal:', err));
        });
    }
});

document.getElementById('kelasSelect').addEventListener('change', function() {
    const kelasId = this.value;
    const siswaContainer = document.getElementById('siswaContainer');
    const mapelSelect = document.querySelector('select[name="mapel_id"]');

    if (!kelasId) {
        siswaContainer.innerHTML = '<p class="text-muted">Pilih kelas terlebih dahulu untuk menampilkan daftar siswa</p>';
        mapelSelect.innerHTML = '<option value="">Pilih Mapel</option>';
        return;
    }

    fetch(`{{ url('/jurnal/get-siswa') }}/${kelasId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                siswaContainer.innerHTML = '<p class="text-muted">Tidak ada siswa untuk kelas ini</p>';
            } else {
                let html = '<table class="table table-sm table-bordered">';
                html += '<thead class="table-light"><tr><th>Nama Siswa</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpha</th></tr></thead>';
                html += '<tbody>';
                data.forEach((siswa, index) => {
                    html += `
                    <tr>
                        <td><small>${siswa.nama_siswa}</small>
                            <input type="hidden" name="absensi[${index}][nama]" value="${siswa.nama_siswa}">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Hadir" class="form-check-input" checked style="cursor: pointer;">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Izin" class="form-check-input" style="cursor: pointer;">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Sakit" class="form-check-input" style="cursor: pointer;">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="absensi[${index}][status]" value="Alpha" class="form-check-input" style="cursor: pointer;">
                        </td>
                    </tr>`;
                });
                html += '</tbody></table>';
                siswaContainer.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            siswaContainer.innerHTML = '<p class="text-danger"><strong>Error:</strong> ' + error.message + '</p>';
        });

    fetch(`{{ url('/jurnal/get-mapel') }}/${kelasId}`)
        .then(r => r.ok ? r.json() : Promise.reject('Failed to load mapel'))
        .then(mapels => {
            mapelSelect.innerHTML = '<option value="">Pilih Mapel</option>';
            mapels.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.nama_mapel;
                mapelSelect.appendChild(opt);
            });
        })
        .catch(err => {
            console.error('Error fetching mapel:', err);
            mapelSelect.innerHTML = '<option value="">Error memuat mapel</option>';
        });
});

(function() {
    const filterProgram = document.querySelector('select[name="program"]');
    const filterKelas = document.getElementById('filterKelasSelect');
    if (filterProgram && filterKelas) {
        filterProgram.addEventListener('change', function() {
            const p = this.value;
            if (!p) {
                filterKelas.innerHTML = '<option value="">Semua Kelas</option>';
                return;
            }
            fetch(`{{ url('/jurnal/get-kelas-by-program') }}/${p}`)
                .then(r => r.ok ? r.json() : Promise.reject('Failed to load kelas'))
                .then(ks => {
                    filterKelas.innerHTML = '<option value="">Semua Kelas</option>';
                    ks.forEach(k => {
                        const opt = document.createElement('option');
                        opt.value = k.id;
                        opt.textContent = k.nama_kelas;
                        filterKelas.appendChild(opt);
                    });
                })
                .catch(e => console.error(e));
        });
    }
})();

(function() {
    const modalProgram = document.querySelector('#addKBMModal select[name="program_modal"]');
    const modalKelas = document.getElementById('kelasSelect');
    if (modalProgram && modalKelas) {
        modalProgram.addEventListener('change', function() {
            const p = this.value;
            if (!p) {
                modalKelas.innerHTML = '<option value="">Pilih Kelas</option>';
                return;
            }
            fetch(`{{ url('/jurnal/get-kelas-by-program') }}/${p}`)
                .then(r => r.ok ? r.json() : Promise.reject('fail'))
                .then(ks => {
                    modalKelas.innerHTML = '<option value="">Pilih Kelas</option>';
                    ks.forEach(k => {
                        const opt = document.createElement('option');
                        opt.value = k.id;
                        opt.textContent = k.nama_kelas;
                        modalKelas.appendChild(opt);
                    });
                    if (ks.length === 1) {
                        modalKelas.value = ks[0].id;
                        modalKelas.dispatchEvent(new Event('change'));
                    }
                })
                .catch(err => console.error('Error fetching kelas for modal:', err));
        });
    }
})();
</script>
@endpush
@endsection