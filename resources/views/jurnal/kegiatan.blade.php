@extends('layouts.app')

@section('title', 'Jurnal Kegiatan Sekolah')

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

    /* Action Buttons Base */
    .action-cell {
        padding: 0.3rem !important;
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: nowrap; /* keep action buttons on a single row on larger screens */
        justify-content: flex-start;
        align-items: center;
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
        display: inline; /* don't force form to full width so buttons stay on one line */
        margin: 0;
        width: auto;
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
        /* Memperbaiki Header: Judul & Tombol Bertumpuk */
        .header-container {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 15px;
        }
        
        .header-actions {
            width: 100%;
        }

        .header-actions .btn {
            flex: 1; /* Tombol membagi lebar 50:50 */
            font-size: 0.75rem !important;
            padding: 0.4rem 0.2rem !important;
            white-space: normal; 
            line-height: 1.2;
        }

        /* Tabel (Font dikecilkan) */
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

        /* Hapus border separator form pada modal di mobile karena susunannya menjadi vertikal */
        .form-group-separator {
            border-right: none;
            padding-right: 0;
        }

        /* Mengatur proporsi 5 kolom di HP */
        .table-fixed th:nth-child(1) { width: 10% !important; } /* No */
        .table-fixed th:nth-child(2) { width: 18% !important; } /* Tgl */
        .table-fixed th:nth-child(3) { width: 24% !important; } /* Penulis */
        .table-fixed th:nth-child(4) { width: 30% !important; } /* Nama Kegiatan */
        .table-fixed th:nth-child(5) { width: 18% !important; } /* Aksi (Sempit vertikal) */
    }

    /* Ensure delete button matches other action buttons on very small screens */
    @media (max-width: 576px) {
        .action-buttons .btn,
        .action-buttons .action-btn {
            width: 100% !important;
            display: block !important;
            box-sizing: border-box;
        }

        .action-cell form {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
        }
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
            flex-direction: column; /* Ditumpuk di tablet agar lebih lega */
            gap: 4px;
        }

        .action-buttons .btn {
            width: 100%;
            font-size: 0.7rem !important;
            padding: 0.2rem 0 !important;
        }

        .table-fixed th:nth-child(1) { width: 8% !important; }
        .table-fixed th:nth-child(2) { width: 15% !important; }
        .table-fixed th:nth-child(3) { width: 25% !important; }
        .table-fixed th:nth-child(4) { width: 32% !important; }
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
                <h2 class="mb-1"></i> Jurnal Kegiatan Sekolah</h2>
                <p class="text-muted small mb-0">Catat dan pantau kegiatan sekolah</p>
            </div>
            <div class="header-actions">
                @if(auth()->user()->isAdmin())
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success action-btn">
                        Unduh Jurnal
                    </a>
                @endif
                <button class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    Tambah Jurnal
                </button>
            </div>
        </div>

        <div class="card mb-4 border-0">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Filter Jurnal</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('jurnal.kegiatan') }}" method="GET">
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
                                <th style="width: 25%;">Penulis</th>
                                <th style="width: 30%;">Nama Kegiatan</th>
                                <th style="width: 22%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurnals as $jurnal)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="text-ellipsis">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->guru->name ?? '-' }}</span></td>
                                    <td><span class="text-ellipsis" title="{{ $jurnal->nama_kegiatan }}">{{ $jurnal->nama_kegiatan }}</span></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <a href="{{ route('jurnal.kegiatan.show', $jurnal->id) }}" class="btn btn-sm btn-outline-info action-btn" title="Lihat Detail">
                                                Lihat
                                            </a>

                                            @if(auth()->user()->isAdmin() || auth()->user()->id == $jurnal->guru_id)
                                                <button type="button" class="btn btn-sm btn-outline-warning action-btn" 
                                                        onclick="loadEditKegiatanModal({{ $jurnal->id }})" title="Edit">
                                                    Edit
                                                </button>

                                                <form id="deleteForm-{{ $jurnal->id }}" method="POST" action="{{ route('jurnal.kegiatan.destroy', $jurnal->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger action-btn"
                                                            onclick="confirmDelete('{{ route('jurnal.kegiatan.destroy', $jurnal->id) }}')" title="Hapus">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @if($jurnal->foto_kegiatan && is_array($jurnal->foto_kegiatan) && count($jurnal->foto_kegiatan) > 0)
                                <div class="modal fade" id="fotoModal{{ $jurnal->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Foto Kegiatan: {{ $jurnal->nama_kegiatan }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    @foreach($jurnal->foto_kegiatan as $foto)
                                                        <div class="col-md-6 mb-3">
                                                            <img src="{{ asset('storage/' . $foto) }}" 
                                                                class="img-fluid rounded" 
                                                                alt="Foto Kegiatan"
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
                                        <small>Belum ada jurnal kegiatan</small>
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

<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('jurnal.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurnal Kegiatan Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Tanggal Kegiatan</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Jenis Kegiatan</label>
                            <select name="jenis_kegiatan" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="upacara">Upacara</option>
                                <option value="sosialisasi">Sosialisasi</option>
                                <option value="pelatihan">Pelatihan</option>
                                <option value="perlombaan">Perlombaan</option>
                                <option value="workshop">Workshop</option>
                                <option value="seminar">Seminar</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" class="form-control" 
                                   placeholder="Contoh: Upacara Bendera Hari Senin" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" step="60" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" step="60" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" 
                                   placeholder="Contoh: Lapangan Sekolah, Aula, dll" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Jumlah Peserta Siswa</label>
                            <input type="number" name="peserta[siswa]" class="form-control" 
                                   min="0" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Jumlah Peserta Guru</label>
                            <input type="number" name="peserta[guru]" class="form-control" 
                                   min="0" value="0" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Deskripsi Kegiatan</label>
                            <textarea name="deskripsi" class="form-control" rows="4" 
                                      placeholder="Deskripsi lengkap kegiatan..." required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Foto Kegiatan (Maksimal 5 foto)</label>
                            <input type="file" id="addKegiatanFotoInput" name="foto_kegiatan[]" class="form-control" 
                                   multiple accept="image/*">
                            <div id="fotoPreviewKegiatan" class="mt-3" style="display:none;">
                                <h6>Preview Foto yang Dipilih:</h6>
                                <div id="fotoPreviewContainerKegiatan" class="row"></div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Dokumentasi Lainnya (PDF/DOC/PPT)</label>
                            <input type="file" name="dokumentasi[]" class="form-control" 
                                   multiple accept=".pdf,.doc,.docx,.ppt,.pptx">
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

<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editEventForm" action="#" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editEventId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jurnal Kegiatan Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Tanggal Kegiatan</label>
                            <input type="date" id="editTanggal" name="tanggal" class="form-control" 
                                   max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Jenis Kegiatan</label>
                            <select id="editJenisKegiatan" name="jenis_kegiatan" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="upacara">Upacara</option>
                                <option value="sosialisasi">Sosialisasi</option>
                                <option value="pelatihan">Pelatihan</option>
                                <option value="perlombaan">Perlombaan</option>
                                <option value="workshop">Workshop</option>
                                <option value="seminar">Seminar</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Nama Kegiatan</label>
                            <input type="text" id="editNamaKegiatan" name="nama_kegiatan" class="form-control" 
                                   placeholder="Contoh: Upacara Bendera Hari Senin" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Waktu Mulai</label>
                            <input type="time" id="editWaktuMulai" name="waktu_mulai" class="form-control" step="60" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Waktu Selesai</label>
                            <input type="time" id="editWaktuSelesai" name="waktu_selesai" class="form-control" step="60" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Lokasi</label>
                            <input type="text" id="editLokasi" name="lokasi" class="form-control" 
                                   placeholder="Contoh: Lapangan Sekolah, Aula, dll" required>
                        </div>
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Jumlah Peserta Siswa</label>
                            <input type="number" id="editPesertaSiswa" name="peserta[siswa]" class="form-control" 
                                   min="0" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Jumlah Peserta Guru</label>
                            <input type="number" id="editPesertaGuru" name="peserta[guru]" class="form-control" 
                                   min="0" value="0" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Deskripsi Kegiatan</label>
                            <textarea id="editDeskripsi" name="deskripsi" class="form-control" rows="4" 
                                      placeholder="Deskripsi lengkap kegiatan..." required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Foto Kegiatan (Maksimal 5 foto)</label>
                            <input type="file" id="editKegiatanFotoInput" name="foto_kegiatan[]" class="form-control" 
                                   multiple accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                            <div id="editFotoPreviewKegiatan" class="mt-3" style="display:none;">
                                <h6>Preview Foto yang Dipilih:</h6>
                                <div id="editFotoPreviewContainerKegiatan" class="row"></div>
                            </div>
                            <div id="existingFotoKegiatanContainer" class="mt-3"></div>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Dokumentasi Lainnya (PDF/DOC/PPT)</label>
                            <input type="file" name="dokumentasi[]" class="form-control" 
                                   multiple accept=".pdf,.doc,.docx,.ppt,.pptx">
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
                <form id="globalDeleteForm" method="POST" style="display:inline;">
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

function confirmDelete(deleteUrl) {
    const form = document.getElementById('globalDeleteForm');
    form.action = deleteUrl;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function loadEditKegiatanModal(id) {
    fetch(`{{ url('/jurnal/kegiatan') }}/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.ok ? r.json() : Promise.reject('Failed to load jurnal'))
    .then(data => {
        // Set form data
        document.getElementById('editEventId').value = data.id;
        document.getElementById('editTanggal').value = data.tanggal;
        document.getElementById('editJenisKegiatan').value = data.jenis_kegiatan;
        document.getElementById('editNamaKegiatan').value = data.nama_kegiatan;
        document.getElementById('editWaktuMulai').value = data.waktu_mulai;
        document.getElementById('editWaktuSelesai').value = data.waktu_selesai;
        document.getElementById('editLokasi').value = data.lokasi;
        document.getElementById('editDeskripsi').value = data.deskripsi;
        
        // Set peserta
        if (data.peserta) {
            document.getElementById('editPesertaSiswa').value = data.peserta.siswa || 0;
            document.getElementById('editPesertaGuru').value = data.peserta.guru || 0;
        }
        
        // Update form action
        const form = document.getElementById('editEventForm');
        form.action = `{{ url('/jurnal/kegiatan') }}/${data.id}`;

        // Render existing photos (if any) so they persist on edit and can be removed individually
        const existingContainer = document.getElementById('existingFotoKegiatanContainer');
        existingContainer.innerHTML = '';
        // Prefer foto_kegiatan_items (path+url) if provided by controller; fallback to foto_kegiatan array
        const fotoItems = (data.foto_kegiatan_items && Array.isArray(data.foto_kegiatan_items) && data.foto_kegiatan_items.length > 0)
            ? data.foto_kegiatan_items
            : (Array.isArray(data.foto_kegiatan) ? data.foto_kegiatan.map(p => ({ path: p, url: `/file/${p}` })) : []);

        if (fotoItems.length > 0) {
            let html = '<label class="mb-2 mt-3 fw-bold">Foto Saat Ini:</label><div class="d-flex flex-wrap">';
            fotoItems.forEach(item => {
                const p = item.path;
                const src = item.url || `/file/${p}`;
                html += `<div class="me-2 mb-2 text-center position-relative" style="width:140px">`;
                html += `<img data-path="${p}" data-url="${src}" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="width:140px;height:90px;object-fit:cover;display:block;margin-bottom:6px;" class="rounded border foto-edit-thumb">`;
                html += `<input type="checkbox" name="remove_photos[]" value="${p}" style="display:none;">`;
                html += `</div>`;
            });
            html += '</div>';
            existingContainer.innerHTML = html;

            // Set each thumb's src from provided data-url (server-side asset if available), fallback to storage/file route
            const thumbs = existingContainer.querySelectorAll('.foto-edit-thumb');
            thumbs.forEach(imgEl => {
                const p = imgEl.dataset.path;
                const provided = imgEl.dataset.url;
                const fallback = '{{ rtrim(asset("storage"), "/") }}' + '/' + encodeURI(p);
                imgEl.src = provided || fallback;
            });

            // dim preview when checked
            existingContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', () => {
                    const parent = cb.closest('div.position-relative');
                    if (!parent) return;
                    if (cb.checked) {
                        parent.style.opacity = '0.5';
                        parent.style.filter = 'grayscale(50%)';
                    } else {
                        parent.style.opacity = '';
                        parent.style.filter = '';
                    }
                });
            });

            // Add remove button (X) for existing photos
            existingContainer.querySelectorAll('.position-relative').forEach(el => {
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger position-absolute';
                removeBtn.style.top = '0px';
                removeBtn.style.right = '0px';
                removeBtn.style.borderRadius = '0 6px 0 6px';
                removeBtn.style.padding = '2px 6px';
                removeBtn.style.fontSize = '1.2rem';
                removeBtn.style.lineHeight = '1';
                removeBtn.innerHTML = '×';
                el.appendChild(removeBtn);
                removeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const cb = el.querySelector('input[type="checkbox"]');
                    if (cb) {
                        cb.checked = true;
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    el.style.opacity = '0.5';
                    el.style.filter = 'grayscale(100%)';
                });
            });
        }

        // Open modal
        new bootstrap.Modal(document.getElementById('editEventModal')).show();
    })
    .catch(error => {
        alert('Gagal memuat data jurnal: ' + error);
        console.error('Error:', error);
    });
}

function removePhotoPreview(fileIndex) {
    event.preventDefault();
    event.stopPropagation();
    
    const addInput = document.getElementById('addKegiatanFotoInput');
    if(!addInput || !addInput.files) return;
    
    const dt = new DataTransfer();
    Array.from(addInput.files).forEach((file, i) => {
        if(i !== fileIndex) {
            dt.items.add(file);
        }
    });
    
    addInput.files = dt.files;
    addInput.dispatchEvent(new Event('change', { bubbles: true }));
}

document.addEventListener('DOMContentLoaded', function(){
    const addInput = document.getElementById('addKegiatanFotoInput');
    const container = document.getElementById('fotoPreviewContainerKegiatan');
    const preview = document.getElementById('fotoPreviewKegiatan');
    
    if(!addInput || !container) return;
    
    addInput.addEventListener('change', function(){
        let files = Array.from(this.files || []);
        const maxFiles = 5;
        const maxSize = 2 * 1024 * 1024; // 2MB
        if(files.length > maxFiles){
            alert('Tidak bisa lebih dari 5 foto.');
            this.value = null;
            return;
        }

        for (let f of files) {
            if (f.size > maxSize) {
                alert('Ukuran tiap foto maksimal 2 MB.');
                this.value = null;
                return;
            }
        }

        container.innerHTML = '';
        if(files.length === 0){
            preview.style.display = 'none';
            return;
        }

        preview.style.display = 'block';

        files.forEach((file, fileIndex) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const photoDiv = document.createElement('div');
                photoDiv.className = 'col-md-3 col-4 mb-3';
                photoDiv.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded border" style="width:100%; height:100px; object-fit:cover;">
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" type="button" onclick="removePhotoPreview(${fileIndex})" style="padding: 1px 6px;">×</button>
                    </div>
                `;
                container.appendChild(photoDiv);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>

<script>
// Initialize popovers and Edit Photo Input logic
document.addEventListener('DOMContentLoaded', function() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // Event listener for edit foto input
    const editFotoInput = document.getElementById('editKegiatanFotoInput');
    if (editFotoInput) {
        editFotoInput.addEventListener('change', function() {
            const preview = document.getElementById('editFotoPreviewKegiatan');
            const container = document.getElementById('editFotoPreviewContainerKegiatan');
            const files = Array.from(this.files || []);
            const maxFiles = 5;
            const maxSize = 2 * 1024 * 1024; // 2MB

            const existingContainer = document.getElementById('existingFotoKegiatanContainer');
            const existingCount = existingContainer ? existingContainer.querySelectorAll('.position-relative').length : 0;
            const removedCount = existingContainer ? Array.from(existingContainer.querySelectorAll('input[type="checkbox"]')).filter(x => x.checked).length : 0;

            if ((existingCount - removedCount + files.length) > maxFiles) {
                alert('Tidak bisa lebih dari 5 foto.');
                this.value = '';
                preview.style.display = 'none';
                return;
            }

            for (let f of files) {
                if (f.size > maxSize) {
                    alert('Ukuran tiap foto maksimal 2 MB.');
                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }
            }

            container.innerHTML = '';
            if (files.length === 0) {
                preview.style.display = 'none';
                return;
            }

            preview.style.display = 'block';

            files.forEach((file, fileIndex) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const photoDiv = document.createElement('div');
                    photoDiv.className = 'col-md-3 col-4 mb-3';
                    photoDiv.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded border" style="width:100%; height:100px; object-fit:cover;">
                            <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" type="button" onclick="removePhotoPreviewFromEdit(${fileIndex})" style="padding: 1px 6px;">×</button>
                        </div>
                    `;
                    container.appendChild(photoDiv);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});

function removePhotoPreviewFromEdit(fileIndex) {
    event.preventDefault();
    event.stopPropagation();
    
    const addInput = document.getElementById('editKegiatanFotoInput');
    if(!addInput || !addInput.files) return;
    
    const dt = new DataTransfer();
    Array.from(addInput.files).forEach((file, i) => {
        if(i !== fileIndex) {
            dt.items.add(file);
        }
    });
    addInput.files = dt.files;
    
    // Trigger change event to update preview
    addInput.dispatchEvent(new Event('change', { bubbles: true }));
}
</script>
@endpush
@endsection