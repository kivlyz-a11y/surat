<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sistem Manajemen Nomor Surat') ?> - <?= esc($settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.min.css">
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <!-- Brand Header -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>
            <div class="sidebar-brand-text">
                SIM-SURAT
                <small>Manajemen Nomor Surat</small>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === 'dashboard' || uri_string() === '' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- MASTER SECTION -->
                <div class="menu-heading">Master Data</div>
                <?php if (session()->get('role') === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(uri_string(), 'users') ? 'active' : '' ?>" href="<?= base_url('users') ?>">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Data Pegawai</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(uri_string(), 'kode-surat') ? 'active' : '' ?>" href="<?= base_url('kode-surat') ?>">
                        <i class="fa-solid fa-tags"></i>
                        <span>Kode Surat</span>
                    </a>
                </li>

                <!-- MANAJEMEN SURAT SECTION -->
                <div class="menu-heading">Manajemen Surat</div>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === 'surat/create' ? 'active' : '' ?>" href="<?= base_url('surat/create') ?>">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Buat Nomor Surat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === 'surat' ? 'active' : '' ?>" href="<?= base_url('surat') ?>">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Daftar Surat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(uri_string(), 'surat') && uri_string() !== 'surat/create' && uri_string() !== 'surat' ? 'active' : '' ?>" href="<?= base_url('surat') ?>">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Riwayat Surat</span>
                    </a>
                </li>

                <!-- LAPORAN SECTION -->
                <div class="menu-heading">Laporan</div>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(uri_string(), 'reports') ? 'active' : '' ?>" href="<?= base_url('reports') ?>">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Laporan Surat</span>
                    </a>
                </li>

                <!-- PENGATURAN SECTION (ADMIN ONLY) -->
                <?php if (session()->get('role') === 'admin'): ?>
                <div class="menu-heading">Pengaturan</div>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(uri_string(), 'settings') ? 'active' : '' ?>" href="<?= base_url('settings') ?>">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Pengaturan Sistem</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(uri_string(), 'users') ? 'active' : '' ?>" href="<?= base_url('users') ?>">
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(uri_string(), 'logs') ? 'active' : '' ?>" href="<?= base_url('logs') ?>">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Log Aktivitas</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="user-mini-profile">
                <div class="avatar-initial">
                    <?= strtoupper(substr(session()->get('name') ?? 'U', 0, 1)) ?>
                </div>
                <div class="text-truncate" style="max-width: 140px;">
                    <div class="text-white fw-bold small text-truncate"><?= esc(session()->get('name')) ?></div>
                    <span class="badge <?= session()->get('role') === 'admin' ? 'bg-danger' : 'bg-success' ?> text-uppercase" style="font-size: 0.65rem;">
                        <?= esc(session()->get('role')) ?>
                    </span>
                </div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-outline-light text-nowrap" title="Logout" onclick="return confirmLogout(event)">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="app-wrapper">
        <!-- Top Navbar -->
        <header class="app-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn-sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle Navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 text-dark fw-bold"><?= esc($title ?? 'Dashboard') ?></h5>
                    <small class="text-muted"><i class="fa-solid fa-building me-1 text-success"></i> <?= esc(session()->get('unit_kerja') ?? 'Instansi Pemerintah') ?></small>
                </div>
            </div>

            <!-- Navbar Right Actions -->
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('surat/create') ?>" class="btn btn-sm btn-primary d-none d-sm-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Ambil Nomor</span>
                </a>

                <!-- User Dropdown Menu -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-pill border d-flex align-items-center gap-2 px-3 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="avatar-initial" style="width: 28px; height: 28px; font-size: 0.75rem;">
                            <?= strtoupper(substr(session()->get('name') ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="fw-semibold small text-dark d-none d-sm-inline"><?= esc(session()->get('name')) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px; min-width: 220px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold small text-dark"><?= esc(session()->get('name')) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;">@<?= esc(session()->get('username')) ?> (<?= esc(session()->get('role')) ?>)</div>
                        </li>
                        <li><a class="dropdown-item py-2" href="<?= base_url('profile') ?>"><i class="fa-solid fa-user me-2 text-primary"></i> Profil Saya</a></li>
                        <?php if (session()->get('role') === 'admin'): ?>
                        <li><a class="dropdown-item py-2" href="<?= base_url('settings') ?>"><i class="fa-solid fa-gear me-2 text-secondary"></i> Pengaturan</a></li>
                        <li><a class="dropdown-item py-2" href="<?= base_url('logs') ?>"><i class="fa-solid fa-clipboard-list me-2 text-info"></i> Log Aktivitas</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('auth/logout') ?>" onclick="return confirmLogout(event)"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content Body -->
        <main class="app-content">
            <!-- Flash Message Alerts -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation fs-5 text-danger"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                        <strong class="text-danger">Terdapat beberapa kesalahan pengisian form:</strong>
                    </div>
                    <ul class="mb-0 ps-4 small">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-3 px-4 bg-white border-top text-center text-muted small">
            <div>&copy; <?= date('Y') ?> <strong><?= esc($settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?></strong>. Hak Cipta Dilindungi.</div>
        </footer>
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.all.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Sidebar Toggle for Mobile/Desktop
        $('#sidebarToggle, #sidebarBackdrop').on('click', function () {
            $('#appSidebar').toggleClass('show');
            $('#sidebarBackdrop').toggleClass('show');
        });

        // SweetAlert2 Confirmation Dialog Helper
        function confirmLogout(event) {
            event.preventDefault();
            const href = event.currentTarget.getAttribute('href');
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar dari sistem?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d7a53',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
            return false;
        }

        // Universal Delete Confirmation
        $(document).on('click', '.btn-confirm-delete', function (e) {
            e.preventDefault();
            const href = $(this).attr('href');
            const item = $(this).data('item') || 'data ini';
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus ${item}? Data yang dihapus tidak dapat dikembalikan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
