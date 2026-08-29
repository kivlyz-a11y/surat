<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-pen text-success fs-5"></i>
                    <h5 class="mb-0 fw-bold">Profil & Keamanan Akun</h5>
                </div>
                <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-success' ?> text-uppercase px-3 py-2">
                    Role: <?= esc($user['role']) ?>
                </span>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('profile') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?= old('name', $user['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username (Hanya Baca)</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['username']) ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= old('email', $user['email']) ?>" placeholder="contoh@instansi.go.id">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" name="unit_kerja" class="form-control" value="<?= old('unit_kerja', $user['unit_kerja']) ?>" placeholder="e.g. Bagian Kepegawaian & TI">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="<?= old('jabatan', $user['jabatan']) ?>" placeholder="e.g. Analis SDM Aparatur">
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-lock me-1 text-warning"></i> Ganti Password (Opsional)</h6>
                        <p class="text-muted small mb-3">Biarkan kosong jika tidak ingin mengubah password akun Anda.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
