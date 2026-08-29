<?php

namespace App\Models;

use CodeIgniter\Model;

class KodeSuratModel extends Model
{
    protected $table            = 'kode_surat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'kode',
        'nama_klasifikasi',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kode'             => 'required|min_length[1]|max_length[50]|is_unique[kode_surat.kode,id,{id}]',
        'nama_klasifikasi' => 'required|min_length[2]|max_length[255]',
    ];

    protected $validationMessages = [
        'kode' => [
            'is_unique' => 'Kode surat sudah terdaftar dalam sistem.',
        ],
    ];
}
