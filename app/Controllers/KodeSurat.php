<?php

namespace App\Controllers;

use App\Models\KodeSuratModel;
use App\Models\SuratLogModel;

class KodeSurat extends BaseController
{
    protected KodeSuratModel $kodeSuratModel;
    protected SuratLogModel $logModel;

    public function __construct()
    {
        $this->kodeSuratModel = new KodeSuratModel();
        $this->logModel       = new SuratLogModel();
    }

    public function index()
    {
        $kodeList = $this->kodeSuratModel->orderBy('kode', 'ASC')->findAll();

        return view('kode_surat/index', [
            'title'    => 'Master Klasifikasi Kode Surat',
            'kodeList' => $kodeList,
        ]);
    }

    public function store()
    {
        $session = session();
        $userId  = $session->get('user_id');

        $rules = [
            'kode'             => 'required|min_length[1]|max_length[50]|is_unique[kode_surat.kode]',
            'nama_klasifikasi' => 'required|min_length[2]|max_length[255]',
            'keterangan'       => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode'             => strtoupper(trim($this->request->getPost('kode'))),
            'nama_klasifikasi' => trim($this->request->getPost('nama_klasifikasi')),
            'keterangan'       => trim($this->request->getPost('keterangan')),
        ];

        $this->kodeSuratModel->insert($data);

        $this->logModel->logActivity(
            null,
            $userId,
            'Tambah Kode Surat',
            "User {$session->get('name')} menambahkan kode klasifikasi surat: {$data['kode']} - {$data['nama_klasifikasi']}."
        );

        return redirect()->to(base_url('kode-surat'))->with('success', "Kode surat {$data['kode']} berhasil ditambahkan.");
    }

    public function update(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');

        $kode = $this->kodeSuratModel->find($id);
        if (!$kode) {
            return redirect()->to(base_url('kode-surat'))->with('error', 'Kode surat tidak ditemukan.');
        }

        $rules = [
            'kode'             => "required|min_length[1]|max_length[50]|is_unique[kode_surat.kode,id,{$id}]",
            'nama_klasifikasi' => 'required|min_length[2]|max_length[255]',
            'keterangan'       => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kode'             => strtoupper(trim($this->request->getPost('kode'))),
            'nama_klasifikasi' => trim($this->request->getPost('nama_klasifikasi')),
            'keterangan'       => trim($this->request->getPost('keterangan')),
        ];

        $this->kodeSuratModel->update($id, $data);

        $this->logModel->logActivity(
            null,
            $userId,
            'Edit Kode Surat',
            "User {$session->get('name')} mengubah data kode surat: {$data['kode']}."
        );

        return redirect()->to(base_url('kode-surat'))->with('success', "Kode surat {$data['kode']} berhasil diperbarui.");
    }

    public function delete(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');
        $role    = $session->get('role');

        if ($role !== 'admin') {
            return redirect()->back()->with('error', 'Hanya Admin yang dapat menghapus data kode surat.');
        }

        $kode = $this->kodeSuratModel->find($id);
        if (!$kode) {
            return redirect()->to(base_url('kode-surat'))->with('error', 'Kode surat tidak ditemukan.');
        }

        $this->kodeSuratModel->delete($id);

        $this->logModel->logActivity(
            null,
            $userId,
            'Hapus Kode Surat',
            "Admin {$session->get('name')} menghapus kode klasifikasi surat {$kode['kode']}."
        );

        return redirect()->to(base_url('kode-surat'))->with('success', "Kode surat {$kode['kode']} berhasil dihapus.");
    }
}
