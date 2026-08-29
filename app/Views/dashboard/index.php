<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 text-white overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #094731 0%, #064e3b 100%); border-radius: 16px;">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success bg-opacity-50 text-white border border-white border-opacity-25 px-3 py-1">
                                <i class="fa-solid fa-circle-check text-warning me-1"></i> Sistem Aktif & Terhubung
                            </span>
                            <span class="badge bg-white bg-opacity-10 text-white px-3 py-1">
                                <i class="fa-regular fa-calendar me-1"></i> <?= date('d F Y') ?>
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1">Selamat Datang, <?= esc(session()->get('name')) ?>!</h3>
                        <p class="text-white-50 mb-0">
                            Kelola penerbitan nomor surat keluar instansi secara aman, terpusat, dan bebas bentrok.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="<?= base_url('surat/create') ?>" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm">
                            <i class="fa-solid fa-plus-circle me-1"></i> Ambil Nomor Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Tahun Berjalan -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-emerald h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Surat Tahun <?= date('Y') ?></div>
                    <div class="stat-value my-1"><?= number_format($stats['total_tahun']) ?></div>
                    <small class="opacity-75"><i class="fa-solid fa-calendar me-1"></i> Tahun berjalan</small>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-folder-closed"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Bulan Ini -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-teal h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Surat Bulan Ini</div>
                    <div class="stat-value my-1"><?= number_format($stats['total_bulan']) ?></div>
                    <small class="opacity-75"><i class="fa-solid fa-calendar-days me-1"></i> Bulan <?= date('F') ?></small>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Jumlah Surat Hari Ini -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-amber h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Surat Hari Ini</div>
                    <div class="stat-value my-1"><?= number_format($stats['total_hari_ini']) ?></div>
                    <small class="opacity-75"><i class="fa-regular fa-clock me-1"></i> <?= date('d/m/Y') ?></small>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Nomor Terakhir -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-card-blue h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-truncate">
                    <div class="stat-title">Nomor Urut Terakhir</div>
                    <div class="stat-value my-1 text-truncate" style="font-size: 1.5rem;">
                        <?= $latestSurat ? esc($latestSurat['nomor_urut']) : '000' ?>
                    </div>
                    <small class="opacity-75 text-truncate d-block" title="<?= $latestSurat ? esc($latestSurat['nomor_surat']) : 'Belum ada surat' ?>">
                        <?= $latestSurat ? esc($latestSurat['nomor_surat']) : 'Belum ada surat' ?>
                    </small>
                </div>
                <div class="stat-icon flex-shrink-0">
                    <i class="fa-solid fa-hashtag"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Rankings Row -->
<div class="row g-3 mb-4">
    <!-- Chart: Tren Surat Bulanan -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chart-line text-success"></i>
                    <span>Tren Penerbitan Surat (Tahun <?= date('Y') ?>)</span>
                </div>
                <span class="badge bg-light text-dark border">12 Bulan</span>
            </div>
            <div class="card-body">
                <div style="height: 280px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard: Jumlah Surat per Pegawai -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-users text-primary"></i>
                    <span>Surat per Pegawai</span>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary">Ranking</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($suratPerPegawai)): ?>
                        <div class="text-center py-4 text-muted small">Belum ada data surat</div>
                    <?php else: ?>
                        <?php foreach ($suratPerPegawai as $idx => $pegawai): ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between px-3 py-2 border-0 border-bottom">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <span class="badge rounded-pill <?= $idx === 0 ? 'bg-warning text-dark' : ($idx === 1 ? 'bg-secondary' : 'bg-light text-dark') ?>" style="width: 24px;">
                                        <?= $idx + 1 ?>
                                    </span>
                                    <div class="text-truncate">
                                        <div class="fw-bold small text-truncate"><?= esc($pegawai['nama_pembuat']) ?></div>
                                        <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;"><?= esc($pegawai['unit_kerja'] ?? '-') ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">
                                    <?= number_format($pegawai['total_surat']) ?> Surat
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Recent Letters & Activity Logs -->
<div class="row g-3">
    <!-- Recent Letters Table -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-success"></i>
                    <span>Surat Keluar Terbaru</span>
                </div>
                <a href="<?= base_url('surat') ?>" class="btn btn-sm btn-outline-success">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th>No. Surat</th>
                                <th>Tanggal</th>
                                <th>Perihal</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentSurat)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data surat keluar.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentSurat as $surat): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark font-monospace"><?= esc($surat['nomor_surat']) ?></div>
                                            <small class="text-muted"><i class="fa-solid fa-user me-1"></i> <?= esc($surat['nama_pembuat']) ?></small>
                                        </td>
                                        <td class="text-nowrap"><?= date('d/m/Y', strtotime($surat['tanggal_surat'])) ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 220px;" title="<?= esc($surat['perihal']) ?>">
                                                <?= esc($surat['perihal']) ?>
                                            </div>
                                            <small class="text-muted d-block text-truncate" style="max-width: 220px;">Tujuan: <?= esc($surat['tujuan']) ?></small>
                                        </td>
                                        <td>
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
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= base_url('surat/show/' . $surat['id']) ?>" class="btn btn-sm btn-light border" title="Lihat Detail">
                                                <i class="fa-solid fa-eye text-primary"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Logs Timeline -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-list-check text-info"></i>
                    <span>Aktivitas Sistem Terbaru</span>
                </div>
                <?php if (session()->get('role') === 'admin'): ?>
                    <a href="<?= base_url('logs') ?>" class="btn btn-sm btn-outline-secondary">Semua Log</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($recentLogs)): ?>
                    <div class="text-center py-4 text-muted small">Belum ada aktivitas tercatat.</div>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <strong class="small text-dark"><?= esc($log['aktivitas']) ?></strong>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <?= date('d/m H:i', strtotime($log['created_at'])) ?>
                                    </small>
                                </div>
                                <p class="text-muted small mb-0" style="line-height: 1.4;">
                                    <?= esc($log['keterangan']) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
    const monthlyData = <?= json_encode($monthlyData) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Jumlah Surat Keluar',
                data: monthlyData,
                borderColor: '#0d7a53',
                backgroundColor: 'rgba(13, 122, 83, 0.12)',
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointBackgroundColor: '#0d7a53',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>
