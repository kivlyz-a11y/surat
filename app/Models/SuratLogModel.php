<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratLogModel extends Model
{
    protected $table            = 'surat_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'surat_id',
        'user_id',
        'aktivitas',
        'keterangan',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Record an activity log
     */
    public function logActivity(?int $suratId, ?int $userId, string $aktivitas, string $keterangan)
    {
        return $this->insert([
            'surat_id'   => $suratId,
            'user_id'    => $userId,
            'aktivitas'  => $aktivitas,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get recent logs with user details
     */
    public function getRecentLogs(int $limit = 10, ?int $userId = null)
    {
        $builder = $this->db->table('surat_logs')
            ->select('surat_logs.*, users.name as user_name, users.username, users.role')
            ->join('users', 'users.id = surat_logs.user_id', 'left')
            ->orderBy('surat_logs.id', 'DESC')
            ->limit($limit);

        if ($userId !== null) {
            $builder->where('surat_logs.user_id', $userId);
        }

        return $builder->get()->getResultArray();
    }
}
