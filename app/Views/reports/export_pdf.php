<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Nomor Surat Keluar</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h3 {
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: top;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            font-size: 10.5px;
        }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .no-print {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; background: #0d7a53; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; margin-left: 8px; background: #64748b; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h3><?= esc($settings['instansi_default'] ?? 'INSTANSI PEMERINTAH') ?></h3>
        <p><strong>BUKU REGISTER LAPORAN SURAT KELUAR</strong></p>
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB | Total Data: <?= count($suratList) ?> Surat</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 45px;">No. Urut</th>
                <th>Nomor Surat</th>
                <th style="width: 65px;">Tanggal</th>
                <th>Perihal</th>
                <th>Tujuan</th>
                <th>Pembuat</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($suratList)): ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px;">Tidak ada data surat.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($suratList as $i => $s): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td class="text-center font-mono"><?= esc($s['nomor_urut']) ?></td>
                        <td class="font-mono" style="font-weight: bold;"><?= esc($s['nomor_surat']) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($s['tanggal_surat'])) ?></td>
                        <td><?= esc($s['perihal']) ?></td>
                        <td><?= esc($s['tujuan']) ?></td>
                        <td><?= esc($s['nama_pembuat']) ?></td>
                        <td class="text-center"><?= esc($s['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
