<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Users extends Seeder
{
    public function run()
    {
        //insert default users
        $data = [
            'username' => 'admin',
            'password' => password_hash('admin', PASSWORD_DEFAULT),
            'email' => 'admin@admin.com',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Using Query Builder
        $this->db->table('users')->insert($data);
    }
}
