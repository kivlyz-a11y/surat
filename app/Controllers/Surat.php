<?php

namespace App\Controllers;

use App\Models\KodeSuratModel;
use App\Models\SettingModel;
use App\Models\SuratLogModel;
use App\Models\SuratModel;
use App\Models\UserModel;
use App\Services\NomorSuratBuilderService;
use App\Services\NomorUrutService;
use Config\Database;

class Surat extends BaseController
{
    protected SuratModel $suratModel;
    protected SuratLogModel $logModel;
    protected KodeSuratModel $kodeSuratModel;
    protected SettingModel $settingModel;
    protected UserModel $userModel;
    protected NomorUrutService $nomorUrutService;
    protected NomorSuratBuilderService $builderService;

    public function __construct()
    {
        $this->suratModel       = new SuratModel();
        $this->logModel         = new SuratLogModel();
        $this->kodeSuratModel   = new KodeSuratModel();
        $this->settingModel     = new SettingModel();
        $this->userModel        = new UserModel();
        $this->nomorUrutService = new NomorUrutService();
        $this->builderService   = new NomorSuratBuilderService();
    }

    /**
     * Display surat history / list
     */
    public function index()
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $currentMonthRoman = NomorSuratBuilderService::getRomanMonth((int)date('n'));
        $currentYear       = (string)date('Y');

        $isFilterSubmitted = ($this->request->getGet('filter_applied') == '1');

        if (!$isFilterSubmitted) {
            // Default to current month and year
            $filterTahun = $currentYear;
            $filterBulan = $currentMonthRoman;
        } else {
            $filterTahun = $this->request->getGet('tahun');
            $filterBulan = $this->request->getGet('bulan');
        }

        $filterStatus  = $this->request->getGet('status');
        $filterFile    = $this->request->getGet('file_status');
        $filterPegawai = $this->request->getGet('pegawai');
        $filterInstansi= $this->request->getGet('instansi');
        $filterKode    = $this->request->getGet('kode_surat');
        $filterDate    = $this->request->getGet('tanggal_surat');
        $search        = $this->request->getGet('q');

        $builder = $this->suratModel->orderBy('id', 'DESC');

        // Role filtering: Pegawai only sees own letters
        if ($role === 'pegawai') {
            $builder->where('pembuat_id', $userId);
        } elseif (!empty($filterPegawai)) {
            $builder->where('pembuat_id', $filterPegawai);
        }

        if (!empty($filterTahun) && $filterTahun !== 'all') {
            $builder->where('tahun_nomor', $filterTahun);
        }
        if (!empty($filterBulan) && $filterBulan !== 'all') {
            $builder->where('bulan_romawi', $filterBulan);
        }
        if (!empty($filterStatus) && $filterStatus !== 'all') {
            $builder->where('status', $filterStatus);
        }
        if ($filterFile === 'belum_upload') {
            $builder->groupStart()
                ->where('file_path IS NULL')
                ->orWhere('file_path', '')
                ->groupEnd();
        } elseif ($filterFile === 'sudah_upload') {
            $builder->where('file_path IS NOT NULL')->where('file_path !=', '');
        }
        if (!empty($filterInstansi)) {
            $builder->like('instansi', $filterInstansi);
        }
        if (!empty($filterKode) && $filterKode !== 'all') {
            $builder->where('kode_surat', $filterKode);
        }
        if (!empty($filterDate)) {
            $builder->where('tanggal_surat', $filterDate);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('nomor_surat', $search)
                ->orLike('perihal', $search)
                ->orLike('tujuan', $search)
                ->orLike('nama_pembuat', $search)
                ->orLike('instansi', $search)
                ->orLike('kode_surat', $search)
                ->groupEnd();
        }

        $suratList = $builder->findAll();

        // Get filter options
        $db = Database::connect();
        $distinctTahun = $db->table('surat')->select('tahun_nomor')->distinct()->orderBy('tahun_nomor', 'DESC')->get()->getResultArray();
        $distinctKode  = $this->kodeSuratModel->orderBy('kode', 'ASC')->findAll();
        $pegawaiList   = $this->userModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        // Count pending upload total for user/all
        $pendingCountBuilder = $this->suratModel->where('status !=', 'Dibatalkan')
            ->groupStart()
            ->where('file_path IS NULL')
            ->orWhere('file_path', '')
            ->groupEnd();
        if ($role === 'pegawai') {
            $pendingCountBuilder->where('pembuat_id', $userId);
        }
        $totalPendingUpload = $pendingCountBuilder->countAllResults(false);

        $yearsList = array_unique(array_merge([$currentYear], array_column($distinctTahun, 'tahun_nomor')));
        rsort($yearsList);

        $isViewingCurrentMonth = ($filterTahun == $currentYear && $filterBulan === $currentMonthRoman && empty($filterFile));

        return view('surat/index', [
            'title'                 => 'Daftar & Riwayat Nomor Surat',
            'suratList'             => $suratList,
            'distinctTahun'         => $yearsList,
            'distinctKode'          => $distinctKode,
            'pegawaiList'           => $pegawaiList,
            'currentMonthRoman'     => $currentMonthRoman,
            'currentYear'           => $currentYear,
            'isViewingCurrentMonth' => $isViewingCurrentMonth,
            'totalPendingUpload'    => $totalPendingUpload,
            'filters'               => [
                'tahun'         => $filterTahun,
                'bulan'         => $filterBulan,
                'status'        => $filterStatus,
                'file_status'   => $filterFile,
                'pegawai'       => $filterPegawai,
                'instansi'      => $filterInstansi,
                'kode_surat'    => $filterKode,
                'tanggal_surat' => $filterDate,
                'q'             => $search,
            ],
        ]);
    }

    /**
     * Form to create / request new surat number
     */
    public function create()
    {
        $session  = session();
        $settings = $this->settingModel->getSettings();
        $currentMonthNumber = (int)date('n');
        $currentRomanMonth  = NomorSuratBuilderService::getRomanMonth($currentMonthNumber);

        $kodeList = $this->kodeSuratModel->orderBy('kode', 'ASC')->findAll();

        return view('surat/create', [
            'title'             => 'Buat / Ambil Nomor Surat Keluar',
            'settings'          => $settings,
            'kodeList'          => $kodeList,
            'currentRomanMonth' => $currentRomanMonth,
            'currentYear'       => date('Y'),
            'todayDate'         => date('Y-m-d'),
            'user'              => [
                'name'       => $session->get('name'),
                'unit_kerja' => $session->get('unit_kerja'),
                'jabatan'    => $session->get('jabatan'),
            ],
        ]);
    }

    /**
     * AJAX endpoint for live preview of nomor urut & full nomor surat
     */
    public function previewAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        $tanggalSurat = $this->request->getPost('tanggal_surat') ?: date('Y-m-d');
        $tahunNomor   = (int)($this->request->getPost('tahun_nomor') ?: date('Y'));
        $instansi     = trim((string)$this->request->getPost('instansi')) ?: 'PTA.KU';
        $kodeSurat    = trim((string)$this->request->getPost('kode_surat')) ?: 'HM2.1.1';
        $bulanRomawi  = trim((string)$this->request->getPost('bulan_romawi')) ?: NomorSuratBuilderService::getRomanMonth((int)date('n'));

        $preview = $this->nomorUrutService->previewNextNomorUrut($tanggalSurat, $tahunNomor);

        $fullNumber = $this->builderService->build(
            $preview['nomor_urut'],
            $instansi,
            $kodeSurat,
            $bulanRomawi,
            $tahunNomor
        );

        return $this->response->setJSON([
            'nomor_urut'   => $preview['nomor_urut'],
            'is_backdate'  => $preview['is_backdate'],
            'message'      => $preview['message'],
            'nomor_surat'  => $fullNumber,
        ]);
    }

    /**
     * Store and generate official surat number using DB transaction & locking
     */
    public function store()
    {
        $session  = session();
        $userId   = $session->get('user_id');
        $settings = $this->settingModel->getSettings();

        // 1. Validate Form Inputs
        $rules = [
            'instansi'      => 'required|min_length[1]|max_length[100]',
            'kode_surat'    => 'required|min_length[1]|max_length[50]',
            'bulan_romawi'  => 'required|in_list[I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII]',
            'tahun_nomor'   => 'required|exact_length[4]|numeric',
            'tanggal_surat' => 'required|valid_date',
            'perihal'       => 'required|min_length[3]',
            'tujuan'        => 'required|min_length[2]|max_length[255]',
            'nama_pembuat'  => 'required|min_length[2]|max_length[150]',
        ];

        // File upload rules if file attached
        $file = $this->request->getFile('file_surat');
        $hasFile = ($file && $file->isValid() && !$file->hasMoved());
        if ($hasFile) {
            $maxMb = (int)($settings['batas_upload_mb'] ?? 10);
            $maxKb = $maxMb * 1024;
            $allowedExts = str_replace(' ', '', $settings['ekstensi_file'] ?? 'pdf,doc,docx');
            $rules['file_surat'] = "uploaded[file_surat]|max_size[file_surat,{$maxKb}]|ext_in[file_surat,{$allowedExts}]";
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggalSurat = $this->request->getPost('tanggal_surat');
        $today        = date('Y-m-d');
        if ($tanggalSurat > $today) {
            return redirect()->back()->withInput()->with('error', "Tanggal surat tidak boleh lebih besar dari hari ini ({$today}).");
        }

        $instansi     = trim($this->request->getPost('instansi'));
        $kodeSurat    = trim($this->request->getPost('kode_surat'));
        $bulanRomawi  = strtoupper(trim($this->request->getPost('bulan_romawi')));
        $tahunNomor   = (int)$this->request->getPost('tahun_nomor');
        $perihal      = trim($this->request->getPost('perihal'));
        $tujuan       = trim($this->request->getPost('tujuan'));
        $unitKerja    = trim($this->request->getPost('unit_kerja'));
        $namaPembuat  = trim($this->request->getPost('nama_pembuat'));
        $jabatan      = trim($this->request->getPost('jabatan'));
        $keterangan   = trim($this->request->getPost('keterangan'));

        $db = Database::connect();
        $db->transBegin();

        try {
            // 2. Safely Generate Nomor Urut with Database Locking
            $nomorUrutResult = $this->nomorUrutService->generateNomorUrut($tanggalSurat, $tahunNomor);
            $nomorUrut       = $nomorUrutResult['nomor_urut'];
            $isBackdate      = $nomorUrutResult['is_backdate'];

            // 3. Build Full Nomor Surat
            $nomorSuratLengkap = $this->builderService->build(
                $nomorUrut,
                $instansi,
                $kodeSurat,
                $bulanRomawi,
                $tahunNomor,
                $settings['format_tampilan'] ?? '{nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}'
            );

            // 4. Verify Unique Constraint
            $existingSurat = $db->table('surat')
                ->where('nomor_surat', $nomorSuratLengkap)
                ->get()
                ->getFirstRow('array');

            if ($existingSurat) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', "Nomor surat {$nomorSuratLengkap} sudah ada dalam database. Terjadi konflik nomor.");
            }

            // 5. Handle File Upload
            $fileNameOriginal = null;
            $filePathRelative = null;
            $status = 'Nomor Diambil';

            if ($hasFile) {
                $yearFolder = (string)$tahunNomor;
                $targetDir  = WRITEPATH . 'uploads/surat/' . $yearFolder;
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $fileNameOriginal = $file->getClientName();
                $newFileName      = $file->getRandomName();
                $file->move($targetDir, $newFileName);

                $filePathRelative = 'uploads/surat/' . $yearFolder . '/' . $newFileName;
                $status = 'File Sudah Upload';
            }

            // 6. Insert Surat Data
            $insertData = [
                'nomor_urut'    => $nomorUrut,
                'nomor_surat'   => $nomorSuratLengkap,
                'instansi'      => $instansi,
                'kode_surat'    => $kodeSurat,
                'bulan_romawi'  => $bulanRomawi,
                'tahun_nomor'   => $tahunNomor,
                'tanggal_surat' => $tanggalSurat,
                'perihal'       => $perihal,
                'tujuan'        => $tujuan,
                'unit_kerja'    => $unitKerja,
                'pembuat_id'    => $userId,
                'nama_pembuat'  => $namaPembuat,
                'jabatan'       => $jabatan,
                'keterangan'    => $keterangan,
                'nama_file'     => $fileNameOriginal,
                'file_path'     => $filePathRelative,
                'status'        => $status,
                'is_backdate'   => $isBackdate,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];

            $db->table('surat')->insert($insertData);
            $suratId = $db->insertID();

            // 7. Log Activity
            $backdateNote = $isBackdate ? " [Tanggal Mundur: {$tanggalSurat}]" : "";
            $logKeterangan = "{$namaPembuat} mengambil nomor urut {$nomorUrut} dan membuat nomor surat {$nomorSuratLengkap}{$backdateNote}";

            $this->logModel->logActivity(
                $suratId,
                $userId,
                'Membuat Nomor Surat',
                $logKeterangan
            );

            // 8. Commit Transaction
            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Gagal memproses transaksi nomor surat. Silakan coba kembali.');
            }

            $db->transCommit();

            $session->setFlashdata('success', "Nomor surat berhasil diterbitkan: <strong>{$nomorSuratLengkap}</strong> (Nomor Urut: {$nomorUrut})");
            return redirect()->to(base_url('surat/show/' . $suratId));

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * View detail of a surat
     */
    public function show(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('surat'))->with('error', 'Data surat tidak ditemukan.');
        }

        // Permission check
        if ($role === 'pegawai' && $surat['pembuat_id'] != $userId) {
            return redirect()->to(base_url('surat'))->with('error', 'Anda hanya berhak melihat surat yang Anda buat sendiri.');
        }

        // Fetch logs for this surat
        $logs = $this->logModel->where('surat_id', $id)->orderBy('id', 'DESC')->findAll();

        return view('surat/show', [
            'title'    => 'Detail Nomor Surat: ' . $surat['nomor_surat'],
            'surat'    => $surat,
            'logs'     => $logs,
            'settings' => $this->settingModel->getSettings(),
        ]);
    }

    /**
     * Form to edit surat data
     */
    public function edit(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('surat'))->with('error', 'Data surat tidak ditemukan.');
        }

        if ($surat['status'] === 'Dibatalkan') {
            return redirect()->to(base_url('surat/show/' . $id))->with('error', 'Nomor surat yang sudah dibatalkan tidak dapat diedit.');
        }

        if ($role === 'pegawai' && $surat['pembuat_id'] != $userId) {
            return redirect()->to(base_url('surat'))->with('error', 'Anda hanya berhak mengedit data surat milik Anda sendiri.');
        }

        $kodeList = $this->kodeSuratModel->orderBy('kode', 'ASC')->findAll();

        return view('surat/edit', [
            'title'    => 'Edit Data Surat: ' . $surat['nomor_surat'],
            'surat'    => $surat,
            'kodeList' => $kodeList,
            'settings' => $this->settingModel->getSettings(),
        ]);
    }

    /**
     * Process update of surat data
     */
    public function update(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('surat'))->with('error', 'Data surat tidak ditemukan.');
        }

        if ($surat['status'] === 'Dibatalkan') {
            return redirect()->to(base_url('surat/show/' . $id))->with('error', 'Surat yang sudah dibatalkan tidak dapat diubah.');
        }

        if ($role === 'pegawai' && $surat['pembuat_id'] != $userId) {
            return redirect()->to(base_url('surat'))->with('error', 'Akses ditolak.');
        }

        $rules = [
            'instansi'      => 'required|min_length[1]|max_length[100]',
            'kode_surat'    => 'required|min_length[1]|max_length[50]',
            'bulan_romawi'  => 'required|in_list[I,II,III,IV,V,VI,VII,VIII,IX,X,XI,XII]',
            'tahun_nomor'   => 'required|exact_length[4]|numeric',
            'tanggal_surat' => 'required|valid_date',
            'perihal'       => 'required|min_length[3]',
            'tujuan'        => 'required|min_length[2]|max_length[255]',
            'nama_pembuat'  => 'required|min_length[2]|max_length[150]',
        ];

        if ($role === 'admin') {
            $rules['status'] = 'required|in_list[Draft,Nomor Diambil,File Sudah Upload,Selesai,Dibatalkan]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $instansi     = trim($this->request->getPost('instansi'));
        $kodeSurat    = trim($this->request->getPost('kode_surat'));
        $bulanRomawi  = strtoupper(trim($this->request->getPost('bulan_romawi')));
        $tahunNomor   = (int)$this->request->getPost('tahun_nomor');
        $perihal      = trim($this->request->getPost('perihal'));
        $tujuan       = trim($this->request->getPost('tujuan'));
        $unitKerja    = trim($this->request->getPost('unit_kerja'));
        $namaPembuat  = trim($this->request->getPost('nama_pembuat'));
        $jabatan      = trim($this->request->getPost('jabatan'));
        $keterangan   = trim($this->request->getPost('keterangan'));

        $settings = $this->settingModel->getSettings();

        // Re-compose nomor surat using existing locked nomor_urut
        $nomorSuratBaru = $this->builderService->build(
            $surat['nomor_urut'],
            $instansi,
            $kodeSurat,
            $bulanRomawi,
            $tahunNomor,
            $settings['format_tampilan'] ?? '{nomor_urut}/{instansi}/{kode_surat}/{bulan_romawi}/{tahun}'
        );

        // Check unique if changed
        if ($nomorSuratBaru !== $surat['nomor_surat']) {
            $existing = $this->suratModel->where('nomor_surat', $nomorSuratBaru)->where('id !=', $id)->first();
            if ($existing) {
                return redirect()->back()->withInput()->with('error', "Nomor surat {$nomorSuratBaru} sudah ada di data lain.");
            }
        }

        $updateData = [
            'nomor_surat'   => $nomorSuratBaru,
            'instansi'      => $instansi,
            'kode_surat'    => $kodeSurat,
            'bulan_romawi'  => $bulanRomawi,
            'tahun_nomor'   => $tahunNomor,
            'tanggal_surat' => $this->request->getPost('tanggal_surat'),
            'perihal'       => $perihal,
            'tujuan'        => $tujuan,
            'unit_kerja'    => $unitKerja,
            'nama_pembuat'  => $namaPembuat,
            'jabatan'       => $jabatan,
            'keterangan'    => $keterangan,
        ];

        if ($role === 'admin' && $this->request->getPost('status')) {
            $updateData['status'] = $this->request->getPost('status');
        }

        $this->suratModel->update($id, $updateData);

        $this->logModel->logActivity(
            $id,
            $userId,
            'Mengedit Data Surat',
            "{$session->get('name')} memperbarui rincian surat {$nomorSuratBaru}."
        );

        return redirect()->to(base_url('surat/show/' . $id))->with('success', 'Data surat berhasil diperbarui.');
    }

    /**
     * Upload or replace document file
     */
    public function uploadFile(int $id)
    {
        $session  = session();
        $userId   = $session->get('user_id');
        $role     = $session->get('role');
        $settings = $this->settingModel->getSettings();

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('surat'))->with('error', 'Data surat tidak ditemukan.');
        }

        if ($role === 'pegawai' && $surat['pembuat_id'] != $userId) {
            return redirect()->to(base_url('surat'))->with('error', 'Akses ditolak.');
        }

        $maxMb = (int)($settings['batas_upload_mb'] ?? 10);
        $maxKb = $maxMb * 1024;
        $allowedExts = str_replace(' ', '', $settings['ekstensi_file'] ?? 'pdf,doc,docx');

        $rules = [
            'file_dokumen' => "uploaded[file_dokumen]|max_size[file_dokumen,{$maxKb}]|ext_in[file_dokumen,{$allowedExts}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'File tidak valid atau ukuran melebihi batas maksimal (' . $maxMb . ' MB). Format yang diizinkan: ' . $allowedExts);
        }

        $file = $this->request->getFile('file_dokumen');
        if ($file->isValid() && !$file->hasMoved()) {
            $yearFolder = (string)$surat['tahun_nomor'];
            $targetDir  = WRITEPATH . 'uploads/surat/' . $yearFolder;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Remove old file if exists
            if (!empty($surat['file_path'])) {
                $oldFullPath = WRITEPATH . $surat['file_path'];
                if (file_exists($oldFullPath)) {
                    unlink($oldFullPath);
                }
            }

            $fileNameOriginal = $file->getClientName();
            $newFileName      = $file->getRandomName();
            $file->move($targetDir, $newFileName);

            $filePathRelative = 'uploads/surat/' . $yearFolder . '/' . $newFileName;

            $status = ($surat['status'] === 'Nomor Diambil' || $surat['status'] === 'Draft') ? 'File Sudah Upload' : $surat['status'];

            $this->suratModel->update($id, [
                'nama_file' => $fileNameOriginal,
                'file_path' => $filePathRelative,
                'status'    => $status,
            ]);

            $this->logModel->logActivity(
                $id,
                $userId,
                'Upload File Surat',
                "{$session->get('name')} mengunggah file dokumen '{$fileNameOriginal}' untuk nomor surat {$surat['nomor_surat']}."
            );

            return redirect()->to(base_url('surat/show/' . $id))->with('success', 'File dokumen surat berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * Download uploaded file
     */
    public function downloadFile(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $surat = $this->suratModel->find($id);
        if (!$surat || empty($surat['file_path'])) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan.');
        }

        if ($role === 'pegawai' && $surat['pembuat_id'] != $userId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $fullPath = WRITEPATH . $surat['file_path'];
        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'File fisik di server tidak ditemukan.');
        }

        $this->logModel->logActivity(
            $id,
            $userId,
            'Download File Surat',
            "{$session->get('name')} mengunduh file '{$surat['nama_file']}' dari nomor surat {$surat['nomor_surat']}."
        );

        return $this->response->download($fullPath, null)->setFileName($surat['nama_file'] ?: basename($fullPath));
    }

    /**
     * View/Stream uploaded file inline (for PDF embed)
     */
    public function viewFile(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $surat = $this->suratModel->find($id);
        if (!$surat || empty($surat['file_path'])) {
            return $this->response->setStatusCode(404)->setBody('File dokumen tidak ditemukan.');
        }

        if ($role === 'pegawai' && $surat['pembuat_id'] != $userId) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        }

        $fullPath = WRITEPATH . $surat['file_path'];
        if (!file_exists($fullPath)) {
            return $this->response->setStatusCode(404)->setBody('File fisik di server tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = match($extension) {
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . ($surat['nama_file'] ?: basename($fullPath)) . '"')
            ->setBody(file_get_contents($fullPath));
    }

    /**
     * Delete document file (Admin only)
     */
    public function deleteFile(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        if ($role !== 'admin') {
            return redirect()->back()->with('error', 'Hanya Admin yang berhak menghapus file surat.');
        }

        $surat = $this->suratModel->find($id);
        if (!$surat || empty($surat['file_path'])) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fullPath = WRITEPATH . $surat['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $this->suratModel->update($id, [
            'nama_file' => null,
            'file_path' => null,
            'status'    => 'Nomor Diambil',
        ]);

        $this->logModel->logActivity(
            $id,
            $userId,
            'Hapus File Surat',
            "Admin {$session->get('name')} menghapus lampiran dokumen surat {$surat['nomor_surat']}."
        );

        return redirect()->to(base_url('surat/show/' . $id))->with('success', 'File dokumen berhasil dihapus.');
    }

    /**
     * Cancel surat number (Admin only)
     */
    public function batalkan(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        if ($role !== 'admin') {
            return redirect()->back()->with('error', 'Hanya Admin yang berhak membatalkan nomor surat.');
        }

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('surat'))->with('error', 'Surat tidak ditemukan.');
        }

        $alasan = trim($this->request->getPost('alasan_batal') ?: 'Pembatalan nomor surat oleh Administrator');

        $this->suratModel->update($id, [
            'status'     => 'Dibatalkan',
            'keterangan' => ($surat['keterangan'] ? $surat['keterangan'] . " | " : "") . "[DIBATALKAN: {$alasan}]",
        ]);

        $this->logModel->logActivity(
            $id,
            $userId,
            'Membatalkan Nomor Surat',
            "Admin {$session->get('name')} membatalkan nomor surat {$surat['nomor_surat']}. Alasan: {$alasan}"
        );

        return redirect()->to(base_url('surat/show/' . $id))->with('success', "Nomor surat {$surat['nomor_surat']} berhasil dibatalkan. Nomor ini tidak akan digunakan ulang.");
    }

    /**
     * Delete surat record (Admin only)
     */
    public function delete(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        if ($role !== 'admin') {
            return redirect()->back()->with('error', 'Hanya Admin yang dapat menghapus data.');
        }

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('surat'))->with('error', 'Data surat tidak ditemukan.');
        }

        // Delete physical file if exists
        if (!empty($surat['file_path'])) {
            $fullPath = WRITEPATH . $surat['file_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $nomorSurat = $surat['nomor_surat'];
        $this->suratModel->delete($id);

        $this->logModel->logActivity(
            null,
            $userId,
            'Menghapus Data Surat',
            "Admin {$session->get('name')} menghapus arsip data nomor surat {$nomorSurat}."
        );

        return redirect()->to(base_url('surat'))->with('success', "Data surat {$nomorSurat} berhasil dihapus dari sistem.");
    }

    /**
     * Printable view of surat number sheet
     */
    public function cetak(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('surat'))->with('error', 'Data tidak ditemukan.');
        }

        if ($role === 'pegawai' && $surat['pembuat_id'] != $userId) {
            return redirect()->to(base_url('surat'))->with('error', 'Akses ditolak.');
        }

        return view('surat/print', [
            'surat'    => $surat,
            'settings' => $this->settingModel->getSettings(),
        ]);
    }
}
