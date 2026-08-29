<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Nomor Surat Keluar</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        th {
            background-color: #0d7a53;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .title-header {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }
        .sub-header {
            font-size: 12px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="title-header">LAPORAN REKAPITULASI BUKU REGISTER NOMOR SURAT KELUAR</div>
    <div class="sub-header">Instansi: <?= esc($settings['instansi_default'] ?? 'PTA.KU') ?> | Tanggal Unduh: <?= date('d/m/Y H:i') ?> WIB</div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 80px;">No. Urut</th>
                <th>Nomor Surat Lengkap</th>
                <th>Instansi</th>
                <th>Kode Surat</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Tanggal Surat</th>
                <th>Perihal</th>
                <th>Tujuan Surat</th>
                <th>Unit Kerja</th>
                <th>Nama Pembuat</th>
                <th>Jabatan</th>
                <th>Status</th>
                <th>Waktu Ambil Nomor</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($suratList)): ?>
                <tr>
                    <td colspan="15" style="text-align: center; padding: 20px;">Tidak ada data surat yang sesuai kriteria.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($suratList as $i => $s): ?>
                    <tr>
                        <td style="text-align: center;"><?= $i + 1 ?></td>
                        <td style="text-align: center; font-family: monospace;">'<?= esc($s['nomor_urut']) ?></td>
                        <td style="font-family: monospace; font-weight: bold;"><?= esc($s['nomor_surat']) ?></td>
                        <td><?= esc($s['instansi']) ?></td>
                        <td><?= esc($s['kode_surat']) ?></td>
                        <td style="text-align: center;"><?= esc($s['bulan_romawi']) ?></td>
                        <td style="text-align: center;"><?= esc($s['tahun_nomor']) ?></td>
                        <td style="text-align: center;"><?= date('d/m/Y', strtotime($s['tanggal_surat'])) ?></td>
                        <td><?= esc($s['perihal']) ?></td>
                        <td><?= esc($s['tujuan']) ?></td>
                        <td><?= esc($s['unit_kerja'] ?? '-') ?></td>
                        <td><?= esc($s['nama_pembuat']) ?></td>
                        <td><?= esc($s['jabatan'] ?? '-') ?></td>
                        <td style="text-align: center;"><?= esc($s['status']) ?></td>
                        <td style="text-align: center;"><?= date('d/m/Y H:i:s', strtotime($s['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
