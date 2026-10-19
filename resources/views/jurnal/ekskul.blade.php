@extends('layouts.app')

@section('title', 'Jurnal Ekstrakurikuler')

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

    /* Action Buttons - Base */
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

    .action-cell form {
        display: inline;
        margin: 0;
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
            flex: 1; /* Membagi rata lebar tombol */
            font-size: 0.75rem !important;
            padding: 0.4rem 0.2rem !important;
            white-space: normal;
            line-height: 1.2;
        }

        /* Tabel (Font dikecilkan agar muat banyak kolom) */
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.75rem !important;
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

        /* Proporsi kolom tabel di HP */
        .table-fixed th:nth-child(1) { width: 10% !important; } /* No */
        .table-fixed th:nth-child(2) { width: 18% !important; } /* Tgl */
        .table-fixed th:nth-child(3) { width: 28% !important; } /* Ekskul */
        .table-fixed th:nth-child(4) { width: 26% !important; } /* Guru */
        .table-fixed th:nth-child(5) { width: 18% !important; } /* Aksi (Sempit, tombol ditumpuk) */
    }

    /* =========================================
       Penyesuaian Khusus Tablet
       ========================================= */
    @media (min-width: 769px) and (max-width: 1024px) {
        .table-responsive table td,
        .table-responsive table th {
            font-size: 0.85rem;
            padding: 0.5rem 0.3rem !important;
        }

        .action-buttons {
            flex-direction: column; /* Ditumpuk juga di tablet agar lega */
            gap: 4px;
        }
        .action-buttons .btn {
            width: 100%;
            font-size: 0.7rem !important;
            padding: 0.2rem 0 !important;
        }

        .table-fixed th:nth-child(1) { width: 8% !important; }
        .table-fixed th:nth-child(2) { width: 15% !important; }
        .table-fixed th:nth-child(3) { width: 28% !important; }
        .table-fixed th:nth-child(4) { width: 29% !important; }
        .table-fixed th:nth-child(5) { width: 20% !important; }

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
                <h2 class="mb-1"></i> Jurnal Ekstrakurikuler</h2>
                <p class="text-muted small mb-0">Catat dan pantau kegiatan ekstrakurikuler</p>
            </div>
            <div class="header-actions">
                @if(auth()->user()->isAdmin())
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success action-btn">
                        Unduh Jurnal
                    </a>
                @endif
                <button class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addEkskulModal">
                    Tambah Jurnal
                </button>
            </div>
        </div>

        <div class="card mb-4 border-0">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Filter Jurnal</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('jurnal.ekskul') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-500">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ request('tanggal') }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
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
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 25%;">Ekskul</th>
                                <th style="width: 30%;">Pengajar</th>
                                <th style="width: 22%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurnals as $jurnal)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="text-ellipsis">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/y') }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->ekskul->nama_ekskul }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->guru?->name ?? '-' }}</span></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <a href="{{ route('jurnal.ekskul.show', $jurnal->id) }}" class="btn btn-sm btn-outline-info action-btn" title="Tampilkan">
                                                Lihat
                                            </a>
                                            @if(auth()->user()->isAdmin() || auth()->user()->id == $jurnal->guru_id)
                                                <button class="btn btn-sm btn-outline-warning action-btn" onclick="editEkskul({{ $jurnal->id }})" title="Edit">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger action-btn"
                                                        data-delete-url="{{ route('jurnal.ekskul.destroy', $jurnal->id) }}"
                                                        onclick="confirmDelete(this)" title="Hapus">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @if($jurnal->foto_kegiatan && is_array($jurnal->foto_kegiatan) && count($jurnal->foto_kegiatan) > 0)
                                <div class="modal fade" id="fotoModal{{ $jurnal->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Foto Kegiatan: {{ $jurnal->ekskul->nama_ekskul }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    @foreach($jurnal->foto_kegiatan as $foto)
                                                        <div class="col-md-6 mb-3">
                                                            <img src="{{ asset('storage/' . $foto) }}" 
                                                                class="img-fluid rounded" 
                                                                alt="Foto Kegiatan Ekskul"
                                                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'250\'%3E%3Crect fill=\'%23e9ecef\' width=\'400\' height=\'250\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'14\' fill=\'%23999\'%3EFile not found%3C/text%3E%3C/svg%3E'; this.style.opacity='0.5';">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada jurnal ekstrakurikuler</small>
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

<div class="modal fade" id="addEkskulModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('jurnal.ekskul.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurnal Ekstrakurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Ekstrakurikuler</label>
                            <select name="ekskul_id" id="addEkskulId" class="form-select" required>
                                <option value="">Pilih Ekstrakurikuler</option>
                                @foreach($ekskulList as $ekskul)
                                    <option value="{{ $ekskul->id }}">{{ $ekskul->nama_ekskul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" step="60" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" step="60" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Materi Kegiatan</label>
                            <textarea name="materi" class="form-control" rows="3" 
                                      placeholder="Deskripsi materi kegiatan ekskul..." required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2" 
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Foto Kegiatan (Maksimal 5 foto)</label>
                            <input type="file" id="addEkskulFotoInput" name="foto_kegiatan[]" class="form-control" multiple accept="image/*">
                            <div id="fotoPreviewEkskul" class="mt-3" style="display:none;">
                                <h6>Preview Foto yang Dipilih:</h6>
                                <div id="fotoPreviewContainerEkskul" class="row"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Presensi Peserta</h6>
                            <div id="siswaContainer" class="table-responsive">
                                <p class="text-muted">Pilih ekstrakurikuler terlebih dahulu untuk menampilkan daftar peserta</p>
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

<div class="modal fade" id="editEkskulModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data" id="editEkskulForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jurnal Ekstrakurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Tanggal</label>
                            <input type="date" id="editTanggal" name="tanggal" class="form-control" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Ekstrakurikuler</label>
                            <select id="editEkskulId" name="ekskul_id" class="form-select" required>
                                <option value="">Pilih Ekstrakurikuler</option>
                                @foreach($ekskulList as $ekskul)
                                    <option value="{{ $ekskul->id }}">{{ $ekskul->nama_ekskul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Jam Mulai</label>
                            <input type="time" id="editJamMulai" name="jam_mulai" class="form-control" step="60" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Jam Selesai</label>
                            <input type="time" id="editJamSelesai" name="jam_selesai" class="form-control" step="60" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Materi Kegiatan</label>
                            <textarea id="editMateri" name="materi" class="form-control" rows="3" 
                                      placeholder="Deskripsi materi kegiatan ekskul..." required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Catatan</label>
                            <textarea id="editCatatan" name="catatan" class="form-control" rows="2" 
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Foto Kegiatan (Maksimal 5 foto)</label>
                            <input type="file" id="editEkskulFotoInput" name="foto_kegiatan[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah foto</small>
                            <div id="fotoPreviewEkskulEdit" class="mt-3" style="display:none;">
                                <h6>Preview Foto yang Dipilih:</h6>
                                <div id="fotoPreviewContainerEkskulEdit" class="row"></div>
                            </div>
                        </div>
                        <div class="col-12 mb-3" id="existingFotoContainer">
                            {{-- Existing photos will be rendered here when editing --}}
                        </div>
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Presensi Peserta</h6>
                            <div id="editSiswaContainer" class="table-responsive">
                                <p class="text-muted">Pilih ekstrakurikuler terlebih dahulu untuk menampilkan daftar peserta</p>
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
// Prevent persistent spinner/animation in the top navbar on initial load for this page
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('navbar');
    if (navbar) {
        // hide any common spinner elements and disable animations inside navbar
        navbar.querySelectorAll('.spinner-border, .fa-spin, .bi-arrow-repeat, .bi-arrow-clockwise, .loading, .loading-spinner').forEach(el => {
            el.style.display = 'none';
            el.style.animation = 'none';
            el.style.transform = 'none';
        });
        navbar.querySelectorAll('*').forEach(el => {
            try { if (getComputedStyle(el).animationName && getComputedStyle(el).animationName !== 'none') el.style.animation = 'none'; } catch(e) {}
        });
    }
});

function confirmDelete(button) {
    const deleteUrl = button.getAttribute('data-delete-url');
    const form = document.getElementById('deleteForm');
    form.action = deleteUrl;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function buildSiswaTable(containerElement, siswaList, existingAbsensi) {
    if (!siswaList || siswaList.length === 0) {
        containerElement.innerHTML = '<p class="text-muted">Tidak ada peserta untuk ekstrakurikuler ini</p>';
        return;
    }

    let html = '<table class="table table-sm table-bordered">';
    html += '<thead class="table-light"><tr><th>Nama Peserta</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpha</th></tr></thead>';
    html += '<tbody>';

    const statusMap = {};
    let parsedAbsensi = existingAbsensi;
    if (typeof existingAbsensi === 'string' && existingAbsensi.trim() !== '') {
        try {
            parsedAbsensi = JSON.parse(existingAbsensi);
        } catch (e) {
            console.error('Error parsing absensi:', e);
        }
    }
    
    if (Array.isArray(parsedAbsensi)) {
        parsedAbsensi.forEach(a => {
            if (!a) return;
            if (a.siswa_id) {
                statusMap['id_' + a.siswa_id] = (a.status || '').toLowerCase();
            }
            if (a.nama) {
                const trimmedName = String(a.nama).trim();
                statusMap['name_' + trimmedName] = (a.status || '').toLowerCase();
            }
        });
    }

    siswaList.forEach((siswa, index) => {
        const keyById = 'id_' + siswa.id;
        const keyByName = 'name_' + String(siswa.nama_siswa || '').trim();
        const existingStatus = statusMap[keyById] ?? statusMap[keyByName] ?? 'hadir';

        html += `<tr>
            <td><small>${siswa.nama_siswa}</small></td>
            <td class="text-center"><input type="radio" name="absensi[${index}][status]" value="hadir" class="form-check-input" ${existingStatus === 'hadir' ? 'checked' : ''} style="cursor: pointer;"></td>
            <td class="text-center"><input type="radio" name="absensi[${index}][status]" value="izin" class="form-check-input" ${existingStatus === 'izin' ? 'checked' : ''} style="cursor: pointer;"></td>
            <td class="text-center"><input type="radio" name="absensi[${index}][status]" value="sakit" class="form-check-input" ${existingStatus === 'sakit' ? 'checked' : ''} style="cursor: pointer;"></td>
            <td class="text-center"><input type="radio" name="absensi[${index}][status]" value="alpha" class="form-check-input" ${existingStatus === 'alpha' ? 'checked' : ''} style="cursor: pointer;"></td>
            <input type="hidden" name="absensi[${index}][siswa_id]" value="${siswa.id}">
            <input type="hidden" name="absensi[${index}][nama]" value="${siswa.nama_siswa}">
        </tr>`;
    });

    html += '</tbody></table>';
    containerElement.innerHTML = html;
}

// Add modal: load siswa when ekskul selected
const addEkskulSelect = document.getElementById('addEkskulId');
if (addEkskulSelect) {
    addEkskulSelect.addEventListener('change', function() {
        const ekskulId = this.value;
        const siswaContainer = document.getElementById('siswaContainer');
        if (!ekskulId) {
            siswaContainer.innerHTML = '<p class="text-muted">Pilih ekstrakurikuler terlebih dahulu untuk menampilkan daftar peserta</p>';
            return;
        }
        siswaContainer.innerHTML = '<p class="text-muted">Memuat peserta...</p>';
        fetch(`{{ url('/jurnal/get-siswa-ekskul') }}/${ekskulId}`)
            .then(async response => {
                if (!response.ok) {
                    const txt = await response.text();
                    throw new Error('Server error: ' + response.status + ' ' + txt);
                }
                return response.json();
            })
            .then(data => buildSiswaTable(siswaContainer, data, []))
            .catch(err => {
                console.error(err);
                siswaContainer.innerHTML = '<p class="text-danger"><strong>Error:</strong> ' + err.message + '</p>';
            });
    });
}

// Edit modal: when ekskul changes load siswa
const editEkskulSelect = document.getElementById('editEkskulId');
if (editEkskulSelect) {
    editEkskulSelect.addEventListener('change', function() {
        const ekskulId = this.value;
        const siswaContainer = document.getElementById('editSiswaContainer');
        if (!ekskulId) {
            siswaContainer.innerHTML = '<p class="text-muted">Pilih ekstrakurikuler terlebih dahulu untuk menampilkan daftar peserta</p>';
            return;
        }
        siswaContainer.innerHTML = '<p class="text-muted">Memuat peserta...</p>';
        fetch(`{{ url('/jurnal/get-siswa-ekskul') }}/${ekskulId}`)
            .then(async response => {
                if (!response.ok) {
                    const txt = await response.text();
                    throw new Error('Server error: ' + response.status + ' ' + txt);
                }
                return response.json();
            })
            .then(data => buildSiswaTable(siswaContainer, data, []))
            .catch(err => {
                console.error(err);
                siswaContainer.innerHTML = '<p class="text-danger"><strong>Error:</strong> ' + err.message + '</p>';
            });
    });
}

function editEkskul(id) {
    fetch(`{{ url('jurnal/ekskul') }}/${id}/edit`)
        .then(async response => {
            if (!response.ok) {
                const txt = await response.text();
                throw new Error('Server error: ' + response.status + ' ' + txt);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('editTanggal').value = data.tanggal ? (data.tanggal.substr ? data.tanggal.substr(0,10) : data.tanggal) : '';
            document.getElementById('editEkskulId').value = data.ekskul_id;
            document.getElementById('editJamMulai').value = data.jam_mulai ?? '';
            document.getElementById('editJamSelesai').value = data.jam_selesai ?? '';
            document.getElementById('editMateri').value = data.materi || '';
            document.getElementById('editCatatan').value = data.catatan || '';

            const existingFotos = document.getElementById('existingFotoContainer');
            existingFotos.innerHTML = '';

                if (data.foto_kegiatan_items && Array.isArray(data.foto_kegiatan_items) && data.foto_kegiatan_items.length > 0) {
                let html = '<label class="form-label d-block">Foto Saat Ini:</label><div class="d-flex flex-wrap gap-2">';
                const storageBase = '{{ rtrim(asset("storage"), "/") }}';
                data.foto_kegiatan_items.forEach(item => {
                    const path = item.path;
                    const url = item.url || (storageBase + '/' + path);
                    html += `<div class="position-relative" style="width: 120px;">`;
                    html += `<img data-path="${path}" data-url="${url}" style="width:100%; height:80px; object-fit:cover;" class="rounded border foto-edit-thumb" src="${url}" onerror="this.src='{{ asset('images/placeholder.png') }}'; this.style.opacity='0.5';">`;
                    html += `<input type="checkbox" name="remove_photos[]" value="${path}" style="display:none;">`;
                    html += `</div>`;
                });
                html += '</div>';
                existingFotos.innerHTML = html;

                existingFotos.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    cb.addEventListener('change', () => {
                        const parent = cb.closest('div.position-relative');
                        if (!parent) return;
                        if (cb.checked) {
                            parent.style.opacity = '0.5';
                            parent.style.filter = 'grayscale(100%)';
                        } else {
                            parent.style.opacity = '';
                            parent.style.filter = '';
                        }
                    });
                });

                existingFotos.querySelectorAll('.position-relative').forEach(el => {
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger position-absolute';
                    removeBtn.style.top = '0px';
                    removeBtn.style.right = '0px';
                    removeBtn.style.borderRadius = '0 0 0 6px';
                    removeBtn.innerHTML = '×';
                    el.appendChild(removeBtn);
                    removeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const cb = el.querySelector('input[type="checkbox"]');
                        if (cb) cb.checked = true;
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                });

                const fileInput = document.querySelector('#editEkskulForm input[name="foto_kegiatan[]"]');
                if (fileInput) {
                    fileInput.addEventListener('change', function(){
                        const existingCount = existingFotos.querySelectorAll('.position-relative').length;
                        const removedCount = Array.from(existingFotos.querySelectorAll('input[type="checkbox"]')).filter(x => x.checked).length;
                        const newCount = this.files ? this.files.length : 0;
                        const maxFiles = 5;
                        const maxSize = 2 * 1024 * 1024; 
                        if ((existingCount - removedCount + newCount) > maxFiles) {
                            alert('Tidak bisa lebih dari 5 foto total.');
                            this.value = null;
                            return;
                        }
                        if (this.files) {
                            for (let i = 0; i < this.files.length; i++) {
                                if (this.files[i].size > maxSize) {
                                    alert('Ukuran tiap foto maksimal 2 MB.');
                                    this.value = null;
                                    return;
                                }
                            }
                        }
                    });
                }
            } else {
                existingFotos.innerHTML = '<p class="text-muted small">Tidak ada foto tersimpan</p>';
            }

            const siswaContainer = document.getElementById('editSiswaContainer');
            siswaContainer.innerHTML = '<p class="text-muted">Memuat peserta...</p>';

            fetch(`{{ url('/jurnal/get-siswa-ekskul') }}/${data.ekskul_id}`)
                .then(async response => {
                    if (!response.ok) throw new Error('Server error');
                    return response.json();
                })
                .then(siswaData => buildSiswaTable(siswaContainer, siswaData, data.absensi || []))
                .catch(err => {
                    console.error(err);
                    siswaContainer.innerHTML = '<p class="text-danger">Gagal memuat peserta</p>';
                });

            const fileInput = document.querySelector('#editEkskulForm input[name="foto_kegiatan[]"]');
            if (fileInput) fileInput.value = null;
            document.getElementById('fotoPreviewEkskulEdit').style.display = 'none';

            const form = document.getElementById('editEkskulForm');
            form.action = `{{ url('jurnal/ekskul') }}/${id}`;

            const editModal = new bootstrap.Modal(document.getElementById('editEkskulModal'));
            editModal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data jurnal.');
        });
}

// Photo preview setup
document.addEventListener('DOMContentLoaded', function() {
    function attachFotoLimitCheck(selector) {
        const input = document.querySelector(selector);
        if (!input) return;
        input.addEventListener('change', function() {
            if (!this.files) return;
            if (this.files.length > 5) {
                alert('Tidak bisa lebih dari 5 foto.');
                this.value = null; 
                return;
            }
            for (let i = 0; i < this.files.length; i++) {
                if (this.files[i].size > 2097152) { // 2MB
                    alert('Ukuran tiap foto maksimal 2 MB.');
                    this.value = null;
                    return;
                }
            }
        });
    }

    attachFotoLimitCheck('#addEkskulModal input[name="foto_kegiatan[]"]');
    
    // Setup previews...
    var selectedFilesEkskul = [];
    const addEkskulFotoInput = document.getElementById('addEkskulFotoInput');
    if (addEkskulFotoInput) {
        addEkskulFotoInput.addEventListener('change', function() {
            if (!this.files || this.files.length === 0) return;
            selectedFilesEkskul = Array.from(this.files);
            displayPhotoPreviewEkskul();
        });
    }

    function displayPhotoPreviewEkskul() {
        const previewContainer = document.getElementById('fotoPreviewContainerEkskul');
        const previewDiv = document.getElementById('fotoPreviewEkskul');
        previewContainer.innerHTML = '';
        if (selectedFilesEkskul.length === 0) {
            previewDiv.style.display = 'none';
            return;
        }
        previewDiv.style.display = 'block';
        selectedFilesEkskul.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-4 mb-2';
                col.innerHTML = `
                    <div class="position-relative border rounded" style="padding-bottom: 75%; overflow: hidden; background: #f8f9fa;">
                        <img src="${e.target.result}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;" alt="Preview">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="deletePhotoEkskul(${index})" style="padding: 1px 5px; font-size: 10px; border-radius: 0 0 0 4px;">✕</button>
                    </div>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }

    window.deletePhotoEkskul = function(index) {
        selectedFilesEkskul.splice(index, 1);
        const dt = new DataTransfer();
        selectedFilesEkskul.forEach(file => dt.items.add(file));
        if (addEkskulFotoInput) addEkskulFotoInput.files = dt.files;
        displayPhotoPreviewEkskul();
    };

    var selectedFilesEkskulEdit = [];
    const editEkskulFotoInput = document.getElementById('editEkskulFotoInput');
    if (editEkskulFotoInput) {
        editEkskulFotoInput.addEventListener('change', function() {
            if (!this.files || this.files.length === 0) return;
            selectedFilesEkskulEdit = Array.from(this.files);
            displayPhotoPreviewEkskulEdit();
        });
    }

    function displayPhotoPreviewEkskulEdit() {
        const previewContainer = document.getElementById('fotoPreviewContainerEkskulEdit');
        const previewDiv = document.getElementById('fotoPreviewEkskulEdit');
        previewContainer.innerHTML = '';
        if (selectedFilesEkskulEdit.length === 0) {
            previewDiv.style.display = 'none';
            return;
        }
        previewDiv.style.display = 'block';
        selectedFilesEkskulEdit.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-4 mb-2';
                col.innerHTML = `
                    <div class="position-relative border rounded" style="padding-bottom: 75%; overflow: hidden; background: #f8f9fa;">
                        <img src="${e.target.result}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;" alt="Preview">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="deletePhotoEkskulEdit(${index})" style="padding: 1px 5px; font-size: 10px; border-radius: 0 0 0 4px;">✕</button>
                    </div>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }

    window.deletePhotoEkskulEdit = function(index) {
        selectedFilesEkskulEdit.splice(index, 1);
        const dt = new DataTransfer();
        selectedFilesEkskulEdit.forEach(file => dt.items.add(file));
        if (editEkskulFotoInput) editEkskulFotoInput.files = dt.files;
        displayPhotoPreviewEkskulEdit();
    };
});
</script>
@endpush
@endsection