<?php

namespace App\Controllers;

use App\Models\NomorSuratCounterModel;
use App\Models\SettingModel;
use App\Models\SuratLogModel;

class Settings extends BaseController
{
    protected SettingModel $settingModel;
    protected NomorSuratCounterModel $counterModel;
    protected SuratLogModel $logModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->counterModel = new NomorSuratCounterModel();
        $this->logModel     = new SuratLogModel();
    }

    public function index()
    {
        $settings = $this->settingModel->getSettings();
        $counters = $this->counterModel->orderBy('tahun_counter', 'ASC')->findAll();

        return view('settings/index', [
            'title'    => 'Pengaturan Konfigurasi Sistem',
            'settings' => $settings,
            'counters' => $counters,
        ]);
    }

    public function update()
    {
        $session = session();
        $userId  = $session->get('user_id');

        $rules = [
            'nama_aplikasi'    => 'required|min_length[3]|max_length[200]',
            'instansi_default' => 'required|min_length[1]|max_length[100]',
            'format_tampilan'  => 'required',
            'batas_upload_mb'  => 'required|numeric|greater_than_equal_to[1]',
            'ekstensi_file'    => 'required',
            'padding_digit'    => 'required|in_list[1,2,3,4,5,6]',
            'mode_counter'     => 'required|in_list[global,per_tahun]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $settingsRow = $this->settingModel->first();
        $settingId   = $settingsRow ? $settingsRow['id'] : null;

        $updateData = [
            'nama_aplikasi'    => trim($this->request->getPost('nama_aplikasi')),
            'instansi_default' => trim($this->request->getPost('instansi_default')),
            'format_tampilan'  => trim($this->request->getPost('format_tampilan')),
            'batas_upload_mb'  => (int)$this->request->getPost('batas_upload_mb'),
            'ekstensi_file'    => trim($this->request->getPost('ekstensi_file')),
            'padding_digit'    => (int)$this->request->getPost('padding_digit'),
            'mode_counter'     => $this->request->getPost('mode_counter'),
        ];

        if ($settingId) {
            $this->settingModel->update($settingId, $updateData);
        } else {
            $this->settingModel->insert($updateData);
        }

        // Optional counter manual adjustment by admin (only if provided and higher than current)
        $counterVal = $this->request->getPost('nomor_terakhir_global');
        if ($counterVal !== null && is_numeric($counterVal)) {
            $globalCounter = $this->counterModel->where('tahun_counter', 0)->first();
            if ($globalCounter) {
                $this->counterModel->update($globalCounter['id'], ['nomor_terakhir' => (int)$counterVal]);
            }
        }

        $this->logModel->logActivity(
            null,
            $userId,
            'Ubah Pengaturan Sistem',
            "Admin {$session->get('name')} memperbarui konfigurasi sistem persuratan."
        );

        return redirect()->to(base_url('settings'))->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
