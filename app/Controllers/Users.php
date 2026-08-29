<?php

namespace App\Controllers;

use App\Models\SuratLogModel;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;
    protected SuratLogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new SuratLogModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('role', 'ASC')->orderBy('name', 'ASC')->findAll();

        return view('users/index', [
            'title' => 'Manajemen Pengguna & Pegawai',
            'users' => $users,
        ]);
    }

    public function store()
    {
        $session = session();
        $userId  = $session->get('user_id');

        $rules = [
            'name'       => 'required|min_length[3]|max_length[150]',
            'username'   => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'      => 'permit_empty|valid_email',
            'password'   => 'required|min_length[6]',
            'role'       => 'required|in_list[admin,pegawai]',
            'unit_kerja' => 'permit_empty|max_length[150]',
            'jabatan'    => 'permit_empty|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'name'       => trim($this->request->getPost('name')),
            'username'   => trim($this->request->getPost('username')),
            'email'      => trim($this->request->getPost('email')),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => $this->request->getPost('role'),
            'unit_kerja' => trim($this->request->getPost('unit_kerja')),
            'jabatan'    => trim($this->request->getPost('jabatan')),
            'is_active'  => 1,
        ];

        $this->userModel->insert($userData);

        $this->logModel->logActivity(
            null,
            $userId,
            'Tambah Pengguna',
            "Admin {$session->get('name')} menambahkan pengguna baru: {$userData['name']} ({$userData['username']}) - Role: {$userData['role']}."
        );

        return redirect()->to(base_url('users'))->with('success', "Pengguna {$userData['name']} berhasil ditambahkan.");
    }

    public function update(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'Data pengguna tidak ditemukan.');
        }

        $rules = [
            'name'       => 'required|min_length[3]|max_length[150]',
            'username'   => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email'      => 'permit_empty|valid_email',
            'role'       => 'required|in_list[admin,pegawai]',
            'unit_kerja' => 'permit_empty|max_length[150]',
            'jabatan'    => 'permit_empty|max_length[150]',
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name'       => trim($this->request->getPost('name')),
            'username'   => trim($this->request->getPost('username')),
            'email'      => trim($this->request->getPost('email')),
            'role'       => $this->request->getPost('role'),
            'unit_kerja' => trim($this->request->getPost('unit_kerja')),
            'jabatan'    => trim($this->request->getPost('jabatan')),
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);

        $this->logModel->logActivity(
            null,
            $userId,
            'Edit Pengguna',
            "Admin {$session->get('name')} memperbarui data pengguna: {$updateData['name']} ({$updateData['username']})."
        );

        return redirect()->to(base_url('users'))->with('success', "Data pengguna {$updateData['name']} berhasil diperbarui.");
    }

    public function toggleStatus(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');

        if ($userId == $id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        $this->logModel->logActivity(
            null,
            $userId,
            'Ubah Status Pengguna',
            "Admin {$session->get('name')} mengubah status akun {$user['name']} menjadi {$statusText}."
        );

        return redirect()->to(base_url('users'))->with('success', "Akun {$user['name']} berhasil {$statusText}.");
    }

    public function delete(int $id)
    {
        $session = session();
        $userId  = $session->get('user_id');

        if ($userId == $id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $this->userModel->delete($id);

        $this->logModel->logActivity(
            null,
            $userId,
            'Hapus Pengguna',
            "Admin {$session->get('name')} menghapus akun pengguna {$user['name']}."
        );

        return redirect()->to(base_url('users'))->with('success', "Pengguna {$user['name']} berhasil dihapus.");
    }
}
