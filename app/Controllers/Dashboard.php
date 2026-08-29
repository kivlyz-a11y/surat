<?php

namespace App\Controllers;

use App\Models\NomorSuratCounterModel;
use App\Models\SettingModel;
use App\Models\SuratLogModel;
use App\Models\SuratModel;
use App\Models\UserModel;
use Config\Database;

class Dashboard extends BaseController
{
    protected SuratModel $suratModel;
    protected SuratLogModel $logModel;
    protected UserModel $userModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->suratModel   = new SuratModel();
        $this->logModel     = new SuratLogModel();
        $this->userModel    = new UserModel();
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        // General / Role Stats
        $stats = $this->suratModel->getDashboardStats();
        $userStats = ($role === 'pegawai') ? $this->suratModel->getDashboardStats($userId) : null;

        // Latest Nomor Surat Info
        $latestSurat = $this->suratModel->orderBy('id', 'DESC')->first();

        // Recent Logs
        $recentLogs = $this->logModel->getRecentLogs(8, ($role === 'pegawai' ? $userId : null));

        // Surat per Pegawai (Grouped for chart & ranking)
        $db = Database::connect();
        $suratPerPegawai = $db->table('surat')
            ->select('nama_pembuat, unit_kerja, COUNT(id) as total_surat')
            ->where('status !=', 'Dibatalkan')
            ->groupBy('nama_pembuat, unit_kerja')
            ->orderBy('total_surat', 'DESC')
            ->limit(7)
            ->get()
            ->getResultArray();

        // Surat per Bulan (Current Year for monthly trend chart)
        $currentYear = date('Y');
        $monthlySurat = $db->table('surat')
            ->select("MONTH(tanggal_surat) as bulan, COUNT(id) as total")
            ->where('tahun_nomor', $currentYear)
            ->where('status !=', 'Dibatalkan')
            ->groupBy("MONTH(tanggal_surat)")
            ->get()
            ->getResultArray();

        $monthlyData = array_fill(1, 12, 0);
        foreach ($monthlySurat as $m) {
            $monthlyData[(int)$m['bulan']] = (int)$m['total'];
        }

        // Recent Surat List (5 latest)
        $builder = $this->suratModel->orderBy('id', 'DESC')->limit(5);
        if ($role === 'pegawai') {
            $builder->where('pembuat_id', $userId);
        }
        $recentSurat = $builder->findAll();

        return view('dashboard/index', [
            'title'           => 'Dashboard Utama',
            'stats'           => $stats,
            'userStats'       => $userStats,
            'latestSurat'     => $latestSurat,
            'recentLogs'      => $recentLogs,
            'suratPerPegawai' => $suratPerPegawai,
            'monthlyData'     => array_values($monthlyData),
            'recentSurat'     => $recentSurat,
            'settings'        => $this->settingModel->getSettings(),
        ]);
    }
}
