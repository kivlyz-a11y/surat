<?php

namespace App\Controllers;

use App\Models\SuratLogModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;
    protected SuratLogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new SuratLogModel();
    }

    public function login()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->request->is('post')) {
            $rules = [
                'username' => 'required',
                'password' => 'required',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', 'Silakan isi username dan password.');
            }

            $username = trim($this->request->getPost('username'));
            $password = (string)$this->request->getPost('password');

            $user = $this->userModel->findByUsername($username);

            if (!$user) {
                return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan atau akun dinonaktifkan.');
            }

            if (!password_verify($password, $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Password yang Anda masukkan salah.');
            }

            // Set session
            $session->set([
                'user_id'    => $user['id'],
                'name'       => $user['name'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'unit_kerja' => $user['unit_kerja'],
                'jabatan'    => $user['jabatan'],
                'isLoggedIn' => true,
            ]);

            // Log activity
            $this->logModel->logActivity(
                null,
                $user['id'],
                'Login Sistem',
                "User {$user['name']} ({$user['username']}) berhasil login ke sistem."
            );

            $session->setFlashdata('success', "Selamat datang kembali, {$user['name']}!");
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login');
    }

    public function logout()
    {
        $session = session();
        $userId  = $session->get('user_id');
        $name    = $session->get('name');

        if ($userId) {
            $this->logModel->logActivity(
                null,
                $userId,
                'Logout Sistem',
                "User {$name} berhasil logout dari sistem."
            );
        }

        $session->destroy();
        return redirect()->to(base_url('auth/login'))->with('success', 'Anda telah berhasil logout.');
    }

    public function profile()
    {
        $session = session();
        $userId  = $session->get('user_id');
        $user    = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('auth/login'));
        }

        if ($this->request->is('post')) {
            $rules = [
                'name'       => 'required|min_length[3]|max_length[150]',
                'email'      => 'permit_empty|valid_email',
                'unit_kerja' => 'permit_empty|max_length[150]',
                'jabatan'    => 'permit_empty|max_length[150]',
            ];

            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $rules['password']         = 'min_length[6]';
                $rules['confirm_password'] = 'matches[password]';
            }

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $updateData = [
                'name'       => $this->request->getPost('name'),
                'email'      => $this->request->getPost('email'),
                'unit_kerja' => $this->request->getPost('unit_kerja'),
                'jabatan'    => $this->request->getPost('jabatan'),
            ];

            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->userModel->update($userId, $updateData);

            // Update active session info
            $session->set([
                'name'       => $updateData['name'],
                'email'      => $updateData['email'],
                'unit_kerja' => $updateData['unit_kerja'],
                'jabatan'    => $updateData['jabatan'],
            ]);

            $this->logModel->logActivity(
                null,
                $userId,
                'Ubah Profil',
                "User {$user['name']} memperbarui profil akun."
            );

            return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
        }

        return view('auth/profile', [
            'title' => 'Profil Saya',
            'user'  => $user,
        ]);
    }
}
