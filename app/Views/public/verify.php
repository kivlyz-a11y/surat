<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= esc($settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?></title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .public-navbar {
            background: linear-gradient(135deg, #094731 0%, #064e3b 100%);
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .card-verify {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .verify-header {
            background: linear-gradient(135deg, #0d7a53 0%, #065438 100%);
            color: #ffffff;
            padding: 2rem 2rem;
            position: relative;
        }
        .badge-verified {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .nomor-surat-box {
            background: rgba(0, 0, 0, 0.25);
            border: 1px dashed rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            padding: 1rem;
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.35rem;
            font-weight: 700;
            color: #a7f3d0;
            letter-spacing: 0.5px;
        }
        .badge-status {
            padding: 0.4em 0.85em;
            font-weight: 600;
            font-size: 0.82rem;
            border-radius: 6px;
        }
        .badge-draft { background-color: #e2e8f0; color: #475569; }
        .badge-nomor-diambil { background-color: #dbeafe; color: #1e40af; }
        .badge-file-uploaded { background-color: #dcfce7; color: #15803d; }
        .badge-selesai { background-color: #d1fae5; color: #065f46; }
        .badge-dibatalkan { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <!-- Public Header -->
    <header class="public-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.25rem;">
                    <i class="fa-solid fa-envelope-circle-check"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-white"><?= esc($settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?></h5>
                    <small class="text-white-50">Portal Publik Informasi & Verifikasi Nomor Surat</small>
                </div>
            </div>
            <a href="<?= base_url('auth/login') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Login Petugas
            </a>
        </div>
    </header>

    <!-- Content -->
    <main class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card-verify">
                    <!-- Top Header -->
                    <div class="verify-header text-center">
                        <div class="d-inline-flex align-items-center gap-2 badge-verified mb-3">
                            <i class="fa-solid fa-circle-check text-warning"></i>
                            <span>Nomor Surat Resmi Terdaftar di Sistem</span>
                        </div>
                        <div class="nomor-surat-box mb-2">
                            <?= esc($surat['nomor_surat']) ?>
                        </div>
                        <small class="text-white-50">
                            Waktu Penerbitan: <?= date('d F Y - H:i:s', strtotime($surat['created_at'])) ?> WIB
                        </small>
                    </div>

                    <!-- Body Details -->
                    <div class="p-4 p-md-5">
                        <!-- Status Alert if Cancelled -->
                        <?php if ($surat['status'] === 'Dibatalkan'): ?>
                            <div class="alert alert-danger d-flex align-items-center gap-3 mb-4 rounded-3">
                                <i class="fa-solid fa-triangle-exclamation fs-3 text-danger"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Nomor Surat Telah Dibatalkan</h6>
                                    <small>Nomor surat ini telah dinonaktifkan / dibatalkan oleh pihak instansi dan tidak berlaku untuk administrasi dinas.</small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Surat Details Table -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-circle-info text-success me-2"></i> Rincian Data Surat Keluar
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Nomor Urut Surat</span>
                                <span class="fw-bold font-monospace fs-5 text-success"><?= esc($surat['nomor_urut']) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Tanggal Surat</span>
                                <span class="fw-bold text-dark fs-6"><?= date('d F Y', strtotime($surat['tanggal_surat'])) ?></span>
                            </div>

                            <div class="col-12">
                                <span class="text-muted small d-block">Perihal Surat</span>
                                <p class="fw-bold text-dark fs-6 mb-0"><?= esc($surat['perihal']) ?></p>
                            </div>

                            <div class="col-12">
                                <span class="text-muted small d-block">Tujuan Surat (Kepada Yth.)</span>
                                <span class="fw-semibold text-dark"><?= esc($surat['tujuan']) ?></span>
                            </div>

                            <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                            <div class="col-sm-4">
                                <span class="text-muted small d-block">Instansi Pengirim</span>
                                <span class="fw-semibold text-dark"><?= esc($surat['instansi']) ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted small d-block">Kode Klasifikasi</span>
                                <span class="badge bg-light text-dark border font-monospace"><?= esc($surat['kode_surat']) ?></span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted small d-block">Status Saat Ini</span>
                                <?php
                                $badgeClass = match($surat['status']) {
                                    'Selesai'           => 'badge-selesai',
                                    'File Sudah Upload' => 'badge-file-uploaded',
                                    'Dibatalkan'        => 'badge-dibatalkan',
                                    'Draft'             => 'badge-draft',
                                    default             => 'badge-nomor-diambil',
                                };
                                ?>
                                <span class="badge badge-status <?= $badgeClass ?>"><?= esc($surat['status']) ?></span>
                            </div>

                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Nama Pembuat Surat</span>
                                <span class="fw-semibold text-dark"><?= esc($surat['nama_pembuat']) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Unit Kerja</span>
                                <span class="text-dark"><?= esc($surat['unit_kerja'] ?? '-') ?></span>
                            </div>
                        </div>

                        <!-- Document Attachment Section -->
                        <div class="p-4 bg-light rounded-3 border">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="fa-solid fa-file-circle-check text-success me-2"></i> Lampiran Berkas Dokumen Resmi
                                </h6>
                                <?php if (!empty($surat['file_path'])): ?>
                                    <div class="d-flex gap-2">
                                        <a href="<?= base_url('cek-surat/view-file/' . $surat['id']) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="fa-solid fa-up-right-from-square me-1"></i> Buka Layar Penuh
                                        </a>
                                        <a href="<?= base_url('cek-surat/download/' . $surat['id']) ?>" class="btn btn-success btn-sm rounded-pill px-3">
                                            <i class="fa-solid fa-download me-1"></i> Unduh File
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($surat['file_path'])): ?>
                                <?php
                                $ext = strtolower(pathinfo($surat['file_path'], PATHINFO_EXTENSION));
                                ?>
                                <div class="p-2 bg-white rounded-3 border mb-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid <?= ($ext === 'pdf') ? 'fa-file-pdf text-danger' : 'fa-file-word text-primary' ?> fs-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 380px;"><?= esc($surat['nama_file']) ?></div>
                                            <small class="text-muted text-uppercase"><?= $ext ?> Dokumen</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="fa-solid fa-check me-1"></i> Terverifikasi
                                    </span>
                                </div>

                                <?php if ($ext === 'pdf'): ?>
                                    <!-- Embedded Interactive PDF Viewer -->
                                    <div class="mt-3">
                                        <div class="ratio ratio-16x9 rounded-3 border overflow-hidden shadow-sm" style="min-height: 620px; background: #525659;">
                                            <iframe src="<?= base_url('cek-surat/view-file/' . $surat['id']) ?>#toolbar=1" allowfullscreen title="Pratinjau Dokumen PDF"></iframe>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Notice for Word Document -->
                                    <div class="alert alert-info py-3 px-4 d-flex align-items-center justify-content-between rounded-3 mb-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-file-word text-primary fs-3"></i>
                                            <div>
                                                <div class="fw-bold">Dokumen Microsoft Word (<?= strtoupper($ext) ?>)</div>
                                                <small class="text-muted">Klik tombol unduh di samping untuk membuka dokumen ini di komputer Anda.</small>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('cek-surat/download/' . $surat['id']) ?>" class="btn btn-primary btn-sm px-3">
                                            <i class="fa-solid fa-download me-1"></i> Unduh Sekarang
                                        </a>
                                    </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="text-muted small py-3 text-center">
                                    <i class="fa-solid fa-file-circle-question fs-2 opacity-50 mb-2 d-block"></i>
                                    <span>Belum ada berkas dokumen digital yang diunggah untuk nomor surat ini.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-center mt-4 text-muted small">
                    Halaman ini merupakan tautan publik resmi untuk memverifikasi keabsahan nomor surat keluar yang tercatat di <strong><?= esc($settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?></strong>.
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-3 bg-white border-top text-center text-muted small">
        <div>&copy; <?= date('Y') ?> <strong><?= esc($settings['instansi_default'] ?? 'PTA.KU') ?></strong>. Hak Cipta Dilindungi.</div>
    </footer>

</body>
</html>
