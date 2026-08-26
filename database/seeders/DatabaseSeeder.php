<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $superAdminRole = \App\Models\Role::where('nama_role', 'super_admin')->first();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@sikap.test',
            'role_id' => $superAdminRole->id,
        ]);
    }
}
