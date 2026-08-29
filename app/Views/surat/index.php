<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Daftar & Riwayat Nomor Surat</h4>
        <p class="text-muted small mb-0">
            <?= (session()->get('role') === 'pegawai') ? 'Menampilkan riwayat nomor surat yang Anda buat.' : 'Menampilkan seluruh buku register nomor surat keluar instansi.' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('reports') ?>" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
            <i class="fa-solid fa-file-excel"></i> Rekap & Laporan
        </a>
        <a href="<?= base_url('surat/create') ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="fa-solid fa-plus-circle"></i> Buat Nomor Surat
        </a>
    </div>
</div>

<!-- Indicator: Bulan Berjalan -->
<?php if ($isViewingCurrentMonth): ?>
    <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 py-2 px-3 mb-4 rounded-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-calendar-check text-success fs-5"></i>
            <div>
                <span class="small text-dark">Secara default sistem menampilkan data surat <strong>Bulan Berjalan (Bulan <?= $currentMonthRoman ?> / Tahun <?= $currentYear ?>)</strong>.</span>
            </div>
        </div>
        <a href="<?= base_url('surat?tahun=all&bulan=all&filter_applied=1') ?>" class="btn btn-sm btn-outline-success py-1 px-3 text-nowrap">
            <i class="fa-solid fa-list-ul me-1"></i> Tampilkan Seluruh Bulan
        </a>
    </div>
<?php endif; ?>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-filter text-success"></i>
                <h6 class="mb-0 fw-bold">Filter Pencarian Surat</h6>
            </div>
            <a href="<?= base_url('surat') ?>" class="btn btn-link btn-sm text-secondary text-decoration-none p-0">
                <i class="fa-solid fa-rotate-left me-1"></i> Reset Filter (Bulan Berjalan)
            </a>
        </div>
    </div>
    <div class="card-body p-3">
        <form action="<?= base_url('surat') ?>" method="GET" class="row g-2">
            <input type="hidden" name="filter_applied" value="1">

            <!-- Search Text -->
            <div class="col-md-4 col-lg-3">
                <label class="form-label small mb-1">Cari Kata Kunci</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="No. surat / perihal / tujuan..." value="<?= esc($filters['q'] ?? '') ?>">
            </div>

            <!-- Filter Tahun -->
            <div class="col-6 col-md-2 col-lg-2">
                <label class="form-label small mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="all" <?= ($filters['tahun'] === 'all') ? 'selected' : '' ?>>Semua Tahun</option>
                    <?php foreach ($distinctTahun as $t): ?>
                        <option value="<?= $t ?>" <?= ($filters['tahun'] == $t) ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Bulan Romawi -->
            <div class="col-6 col-md-2 col-lg-2">
                <label class="form-label small mb-1">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    <option value="all" <?= ($filters['bulan'] === 'all') ? 'selected' : '' ?>>Semua Bulan</option>
                    <?php 
                    $bulanList = [
                        'I'    => 'I (Januari)',
                        'II'   => 'II (Februari)',
                        'III'  => 'III (Maret)',
                        'IV'   => 'IV (April)',
                        'V'    => 'V (Mei)',
                        'VI'   => 'VI (Juni)',
                        'VII'  => 'VII (Juli)',
                        'VIII' => 'VIII (Agustus)',
                        'IX'   => 'IX (September)',
                        'X'    => 'X (Oktober)',
                        'XI'   => 'XI (November)',
                        'XII'  => 'XII (Desember)'
                    ];
                    foreach ($bulanList as $code => $label): 
                    ?>
                        <option value="<?= $code ?>" <?= ($filters['bulan'] === $code) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-6 col-md-2 col-lg-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="all" <?= ($filters['status'] === 'all' || empty($filters['status'])) ? 'selected' : '' ?>>Semua Status</option>
                    <?php foreach (['Draft', 'Nomor Diambil', 'File Sudah Upload', 'Selesai', 'Dibatalkan'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($filters['status'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Kode Surat -->
            <div class="col-6 col-md-2 col-lg-2">
                <label class="form-label small mb-1">Kode Surat</label>
                <select name="kode_surat" class="form-select form-select-sm">
                    <option value="all" <?= ($filters['kode_surat'] === 'all' || empty($filters['kode_surat'])) ? 'selected' : '' ?>>Semua Kode</option>
                    <?php foreach ($distinctKode as $kd): ?>
                        <option value="<?= esc($kd['kode']) ?>" <?= ($filters['kode_surat'] == $kd['kode']) ? 'selected' : '' ?>>
                            <?= esc($kd['kode']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Status Upload Berkas -->
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label small mb-1 fw-bold text-dark">Status Berkas File</label>
                <select name="file_status" class="form-select form-select-sm border-danger border-opacity-50">
                    <option value="">Semua Berkas</option>
                    <option value="belum_upload" <?= ($filters['file_status'] === 'belum_upload') ? 'selected' : '' ?>>🔴 Belum Upload File</option>
                    <option value="sudah_upload" <?= ($filters['file_status'] === 'sudah_upload') ? 'selected' : '' ?>>🟢 Sudah Ada File</option>
                </select>
            </div>

            <?php if (session()->get('role') === 'admin'): ?>
            <!-- Filter Pegawai (Admin only) -->
            <div class="col-md-4 col-lg-3">
                <label class="form-label small mb-1">Pembuat</label>
                <select name="pegawai" class="form-select form-select-sm">
                    <option value="">Semua Pegawai</option>
                    <?php foreach ($pegawaiList as $pg): ?>
                        <option value="<?= $pg['id'] ?>" <?= ($filters['pegawai'] == $pg['id']) ? 'selected' : '' ?>>
                            <?= esc($pg['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= base_url('surat?file_status=belum_upload&tahun=all&bulan=all&filter_applied=1') ?>" class="btn btn-sm <?= ($filters['file_status'] === 'belum_upload') ? 'btn-danger text-white' : 'btn-outline-danger' ?> py-1 px-3 d-flex align-items-center gap-1 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Hanya Belum Upload (<strong><?= $totalPendingUpload ?></strong>)</span>
                    </a>
                    <a href="<?= base_url('surat?tahun=' . $currentYear . '&bulan=' . $currentMonthRoman . '&filter_applied=1') ?>" class="btn btn-sm btn-outline-success py-1 px-3 d-flex align-items-center gap-1">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span>Bulan Berjalan (<?= $currentMonthRoman ?>)</span>
                    </a>
                    <a href="<?= base_url('surat?tahun=all&bulan=all&filter_applied=1') ?>" class="btn btn-sm btn-outline-secondary py-1 px-3 d-flex align-items-center gap-1">
                        <i class="fa-solid fa-list-ul"></i>
                        <span>Semua Data Surat</span>
                    </a>
                </div>

                <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0" id="tableSurat" style="font-size: 0.86rem; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th>Nomor Surat</th>
                        <th>No. Urut</th>
                        <th>Instansi</th>
                        <th>Kode</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Tanggal</th>
                        <th>Perihal & Tujuan</th>
                        <th>Pembuat</th>
                        <th class="text-center">File Dokumen</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="min-width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suratList as $i => $s): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td>
                                <a href="<?= base_url('surat/show/' . $s['id']) ?>" class="fw-bold font-monospace text-success text-decoration-none">
                                    <?= esc($s['nomor_surat']) ?>
                                </a>
                                <?php if ($s['is_backdate']): ?>
                                    <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;" title="Pengambilan Nomor Tanggal Mundur">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Mundur
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= esc($s['nomor_urut']) ?></span></td>
                            <td><?= esc($s['instansi']) ?></td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?= esc($s['kode_surat']) ?></span></td>
                            <td><?= esc($s['bulan_romawi']) ?></td>
                            <td><?= esc($s['tahun_nomor']) ?></td>
                            <td class="text-nowrap"><?= date('d/m/Y', strtotime($s['tanggal_surat'])) ?></td>
                            <td>
                                <div class="fw-semibold text-truncate" style="max-width: 220px;" title="<?= esc($s['perihal']) ?>">
                                    <?= esc($s['perihal']) ?>
                                </div>
                                <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="<?= esc($s['tujuan']) ?>">
                                    <i class="fa-solid fa-paper-plane me-1"></i> <?= esc($s['tujuan']) ?>
                                </small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 130px;" title="<?= esc($s['nama_pembuat']) ?>">
                                    <?= esc($s['nama_pembuat']) ?>
                                </div>
                            </td>

                            <!-- File Column with RED notice if empty -->
                            <td class="text-center">
                                <?php if (!empty($s['file_path'])): ?>
                                    <a href="<?= base_url('surat/download-file/' . $s['id']) ?>" class="btn btn-sm btn-outline-success py-1 px-2 text-nowrap" title="Unduh: <?= esc($s['nama_file']) ?>">
                                        <i class="fa-solid fa-file-arrow-down me-1"></i> Unduh File
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-danger text-white border border-danger px-2 py-1 shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;" title="Dokumen fisik / scan surat belum diunggah">
                                        <i class="fa-solid fa-circle-xmark"></i> Belum Upload
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Status Column -->
                            <td class="text-center">
                                <?php
                                $badgeClass = match($s['status']) {
                                    'Selesai'           => 'badge-selesai',
                                    'File Sudah Upload' => 'badge-file-uploaded',
                                    'Dibatalkan'        => 'badge-dibatalkan',
                                    'Draft'             => 'badge-draft',
                                    default             => 'badge-nomor-diambil',
                                };
                                ?>
                                <span class="badge badge-status <?= $badgeClass ?>"><?= esc($s['status']) ?></span>
                            </td>

                            <!-- Action Column with Public Copy Link Button -->
                            <td class="text-center">
                                <div class="btn-group btn-group-sm shadow-sm">
                                    <!-- Public Share Link Button (for guests / people without login) -->
                                    <button type="button" class="btn btn-light border btn-copy-public-link" 
                                            data-url="<?= base_url('cek-surat/' . $s['id']) ?>" 
                                            data-nomor="<?= esc($s['nomor_surat']) ?>" 
                                            title="Salin Tautan Publik (Dapat dibuka siapa saja tanpa perlu login)">
                                        <i class="fa-solid fa-share-nodes text-primary"></i>
                                    </button>

                                    <!-- Detail Button -->
                                    <a href="<?= base_url('surat/show/' . $s['id']) ?>" class="btn btn-light border" title="Lihat Rincian Lengkap">
                                        <i class="fa-solid fa-eye text-success"></i>
                                    </a>

                                    <!-- Print Button -->
                                    <a href="<?= base_url('surat/cetak/' . $s['id']) ?>" target="_blank" class="btn btn-light border" title="Cetak Lembar Tanda Bukti">
                                        <i class="fa-solid fa-print text-secondary"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <?php if ($s['status'] !== 'Dibatalkan'): ?>
                                        <a href="<?= base_url('surat/edit/' . $s['id']) ?>" class="btn btn-light border" title="Edit Data Surat">
                                            <i class="fa-solid fa-pen text-warning"></i>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Admin Delete Button -->
                                    <?php if (session()->get('role') === 'admin'): ?>
                                        <a href="<?= base_url('surat/delete/' . $s['id']) ?>" class="btn btn-light border btn-confirm-delete" data-item="Nomor Surat: <?= esc($s['nomor_surat']) ?>" title="Hapus Data">
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        $('#tableSurat').DataTable({
            responsive: false,
            pageLength: 15,
            lengthMenu: [10, 15, 25, 50, 100],
            order: [[0, 'asc']],
            language: {
                search: "Pencarian Cepat:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                zeroRecords: "Tidak ada data surat yang sesuai dengan pencarian / filter.",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Copy Public Verification Link Handler
        $(document).on('click', '.btn-copy-public-link', function (e) {
            e.preventDefault();
            const url   = $(this).data('url');
            const nomor = $(this).data('nomor');

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tautan Publik Berhasil Disalin!',
                        html: `
                            <p class="small text-muted mb-2">Tautan verifikasi untuk nomor surat <strong>${nomor}</strong>:</p>
                            <input type="text" class="form-control text-center font-monospace bg-light mb-3" value="${url}" readonly onclick="this.select()">
                            <div class="alert alert-info py-2 px-3 small mb-0 text-start">
                                <i class="fa-solid fa-info-circle me-1"></i> Tautan ini dapat dibuka langsung oleh masyarakat/pihak luar tanpa perlu login ke sistem.
                            </div>
                        `,
                        confirmButtonColor: '#0d7a53',
                        confirmButtonText: 'Tutup'
                    });
                }).catch(function () {
                    promptFallback(url, nomor);
                });
            } else {
                promptFallback(url, nomor);
            }
        });

        function promptFallback(url, nomor) {
            Swal.fire({
                title: 'Tautan Publik Surat',
                html: `
                    <p class="small text-muted mb-2">Silakan salin tautan berikut untuk dibagikan:</p>
                    <input type="text" class="form-control text-center font-monospace bg-light mb-2" value="${url}" autofocus onclick="this.select()">
                    <small class="text-muted">Dapat dibuka tanpa login.</small>
                `,
                confirmButtonColor: '#0d7a53',
                confirmButtonText: 'Selesai'
            });
        }
    });
</script>
<?= $this->endSection() ?>
