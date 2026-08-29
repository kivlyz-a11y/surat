<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'unit_kerja',
        'jabatan',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[150]',
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'role'     => 'required|in_list[admin,pegawai]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan oleh pengguna lain.',
        ],
    ];

    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->where('is_active', 1)->first();
    }
}
