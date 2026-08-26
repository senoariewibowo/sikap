<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriverUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('nama_role', 'driver')->firstOrFail();
        $k = Karyawan::firstOrCreate(
            ['nik' => '3201010511990006'],
            [
                'nama' => 'Driver SIKAP',
                'no_hp' => '081234567891',
                'alamat' => 'Ds. Test',
                'jabatan' => 'Driver',
                'tanggal_masuk' => '2024-01-01',
                'status' => 'aktif',
            ]
        );
        $driver = User::firstOrCreate(
            ['email' => 'driver@sikap.test'],
            [
                'name' => 'Driver SIKAP',
                'password' => Hash::make('password'),
                'role_id' => $role->id,
                'karyawan_id' => $k->id,
            ]
        );
        $driver->update(['karyawan_id' => $k->id]);
    }
}
