@extends('layouts.app')

@section('title', 'Edit Jurnal Kegiatan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Jurnal Kegiatan</h2>
            <a href="{{ route('jurnal.kegiatan') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-body">

                <form action="{{ route('jurnal.kegiatan.update', $jurnal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', \Carbon\Carbon::parse($jurnal->tanggal)->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $jurnal->nama_kegiatan) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kegiatan</label>
                        <select name="jenis_kegiatan" class="form-control form-select" required>
                            @php $jenis = old('jenis_kegiatan', $jurnal->jenis_kegiatan); @endphp
                            <option value="upacara" {{ $jenis=='upacara' ? 'selected' : '' }}>Upacara</option>
                            <option value="sosialisasi" {{ $jenis=='sosialisasi' ? 'selected' : '' }}>Sosialisasi</option>
                            <option value="pelatihan" {{ $jenis=='pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                            <option value="perlombaan" {{ $jenis=='perlombaan' ? 'selected' : '' }}>Perlombaan</option>
                            <option value="lainnya" {{ $jenis=='lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai', \Carbon\Carbon::parse($jurnal->waktu_mulai)->format('H:i')) }}" step="60" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai', \Carbon\Carbon::parse($jurnal->waktu_selesai)->format('H:i')) }}" step="60" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $jurnal->lokasi) }}" required>
                    </div>

                    @php
                        $peserta = is_array($jurnal->peserta) ? $jurnal->peserta : (is_string($jurnal->peserta) ? json_decode($jurnal->peserta, true) : []);
                        $peserta = $peserta ?: ['siswa' => 0, 'guru' => 0];
                    @endphp

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Peserta - Siswa</label>
                            <input type="number" name="peserta[siswa]" class="form-control" value="{{ old('peserta.siswa', $peserta['siswa'] ?? 0) }}" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Peserta - Guru</label>
                            <input type="number" name="peserta[guru]" class="form-control" value="{{ old('peserta.guru', $peserta['guru'] ?? 0) }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $jurnal->deskripsi) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Kegiatan (upload untuk menambah, Maksimal 5 foto; biarkan kosong jika tak ingin mengubah)</label>
                        <input type="file" id="kegiatanFotoInput" name="foto_kegiatan[]" class="form-control" multiple accept="image/*">
                        @if($jurnal->foto_kegiatan && is_array($jurnal->foto_kegiatan) && count($jurnal->foto_kegiatan)>0)
                            <div class="mt-2">
                                <small>Foto saat ini:</small>
                                <div id="existingKegiatanFotos" class="d-flex flex-wrap mt-2"></div>
                            </div>
                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function(){
                                    const container = document.getElementById('existingKegiatanFotos');
                                    const fotos = @json($jurnal->foto_kegiatan ?? []);
                                    
                                    if (!fotos || fotos.length === 0) {
                                        container.innerHTML = '<p class="text-muted">Tidak ada foto yang tersimpan</p>';
                                        return;
                                    }
                                    
                                    let html = '';
                                    fotos.forEach(p => {
                                        const safePath = p;
                                        html += `<div class="me-2 mb-2 text-center position-relative" style="width:140px">`;
                                        html += `<img data-path="${safePath}" style="width:140px;height:90px;object-fit:cover;display:block;margin-bottom:6px;" class="rounded border kegiatan-edit-thumb" src="" alt="Loading...">`;
                                        html += `<input type="checkbox" name="remove_photos[]" value="${safePath}" style="display:none;">`;
                                        html += `</div>`;
                                    });
                                    container.innerHTML = html;

                                    // Load foto dengan multiple fallbacks - improved
                                    const thumbs = container.querySelectorAll('.kegiatan-edit-thumb');
                                    thumbs.forEach(imgEl => {
                                        const p = imgEl.dataset.path;
                                        const origin = window.location.origin;
                                        const pathNormalized = p.replace(/\\/g, '/');
                                        
                                        // Use canonical storage URL directly (fewer requests)
                                        const candidate = `${origin}/storage/${pathNormalized}`;
                                        imgEl.src = candidate;
                                        imgEl.style.opacity = '0.5';
                                        imgEl.onerror = function() {
                                            imgEl.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="140" height="90"%3E%3Crect fill="%23e9ecef" width="140" height="90"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial" font-size="11" fill="%23999"%3EFile not found%3C/text%3E%3C/svg%3E';
                                            imgEl.style.opacity = '1';
                                        };
                                    });

                                    // UI feedback on checkbox
                                    container.querySelectorAll('input[type="checkbox"]').forEach(cb => {
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

                                    // Add remove button for immediate visual feedback
                                    container.querySelectorAll('.position-relative').forEach(el => {
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

                                    // Enforce max 5 files when user picks new files and per-file size
                                    const input = document.getElementById('kegiatanFotoInput');
                                    if(input) {
                                        input.addEventListener('change', function(){
                                            const existingCount = container.querySelectorAll('.position-relative').length;
                                            const removedCount = Array.from(container.querySelectorAll('input[type="checkbox"]')).filter(x => x.checked).length;
                                            const newCount = this.files ? this.files.length : 0;
                                            const maxFiles = 5;
                                            const maxSize = 2 * 1024 * 1024; // 2MB
                                            if ((existingCount - removedCount + newCount) > maxFiles) {
                                                alert('Tidak bisa lebih dari 5 foto.');
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
                                });
                            </script>
                            @endpush
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumentasi (upload untuk mengganti)</label>
                        <input type="file" name="dokumentasi[]" class="form-control" multiple accept=".pdf,.doc,.docx,.ppt,.pptx">
                        @if($jurnal->dokumentasi && is_array($jurnal->dokumentasi) && count($jurnal->dokumentasi)>0)
                            <div class="mt-2">
                                <small>Dokumentasi saat ini:</small>
                                <div id="existingDokumentasi" class="mt-2">
                                    @foreach($jurnal->dokumentasi as $index => $doc)
                                        @php $docPath = $doc['path'] ?? null; $docName = $doc['nama'] ?? ($doc['original_name'] ?? basename($docPath ?? 'file')); @endphp
                                        <div class="doc-item position-relative mb-2 p-2 border rounded" style="padding-right:40px">
                                            @if($docPath && Storage::disk('public')->exists($docPath))
                                                <a href="{{ route('jurnal.kegiatan.download', ['jurnal' => $jurnal->id, 'index' => $index]) }}" download>
                                                    <i class="bi bi-file-earmark"></i> {{ $docName }}
                                                </a>
                                            @else
                                                <span class="text-muted">(file tidak ditemukan) {{ $docName }}</span>
                                            @endif
                                            <div class="mt-1"><label class="small"><input type="checkbox" name="remove_documents[]" value="{{ $docPath }}"> Hapus</label></div>
                                            <button type="button" class="btn btn-sm btn-outline-danger doc-remove-btn position-absolute" style="top:6px; right:6px"><i class="bi bi-trash"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function(){
                                    const container = document.getElementById('existingDokumentasi');
                                    if (!container) return;
                                    container.querySelectorAll('.doc-remove-btn').forEach(btn => {
                                        btn.addEventListener('click', function(){
                                            const parent = this.closest('.doc-item');
                                            if (!parent) return;
                                            const cb = parent.querySelector('input[type="checkbox"]');
                                            if (cb) cb.checked = true;
                                            parent.style.display = 'none';
                                        });
                                    });

                                    container.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                                        cb.addEventListener('change', () => {
                                            const parent = cb.closest('.doc-item');
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
                                });
                            </script>
                            @endpush
                        @endif
                    </div>

                    <button class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection