<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Manajemen Pengguna & Pegawai</h4>
        <p class="text-muted small mb-0">Kelola akun pegawai, role hak akses (Admin / Pegawai), dan kredensial login.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="fa-solid fa-user-plus"></i>
        <span>Tambah Pengguna Baru</span>
    </button>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0" id="tableUsers" style="font-size: 0.88rem; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">No</th>
                        <th>Nama & Gelar</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Unit Kerja & Jabatan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($u['name']) ?></div>
                                <small class="text-muted">ID: #<?= $u['id'] ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border">@<?= esc($u['username']) ?></span></td>
                            <td><?= esc($u['email'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : 'bg-success' ?> text-uppercase px-2 py-1">
                                    <?= esc($u['role']) ?>
                                </span>
                            </td>
                            <td>
                                <div><?= esc($u['unit_kerja'] ?? '-') ?></div>
                                <small class="text-muted"><?= esc($u['jabatan'] ?? '-') ?></small>
                            </td>
                            <td class="text-center">
                                <?php if ($u['is_active']): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light border btn-edit-user" 
                                            data-id="<?= $u['id'] ?>"
                                            data-name="<?= esc($u['name']) ?>"
                                            data-username="<?= esc($u['username']) ?>"
                                            data-email="<?= esc($u['email']) ?>"
                                            data-role="<?= esc($u['role']) ?>"
                                            data-unit="<?= esc($u['unit_kerja']) ?>"
                                            data-jabatan="<?= esc($u['jabatan']) ?>"
                                            title="Edit Pengguna">
                                        <i class="fa-solid fa-pen text-warning"></i>
                                    </button>
                                    <a href="<?= base_url('users/toggle-status/' . $u['id']) ?>" class="btn btn-light border" title="Aktifkan/Nonaktifkan Akun">
                                        <i class="fa-solid <?= $u['is_active'] ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' ?>"></i>
                                    </a>
                                    <?php if ($u['id'] != session()->get('user_id')): ?>
                                        <a href="<?= base_url('users/delete/' . $u['id']) ?>" class="btn btn-light border btn-confirm-delete" data-item="Pengguna <?= esc($u['name']) ?>" title="Hapus Pengguna">
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

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('users/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i> Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Ahmad Fauzi, S.Kom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="e.g. ahmad.fauzi" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="ahmad@instansi.go.id">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="pegawai">Pegawai (User Biasa)</option>
                                <option value="admin">Administrator (Akses Penuh)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" name="unit_kerja" class="form-control" placeholder="e.g. Bagian Kepegawaian & TI">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" placeholder="e.g. Analis SDM Aparatur">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="formEditUser" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fa-solid fa-user-pen me-2"></i> Edit Data Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="editUsername" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role Akses <span class="text-danger">*</span></label>
                            <select name="role" id="editRole" class="form-select" required>
                                <option value="pegawai">Pegawai (User Biasa)</option>
                                <option value="admin">Administrator (Akses Penuh)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" name="unit_kerja" id="editUnit" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" id="editJabatan" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ganti Password (Biarkan kosong jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Ketik password baru jika ingin mengubah">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        $('#tableUsers').DataTable({
            pageLength: 10,
            language: {
                search: "Cari Pengguna:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pengguna",
                paginate: { next: "›", previous: "‹" }
            }
        });

        // Edit User Click
        $('.btn-edit-user').on('click', function () {
            const id = $(this).data('id');
            $('#editName').val($(this).data('name'));
            $('#editUsername').val($(this).data('username'));
            $('#editEmail').val($(this).data('email'));
            $('#editRole').val($(this).data('role'));
            $('#editUnit').val($(this).data('unit'));
            $('#editJabatan').val($(this).data('jabatan'));

            $('#formEditUser').attr('action', '<?= base_url('users/update') ?>/' + id);
            $('#modalEditUser').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
