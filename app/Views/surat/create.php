<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Breadcrumb / Header -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('surat') ?>" class="text-decoration-none">Surat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buat Nomor Surat</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-dark">Formulir Pengambilan Nomor Surat Keluar</h4>
            </div>
            <a href="<?= base_url('surat') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <form action="<?= base_url('surat/store') ?>" method="POST" enctype="multipart/form-data" id="formSurat">
            <?= csrf_field() ?>

            <!-- Preview Card (Top / Sticky) -->
            <div class="preview-box mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-eye text-warning fs-5"></i>
                        <span class="fw-bold text-white">Live Preview Format Nomor Surat:</span>
                    </div>
                    <span id="backdateBadge" class="badge bg-warning text-dark d-none">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Tanggal Mundur Terdeteksi (Suffix Alfabet)
                    </span>
                </div>
                <div class="preview-number text-center text-md-start" id="livePreviewText">
                    --- / <?= esc($settings['instansi_default'] ?? 'PTA.KU') ?> / --- / <?= $currentRomanMonth ?> / <?= $currentYear ?>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2 small text-white-50">
                    <span id="previewNote"><i class="fa-solid fa-lock me-1"></i> Nomor urut resmi akan dikunci dan diberikan secara atomik saat data disimpan.</span>
                    <span id="previewNomorUrut">Estimasi Nomor Urut: <strong>...</strong></span>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-success fs-5"></i>
                        <h6 class="mb-0 fw-bold">1. Komponen Nomor Surat</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Instansi -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Instansi <span class="text-danger">*</span></label>
                            <input type="text" name="instansi" id="inputInstansi" class="form-control" value="<?= old('instansi', $settings['instansi_default'] ?? 'PTA.KU') ?>" placeholder="e.g. PTA.KU" required>
                            <div class="form-text">Nama atau kode instansi pembuat.</div>
                        </div>

                        <!-- Kode Surat -->
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Kode Surat <span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-success text-decoration-none small" data-bs-toggle="modal" data-bs-target="#modalPilihKode">
                                    <i class="fa-solid fa-list-check me-1"></i> Pilih Kode
                                </button>
                            </div>
                            <input type="text" name="kode_surat" id="inputKodeSurat" class="form-control" value="<?= old('kode_surat', 'HM2.1.1') ?>" placeholder="e.g. HM2.1.1" required>
                            <div class="form-text" id="kodeSuratDesc">Klasifikasi urusan surat.</div>
                        </div>

                        <!-- Bulan Romawi -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Bulan Romawi <span class="text-danger">*</span></label>
                            <select name="bulan_romawi" id="inputBulanRomawi" class="form-select" required>
                                <?php
                                $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                                foreach ($romans as $r):
                                ?>
                                    <option value="<?= $r ?>" <?= (old('bulan_romawi', $currentRomanMonth) === $r) ? 'selected' : '' ?>><?= $r ?> (Bulan <?= array_search($r, $romans) + 1 ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Format Romawi I - XII.</div>
                        </div>

                        <!-- Tahun Nomor -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Tahun Surat <span class="text-danger">*</span></label>
                            <input type="number" name="tahun_nomor" id="inputTahunNomor" class="form-control" value="<?= old('tahun_nomor', $currentYear) ?>" min="2000" max="2099" required>
                            <div class="form-text">4 digit angka tahun.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Detail Surat Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-lines text-success fs-5"></i>
                        <h6 class="mb-0 fw-bold">2. Data & Rincian Surat</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <!-- Tanggal Surat -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" id="inputTanggalSurat" class="form-control" value="<?= old('tanggal_surat', $todayDate) ?>" max="<?= $todayDate ?>" required>
                            <div class="form-text text-muted">
                                <i class="fa-solid fa-info-circle me-1"></i> Boleh memilih tanggal hari ini atau tanggal sebelumnya (tanggal mundur). Tidak boleh melewati hari ini.
                            </div>
                        </div>

                        <!-- Tujuan Surat -->
                        <div class="col-md-6">
                            <label class="form-label">Tujuan Surat (Kepada Yth.) <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan" class="form-control" value="<?= old('tujuan') ?>" placeholder="e.g. Kepala Kantor Pelayanan Perbendaharaan Negara" required>
                        </div>
                    </div>

                    <!-- Perihal -->
                    <div class="mb-3">
                        <label class="form-label">Perihal Surat <span class="text-danger">*</span></label>
                        <textarea name="perihal" class="form-control" rows="2" placeholder="Masukkan perihal / isi ringkas surat keluar..." required><?= old('perihal') ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Unit Kerja -->
                        <div class="col-md-4">
                            <label class="form-label">Unit Kerja Pembuat</label>
                            <input type="text" name="unit_kerja" class="form-control" value="<?= old('unit_kerja', $user['unit_kerja']) ?>" placeholder="e.g. Bagian Kepegawaian & TI">
                        </div>

                        <!-- Nama Pembuat -->
                        <div class="col-md-4">
                            <label class="form-label">Nama Pembuat Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pembuat" class="form-control" value="<?= old('nama_pembuat', $user['name']) ?>" required>
                        </div>

                        <!-- Jabatan -->
                        <div class="col-md-4">
                            <label class="form-label">Jabatan Pembuat</label>
                            <input type="text" name="jabatan" class="form-control" value="<?= old('jabatan', $user['jabatan']) ?>" placeholder="e.g. Analis SDM Aparatur">
                        </div>
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Catatan Tambahan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan internal atau keterangan lampiran..."><?= old('keterangan') ?></textarea>
                    </div>

                    <!-- Upload Dokumen Surat -->
                    <div class="p-3 bg-light rounded-3 border">
                        <label class="form-label fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                            <span>Upload Berkas / Dokumen Surat (Opsional)</span>
                        </label>
                        <input type="file" name="file_surat" class="form-control mb-2" accept=".pdf,.doc,.docx">
                        <small class="text-muted d-block">
                            Format yang diperbolehkan: <strong><?= strtoupper(esc($settings['ekstensi_file'] ?? 'pdf,doc,docx')) ?></strong> | Maksimal: <strong><?= esc($settings['batas_upload_mb'] ?? 10) ?> MB</strong>. Anda juga dapat mengunggah file nanti.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Submit Button Card -->
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <div>
                        <div class="fw-bold text-dark">Konfirmasi Penerbitan Nomor</div>
                        <small class="text-muted">Nomor urut akan diambil secara transaksi aman dan tercatat pada buku register.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('surat') ?>" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 py-2" id="btnSubmit">
                            <i class="fa-solid fa-check-circle me-1"></i> Generate & Simpan Nomor Surat
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pilih Kode Surat -->
<div class="modal fade" id="modalPilihKode" tabindex="-1" aria-labelledby="modalPilihKodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalPilihKodeLabel"><i class="fa-solid fa-tags me-2"></i> Pilih Kode Klasifikasi Surat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <p class="text-muted small mb-3">Klik salah satu kode di bawah ini untuk mengisinya ke formulir pembuatan surat.</p>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle" id="tableKodeHelper" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 120px;">Kode</th>
                                <th>Klasifikasi Urusan</th>
                                <th>Keterangan</th>
                                <th style="width: 80px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kodeList as $k): ?>
                                <tr>
                                    <td class="fw-bold font-monospace text-success"><?= esc($k['kode']) ?></td>
                                    <td><?= esc($k['nama_klasifikasi']) ?></td>
                                    <td class="small text-muted"><?= esc($k['keterangan'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-success btn-select-kode" data-kode="<?= esc($k['kode']) ?>" data-nama="<?= esc($k['nama_klasifikasi']) ?>">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        // Init DataTable in helper modal
        $('#tableKodeHelper').DataTable({
            pageLength: 8,
            lengthChange: false,
            language: {
                search: "Cari Kode / Klasifikasi:",
                paginate: { next: "›", previous: "‹" },
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ kode"
            }
        });

        // Pick Kode Surat from Modal
        $('.btn-select-kode').on('click', function () {
            const kode = $(this).data('kode');
            const nama = $(this).data('nama');
            $('#inputKodeSurat').val(kode);
            $('#kodeSuratDesc').text(nama);
            $('#modalPilihKode').modal('hide');
            updateLivePreview();
        });

        // Trigger preview on input change
        $('#inputInstansi, #inputKodeSurat, #inputBulanRomawi, #inputTahunNomor, #inputTanggalSurat').on('input change', function () {
            updateLivePreview();
        });

        // Automatically update Roman Month when Tanggal Surat changes (if user hasn't manually set other)
        const romanMonthsMap = {
            1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI',
            7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X', 11: 'XI', 12: 'XII'
        };

        $('#inputTanggalSurat').on('change', function () {
            const val = $(this).val();
            if (val) {
                const parts = val.split('-');
                if (parts.length === 3) {
                    const monthNum = parseInt(parts[1], 10);
                    const yearNum  = parseInt(parts[0], 10);
                    if (romanMonthsMap[monthNum]) {
                        $('#inputBulanRomawi').val(romanMonthsMap[monthNum]);
                    }
                    $('#inputTahunNomor').val(yearNum);
                }
            }
            updateLivePreview();
        });

        let previewAjaxTimer = null;
        function updateLivePreview() {
            clearTimeout(previewAjaxTimer);
            previewAjaxTimer = setTimeout(function () {
                const instansi    = $('#inputInstansi').val() || '...';
                const kodeSurat   = $('#inputKodeSurat').val() || '...';
                const bulanRomawi = $('#inputBulanRomawi').val() || '...';
                const tahunNomor  = $('#inputTahunNomor').val() || '...';
                const tanggal     = $('#inputTanggalSurat').val() || '<?= date('Y-m-d') ?>';

                $.ajax({
                    url: '<?= base_url('surat/preview-ajax') ?>',
                    type: 'POST',
                    data: {
                        instansi: instansi,
                        kode_surat: kodeSurat,
                        bulan_romawi: bulanRomawi,
                        tahun_nomor: tahunNomor,
                        tanggal_surat: tanggal,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function (res) {
                        $('#livePreviewText').text(res.nomor_surat);
                        $('#previewNomorUrut').html('Estimasi Nomor Urut: <strong>' + res.nomor_urut + '</strong>');

                        if (res.is_backdate == 1) {
                            $('#backdateBadge').removeClass('d-none');
                            $('#previewNote').html('<i class="fa-solid fa-info-circle me-1 text-warning"></i> ' + res.message);
                        } else {
                            $('#backdateBadge').addClass('d-none');
                            $('#previewNote').html('<i class="fa-solid fa-lock me-1"></i> Nomor urut resmi akan dikunci dan diberikan saat data disimpan.');
                        }
                    },
                    error: function () {
                        // Fallback client-side preview
                        const fallbackText = `---/${instansi}/${kodeSurat}/${bulanRomawi}/${tahunNomor}`;
                        $('#livePreviewText').text(fallbackText);
                    }
                });
            }, 250);
        }

        // Initial preview call
        updateLivePreview();
    });
</script>
<?= $this->endSection() ?>
