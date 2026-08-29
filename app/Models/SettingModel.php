<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'nomor_surat_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'nama_aplikasi',
        'instansi_default',
        'format_tampilan',
        'batas_upload_mb',
        'ekstensi_file',
        'padding_digit',
        'mode_counter',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active settings (first row)
     */
    public function getSettings()
    {
        $setting = $this->first();
        if (!$setting) {
            return [
                'nama_aplikasi'    => 'Sistem Manajemen Nomor Surat',
                'instansi_default' => 'PTA.KU',
                'format_tampilan'  => '{nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}',
                'batas_upload_mb'  => 10,
                'ekstensi_file'    => 'pdf,doc,docx',
                'padding_digit'    => 3,
                'mode_counter'     => 'global',
            ];
        }
        return $setting;
    }
}
