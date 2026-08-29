<?php

namespace App\Controllers;

use App\Models\KodeSuratModel;
use App\Models\SettingModel;
use App\Models\SuratModel;
use App\Models\UserModel;
use Config\Database;

class Reports extends BaseController
{
    protected SuratModel $suratModel;
    protected UserModel $userModel;
    protected KodeSuratModel $kodeSuratModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->suratModel     = new SuratModel();
        $this->userModel      = new UserModel();
        $this->kodeSuratModel = new KodeSuratModel();
        $this->settingModel   = new SettingModel();
    }

    private function buildReportQuery()
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $filterTahun    = $this->request->getGet('tahun');
        $filterBulan    = $this->request->getGet('bulan');
        $filterStatus   = $this->request->getGet('status');
        $filterPegawai  = $this->request->getGet('pegawai');
        $filterInstansi = $this->request->getGet('instansi');
        $filterKode     = $this->request->getGet('kode_surat');
        $startDate      = $this->request->getGet('start_date');
        $endDate        = $this->request->getGet('end_date');

        $builder = $this->suratModel->orderBy('tanggal_surat', 'ASC')->orderBy('id', 'ASC');

        if ($role === 'pegawai') {
            $builder->where('pembuat_id', $userId);
        } elseif (!empty($filterPegawai)) {
            $builder->where('pembuat_id', $filterPegawai);
        }

        if (!empty($filterTahun)) {
            $builder->where('tahun_nomor', $filterTahun);
        }
        if (!empty($filterBulan)) {
            $builder->where('bulan_romawi', $filterBulan);
        }
        if (!empty($filterStatus)) {
            $builder->where('status', $filterStatus);
        }
        if (!empty($filterInstansi)) {
            $builder->like('instansi', $filterInstansi);
        }
        if (!empty($filterKode)) {
            $builder->where('kode_surat', $filterKode);
        }
        if (!empty($startDate)) {
            $builder->where('tanggal_surat >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('tanggal_surat <=', $endDate);
        }

        return $builder;
    }

    public function index()
    {
        $builder   = $this->buildReportQuery();
        $suratList = $builder->findAll();

        $db = Database::connect();
        $distinctTahun = $db->table('surat')->select('tahun_nomor')->distinct()->orderBy('tahun_nomor', 'DESC')->get()->getResultArray();
        $distinctKode  = $this->kodeSuratModel->orderBy('kode', 'ASC')->findAll();
        $pegawaiList   = $this->userModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        // Calculate statistics for report
        $totalSurat      = count($suratList);
        $totalSelesai    = count(array_filter($suratList, fn($s) => $s['status'] === 'Selesai'));
        $totalUpload     = count(array_filter($suratList, fn($s) => $s['status'] === 'File Sudah Upload'));
        $totalDibatalkan = count(array_filter($suratList, fn($s) => $s['status'] === 'Dibatalkan'));

        return view('reports/index', [
            'title'           => 'Laporan Rekapitulasi Nomor Surat',
            'suratList'       => $suratList,
            'distinctTahun'   => array_column($distinctTahun, 'tahun_nomor'),
            'distinctKode'    => $distinctKode,
            'pegawaiList'     => $pegawaiList,
            'totalSurat'      => $totalSurat,
            'totalSelesai'    => $totalSelesai,
            'totalUpload'     => $totalUpload,
            'totalDibatalkan' => $totalDibatalkan,
            'filters'         => [
                'tahun'      => $this->request->getGet('tahun'),
                'bulan'      => $this->request->getGet('bulan'),
                'status'     => $this->request->getGet('status'),
                'pegawai'    => $this->request->getGet('pegawai'),
                'instansi'   => $this->request->getGet('instansi'),
                'kode_surat' => $this->request->getGet('kode_surat'),
                'start_date' => $this->request->getGet('start_date'),
                'end_date'   => $this->request->getGet('end_date'),
            ],
            'settings'        => $this->settingModel->getSettings(),
        ]);
    }

    public function exportExcel()
    {
        $builder   = $this->buildReportQuery();
        $suratList = $builder->findAll();
        $settings  = $this->settingModel->getSettings();

        $filename = 'Laporan_Nomor_Surat_' . date('Ymd_His') . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        return view('reports/export_excel', [
            'suratList' => $suratList,
            'settings'  => $settings,
            'filters'   => $this->request->getGet(),
        ]);
    }

    public function exportPdf()
    {
        $builder   = $this->buildReportQuery();
        $suratList = $builder->findAll();
        $settings  = $this->settingModel->getSettings();

        return view('reports/export_pdf', [
            'suratList' => $suratList,
            'settings'  => $settings,
            'filters'   => $this->request->getGet(),
        ]);
    }
}
