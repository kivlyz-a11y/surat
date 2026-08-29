<?php

namespace App\Models;

use CodeIgniter\Model;

class NomorSuratCounterModel extends Model
{
    protected $table            = 'nomor_surat_counters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'tahun_counter',
        'nomor_terakhir',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
