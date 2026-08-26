<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Kandang;
use App\Models\Karyawan;
use App\Models\Gudang;
use App\Models\PopulasiAyam;
use App\Models\ProduksiTelur;
use App\Models\Customer;
use App\Models\HargaTelur;
use App\Models\JenisPakan;
use App\Models\StokPakan;
use Carbon\Carbon;

class MinimalSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // Truncate existing data except users
        \DB::table('produksi_foto')->delete();
        \DB::table('produksi_telur')->delete();
        \DB::table('populasi_ayam')->delete();
        \DB::table('stok_pakan')->delete();
        \DB::table('jenis_pakan')->delete();
        \DB::table('harga_telur')->delete();
        \DB::table('customer')->delete();
        \DB::table('gudang')->delete();
        \DB::table('kandang_karyawan')->delete();
        \DB::table('kandang')->delete();
        \DB::table('karyawan')->delete();
        \DB::statement('DELETE FROM users WHERE email != ?', ['admin@sikap.test']);

        $roles = Role::all()->keyBy('nama_role');

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@sikap.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'role_id' => $roles['super_admin']->id]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@sikap.test'],
            ['name' => 'Petugas Kandang', 'password' => Hash::make('password'), 'role_id' => $roles['petugas_kandang']->id]
        );

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@sikap.test'],
            ['name' => 'Owner Viewer', 'password' => Hash::make('password'), 'role_id' => $roles['viewer']->id]
        );

        $kg1 = Kandang::create(['kode_kandang'=>'KDG-001','nama_kandang'=>'Kandang A','alamat_jalan'=>'Jl. Raya No. 1','desa_kelurahan'=>'Sukamaju','kecamatan'=>'Caringin','kabupaten_kota'=>'Bogor','provinsi'=>'Jawa Barat','kode_pos'=>'16730','latitude'=>-6.5891,'longitude'=>106.7932,'kapasitas'=>5000,'tipe_kandang'=>'baterai','status'=>'aktif']);
        $kg2 = Kandang::create(['kode_kandang'=>'KDG-002','nama_kandang'=>'Kandang B','alamat_jalan'=>'Jl. Raya No. 2','desa_kelurahan'=>'Harapan Jaya','kecamatan'=>'Cibungbulang','kabupaten_kota'=>'Bogor','provinsi'=>'Jawa Barat','kode_pos'=>'16650','latitude'=>-6.5678,'longitude'=>106.7012,'kapasitas'=>3000,'tipe_kandang'=>'postal','status'=>'aktif']);

        $g1 = Gudang::create(['kode_gudang'=>'GDG-001','nama_gudang'=>'Gudang A','lokasi'=>'Jl. Raya No. 1','status'=>'aktif']);
        $g2 = Gudang::create(['kode_gudang'=>'GDG-002','nama_gudang'=>'Gudang B','lokasi'=>'Jl. Raya No. 2','status'=>'aktif']);

        $kg1->update(['gudang_id' => $g1->id]);
        $kg2->update(['gudang_id' => $g2->id]);

        $k1 = Karyawan::create(['nik'=>'3201010101800001','nama'=>'Ahmad Fauzi','no_hp'=>'081234567890','alamat'=>'Ds. Sukamaju RT 02','jabatan'=>'Manajer Kandang','tanggal_masuk'=>'2020-03-15','status'=>'aktif']);
        $k2 = Karyawan::create(['nik'=>'3201010204850002','nama'=>'Supriyanto','no_hp'=>'085678901234','alamat'=>'Ds. Harapan Jaya RT 01','jabatan'=>'Petugas Kandang','tanggal_masuk'=>'2021-06-01','status'=>'aktif']);
        $k3 = Karyawan::create(['nik'=>'3201010307900003','nama'=>'Rudi Hartono','no_hp'=>'089012345678','alamat'=>'Ds. Mekarsari RT 03','jabatan'=>'Petugas Gudang','tanggal_masuk'=>'2022-01-10','status'=>'aktif']);
        $k4 = Karyawan::create(['nik'=>'3201010411920004','nama'=>'Hendra Gunawan','no_hp'=>'081345678901','alamat'=>'Ds. Pasir Muncang RT 04','jabatan'=>'Petugas Kandang','tanggal_masuk'=>'2023-06-01','status'=>'aktif']);

        $superAdmin->update(['karyawan_id' => $k1->id]);
        $petugas->update(['karyawan_id' => $k2->id]);

        $gudangUser = User::firstOrCreate(
            ['email' => 'gudang@sikap.test'],
            ['name' => 'Petugas Gudang', 'password' => Hash::make('password'), 'role_id' => $roles['petugas_gudang']->id, 'karyawan_id' => $k3->id, 'gudang_id' => $g1->id]
        );

        $kandangUser2 = User::firstOrCreate(
            ['email' => 'kandang2@sikap.test'],
            ['name' => 'Petugas Kandang 2', 'password' => Hash::make('password'), 'role_id' => $roles['petugas_kandang']->id, 'karyawan_id' => $k4->id]
        );

        \DB::table('kandang_karyawan')->insert([
            ['kandang_id'=>$kg1->id,'karyawan_id'=>$k1->id,'tanggal_mulai'=>'2024-01-01','is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['kandang_id'=>$kg2->id,'karyawan_id'=>$k2->id,'tanggal_mulai'=>'2024-01-01','is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['kandang_id'=>$kg2->id,'karyawan_id'=>$k4->id,'tanggal_mulai'=>'2024-01-01','is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);

        \DB::table('gudang_karyawan')->insert([
            ['gudang_id'=>$g1->id,'karyawan_id'=>$k3->id,'tanggal_mulai'=>'2024-01-01','is_active'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);

        PopulasiAyam::insert([
            ['kandang_id'=>$kg1->id,'tanggal'=>Carbon::now()->subDays(5)->format('Y-m-d'),'jumlah_masuk'=>4800,'jumlah_mati'=>0,'jumlah_afkir'=>0,'keterangan'=>'DOC awal','created_by'=>$superAdmin->id,'created_at'=>now(),'updated_at'=>now()],
            ['kandang_id'=>$kg2->id,'tanggal'=>Carbon::now()->subDays(5)->format('Y-m-d'),'jumlah_masuk'=>2850,'jumlah_mati'=>0,'jumlah_afkir'=>0,'keterangan'=>'DOC awal','created_by'=>$superAdmin->id,'created_at'=>now(),'updated_at'=>now()],
        ]);

        ProduksiTelur::insert([
            ['kandang_id'=>$kg1->id,'tanggal'=>Carbon::now()->format('Y-m-d'),'jumlah_butir'=>540,'karpet'=>18,'sisa'=>0,'shift'=>'siang','input_by'=>$superAdmin->id,'status_setor'=>'belum_disetor','created_at'=>now(),'updated_at'=>now()],
            ['kandang_id'=>$kg2->id,'tanggal'=>Carbon::now()->format('Y-m-d'),'jumlah_butir'=>360,'karpet'=>12,'sisa'=>0,'shift'=>'siang','input_by'=>$superAdmin->id,'status_setor'=>'belum_disetor','created_at'=>now(),'updated_at'=>now()],
        ]);

        $jp1 = JenisPakan::create(['nama'=>'Pakan Layer 105','kategori'=>'pakan','satuan'=>'kg','stok_minimal'=>500]);
        $jp2 = JenisPakan::create(['nama'=>'Vitachick','kategori'=>'vitamin','satuan'=>'sachet','stok_minimal'=>50]);

        StokPakan::insert([
            ['kandang_id'=>null,'jenis_pakan_id'=>$jp1->id,'tipe'=>'masuk','jumlah_kg'=>2000,'tanggal'=>Carbon::now()->subDays(3)->format('Y-m-d'),'keterangan'=>'Pembelian','created_by'=>$superAdmin->id,'created_at'=>now(),'updated_at'=>now()],
            ['kandang_id'=>null,'jenis_pakan_id'=>$jp2->id,'tipe'=>'masuk','jumlah_kg'=>200,'tanggal'=>Carbon::now()->subDays(3)->format('Y-m-d'),'keterangan'=>'Pembelian','created_by'=>$superAdmin->id,'created_at'=>now(),'updated_at'=>now()],
        ]);

        $c1 = Customer::create(['nama_customer'=>'UD Telur Makmur','tipe_customer'=>'agen','alamat'=>'Pasar Induk Bogor','no_hp'=>'081212345678','kontak_person'=>'Haji Soleh','status'=>'aktif']);
        $c2 = Customer::create(['nama_customer'=>'Warung Bu Nani','tipe_customer'=>'retail','alamat'=>'Pasar Anyar','no_hp'=>'085698765432','kontak_person'=>'Bu Nani','status'=>'aktif']);

        HargaTelur::insert([
            ['harga'=>2500,'satuan'=>'per_butir','customer_id'=>null,'tanggal_mulai_berlaku'=>Carbon::now()->subDays(30)->format('Y-m-d'),'created_by'=>$superAdmin->id,'created_at'=>now(),'updated_at'=>now()],
            ['harga'=>40000,'satuan'=>'per_kg','customer_id'=>null,'tanggal_mulai_berlaku'=>Carbon::now()->subDays(30)->format('Y-m-d'),'created_by'=>$superAdmin->id,'created_at'=>now(),'updated_at'=>now()],
        ]);

        echo "\n=== DATA MINIMAL BERHASIL DI-SEED ===\n";
        echo "Users: " . User::count() . " | Kandang: " . Kandang::count() . " | Karyawan: " . Karyawan::count() . "\n";
        echo "Users: " . User::count() . " | Kandang: " . Kandang::count() . " | Karyawan: " . Karyawan::count() . "\n";
        echo "Gudang: " . Gudang::count() . " | Populasi: " . PopulasiAyam::count() . " | Produksi: " . ProduksiTelur::count() . "\n";
        echo "Customer: " . Customer::count() . " | Harga: " . HargaTelur::count() . "\n";
    }
}
