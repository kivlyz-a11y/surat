<?php

namespace App\Controllers;

use App\Models\SuratLogModel;
use App\Models\UserModel;
use Config\Database;

class ActivityLogs extends BaseController
{
    protected SuratLogModel $logModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->logModel  = new SuratLogModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $filterUser = $this->request->getGet('user_id');
        $filterDate = $this->request->getGet('date');
        $filterType = $this->request->getGet('type');

        $db = Database::connect();
        $builder = $db->table('surat_logs')
            ->select('surat_logs.*, users.name as user_name, users.username, users.role, surat.nomor_surat')
            ->join('users', 'users.id = surat_logs.user_id', 'left')
            ->join('surat', 'surat.id = surat_logs.surat_id', 'left')
            ->orderBy('surat_logs.id', 'DESC');

        if (!empty($filterUser)) {
            $builder->where('surat_logs.user_id', $filterUser);
        }
        if (!empty($filterDate)) {
            $builder->where("DATE(surat_logs.created_at)", $filterDate);
        }
        if (!empty($filterType)) {
            $builder->where('surat_logs.aktivitas', $filterType);
        }

        $logs  = $builder->limit(500)->get()->getResultArray();
        $users = $this->userModel->orderBy('name', 'ASC')->findAll();

        $distinctAktivitas = $db->table('surat_logs')->select('aktivitas')->distinct()->get()->getResultArray();

        return view('logs/index', [
            'title'             => 'Log Aktivitas Sistem & Persuratan',
            'logs'              => $logs,
            'users'             => $users,
            'distinctAktivitas' => array_column($distinctAktivitas, 'aktivitas'),
            'filters'           => [
                'user_id' => $filterUser,
                'date'    => $filterDate,
                'type'    => $filterType,
            ],
        ]);
    }
}
