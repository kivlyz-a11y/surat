<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Master Kode Klasifikasi Surat</h4>
        <p class="text-muted small mb-0">Daftar kode klasifikasi urusan surat untuk membantu pegawai saat membuat nomor surat.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahKode">
        <i class="fa-solid fa-plus-circle"></i>
        <span>Tambah Kode Baru</span>
    </button>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0" id="tableKode" style="font-size: 0.88rem; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th style="width: 140px;">Kode Surat</th>
                        <th>Nama Klasifikasi Urusan</th>
                        <th>Keterangan / Catatan</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kodeList as $i => $k): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold font-monospace px-2 py-1" style="font-size: 0.9rem;">
                                    <?= esc($k['kode']) ?>
                                </span>
                            </td>
                            <td class="fw-semibold text-dark"><?= esc($k['nama_klasifikasi']) ?></td>
                            <td class="text-muted small"><?= esc($k['keterangan'] ?? '-') ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light border btn-edit-kode"
                                            data-id="<?= $k['id'] ?>"
                                            data-kode="<?= esc($k['kode']) ?>"
                                            data-nama="<?= esc($k['nama_klasifikasi']) ?>"
                                            data-ket="<?= esc($k['keterangan']) ?>"
                                            title="Edit Kode">
                                        <i class="fa-solid fa-pen text-warning"></i>
                                    </button>
                                    <?php if (session()->get('role') === 'admin'): ?>
                                        <a href="<?= base_url('kode-surat/delete/' . $k['id']) ?>" class="btn btn-light border btn-confirm-delete" data-item="Kode Surat: <?= esc($k['kode']) ?>" title="Hapus Kode">
                                            <i class="fa-solid fa-trash text-danger"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kode -->
<div class="modal fade" id="modalTambahKode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('kode-surat/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-tag me-2"></i> Tambah Kode Surat Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Kode Surat / Klasifikasi <span class="text-danger">*</span></label>
                        <input type="text" name="kode" class="form-control" placeholder="e.g. HM2.1.1, KU1.2, KP.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Klasifikasi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_klasifikasi" class="form-control" placeholder="e.g. Hubungan Masyarakat & Keprotokolan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Penjelasan rincian peruntukan kode ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Simpan Kode
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kode -->
<div class="modal fade" id="modalEditKode" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form id="formEditKode" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Kode Klasifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Kode Surat <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="editKodeVal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Klasifikasi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_klasifikasi" id="editNamaVal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea name="keterangan" id="editKetVal" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        $('#tableKode').DataTable({
            pageLength: 10,
            language: {
                search: "Cari Kode:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ kode",
                paginate: { next: "›", previous: "‹" }
            }
        });

        $('.btn-edit-kode').on('click', function () {
            const id = $(this).data('id');
            $('#editKodeVal').val($(this).data('kode'));
            $('#editNamaVal').val($(this).data('nama'));
            $('#editKetVal').val($(this).data('ket'));

            $('#formEditKode').attr('action', '<?= base_url('kode-surat/update') ?>/' + id);
            $('#modalEditKode').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
