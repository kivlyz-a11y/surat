<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuratSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Seed Users
        $users = [
            [
                'name'       => 'Administrator Sistem',
                'username'   => 'admin',
                'email'      => 'admin@instansi.go.id',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'unit_kerja' => 'Sub Bagian Tata Usaha & Kearsipan',
                'jabatan'    => 'Administrator Persuratan',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Ahmad Fauzi, S.Kom',
                'username'   => 'pegawai1',
                'email'      => 'ahmad.fauzi@instansi.go.id',
                'password'   => password_hash('pegawai123', PASSWORD_DEFAULT),
                'role'       => 'pegawai',
                'unit_kerja' => 'Bagian Kepegawaian & TI',
                'jabatan'    => 'Analis SDM Aparatur',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Budi Santoso, S.H.',
                'username'   => 'budi',
                'email'      => 'budi.santoso@instansi.go.id',
                'password'   => password_hash('budi123', PASSWORD_DEFAULT),
                'role'       => 'pegawai',
                'unit_kerja' => 'Bagian Umum & Keuangan',
                'jabatan'    => 'Pengelola Keuangan APBN',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Siti Rahmawati, S.Sos',
                'username'   => 'siti',
                'email'      => 'siti.rahmawati@instansi.go.id',
                'password'   => password_hash('siti123', PASSWORD_DEFAULT),
                'role'       => 'pegawai',
                'unit_kerja' => 'Bagian Perencanaan & Hubmas',
                'jabatan'    => 'Pranata Hubungan Masyarakat',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($users);

        // 2. Seed Settings
        $setting = [
            'nama_aplikasi'    => 'Sistem Manajemen Nomor Surat',
            'instansi_default' => 'PTA.KU',
            'format_tampilan'  => '{nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}',
            'batas_upload_mb'  => 10,
            'ekstensi_file'    => 'pdf,doc,docx',
            'padding_digit'    => 3,
            'mode_counter'     => 'global',
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
        $this->db->table('nomor_surat_settings')->insert($setting);

        // 3. Seed Counter
        $counter = [
            'tahun_counter'  => 0, // global
            'nomor_terakhir' => 4,
            'created_at'     => $now,
            'updated_at'     => $now,
        ];
        $this->db->table('nomor_surat_counters')->insert($counter);

        // 4. Seed Master Kode Surat
        $kodeSurat = [
            ['kode' => 'HM2.1.1', 'nama_klasifikasi' => 'Hubungan Masyarakat & Publikasi', 'keterangan' => 'Surat keluar terkait publikasi, rilis pers, peliputan acara'],
            ['kode' => 'KU1.2',   'nama_klasifikasi' => 'Pengelolaan Keuangan & Perbendaharaan', 'keterangan' => 'Surat terkait SP2D, tagihan, dan laporan pertanggungjawaban'],
            ['kode' => 'KP.01',   'nama_klasifikasi' => 'Administrasi Kepegawaian & Mutasi', 'keterangan' => 'Kenaikan pangkat, izin belajar, pengusulan cuti'],
            ['kode' => 'UM.01',   'nama_klasifikasi' => 'Tata Usaha & Kearsipan', 'keterangan' => 'Surat pengantar dinas, peminjaman berkas umum'],
            ['kode' => 'OT.01',   'nama_klasifikasi' => 'Organisasi & Tata Laksana', 'keterangan' => 'Surat tugas, SOP, struktur organisasi internal'],
            ['kode' => 'HK.01',   'nama_klasifikasi' => 'Hukum & Perundang-undangan', 'keterangan' => 'Telaah hukum, penanganan perkara & konsultasi hukum'],
            ['kode' => 'PL.01',   'nama_klasifikasi' => 'Pengadaan Perlengkapan & BMN', 'keterangan' => 'Pengadaan barang jasa dan pemeliharaan aset'],
            ['kode' => 'TI.01',   'nama_klasifikasi' => 'Teknologi Informasi & Komunikasi', 'keterangan' => 'Pemeliharaan jaringan server dan sistem informasi'],
        ];
        foreach ($kodeSurat as &$k) {
            $k['created_at'] = $now;
            $k['updated_at'] = $now;
        }
        $this->db->table('kode_surat')->insertBatch($kodeSurat);

        // 5. Seed Sample Surat
        $currentYear = date('Y');
        $suratData = [
            [
                'nomor_urut'    => '001',
                'nomor_surat'   => "001/PTA.KU/HM2.1.1/I/{$currentYear}",
                'instansi'      => 'PTA.KU',
                'kode_surat'    => 'HM2.1.1',
                'bulan_romawi'  => 'I',
                'tahun_nomor'   => $currentYear,
                'tanggal_surat' => "{$currentYear}-01-15",
                'perihal'       => 'Undangan Liputan Sosialisasi Aplikasi Persuratan',
                'tujuan'        => 'Pimpinan Redaksi Media Cetak & Online',
                'unit_kerja'    => 'Bagian Perencanaan & Hubmas',
                'pembuat_id'    => 4,
                'nama_pembuat'  => 'Siti Rahmawati, S.Sos',
                'jabatan'       => 'Pranata Hubungan Masyarakat',
                'keterangan'    => 'Konfirmasi kehadiran paling lambat 1 hari sebelum acara',
                'status'        => 'Selesai',
                'is_backdate'   => 0,
                'created_at'    => "{$currentYear}-01-15 08:30:00",
                'updated_at'    => "{$currentYear}-01-15 08:30:00",
            ],
            [
                'nomor_urut'    => '002',
                'nomor_surat'   => "002/PTA.KU/KU1.2/II/{$currentYear}",
                'instansi'      => 'PTA.KU',
                'kode_surat'    => 'KU1.2',
                'bulan_romawi'  => 'II',
                'tahun_nomor'   => $currentYear,
                'tanggal_surat' => "{$currentYear}-02-10",
                'perihal'       => 'Permintaan Rekonsiliasi Laporan Keuangan Semesteran',
                'tujuan'        => 'Kepala Kantor Pelayanan Perbendaharaan Negara (KPPN)',
                'unit_kerja'    => 'Bagian Umum & Keuangan',
                'pembuat_id'    => 3,
                'nama_pembuat'  => 'Budi Santoso, S.H.',
                'jabatan'       => 'Pengelola Keuangan APBN',
                'keterangan'    => 'Lampiran data dukung terlampir dalam berkas fisik',
                'status'        => 'Selesai',
                'is_backdate'   => 0,
                'created_at'    => "{$currentYear}-02-10 09:15:00",
                'updated_at'    => "{$currentYear}-02-10 09:15:00",
            ],
            [
                'nomor_urut'    => '003',
                'nomor_surat'   => "003/PTA.KU/KP.01/II/{$currentYear}",
                'instansi'      => 'PTA.KU',
                'kode_surat'    => 'KP.01',
                'bulan_romawi'  => 'II',
                'tahun_nomor'   => $currentYear,
                'tanggal_surat' => "{$currentYear}-02-20",
                'perihal'       => 'Usulan Kenaikan Pangkat Pegawai Periode April',
                'tujuan'        => 'Kepala Badan Kepegawaian Negara (BKN)',
                'unit_kerja'    => 'Bagian Kepegawaian & TI',
                'pembuat_id'    => 2,
                'nama_pembuat'  => 'Ahmad Fauzi, S.Kom',
                'jabatan'       => 'Analis SDM Aparatur',
                'keterangan'    => 'Berkas digital telah diunggah ke portal SIASN',
                'status'        => 'Nomor Diambil',
                'is_backdate'   => 0,
                'created_at'    => "{$currentYear}-02-20 11:00:00",
                'updated_at'    => "{$currentYear}-02-20 11:00:00",
            ],
            [
                'nomor_urut'    => '004',
                'nomor_surat'   => "004/PTA.KU/TI.01/III/{$currentYear}",
                'instansi'      => 'PTA.KU',
                'kode_surat'    => 'TI.01',
                'bulan_romawi'  => 'III',
                'tahun_nomor'   => $currentYear,
                'tanggal_surat' => "{$currentYear}-03-01",
                'perihal'       => 'Pemberitahuan Pemeliharaan Server & Jaringan Internal',
                'tujuan'        => 'Seluruh Pegawai & Pejabat Struktural',
                'unit_kerja'    => 'Bagian Kepegawaian & TI',
                'pembuat_id'    => 2,
                'nama_pembuat'  => 'Ahmad Fauzi, S.Kom',
                'jabatan'       => 'Analis SDM Aparatur',
                'keterangan'    => 'Maintenance dijadwalkan pada hari Sabtu pukul 20.00 WIB',
                'status'        => 'Nomor Diambil',
                'is_backdate'   => 0,
                'created_at'    => "{$currentYear}-03-01 14:20:00",
                'updated_at'    => "{$currentYear}-03-01 14:20:00",
            ],
        ];

        $this->db->table('surat')->insertBatch($suratData);

        // 6. Seed Logs
        $logs = [
            [
                'surat_id'   => 1,
                'user_id'    => 4,
                'aktivitas'  => 'Mengambil Nomor Surat',
                'keterangan' => "Siti Rahmawati, S.Sos mengambil nomor urut 001 dan membuat nomor surat 001/PTA.KU/HM2.1.1/I/{$currentYear}",
                'created_at' => "{$currentYear}-01-15 08:30:00",
            ],
            [
                'surat_id'   => 2,
                'user_id'    => 3,
                'aktivitas'  => 'Mengambil Nomor Surat',
                'keterangan' => "Budi Santoso, S.H. mengambil nomor urut 002 dan membuat nomor surat 002/PTA.KU/KU1.2/II/{$currentYear}",
                'created_at' => "{$currentYear}-02-10 09:15:00",
            ],
            [
                'surat_id'   => 3,
                'user_id'    => 2,
                'aktivitas'  => 'Mengambil Nomor Surat',
                'keterangan' => "Ahmad Fauzi, S.Kom mengambil nomor urut 003 dan membuat nomor surat 003/PTA.KU/KP.01/II/{$currentYear}",
                'created_at' => "{$currentYear}-02-20 11:00:00",
            ],
            [
                'surat_id'   => 4,
                'user_id'    => 2,
                'aktivitas'  => 'Mengambil Nomor Surat',
                'keterangan' => "Ahmad Fauzi, S.Kom mengambil nomor urut 004 dan membuat nomor surat 004/PTA.KU/TI.01/III/{$currentYear}",
                'created_at' => "{$currentYear}-03-01 14:20:00",
            ],
        ];
        $this->db->table('surat_logs')->insertBatch($logs);

        // 7. Seed Pegawai from daftar_pegawai (7).xls
        $this->call('PegawaiImportSeeder');
    }
}
