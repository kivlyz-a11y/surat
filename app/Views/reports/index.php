<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Laporan & Rekapitulasi Nomor Surat</h4>
        <p class="text-muted small mb-0">Cetak rekapitulasi buku register surat dan ekspor ke Excel atau format cetak PDF.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('reports/export-excel?' . http_build_query($filters)) ?>" class="btn btn-success btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-file-excel"></i>
            <span>Ekspor ke Excel</span>
        </a>
        <a href="<?= base_url('reports/export-pdf?' . http_build_query($filters)) ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak / Simpan PDF</span>
        </a>
    </div>
</div>

<!-- Summary Mini Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <span class="text-muted small fw-bold">Total Hasil Filter</span>
            <div class="fs-4 fw-bold text-dark my-1"><?= number_format($totalSurat) ?></div>
            <small class="text-success small">Surat Terdaftar</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <span class="text-muted small fw-bold">Surat Selesai</span>
            <div class="fs-4 fw-bold text-success my-1"><?= number_format($totalSelesai) ?></div>
            <small class="text-muted small">Status Selesai</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <span class="text-muted small fw-bold">File Terunggah</span>
            <div class="fs-4 fw-bold text-primary my-1"><?= number_format($totalUpload) ?></div>
            <small class="text-muted small">Memiliki Dokumen</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <span class="text-muted small fw-bold">Dibatalkan</span>
            <div class="fs-4 fw-bold text-danger my-1"><?= number_format($totalDibatalkan) ?></div>
            <small class="text-muted small">Nomor Dibatalkan</small>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="fa-solid fa-filter text-success me-2"></i> Kriteria Filter Laporan</h6>
    </div>
    <div class="card-body p-3">
        <form action="<?= base_url('reports') ?>" method="GET" class="row g-2">
            <!-- Filter Tanggal Mulai & Selesai -->
            <div class="col-md-3">
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($filters['start_date'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($filters['end_date'] ?? '') ?>">
            </div>

            <!-- Filter Tahun -->
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($distinctTahun as $t): ?>
                        <option value="<?= $t ?>" <?= ($filters['tahun'] == $t) ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Bulan -->
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Bulan Romawi</label>
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <?php foreach (['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'] as $b): ?>
                        <option value="<?= $b ?>" <?= ($filters['bulan'] == $b) ? 'selected' : '' ?>><?= $b ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <?php foreach (['Draft', 'Nomor Diambil', 'File Sudah Upload', 'Selesai', 'Dibatalkan'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($filters['status'] == $st) ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Kode -->
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Kode Klasifikasi</label>
                <select name="kode_surat" class="form-select form-select-sm">
                    <option value="">Semua Kode</option>
                    <?php foreach ($distinctKode as $kd): ?>
                        <option value="<?= esc($kd['kode']) ?>" <?= ($filters['kode_surat'] == $kd['kode']) ? 'selected' : '' ?>>
                            <?= esc($kd['kode']) ?> - <?= esc($kd['nama_klasifikasi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (session()->get('role') === 'admin'): ?>
            <!-- Filter Pegawai -->
            <div class="col-md-3">
                <label class="form-label small mb-1">Pegawai Pembuat</label>
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

            <div class="col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Terapkan Filter
                </button>
                <a href="<?= base_url('reports') ?>" class="btn btn-sm btn-light border px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Report Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-bordered table-hover align-middle mb-0" id="tableReport" style="font-size: 0.86rem; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th>Nomor Urut</th>
                        <th>Nomor Surat Lengkap</th>
                        <th>Instansi</th>
                        <th>Kode</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Tanggal Surat</th>
                        <th>Perihal</th>
                        <th>Tujuan Surat</th>
                        <th>Pembuat</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suratList as $idx => $s): ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1 ?></td>
                            <td class="font-monospace text-center"><?= esc($s['nomor_urut']) ?></td>
                            <td class="font-monospace fw-bold text-success"><?= esc($s['nomor_surat']) ?></td>
                            <td><?= esc($s['instansi']) ?></td>
                            <td><?= esc($s['kode_surat']) ?></td>
                            <td class="text-center"><?= esc($s['bulan_romawi']) ?></td>
                            <td class="text-center"><?= esc($s['tahun_nomor']) ?></td>
                            <td class="text-nowrap"><?= date('d/m/Y', strtotime($s['tanggal_surat'])) ?></td>
                            <td><?= esc($s['perihal']) ?></td>
                            <td><?= esc($s['tujuan']) ?></td>
                            <td><?= esc($s['nama_pembuat']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?= esc($s['status']) ?></span>
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
        $('#tableReport').DataTable({
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "Filter Data Tabel:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ surat",
                paginate: { next: "›", previous: "‹" }
            }
        });
    });
</script>
<?= $this->endSection() ?>
