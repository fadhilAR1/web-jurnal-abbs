@push('scripts')
<script>
/* Piket scripts (partial): core functions for edit modal and AJAX submit */

// getIndoDay already defined where included; redefine here defensively
function getIndoDay(dateStr) {
    const d = new Date(dateStr);
    if (isNaN(d)) return '';
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    return days[d.getDay()];
}

// editPiket now accepts optional editUrl/updateUrl when provided
function editPiket(id, editUrl = null, updateUrl = null) {
    if (!id) { alert('ID jurnal tidak valid'); return; }

    const url = editUrl || (`/jurnal/piket/${id}/edit`);

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    .then(response => {
        const contentType = response.headers.get('content-type') || '';
        if (!response.ok) {
            if (contentType.includes('application/json')) return response.json().then(err => { throw new Error(err.error || JSON.stringify(err)); });
            return response.text().then(text => {
                if (response.status === 401 || response.status === 419) throw new Error('Sesi berakhir atau belum login. Silakan login ulang.');
                throw new Error((text && text.slice(0,200)) || 'Gagal memuat data jurnal (non-JSON response)');
            });
        }
        if (contentType.includes('application/json')) return response.json();
        return response.text().then(text => { throw new Error('Expected JSON but got HTML/other: ' + (text && text.slice(0,200))); });
    })
    .then(data => {
        document.getElementById('editPiketId').value = data.id;
        const tanggalInput = document.getElementById('editTanggal');
        let tanggalVal = '';
        if (data.tanggal) tanggalVal = data.tanggal.toString().slice(0,10);
        tanggalInput.value = tanggalVal;
        document.getElementById('editHari').value = getIndoDay(tanggalVal);
        document.getElementById('editCatatan').value = data.catatan || '';

        populateEditForm(data);

        const form = document.getElementById('editPiketForm');
        // Prefer explicit updateUrl if provided, else fallback to /jurnal/piket/{id}
        form.action = updateUrl || `/jurnal/piket/${id}`;

        const editModal = new bootstrap.Modal(document.getElementById('editPiketModal'));
        editModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Terjadi kesalahan saat memuat data jurnal');
    });
}

// AJAX submit for edit form
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editPiketForm');
    if (!editForm) return;

    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const url = form.action;
        const fd = new FormData(form);
        fd.set('_method', 'PUT');
        const token = form.querySelector('input[name="_token"]')?.value;

        // If user opened the edit containers and removed all entries, append cleared flags
        const guruContainer = document.getElementById('editGuruTidakHadirContainer');
        if (guruContainer && guruContainer.style.display !== 'none') {
            const namaInputs = guruContainer.querySelectorAll('input[name$="[nama]"]');
            let anyNama = false;
            namaInputs.forEach(inp => { if (inp.value && inp.value.trim() !== '') anyNama = true; });
            if (!anyNama) fd.append('guru_tidak_hadir_cleared', '1');
        }

        const tamuContainer = document.getElementById('editTamuSekolahContainer');
        if (tamuContainer && tamuContainer.style.display !== 'none') {
            const namaInputs = tamuContainer.querySelectorAll('input[name$="[nama]"]');
            let anyNama = false;
            namaInputs.forEach(inp => { if (inp.value && inp.value.trim() !== '') anyNama = true; });
            if (!anyNama) fd.append('tamu_sekolah_cleared', '1');
        }

        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
            body: fd
        })
        .then(response => {
            const ct = response.headers.get('content-type') || '';
            if (!response.ok) {
                if (ct.includes('application/json')) return response.json().then(data => { const msgs = (data.errors && Object.values(data.errors).flat().join('\n')) || data.error || 'Gagal menyimpan jurnal'; throw new Error(msgs); });
                return response.text().then(text => { if (response.status === 419) throw new Error('Sesi berakhir. Silakan refresh dan login ulang.'); if (response.status === 401) throw new Error('Belum terautentikasi. Silakan login.'); throw new Error((text && text.slice(0,200)) || 'Gagal menyimpan jurnal (non-JSON response)'); });
            }
            if (ct.includes('application/json')) return response.json().catch(() => ({success: true}));
            return response.text().then(text => { throw new Error('Expected JSON but got HTML/other: ' + (text && text.slice(0,200))); });
        })
        .then(() => {
            const editModalEl = document.getElementById('editPiketModal');
            const modal = bootstrap.Modal.getInstance(editModalEl);
            if (modal) modal.hide();
            location.reload();
        })
        .catch(err => { console.error(err); alert(err.message); });
    });
});

// Function to parse list fields flexibly (array, JSON-string, comma-separated)
function parseListField(field) {
    if (!field) return [];
    if (Array.isArray(field)) return field;
    if (typeof field === 'string') {
        // Try JSON parse first
        try {
            const parsed = JSON.parse(field);
            if (Array.isArray(parsed)) return parsed;
        } catch(e) {
            // not JSON, continue to fallback
        }
        // Fallback: comma-separated values -> convert to objects with nama
        const parts = field.split(',').map(s => s.trim()).filter(Boolean);
        if (parts.length > 0) return parts.map(p => ({ nama: p }));
    }
    return [];
}

// Function to populate edit form: prepare display-only data and hide inputs by default
function populateEditForm(data) {
    // Store current arrays globally so other functions can use them
    window.currentGuruData = parseListField(data.guru_tidak_hadir);
    window.currentTamuData = parseListField(data.tamu_sekolah);

    // Clear and hide input containers (they will be shown only when user clicks '+')
    const guruContainer = document.getElementById('editGuruTidakHadirContainer');
    const tamuContainer = document.getElementById('editTamuSekolahContainer');
    if (guruContainer) { guruContainer.innerHTML = ''; guruContainer.style.display = 'none'; }
    if (tamuContainer) { tamuContainer.innerHTML = ''; tamuContainer.style.display = 'none'; }

    // Render display-only representations
    renderGuruDisplay();
    renderTamuDisplay();
}


// Render display-only lists for Guru Tidak Hadir
function renderGuruDisplay() {
    const disp = document.getElementById('editGuruTidakHadirDisplay');
    if (!disp) return;
    const arr = window.currentGuruData || [];
    if (arr.length === 0) {
        disp.innerHTML = '<span class="text-muted">-</span>';
        return;
    }
    let html = '<ul class="mb-0">';
    arr.forEach(g => {
        const nama = (g && (g.nama ?? g)) || '-';
        const cat = (g && (g.catatan ?? g.alasan ?? '-')) || '-';
        html += `<li>${nama} - ${cat}</li>`;
    });
    html += '</ul>';
    disp.innerHTML = html;
}

// Render display-only lists for Tamu
function renderTamuDisplay() {
    const disp = document.getElementById('editTamuSekolahDisplay');
    if (!disp) return;
    const arr = window.currentTamuData || [];
    if (arr.length === 0) {
        disp.innerHTML = '<span class="text-muted">-</span>';
        return;
    }
    let html = '<ul class="mb-0">';
    arr.forEach(t => {
        const nama = (t && (t.nama ?? t)) || '-';
        const tujuan = (t && (t.tujuan ?? '-')) || '-';
        html += `<li>${nama} - ${tujuan}</li>`;
    });
    html += '</ul>';
    disp.innerHTML = html;
}

// Populate edit container for Guru Tidak Hadir
function enableGuruEdit() {
    const container = document.getElementById('editGuruTidakHadirContainer');
    if (!container) return;
    container.innerHTML = '';
    const arr = window.currentGuruData || [];
    if (arr.length > 0) {
        arr.forEach((g, index) => {
            const nama = g.nama ?? g;
            const catatan = g.catatan ?? g.alasan ?? '';
            const div = document.createElement('div');
            div.className = 'row mb-2';
            div.innerHTML = `
                <div class="col-md-6">
                    <input type="text" name="guru_tidak_hadir[${index}][nama]" class="form-control" value="${nama}">
                </div>
                <div class="col-md-5">
                    <input type="text" name="guru_tidak_hadir[${index}][catatan]" class="form-control" value="${catatan}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.parentElement.remove()">-</button>
                </div>
            `;
            container.appendChild(div);
        });
    } else {
        // create a single empty input row
        const div = document.createElement('div');
        div.className = 'row mb-2';
        div.innerHTML = `
            <div class="col-md-6">
                <input type="text" name="guru_tidak_hadir[0][nama]" class="form-control" placeholder="Nama Guru">
            </div>
            <div class="col-md-5">
                <input type="text" name="guru_tidak_hadir[0][catatan]" class="form-control" placeholder="Catatan">
            </div>
            <div class="col-md-1">
            </div>
        `;
        container.appendChild(div);
    }
}

// Populate edit container for Tamu Sekolah
function enableTamuEdit() {
    const container = document.getElementById('editTamuSekolahContainer');
    if (!container) return;
    container.innerHTML = '';
    const arr = window.currentTamuData || [];
    if (arr.length > 0) {
        arr.forEach((t, index) => {
            const namaTamu = t.nama ?? t;
            const tujuanTamu = t.tujuan ?? '';
            const div = document.createElement('div');
            div.className = 'row mb-2';
            div.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="tamu_sekolah[${index}][nama]" class="form-control" value="${namaTamu}">
                </div>
                <div class="col-md-6">
                    <input type="text" name="tamu_sekolah[${index}][tujuan]" class="form-control" value="${tujuanTamu}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.parentElement.remove()">-</button>
                </div>
            `;
            container.appendChild(div);
        });
    } else {
        const div = document.createElement('div');
        div.className = 'row mb-2';
        div.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="tamu_sekolah[0][nama]" class="form-control" placeholder="Nama Tamu">
            </div>
            <div class="col-md-6">
                <input type="text" name="tamu_sekolah[0][tujuan]" class="form-control" placeholder="Tujuan">
            </div>
            <div class="col-md-1">
            </div>
        `;
        container.appendChild(div);
    }
}

</script>
@endpush