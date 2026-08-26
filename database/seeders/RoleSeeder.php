<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super_admin', 'petugas_kandang', 'petugas_gudang', 'viewer', 'driver'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama_role' => $role]);
        }
    }
}
