<!-- Edit Piket Modal (partial) -->
<div class="modal fade" id="editPiketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jurnal Piket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('jurnal.piket.update', ':id') }}" method="POST" id="editPiketForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editPiketId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal</label>
                            <input type="date" id="editTanggal" name="tanggal" class="form-control" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Hari</label>
                            <input type="text" id="editHari" name="hari" class="form-control" readonly required>
                        </div>

                        <!-- Guru Tidak Hadir (display mode + inline + button) -->
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0">Guru Tidak Hadir</h6>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="enableGuruEdit()">+</button>
                            </div>
                            <div id="editGuruTidakHadirDisplay" class="mb-2 small text-muted">
                                <!-- Display-only content will be rendered here -->
                            </div>
                            <div id="editGuruTidakHadirContainer" style="display:none">
                                <!-- Dynamic input fields will be created here when editing -->
                            </div>
                        </div>

                        <!-- Tamu Sekolah (display mode + inline + button) -->
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0">Tamu Sekolah</h6>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="enableTamuEdit()">+</button>
                            </div>
                            <div id="editTamuSekolahDisplay" class="mb-2 small text-muted">
                                <!-- Display-only content will be rendered here -->
                            </div>
                            <div id="editTamuSekolahContainer" style="display:none">
                                <!-- Dynamic input fields will be created here when editing -->
                            </div>
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