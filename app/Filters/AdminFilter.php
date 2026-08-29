<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Silakan login terlebih dahulu.');
            return redirect()->to(base_url('auth/login'));
        }

        if ($session->get('role') !== 'admin') {
            $session->setFlashdata('error', 'Akses ditolak! Menu ini khusus untuk Administrator.');
            return redirect()->to(base_url('dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
