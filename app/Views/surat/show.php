<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumbs & Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('surat') ?>" class="text-decoration-none">Daftar Surat</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Surat</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-dark">Rincian Nomor Surat Keluar</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('surat/cetak/' . $surat['id']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="fa-solid fa-print"></i> Cetak Bukti
        </a>
        <?php if ($surat['status'] !== 'Dibatalkan'): ?>
            <a href="<?= base_url('surat/edit/' . $surat['id']) ?>" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                <i class="fa-solid fa-pen-to-square"></i> Edit Data
            </a>
        <?php endif; ?>
        <?php if (session()->get('role') === 'admin' && $surat['status'] !== 'Dibatalkan'): ?>
            <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalBatalkan">
                <i class="fa-solid fa-ban"></i> Batalkan Nomor
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Left Main Detail Card -->
    <div class="col-lg-8">
        <!-- Number Callout Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-left: 5px solid #0d7a53 !important;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-hashtag me-1"></i> NOMOR URUT: <?= esc($surat['nomor_urut']) ?>
                    </span>
                    <?php
                    $badgeClass = match($surat['status']) {
                        'Selesai'           => 'badge-selesai',
                        'File Sudah Upload' => 'badge-file-uploaded',
                        'Dibatalkan'        => 'badge-dibatalkan',
                        'Draft'             => 'badge-draft',
                        default             => 'badge-nomor-diambil',
                    };
                    ?>
                    <span class="badge badge-status <?= $badgeClass ?> px-3 py-2" style="font-size: 0.85rem;">
                        Status: <?= esc($surat['status']) ?>
                    </span>
                </div>

                <h3 class="fw-bold font-monospace text-dark mb-2"><?= esc($surat['nomor_surat']) ?></h3>
                
                <?php if ($surat['is_backdate']): ?>
                    <div class="alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 mb-0">
                        <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                        <div>
                            <strong>Pengambilan Nomor Tanggal Mundur:</strong> Surat ini didaftarkan untuk tanggal lampau (<?= date('d/m/Y', strtotime($surat['tanggal_surat'])) ?>) dengan suffix alfabet otomatis <code><?= esc($surat['nomor_urut']) ?></code>.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-file-lines me-2 text-success"></i> Informasi Surat Keluar</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Tanggal Surat</label>
                        <span class="fw-bold text-dark"><?= date('d F Y', strtotime($surat['tanggal_surat'])) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small d-block">Waktu Pengambilan Nomor</label>
                        <span class="fw-bold text-dark"><?= date('d/m/Y H:i:s', strtotime($surat['created_at'])) ?> WIB</span>
                    </div>

                    <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                    <div class="col-12">
                        <label class="text-muted small d-block">Perihal Surat</label>
                        <p class="fw-semibold text-dark fs-6 mb-0"><?= esc($surat['perihal']) ?></p>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small d-block">Tujuan Surat (Kepada Yth.)</label>
                        <span class="fw-bold text-dark"><?= esc($surat['tujuan']) ?></span>
                    </div>

                    <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                    <!-- Komponen Nomor Surat Breakdown -->
                    <div class="col-sm-3 col-6">
                        <label class="text-muted small d-block">Instansi</label>
                        <span class="badge bg-light text-dark border font-monospace"><?= esc($surat['instansi']) ?></span>
                    </div>
                    <div class="col-sm-3 col-6">
                        <label class="text-muted small d-block">Kode Klasifikasi</label>
                        <span class="badge bg-light text-dark border font-monospace"><?= esc($surat['kode_surat']) ?></span>
                    </div>
                    <div class="col-sm-3 col-6">
                        <label class="text-muted small d-block">Bulan Romawi</label>
                        <span class="badge bg-light text-dark border font-monospace"><?= esc($surat['bulan_romawi']) ?></span>
                    </div>
                    <div class="col-sm-3 col-6">
                        <label class="text-muted small d-block">Tahun Nomor</label>
                        <span class="badge bg-light text-dark border font-monospace"><?= esc($surat['tahun_nomor']) ?></span>
                    </div>

                    <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                    <div class="col-sm-4">
                        <label class="text-muted small d-block">Nama Pembuat</label>
                        <span class="fw-bold text-dark"><?= esc($surat['nama_pembuat']) ?></span>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small d-block">Unit Kerja</label>
                        <span class="text-dark"><?= esc($surat['unit_kerja'] ?? '-') ?></span>
                    </div>
                    <div class="col-sm-4">
                        <label class="text-muted small d-block">Jabatan</label>
                        <span class="text-dark"><?= esc($surat['jabatan'] ?? '-') ?></span>
                    </div>

                    <?php if (!empty($surat['keterangan'])): ?>
                        <div class="col-12">
                            <label class="text-muted small d-block">Keterangan Tambahan</label>
                            <div class="p-3 bg-light rounded-3 text-dark small">
                                <?= nl2br(esc($surat['keterangan'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Info (File Attachment & Logs) -->
    <div class="col-lg-4">
        <!-- Document Attachment Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-paperclip me-2 text-primary"></i> Berkas Dokumen</h6>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUploadFile">
                    <i class="fa-solid fa-upload me-1"></i> <?= empty($surat['file_path']) ? 'Unggah' : 'Ganti' ?>
                </button>
            </div>
            <div class="card-body p-4 text-center">
                <?php if (!empty($surat['file_path'])): ?>
                    <?php
                    $extShow = strtolower(pathinfo($surat['file_path'], PATHINFO_EXTENSION));
                    ?>
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <i class="fa-solid <?= ($extShow === 'pdf') ? 'fa-file-pdf text-danger' : 'fa-file-word text-primary' ?> fs-1 mb-2"></i>
                        <div class="fw-bold text-truncate small text-dark" title="<?= esc($surat['nama_file']) ?>">
                            <?= esc($surat['nama_file']) ?>
                        </div>
                        <small class="text-muted text-uppercase"><?= $extShow ?> Dokumen</small>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <?php if ($extShow === 'pdf'): ?>
                            <a href="<?= base_url('surat/view-file/' . $surat['id']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-up-right-from-square me-1"></i> Buka Layar Penuh
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('surat/download-file/' . $surat['id']) ?>" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-download me-1"></i> Unduh File Surat
                        </a>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <form action="<?= base_url('surat/delete-file/' . $surat['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas dokumen ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus File Lampiran
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if ($extShow === 'pdf'): ?>
                        <div class="ratio ratio-4x3 rounded-3 border overflow-hidden shadow-sm" style="min-height: 400px; background: #525659;">
                            <iframe src="<?= base_url('surat/view-file/' . $surat['id']) ?>#toolbar=1" allowfullscreen title="Pratinjau Dokumen"></iframe>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="py-4 text-muted">
                        <i class="fa-solid fa-file-circle-question fs-1 opacity-50 mb-2"></i>
                        <p class="small mb-0">Belum ada berkas dokumen yang diunggah untuk surat ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Audit Trail / Activity Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Riwayat & Audit Trail</h6>
            </div>
            <div class="card-body p-4">
                <div class="timeline">
                    <?php foreach ($logs as $log): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <strong class="small text-dark"><?= esc($log['aktivitas']) ?></strong>
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                </small>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.4;">
                                <?= esc($log['keterangan']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Dokumen -->
<div class="modal fade" id="modalUploadFile" tabindex="-1" aria-labelledby="modalUploadFileLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('surat/upload-file/' . $surat['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalUploadFileLabel"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Unggah / Ganti Berkas Dokumen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Berkas Dokumen (PDF, DOC, DOCX)</label>
                        <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.doc,.docx" required>
                        <div class="form-text">Maksimal ukuran file: <?= esc($settings['batas_upload_mb'] ?? 10) ?> MB.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-upload me-1"></i> Unggah Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (session()->get('role') === 'admin'): ?>
<!-- Modal Batalkan Nomor Surat -->
<div class="modal fade" id="modalBatalkan" tabindex="-1" aria-labelledby="modalBatalkanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('surat/batalkan/' . $surat['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalBatalkanLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i> Batalkan Nomor Surat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning small mb-3">
                        <i class="fa-solid fa-info-circle me-1"></i> <strong>Perhatian:</strong> Nomor surat yang dibatalkan akan diubah statusnya menjadi <code>Dibatalkan</code> dan tetap tersimpan sebagai arsip. Nomor urut ini tidak akan pernah digunakan kembali.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="alasan_batal" class="form-control" rows="3" placeholder="Masukkan alasan pembatalan nomor surat..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-ban me-1"></i> Ya, Batalkan Nomor Ini
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
