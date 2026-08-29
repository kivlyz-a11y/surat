<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratModel extends Model
{
    protected $table            = 'surat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'nomor_urut',
        'nomor_surat',
        'instansi',
        'kode_surat',
        'bulan_romawi',
        'tahun_nomor',
        'tanggal_surat',
        'perihal',
        'tujuan',
        'unit_kerja',
        'pembuat_id',
        'nama_pembuat',
        'jabatan',
        'keterangan',
        'nama_file',
        'file_path',
        'status',
        'is_backdate',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'instansi'      => 'required|min_length[1]|max_length[100]',
        'kode_surat'    => 'required|min_length[1]|max_length[50]',
        'bulan_romawi'  => 'required|in_list[I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII]',
        'tahun_nomor'   => 'required|exact_length[4]|numeric',
        'tanggal_surat' => 'required|valid_date',
        'perihal'       => 'required|min_length[3]',
        'tujuan'        => 'required|min_length[2]|max_length[255]',
        'nama_pembuat'  => 'required|min_length[2]|max_length[150]',
    ];

    protected $validationMessages = [
        'bulan_romawi' => [
            'in_list' => 'Bulan Romawi hanya boleh bernilai I sampai XII.',
        ],
        'tahun_nomor' => [
            'exact_length' => 'Tahun harus 4 digit angka.',
            'numeric'      => 'Tahun harus berupa angka.',
        ],
    ];

    /**
     * Get summary statistics for dashboard
     */
    public function getDashboardStats(?int $userId = null)
    {
        $currentYear  = date('Y');
        $currentMonth = date('m');
        $today        = date('Y-m-d');

        $builder = $this->builder();
        if ($userId !== null) {
            $builder->where('pembuat_id', $userId);
        }

        // Total Tahun Berjalan
        $b1 = clone $builder;
        $totalTahun = $b1->where('tahun_nomor', $currentYear)->countAllResults(false);

        // Total Bulan Ini
        $b2 = clone $builder;
        $totalBulan = $b2->where("DATE_FORMAT(tanggal_surat, '%Y-%m')", date('Y-m'))->countAllResults(false);

        // Total Hari Ini
        $b3 = clone $builder;
        $totalHariIni = $b3->where('tanggal_surat', $today)->countAllResults(false);

        // Total Keseluruhan
        $b4 = clone $builder;
        $totalSemua = $b4->countAllResults(false);

        return [
            'total_tahun'    => $totalTahun,
            'total_bulan'    => $totalBulan,
            'total_hari_ini' => $totalHariIni,
            'total_semua'    => $totalSemua,
        ];
    }
}
