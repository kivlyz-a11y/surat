<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Pengaturan Konfigurasi Sistem</h4>
                <p class="text-muted small mb-0">Konfigurasi umum aplikasi, batas berkas, format tampilan nomor surat, dan status counter.</p>
            </div>
        </div>

        <form action="<?= base_url('settings/update') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row g-4">
                <!-- General Settings Card -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-sliders text-success me-2"></i> Konfigurasi Umum</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Nama Aplikasi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_aplikasi" class="form-control" value="<?= old('nama_aplikasi', $settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Instansi Default <span class="text-danger">*</span></label>
                                <input type="text" name="instansi_default" class="form-control" value="<?= old('instansi_default', $settings['instansi_default'] ?? 'PTA.KU') ?>" required>
                                <div class="form-text">Nilai default yang akan otomatis terisi pada formulir pembuatan surat.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Format Susunan Nomor Surat <span class="text-danger">*</span></label>
                                <input type="text" name="format_tampilan" class="form-control font-monospace" value="<?= old('format_tampilan', $settings['format_tampilan'] ?? '{nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}') ?>" required>
                                <div class="form-text">
                                    Placeholder yang tersedia: <code>{nomor_urut}</code>, <code>{instansi}</code>, <code>{kode_surat}</code>, <code>{bulan_romawi}</code>, <code>{tahun}</code>.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Batas Maksimal Upload (MB) <span class="text-danger">*</span></label>
                                    <input type="number" name="batas_upload_mb" class="form-control" value="<?= old('batas_upload_mb', $settings['batas_upload_mb'] ?? 10) ?>" min="1" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Padding Digit Nomor Urut <span class="text-danger">*</span></label>
                                    <select name="padding_digit" class="form-select" required>
                                        <option value="1" <?= ($settings['padding_digit'] == 1) ? 'selected' : '' ?>>1 Digit (1, 2, ...)</option>
                                        <option value="2" <?= ($settings['padding_digit'] == 2) ? 'selected' : '' ?>>2 Digit (01, 02, ...)</option>
                                        <option value="3" <?= ($settings['padding_digit'] == 3) ? 'selected' : '' ?>>3 Digit (001, 002, ...)</option>
                                        <option value="4" <?= ($settings['padding_digit'] == 4) ? 'selected' : '' ?>>4 Digit (0001, 0002, ...)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-bold small">Ekstensi File yang Diizinkan <span class="text-danger">*</span></label>
                                <input type="text" name="ekstensi_file" class="form-control" value="<?= old('ekstensi_file', $settings['ekstensi_file'] ?? 'pdf,doc,docx') ?>" required>
                                <div class="form-text">Pisahkan dengan koma (contoh: <code>pdf,doc,docx</code>).</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Counter Settings Card -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-hashtag text-primary me-2"></i> Mode & Status Counter</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Mode Penomoran Counter <span class="text-danger">*</span></label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="mode_counter" id="modeGlobal" value="global" <?= ($settings['mode_counter'] ?? 'global') === 'global' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="modeGlobal">
                                        <strong>Counter Global Terpusat</strong>
                                        <small class="text-muted d-block">Nomor urut terus berlanjut berurutan tanpa reset tahunan.</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mode_counter" id="modeTahunan" value="per_tahun" <?= ($settings['mode_counter'] ?? '') === 'per_tahun' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="modeTahunan">
                                        <strong>Counter Reset Per Tahun</strong>
                                        <small class="text-muted d-block">Nomor urut dimulai kembali dari 001 setiap pergantian tahun baru.</small>
                                    </label>
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold small text-dark mb-2"><i class="fa-solid fa-database text-secondary me-1"></i> Data Counter Saat Ini:</h6>
                                <?php foreach ($counters as $c): ?>
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                                        <span class="small"><?= $c['tahun_counter'] == 0 ? 'Counter Global' : 'Tahun ' . $c['tahun_counter'] ?>:</span>
                                        <span class="badge bg-success font-monospace px-2 py-1 fs-6">Terakhir: <?= $c['nomor_terakhir'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Semua Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
