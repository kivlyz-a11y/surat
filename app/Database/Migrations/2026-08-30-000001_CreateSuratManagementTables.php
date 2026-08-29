<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSuratManagementTables extends Migration
{
    public function up()
    {
        // 1. Table Users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'pegawai'],
                'default'    => 'pegawai',
            ],
            'unit_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // 2. Table nomor_surat_counters
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun_counter' => [
                'type'       => 'INT',
                'constraint' => 4,
                'default'    => 0, // 0 for global counter
            ],
            'nomor_terakhir' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('tahun_counter');
        $this->forge->createTable('nomor_surat_counters', true);

        // 3. Table nomor_surat_settings
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_aplikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
                'default'    => 'Sistem Manajemen Nomor Surat',
            ],
            'instansi_default' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'PTA.KU',
            ],
            'format_tampilan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'default'    => '{nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}',
            ],
            'batas_upload_mb' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 10,
            ],
            'ekstensi_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'pdf,doc,docx',
            ],
            'padding_digit' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 3,
            ],
            'mode_counter' => [
                'type'       => 'ENUM',
                'constraint' => ['global', 'per_tahun'],
                'default'    => 'global',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('nomor_surat_settings', true);

        // 4. Table kode_surat (Master Helper)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'nama_klasifikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kode_surat', true);

        // 5. Table surat
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_urut' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'nomor_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'instansi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'kode_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'bulan_romawi' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'tahun_nomor' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'tanggal_surat' => [
                'type' => 'DATE',
            ],
            'perihal' => [
                'type' => 'TEXT',
            ],
            'tujuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'unit_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'pembuat_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nama_pembuat' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nama_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Draft', 'Nomor Diambil', 'File Sudah Upload', 'Selesai', 'Dibatalkan'],
                'default'    => 'Nomor Diambil',
            ],
            'is_backdate' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tanggal_surat');
        $this->forge->addKey('tahun_nomor');
        $this->forge->addKey('pembuat_id');
        $this->forge->addKey('status');
        $this->forge->createTable('surat', true);

        // 6. Table surat_logs
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'surat_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'aktivitas' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('surat_id');
        $this->forge->addKey('user_id');
        $this->forge->createTable('surat_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('surat_logs', true);
        $this->forge->dropTable('surat', true);
        $this->forge->dropTable('kode_surat', true);
        $this->forge->dropTable('nomor_surat_settings', true);
        $this->forge->dropTable('nomor_surat_counters', true);
        $this->forge->dropTable('users', true);
    }
}
