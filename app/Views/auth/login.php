<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Nomor Surat</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #094731 0%, #032b1d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 950px;
        }
        .login-banner {
            background: linear-gradient(145deg, #0d7a53 0%, #064e3b 100%);
            color: #ffffff;
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .login-banner::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
        }
        .login-form-side {
            padding: 3.5rem 3rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem 0.75rem 2.85rem;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #0d7a53;
            box-shadow: 0 0 0 4px rgba(13, 122, 83, 0.15);
        }
        .input-icon-group {
            position: relative;
        }
        .input-icon-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #0d7a53 0%, #065438 100%);
            color: #ffffff;
            font-weight: 700;
            padding: 0.85rem;
            border-radius: 10px;
            border: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(13, 122, 83, 0.3);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #109e6c 0%, #0d7a53 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 122, 83, 0.4);
        }
        .quick-badge {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            font-size: 0.78rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .quick-badge:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="row g-0">
            <!-- Left Info Banner -->
            <div class="col-lg-5 login-banner d-none d-lg-flex">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-white text-success rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; font-size: 1.5rem;">
                            <i class="fa-solid fa-envelope-circle-check"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-white fw-bold">SIM-SURAT</h4>
                            <small class="text-white-50">Sistem Nomor Surat Keluar</small>
                        </div>
                    </div>

                    <h3 class="fw-bold mb-3">Penerbitan Nomor Surat Terpusat & Akurat</h3>
                    <p class="text-white-50" style="line-height: 1.6;">
                        Mencegah duplikasi nomor surat, pencarian arsip instan, dan pengambilan nomor otomatis yang aman berbasis Database Transaction & Locking.
                    </p>
                </div>

                <div class="mt-4 pt-4 border-top border-white border-opacity-10">
                    <div class="d-flex align-items-center gap-2 text-white-50 small mb-2">
                        <i class="fa-solid fa-shield-halved text-warning"></i>
                        <span>Keamanan Data & Role-Based Access Control</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="fa-solid fa-calendar-check text-info"></i>
                        <span>Mendukung Pengambilan Nomor Tanggal Mundur</span>
                    </div>
                </div>
            </div>

            <!-- Right Login Form -->
            <div class="col-lg-7 login-form-side">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Masuk ke Sistem</h3>
                    <p class="text-muted small">Silakan masukkan kredensial akun Anda untuk melanjutkan.</p>
                </div>

                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 small rounded-3 mb-3 border-0">
                        <i class="fa-solid fa-circle-exclamation text-danger"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 small rounded-3 mb-3 border-0">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/login') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Username</label>
                        <div class="input-icon-group">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="username" id="inputUsername" class="form-control" placeholder="Masukkan username" value="<?= old('username') ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-dark">Password</label>
                        <div class="input-icon-group">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100 mb-4">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
                    </button>
                </form>

                <!-- Demo Account Quick Helper -->
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-muted"><i class="fa-solid fa-key me-1 text-success"></i> Akun Uji Coba:</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="quick-badge" onclick="fillCredentials('admin', 'admin123')">
                            <strong>Admin:</strong> admin / admin123
                        </button>
                        <button type="button" class="quick-badge" onclick="fillCredentials('pegawai1', 'pegawai123')">
                            <strong>Pegawai:</strong> pegawai1 / pegawai123
                        </button>
                        <button type="button" class="quick-badge" onclick="fillCredentials('budi', 'budi123')">
                            <strong>Pegawai 2:</strong> budi / budi123
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillCredentials(user, pass) {
            document.getElementById('inputUsername').value = user;
            document.getElementById('inputPassword').value = pass;
        }
    </script>
</body>
</html>
