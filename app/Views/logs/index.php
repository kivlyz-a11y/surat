<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Log Aktivitas & Audit Trail</h4>
        <p class="text-muted small mb-0">Catatan kronologis seluruh interaksi pengguna, penerbitan nomor surat, perubahan data, dan akses sistem.</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('logs') ?>" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Pengguna</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua Pengguna</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] == $u['id']) ? 'selected' : '' ?>>
                            <?= esc($u['name']) ?> (@<?= esc($u['username']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Tanggal Aktivitas</label>
                <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($filters['date'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Jenis Aktivitas</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Semua Aktivitas</option>
                    <?php foreach ($distinctAktivitas as $act): ?>
                        <option value="<?= esc($act) ?>" <?= ($filters['type'] == $act) ? 'selected' : '' ?>><?= esc($act) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Filter Log
                </button>
                <a href="<?= base_url('logs') ?>" class="btn btn-sm btn-light border px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Logs -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0" id="tableLogs" style="font-size: 0.88rem; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th style="width: 150px;">Waktu Kejadian</th>
                        <th style="width: 180px;">Pengguna</th>
                        <th style="width: 170px;">Jenis Aktivitas</th>
                        <th>Keterangan Rinci</th>
                        <th style="width: 150px;">Terkait Surat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $i => $log): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td class="text-nowrap font-monospace text-muted small">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td>
                                <?php if (!empty($log['user_name'])): ?>
                                    <div class="fw-bold text-dark"><?= esc($log['user_name']) ?></div>
                                    <small class="text-muted">@<?= esc($log['username']) ?> (<?= esc($log['role']) ?>)</small>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Sistem / Tamu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badgeStyle = match($log['aktivitas']) {
                                    'Membuat Nomor Surat' => 'bg-success text-white',
                                    'Mengambil Nomor Surat' => 'bg-success text-white',
                                    'Membatalkan Nomor Surat' => 'bg-danger text-white',
                                    'Menghapus Data Surat' => 'bg-danger text-white',
                                    'Login Sistem' => 'bg-info text-white',
                                    'Logout Sistem' => 'bg-secondary text-white',
                                    'Upload File Surat' => 'bg-primary text-white',
                                    default => 'bg-light text-dark border',
                                };
                                ?>
                                <span class="badge <?= $badgeStyle ?> px-2 py-1"><?= esc($log['aktivitas']) ?></span>
                            </td>
                            <td>
                                <div style="line-height: 1.4;"><?= esc($log['keterangan']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($log['surat_id'])): ?>
                                    <a href="<?= base_url('surat/show/' . $log['surat_id']) ?>" class="badge bg-light text-success border font-monospace text-decoration-none">
                                        <?= esc($log['nomor_surat'] ?? 'Surat #' . $log['surat_id']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
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
        $('#tableLogs').DataTable({
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "Cari Log:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ log",
                paginate: { next: "›", previous: "‹" }
            }
        });
    });
</script>
<?= $this->endSection() ?>
