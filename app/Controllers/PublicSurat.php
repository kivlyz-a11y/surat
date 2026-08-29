<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\SuratModel;

class PublicSurat extends BaseController
{
    protected SuratModel $suratModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->suratModel   = new SuratModel();
        $this->settingModel = new SettingModel();
    }

    /**
     * Public page to view & verify a surat without logging in
     * Accessible by anyone via share link
     */
    public function verify($id)
    {
        $surat = $this->suratModel->find($id);

        if (!$surat) {
            // Also try search by nomor_surat if string passed
            $surat = $this->suratModel->where('nomor_surat', urldecode($id))->first();
        }

        $settings = $this->settingModel->getSettings();

        if (!$surat) {
            return view('public/not_found', [
                'title'    => 'Surat Tidak Ditemukan',
                'settings' => $settings,
            ]);
        }

        return view('public/verify', [
            'title'    => 'Verifikasi Nomor Surat - ' . $surat['nomor_surat'],
            'surat'    => $surat,
            'settings' => $settings,
        ]);
    }

    /**
     * Public download file for non-logged-in users with the link
     */
    public function download($id)
    {
        $surat = $this->suratModel->find($id);

        if (!$surat || empty($surat['file_path'])) {
            return redirect()->to(base_url('cek-surat/' . $id))->with('error', 'Berkas dokumen tidak tersedia.');
        }

        $fullPath = WRITEPATH . $surat['file_path'];
        if (!file_exists($fullPath)) {
            return redirect()->to(base_url('cek-surat/' . $id))->with('error', 'File fisik di server tidak ditemukan.');
        }

        return $this->response->download($fullPath, null)->setFileName($surat['nama_file'] ?: basename($fullPath));
    }

    /**
     * Public inline file view (for PDF embed / preview)
     */
    public function viewFile($id)
    {
        $surat = $this->suratModel->find($id);

        if (!$surat || empty($surat['file_path'])) {
            return $this->response->setStatusCode(404)->setBody('Berkas tidak ditemukan.');
        }

        $fullPath = WRITEPATH . $surat['file_path'];
        if (!file_exists($fullPath)) {
            return $this->response->setStatusCode(404)->setBody('Berkas fisik tidak ditemukan di server.');
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
            ->setHeader('Cache-Control', 'public, max-age=3600')
            ->setBody(file_get_contents($fullPath));
    }
}
