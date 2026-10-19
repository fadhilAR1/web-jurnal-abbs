@extends('layouts.app')

@section('title', 'Jurnal Piket')

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

    /* Mengizinkan teks turun 2 baris sebelum dipotong */
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

        /* Hapus border separator form pada modal di mobile */
        .form-group-separator {
            border-right: none;
            padding-right: 0;
        }

        /* Mengatur proporsi 5 kolom di HP */
        .table-fixed th:nth-child(1) { width: 10% !important; } /* No */
        .table-fixed th:nth-child(2) { width: 18% !important; } /* Tgl */
        .table-fixed th:nth-child(3) { width: 18% !important; } /* Hari */
        .table-fixed th:nth-child(4) { width: 34% !important; } /* Guru */
        .table-fixed th:nth-child(5) { width: 20% !important; } /* Aksi (Sempit vertikal) */
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
            flex-direction: column; /* Ditumpuk di tablet */
            gap: 4px;
        }

        .action-buttons .btn {
            width: 100%;
            font-size: 0.7rem !important;
            padding: 0.2rem 0 !important;
        }

        .table-fixed th:nth-child(1) { width: 8% !important; }
        .table-fixed th:nth-child(2) { width: 15% !important; }
        .table-fixed th:nth-child(3) { width: 15% !important; }
        .table-fixed th:nth-child(4) { width: 40% !important; }
        .table-fixed th:nth-child(5) { width: 22% !important; }

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
                <h2 class="mb-1"></i> Jurnal Piket</h2>
                <p class="text-muted small mb-0">Catat dan pantau piket harian sekolah</p>
            </div>
            <div class="header-actions">
                @if(auth()->user()->isAdmin())
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success action-btn">
                        Unduh Jurnal
                    </a>
                @endif
                <button class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addPiketModal">
                    Tambah Jurnal
                </button>
            </div>
        </div>

        <div class="card mb-4 border-0">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Filter Jurnal</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('jurnal.piket') }}" method="GET">
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
                                <th style="width: 25%;">Hari</th>
                                <th style="width: 30%;">Guru Piket</th>
                                <th style="width: 22%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurnals as $jurnal)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="text-ellipsis">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/y') }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->hari }}</span></td>
                                    <td><span class="text-ellipsis">{{ $jurnal->guru->name ?? '-' }}</span></td>
                                    <td class="action-cell">
                                        <div class="action-buttons">
                                            <a href="{{ route('jurnal.piket.show', $jurnal->id) }}" class="btn btn-sm btn-outline-info action-btn" title="Lihat Detail">
                                                Lihat
                                            </a>
                                            @if(auth()->user()->isAdmin() || auth()->user()->id == $jurnal->guru_id)
                                                <button type="button" class="btn btn-sm btn-outline-warning action-btn" 
                                                        onclick="loadEditPiketModal({{ $jurnal->id }})" title="Edit">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger action-btn" 
                                                        onclick="confirmDelete('{{ route('jurnal.piket.destroy', $jurnal->id) }}', 'Hapus jurnal piket ini?')" title="Hapus">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i><br>
                                        <small>Belum ada jurnal piket</small>
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

<div class="modal fade" id="addPiketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('jurnal.piket.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurnal Piket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Tanggal</label>
                            <input type="date" id="addTanggal" name="tanggal" class="form-control" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Hari</label>
                            <input type="text" id="addHari" name="hari" class="form-control" value="" readonly required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <h6 class="border-bottom pb-2">Guru Tidak Hadir</h6>
                            <div id="guruTidakHadirContainer">
                                <div class="row mb-2">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <input type="text" name="guru_tidak_hadir[0][nama]" 
                                               class="form-control" placeholder="Nama Guru">
                                    </div>
                                    <div class="col-md-5 mb-2 mb-md-0">
                                        <input type="text" name="guru_tidak_hadir[0][catatan]" 
                                               class="form-control" placeholder="Catatan">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-secondary w-100" 
                                                onclick="addGuruTidakHadir()">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <h6 class="border-bottom pb-2">Tamu Sekolah</h6>
                            <div id="tamuSekolahContainer">
                                <div class="row mb-2">
                                    <div class="col-md-5 mb-2 mb-md-0">
                                        <input type="text" name="tamu_sekolah[0][nama]" 
                                               class="form-control" placeholder="Nama Tamu">
                                    </div>
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <input type="text" name="tamu_sekolah[0][tujuan]" 
                                               class="form-control" placeholder="Tujuan">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-secondary w-100" 
                                                onclick="addTamuSekolah()">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" 
                                      placeholder="Catatan tambahan..."></textarea>
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

<div class="modal fade" id="editPiketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jurnal Piket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST" id="editPiketForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editPiketId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group-separator">
                            <label>Tanggal</label>
                            <input type="date" id="editTanggal" name="tanggal" class="form-control" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Hari</label>
                            <input type="text" id="editHari" name="hari" class="form-control" readonly required>
                        </div>

                        <div class="col-12 mb-3">
                            <h6 class="border-bottom pb-2">Guru Tidak Hadir</h6>
                            <div id="editGuruTidakHadirContainer">
                                </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="guruAddMoreBtn" onclick="addEditGuruTidakHadir()">Tambah Guru</button>
                        </div>

                        <div class="col-12 mb-3">
                            <h6 class="border-bottom pb-2">Tamu Sekolah</h6>
                            <div id="editTamuSekolahContainer">
                                </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="tamuAddMoreBtn" onclick="addEditTamuSekolah()">Tambah Tamu</button>
                        </div>

                        <div class="col-12 mb-3">
                            <label>Catatan</label>
                            <textarea id="editCatatan" name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
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

@include('jurnal._piket-scripts')
<script>
let guruCounter = 1;
let tamuCounter = 1;

function addGuruTidakHadir() {
    const container = document.getElementById('guruTidakHadirContainer');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-6 mb-2 mb-md-0">
            <input type="text" name="guru_tidak_hadir[${guruCounter}][nama]" 
                   class="form-control" placeholder="Nama Guru">
        </div>
        <div class="col-md-5 mb-2 mb-md-0">
            <input type="text" name="guru_tidak_hadir[${guruCounter}][catatan]" 
                   class="form-control" placeholder="Catatan">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100" 
                    onclick="this.parentElement.parentElement.remove()">-</button>
        </div>
    `;
    container.appendChild(div);
    guruCounter++;
}

function addTamuSekolah() {
    const container = document.getElementById('tamuSekolahContainer');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-5 mb-2 mb-md-0">
            <input type="text" name="tamu_sekolah[${tamuCounter}][nama]" 
                   class="form-control" placeholder="Nama Tamu">
        </div>
        <div class="col-md-6 mb-2 mb-md-0">
            <input type="text" name="tamu_sekolah[${tamuCounter}][tujuan]" 
                   class="form-control" placeholder="Tujuan">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100" 
                    onclick="this.parentElement.parentElement.remove()">-</button>
        </div>
    `;
    container.appendChild(div);
    tamuCounter++;
}

// Initialize popovers and auto-fill Hari from Tanggal
document.addEventListener('DOMContentLoaded', function() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    const addTanggal = document.getElementById('addTanggal');
    const addHari = document.getElementById('addHari');
    if (addTanggal && addHari) {
        addHari.value = getIndoDay(addTanggal.value || new Date().toISOString().slice(0,10));
        addTanggal.addEventListener('change', function() {
            addHari.value = getIndoDay(this.value);
        });
    }

    const editTanggal = document.getElementById('editTanggal');
    const editHari = document.getElementById('editHari');
    if (editTanggal && editHari) {
        editTanggal.addEventListener('change', function() {
            editHari.value = getIndoDay(this.value);
        });
    }
});

function addEditGuruTidakHadir() {
    const container = document.getElementById('editGuruTidakHadirContainer');
    if (!container) return;
    const rows = container.querySelectorAll('.row');
    const nextIdx = rows.length;
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-6 mb-2 mb-md-0">
            <input type="text" name="guru_tidak_hadir[${nextIdx}][nama]" class="form-control" placeholder="Nama Guru">
        </div>
        <div class="col-md-5 mb-2 mb-md-0">
            <input type="text" name="guru_tidak_hadir[${nextIdx}][catatan]" class="form-control" placeholder="Catatan">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100" onclick="this.parentElement.parentElement.remove()">-</button>
        </div>
    `;
    container.appendChild(div);
}

function addEditTamuSekolah() {
    const container = document.getElementById('editTamuSekolahContainer');
    if (!container) return;
    const rows = container.querySelectorAll('.row');
    const nextIdx = rows.length;
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-5 mb-2 mb-md-0">
            <input type="text" name="tamu_sekolah[${nextIdx}][nama]" class="form-control" placeholder="Nama Tamu">
        </div>
        <div class="col-md-6 mb-2 mb-md-0">
            <input type="text" name="tamu_sekolah[${nextIdx}][tujuan]" class="form-control" placeholder="Tujuan">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100" onclick="this.parentElement.parentElement.remove()">-</button>
        </div>
    `;
    container.appendChild(div);
}

function loadEditPiketModal(id) {
    fetch(`{{ url('/jurnal/piket') }}/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.ok ? r.json() : Promise.reject('Failed to load jurnal'))
    .then(data => {
        document.getElementById('editPiketId').value = data.id;
        document.getElementById('editTanggal').value = data.tanggal;
        document.getElementById('editHari').value = getIndoDay(data.tanggal);
        document.getElementById('editCatatan').value = data.catatan || '';
        
        const form = document.getElementById('editPiketForm');
        form.action = `{{ url('/jurnal/piket') }}/${data.id}`;
        
        window.currentGuruData = parseListField(data.guru_tidak_hadir);
        window.currentTamuData = parseListField(data.tamu_sekolah);
        
        enableGuruEdit();
        enableTamuEdit();
        
        document.getElementById('guruAddMoreBtn').style.display = 'inline-block';
        document.getElementById('tamuAddMoreBtn').style.display = 'inline-block';
        
        new bootstrap.Modal(document.getElementById('editPiketModal')).show();
    })
    .catch(error => {
        alert('Gagal memuat data jurnal: ' + error);
        console.error('Error:', error);
    });
}

function getIndoDay(input) {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    if (typeof input === 'number') {
        return days[input] || 'Unknown';
    } else if (typeof input === 'string') {
        const d = new Date(input);
        return isNaN(d) ? 'Unknown' : days[d.getDay()];
    }
    return 'Unknown';
}

function confirmDelete(url, message = 'Apakah Anda yakin ingin menghapus item ini?') {
    document.getElementById('deleteForm').action = url;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Fallback functions for parsing and rendering (assuming they are in _piket-scripts, adding safety)
function parseListField(data) {
    if (typeof data === 'string') {
        try {
            return JSON.parse(data);
        } catch(e) {
            return [];
        }
    }
    return Array.isArray(data) ? data : [];
}

function enableGuruEdit() {
    const container = document.getElementById('editGuruTidakHadirContainer');
    if (!container) return;
    container.innerHTML = '';
    const data = window.currentGuruData || [];
    
    if (data.length === 0) {
        addEditGuruTidakHadir();
        return;
    }

    data.forEach((item, idx) => {
        const div = document.createElement('div');
        div.className = 'row mb-2';
        div.innerHTML = `
            <div class="col-md-6 mb-2 mb-md-0">
                <input type="text" name="guru_tidak_hadir[${idx}][nama]" class="form-control" value="${item.nama || ''}" placeholder="Nama Guru">
            </div>
            <div class="col-md-5 mb-2 mb-md-0">
                <input type="text" name="guru_tidak_hadir[${idx}][catatan]" class="form-control" value="${item.catatan || ''}" placeholder="Catatan">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100" onclick="this.parentElement.parentElement.remove()">-</button>
            </div>
        `;
        container.appendChild(div);
    });
}

function enableTamuEdit() {
    const container = document.getElementById('editTamuSekolahContainer');
    if (!container) return;
    container.innerHTML = '';
    const data = window.currentTamuData || [];

    if (data.length === 0) {
        addEditTamuSekolah();
        return;
    }

    data.forEach((item, idx) => {
        const div = document.createElement('div');
        div.className = 'row mb-2';
        div.innerHTML = `
            <div class="col-md-5 mb-2 mb-md-0">
                <input type="text" name="tamu_sekolah[${idx}][nama]" class="form-control" value="${item.nama || ''}" placeholder="Nama Tamu">
            </div>
            <div class="col-md-6 mb-2 mb-md-0">
                <input type="text" name="tamu_sekolah[${idx}][tujuan]" class="form-control" value="${item.tujuan || ''}" placeholder="Tujuan">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100" onclick="this.parentElement.parentElement.remove()">-</button>
            </div>
        `;
        container.appendChild(div);
    });
}
</script>

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

@endsection