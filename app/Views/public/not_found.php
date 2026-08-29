<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tidak Ditemukan - <?= esc($settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?></title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card-404 {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 3rem 2rem;
            max-width: 550px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <div class="card-404">
        <div class="text-danger mb-3">
            <i class="fa-solid fa-file-circle-xmark fs-1"></i>
        </div>
        <h4 class="fw-bold mb-2">Data Surat Tidak Ditemukan</h4>
        <p class="text-muted small mb-4">
            Nomor surat atau tautan yang Anda tuju tidak terdaftar pada basis data resmi sistem. Pastikan tautan yang Anda buka sudah benar.
        </p>
        <a href="<?= base_url('auth/login') ?>" class="btn btn-outline-success btn-sm px-4 rounded-pill">
            <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk ke Sistem
        </a>
    </div>
</body>
</html>
