<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('surat') ?>" class="text-decoration-none">Surat</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('surat/show/' . $surat['id']) ?>" class="text-decoration-none">Detail</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-dark">Edit Data Surat Keluar</h4>
            </div>
            <a href="<?= base_url('surat/show/' . $surat['id']) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Detail
            </a>
        </div>

        <form action="<?= base_url('surat/update/' . $surat['id']) ?>" method="POST">
            <?= csrf_field() ?>

            <!-- Locked Number Notice -->
            <div class="card border-0 shadow-sm mb-4" style="background: #f8fafc; border-left: 5px solid #0d7a53 !important;">
                <div class="card-body p-3">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                        <div>
                            <span class="text-muted small d-block">Nomor Urut Resmi (Terkunci / Readonly):</span>
                            <span class="fw-bold font-monospace fs-5 text-success"><?= esc($surat['nomor_urut']) ?></span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Nomor Surat Saat Ini:</span>
                            <span class="fw-bold font-monospace fs-6 text-dark"><?= esc($surat['nomor_surat']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Components Form Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square text-success me-2"></i> 1. Komponen Nomor Surat</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Instansi -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Instansi <span class="text-danger">*</span></label>
                            <input type="text" name="instansi" class="form-control" value="<?= old('instansi', $surat['instansi']) ?>" required>
                        </div>

                        <!-- Kode Surat -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Kode Surat <span class="text-danger">*</span></label>
                            <input type="text" name="kode_surat" class="form-control" value="<?= old('kode_surat', $surat['kode_surat']) ?>" required>
                        </div>

                        <!-- Bulan Romawi -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Bulan Romawi <span class="text-danger">*</span></label>
                            <select name="bulan_romawi" class="form-select" required>
                                <?php foreach (['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'] as $r): ?>
                                    <option value="<?= $r ?>" <?= (old('bulan_romawi', $surat['bulan_romawi']) === $r) ? 'selected' : '' ?>><?= $r ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tahun Nomor -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Tahun Surat <span class="text-danger">*</span></label>
                            <input type="number" name="tahun_nomor" class="form-control" value="<?= old('tahun_nomor', $surat['tahun_nomor']) ?>" min="2000" max="2099" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Form Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-file-lines text-success me-2"></i> 2. Rincian Surat</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" class="form-control" value="<?= old('tanggal_surat', $surat['tanggal_surat']) ?>" max="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tujuan Surat (Kepada Yth.) <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan" class="form-control" value="<?= old('tujuan', $surat['tujuan']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                        <textarea name="perihal" class="form-control" rows="2" required><?= old('perihal', $surat['perihal']) ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" name="unit_kerja" class="form-control" value="<?= old('unit_kerja', $surat['unit_kerja']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Pembuat Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pembuat" class="form-control" value="<?= old('nama_pembuat', $surat['nama_pembuat']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jabatan Pembuat</label>
                            <input type="text" name="jabatan" class="form-control" value="<?= old('jabatan', $surat['jabatan']) ?>">
                        </div>
                    </div>

                    <?php if (session()->get('role') === 'admin'): ?>
                        <div class="mb-3">
                            <label class="form-label">Status Surat (Admin Control)</label>
                            <select name="status" class="form-select">
                                <?php foreach (['Draft', 'Nomor Diambil', 'File Sudah Upload', 'Selesai', 'Dibatalkan'] as $st): ?>
                                    <option value="<?= $st ?>" <?= (old('status', $surat['status']) === $st) ? 'selected' : '' ?>><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Keterangan / Catatan</label>
                        <textarea name="keterangan" class="form-control" rows="2"><?= old('keterangan', $surat['keterangan']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="<?= base_url('surat/show/' . $surat['id']) ?>" class="btn btn-light border px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan Data
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
