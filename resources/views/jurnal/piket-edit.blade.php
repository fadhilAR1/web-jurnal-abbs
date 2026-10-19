@extends('layouts.app')

@section('title', 'Edit Jurnal Piket')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3>Edit Jurnal Piket</h3>

                <form action="{{ route('jurnal.piket.update', $jurnal->id) }}" method="POST" id="pageEditForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="pageGuruCleared" name="guru_tidak_hadir_cleared" value="0">
                    <input type="hidden" id="pageTamuCleared" name="tamu_sekolah_cleared" value="0">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal</label>
                            <input type="date" id="pageEditTanggal" name="tanggal" class="form-control" value="{{ $jurnal->tanggal ? $jurnal->tanggal->format('Y-m-d') : '' }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Hari</label>
                            <input type="text" id="pageEditHari" name="hari" class="form-control" value="{{ $jurnal->hari }}" readonly required>
                        </div>

                        <div class="col-12 mb-3">
                            <h6>Guru Tidak Hadir</h6>
                            <div id="pageEditGuruContainer">
                                @if(!empty($guruTidakHadir) && count($guruTidakHadir) > 0)
                                    @foreach($guruTidakHadir as $i => $g)
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="guru_tidak_hadir[{{ $i }}][nama]" class="form-control" value="{{ is_array($g) ? ($g['nama'] ?? '') : $g }}" placeholder="Nama Guru">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="guru_tidak_hadir[{{ $i }}][catatan]" class="form-control" value="{{ is_array($g) ? ($g['catatan'] ?? ($g['alasan'] ?? '')) : '' }}" placeholder="Catatan">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.parentElement.remove()">-</button>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="guru_tidak_hadir[0][nama]" class="form-control" placeholder="Nama Guru">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="guru_tidak_hadir[0][catatan]" class="form-control" placeholder="Catatan">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-secondary" onclick="addPageEditGuru()">+</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPageEditGuru()">Tambah Guru</button>
                        </div>

                        <div class="col-12 mb-3">
                            <h6>Tamu Sekolah</h6>
                            <div id="pageEditTamuContainer">
                                @if(!empty($tamuSekolah) && count($tamuSekolah) > 0)
                                    @foreach($tamuSekolah as $i => $t)
                                    <div class="row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" name="tamu_sekolah[{{ $i }}][nama]" class="form-control" value="{{ is_array($t) ? ($t['nama'] ?? '') : $t }}" placeholder="Nama Tamu">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="tamu_sekolah[{{ $i }}][tujuan]" class="form-control" value="{{ is_array($t) ? ($t['tujuan'] ?? '') : '' }}" placeholder="Tujuan">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.parentElement.remove()">-</button>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" name="tamu_sekolah[0][nama]" class="form-control" placeholder="Nama Tamu">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="tamu_sekolah[0][tujuan]" class="form-control" placeholder="Tujuan">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-secondary" onclick="addPageEditTamu()">+</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPageEditTamu()">Tambah Tamu</button>
                        </div>

                        <div class="col-12 mb-3">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan...">{{ $jurnal->catatan }}</textarea>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('jurnal.piket') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function getIndoDay(dateStr) {
    const d = new Date(dateStr);
    if (isNaN(d)) return '';
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    return days[d.getDay()];
}

// Auto-update Hari on date change
const pageTanggal = document.getElementById('pageEditTanggal');
const pageHari = document.getElementById('pageEditHari');
if (pageTanggal && pageHari) {
    pageTanggal.addEventListener('change', function() { pageHari.value = getIndoDay(this.value); });
}

let pageGuruCounter = document.querySelectorAll('#pageEditGuruContainer .row').length || 1;
function addPageEditGuru() {
    const container = document.getElementById('pageEditGuruContainer');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-6">
            <input type="text" name="guru_tidak_hadir[${pageGuruCounter}][nama]" class="form-control" placeholder="Nama Guru">
        </div>
        <div class="col-md-5">
            <input type="text" name="guru_tidak_hadir[${pageGuruCounter}][catatan]" class="form-control" placeholder="Catatan">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.parentElement.remove()">-</button>
        </div>
    `;
    container.appendChild(div);
    pageGuruCounter++;
}

let pageTamuCounter = document.querySelectorAll('#pageEditTamuContainer .row').length || 1;
function addPageEditTamu() {
    const container = document.getElementById('pageEditTamuContainer');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="tamu_sekolah[${pageTamuCounter}][nama]" class="form-control" placeholder="Nama Tamu">
        </div>
        <div class="col-md-6">
            <input type="text" name="tamu_sekolah[${pageTamuCounter}][tujuan]" class="form-control" placeholder="Tujuan">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.parentElement.remove()">-</button>
        </div>
    `;
    container.appendChild(div);
    pageTamuCounter++;
}

// Before submit: set cleared flags if user removed all entries
(function() {
    const form = document.getElementById('pageEditForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        const guruContainer = document.getElementById('pageEditGuruContainer');
        const tamuContainer = document.getElementById('pageEditTamuContainer');
        const guruCleared = document.getElementById('pageGuruCleared');
        const tamuCleared = document.getElementById('pageTamuCleared');

        // Check Guru Tidak Hadir: if no nama inputs with non-empty value, mark cleared
        if (guruContainer) {
            const namaInputs = guruContainer.querySelectorAll('input[name$="[nama]"]');
            let anyNama = false;
            namaInputs.forEach(inp => { if (inp.value && inp.value.trim() !== '') anyNama = true; });
            guruCleared.value = anyNama ? '0' : '1';
        }

        // Check Tamu Sekolah similarly
        if (tamuContainer) {
            const namaInputs = tamuContainer.querySelectorAll('input[name$="[nama]"]');
            let anyNama = false;
            namaInputs.forEach(inp => { if (inp.value && inp.value.trim() !== '') anyNama = true; });
            tamuCleared.value = anyNama ? '0' : '1';
        }
    });
})();
</script>
@endpush

@endsection
