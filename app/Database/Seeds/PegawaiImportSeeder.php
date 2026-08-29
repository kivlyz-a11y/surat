<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PegawaiImportSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $userTable = $db->table('users');

        $jsonFile = ROOTPATH . 'scratch_pegawai.json';
        if (!file_exists($jsonFile)) {
            echo "File scratch_pegawai.json tidak ditemukan.\n";
            return;
        }

        $pegawaiList = json_decode(file_get_contents($jsonFile), true);
        $defaultPasswordHash = password_hash('123456', PASSWORD_DEFAULT);
        $inserted = 0;
        $updated  = 0;

        foreach ($pegawaiList as $p) {
            $existing = $userTable->where('username', $p['username'])->get()->getRowArray();

            $data = [
                'name'       => $p['name'],
                'username'   => $p['username'],
                'email'      => $p['email'],
                'password'   => $defaultPasswordHash,
                'role'       => 'pegawai',
                'unit_kerja' => $p['unit_kerja'],
                'jabatan'    => $p['jabatan'],
                'is_active'  => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $userTable->where('id', $existing['id'])->update($data);
                $updated++;
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $userTable->insert($data);
                $inserted++;
            }
        }

        echo "Selesai mengimpor pegawai dari daftar_pegawai (7).xls:\n";
        echo "- Ditambahkan: {$inserted} pegawai baru\n";
        echo "- Diperbarui: {$updated} pegawai\n";
        echo "- Total pegawai: " . count($pegawaiList) . "\n";
        echo "- Password default: 123456\n";
        echo "- Role: pegawai\n";
    }
}
