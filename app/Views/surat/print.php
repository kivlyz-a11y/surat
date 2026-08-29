<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Bukti Nomor Surat - <?= esc($surat['nomor_surat']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 4px 0 0 0;
            font-size: 15px;
            font-weight: normal;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 12px;
            font-style: italic;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .box-nomor {
            border: 2px solid #000;
            padding: 10px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.detail-table td {
            padding: 6px 8px;
            vertical-align: top;
        }
        table.detail-table td.label {
            width: 25%;
            font-weight: bold;
        }
        table.detail-table td.colon {
            width: 3%;
            text-align: center;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-space {
            height: 70px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; padding: 10px; background: #e2e8f0; border-radius: 6px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; background: #0d7a53; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak Lembar Ini (Print)
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; margin-left: 8px; background: #64748b; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <!-- Kop Surat -->
    <div class="header">
        <h2><?= esc($settings['instansi_default'] ?? 'INSTANSI PEMERINTAH') ?></h2>
        <h3>BUKU REGISTER DAN TANDA BUKTI PENGAMBILAN NOMOR SURAT KELUAR</h3>
        <p>Dicetak otomatis melalui <?= esc($settings['nama_aplikasi'] ?? 'Sistem Manajemen Nomor Surat') ?> pada <?= date('d/m/Y H:i') ?> WIB</p>
    </div>

    <div class="title">TANDA BUKTI REGISTER NOMOR SURAT</div>

    <div class="box-nomor">
        NOMOR: <?= esc($surat['nomor_surat']) ?>
    </div>

    <table class="detail-table">
        <tr>
            <td class="label">Nomor Urut</td>
            <td class="colon">:</td>
            <td><strong><?= esc($surat['nomor_urut']) ?></strong></td>
        </tr>
        <tr>
            <td class="label">Tanggal Surat</td>
            <td class="colon">:</td>
            <td><?= date('d F Y', strtotime($surat['tanggal_surat'])) ?></td>
        </tr>
        <tr>
            <td class="label">Tujuan Surat</td>
            <td class="colon">:</td>
            <td><strong><?= esc($surat['tujuan']) ?></strong></td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="colon">:</td>
            <td><?= esc($surat['perihal']) ?></td>
        </tr>
        <tr>
            <td class="label">Kode Klasifikasi</td>
            <td class="colon">:</td>
            <td><?= esc($surat['kode_surat']) ?></td>
        </tr>
        <tr>
            <td class="label">Unit Kerja</td>
            <td class="colon">:</td>
            <td><?= esc($surat['unit_kerja'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Pembuat Surat</td>
            <td class="colon">:</td>
            <td><?= esc($surat['nama_pembuat']) ?> (<?= esc($surat['jabatan'] ?? '-') ?>)</td>
        </tr>
        <tr>
            <td class="label">Waktu Register</td>
            <td class="colon">:</td>
            <td><?= date('d/m/Y H:i:s', strtotime($surat['created_at'])) ?> WIB</td>
        </tr>
        <tr>
            <td class="label">Status Surat</td>
            <td class="colon">:</td>
            <td><?= esc($surat['status']) ?></td>
        </tr>
        <?php if (!empty($surat['keterangan'])): ?>
        <tr>
            <td class="label">Keterangan</td>
            <td class="colon">:</td>
            <td><?= nl2br(esc($surat['keterangan'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="signature-section" style="width: 100%; display: table;">
        <div style="display: table-cell; width: 50%; text-align: center;">
            <p>Petugas Register Persuratan,</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">( .................................................. )</p>
            <p style="margin: 0; font-size: 12px;">NIP. ..........................................</p>
        </div>
        <div style="display: table-cell; width: 50%; text-align: center;">
            <p>Pengambil Nomor / Pembuat Surat,</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;"><?= esc($surat['nama_pembuat']) ?></p>
            <p style="margin: 0; font-size: 12px;"><?= esc($surat['jabatan'] ?? '') ?></p>
        </div>
    </div>

</body>
</html>
