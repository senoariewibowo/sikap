<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Kandang;
use App\Models\Karyawan;
use App\Models\PopulasiAyam;
use App\Models\ProduksiTelur;
use App\Models\StokTelurKeluar;
use App\Models\JenisPakan;
use App\Models\StokPakan;
use App\Models\Customer;
use App\Models\HargaTelur;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;

class DevSeeder extends Seeder
{
    private $kandangIds = [];
    private $karyawanIds = [];
    private $userIds = [];
    private $jenisPakanIds = [];
    private $customerIds = [];
    private $gradeMap = ['besar', 'sedang', 'kecil', 'retak'];

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $superAdminRole = Role::where('nama_role', 'super_admin')->first();
        User::firstOrCreate(
            ['email' => 'admin@sikap.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'role_id' => $superAdminRole->id]
        );

        $this->seedKaryawan();
        $this->seedUsers();
        $this->seedKandang();
        $this->seedPenugasan();
        $this->seedPopulasi();
        $this->seedProduksi();
        $this->seedJenisPakan();
        $this->seedStokPakan();
        $this->seedCustomer();
        $this->seedHargaTelur();
        $this->seedStokTelurDanPenjualan();

        echo "\n--- SEMUA DATA DUMMY BERHASIL DI-SEED ---\n";
    }

    private function seedUsers(): void
    {
        $roles = Role::all()->keyBy('nama_role');

        $users = [
            ['name' => 'Budi Santoso', 'email' => 'budi@sikap.test', 'role' => 'petugas_kandang', 'nis' => '3201010101800001'],
            ['name' => 'Agus Hermawan', 'email' => 'agus@sikap.test', 'role' => 'petugas_kandang', 'nis' => '3201010204850002'],
            ['name' => 'Siti Rahayu', 'email' => 'siti@sikap.test', 'role' => 'petugas_kandang', 'nis' => '3201010307900003'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@sikap.test', 'role' => 'petugas_kandang', 'nis' => '3201010515950005'],
            ['name' => 'Pak Hartono', 'email' => 'hartono@sikap.test', 'role' => 'viewer', 'nis' => null],
        ];

        foreach ($users as $u) {
            $karyawanId = $u['nis'] ? Karyawan::where('nik', $u['nis'])->value('id') : null;
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password'), 'role_id' => $roles[$u['role']]->id, 'karyawan_id' => $karyawanId]
            );
            $this->userIds[$u['role']][] = $user->id;
        }
        $this->userIds['super_admin'][] = User::where('email', 'admin@sikap.test')->first()->id;

        echo "Users: " . User::count() . " created\n";
    }

    private function seedKandang(): void
    {
        $kandangs = [
            ['kode_kandang' => 'KDG-001', 'nama_kandang' => 'Kandang A - Sukamaju', 'alamat_jalan' => 'Jl. Raya Sukamaju No. 45', 'desa_kelurahan' => 'Desa Sukamaju', 'kecamatan' => 'Caringin', 'kabupaten_kota' => 'Bogor', 'provinsi' => 'Jawa Barat', 'kode_pos' => '16730', 'latitude' => -6.5891, 'longitude' => 106.7932, 'kapasitas' => 5000, 'tipe_kandang' => 'baterai', 'status' => 'aktif'],
            ['kode_kandang' => 'KDG-002', 'nama_kandang' => 'Kandang B - Harapan Jaya', 'alamat_jalan' => 'Jl. Harapan Jaya No. 12', 'desa_kelurahan' => 'Desa Harapan Jaya', 'kecamatan' => 'Cibungbulang', 'kabupaten_kota' => 'Bogor', 'provinsi' => 'Jawa Barat', 'kode_pos' => '16650', 'latitude' => -6.5678, 'longitude' => 106.7012, 'kapasitas' => 3500, 'tipe_kandang' => 'postal', 'status' => 'aktif'],
            ['kode_kandang' => 'KDG-003', 'nama_kandang' => 'Kandang C - Mekarsari', 'alamat_jalan' => 'Jl. Mekarsari No. 8', 'desa_kelurahan' => 'Desa Mekarsari', 'kecamatan' => 'Ciampea', 'kabupaten_kota' => 'Bogor', 'provinsi' => 'Jawa Barat', 'kode_pos' => '16620', 'latitude' => -6.5423, 'longitude' => 106.6854, 'kapasitas' => 8000, 'tipe_kandang' => 'closed_house', 'status' => 'aktif'],
            ['kode_kandang' => 'KDG-004', 'nama_kandang' => 'Kandang D - Pasir Muncang', 'alamat_jalan' => 'Jl. Pasir Muncang No. 33', 'desa_kelurahan' => 'Desa Pasir Muncang', 'kecamatan' => 'Caringin', 'kabupaten_kota' => 'Bogor', 'provinsi' => 'Jawa Barat', 'kode_pos' => '16730', 'latitude' => -6.6023, 'longitude' => 106.8015, 'kapasitas' => 4000, 'tipe_kandang' => 'baterai', 'status' => 'aktif'],
            ['kode_kandang' => 'KDG-005', 'nama_kandang' => 'Kandang E - Lembah Hijau', 'alamat_jalan' => 'Jl. Lembah Hijau No. 21', 'desa_kelurahan' => 'Desa Cijeruk', 'kecamatan' => 'Cijeruk', 'kabupaten_kota' => 'Bogor', 'provinsi' => 'Jawa Barat', 'kode_pos' => '16760', 'latitude' => -6.6123, 'longitude' => 106.7200, 'kapasitas' => 6000, 'tipe_kandang' => 'closed_house', 'status' => 'aktif'],
        ];

        foreach ($kandangs as $k) {
            Kandang::create($k);
            $this->kandangIds[] = Kandang::where('kode_kandang', $k['kode_kandang'])->first()->id;
        }
        echo "Kandang: " . Kandang::count() . " created\n";
    }

    private function seedKaryawan(): void
    {
        $karyawans = [
            ['nik' => '3201010101800001', 'nama' => 'Ahmad Fauzi', 'no_hp' => '081234567890', 'alamat' => 'Ds. Sukamaju RT 02/04', 'jabatan' => 'Manajer Kandang', 'tanggal_masuk' => '2020-03-15', 'status' => 'aktif'],
            ['nik' => '3201010204850002', 'nama' => 'Supriyanto', 'no_hp' => '085678901234', 'alamat' => 'Ds. Harapan Jaya RT 01/03', 'jabatan' => 'Petugas Kandang', 'tanggal_masuk' => '2021-06-01', 'status' => 'aktif'],
            ['nik' => '3201010307900003', 'nama' => 'Rudi Hartono', 'no_hp' => '089012345678', 'alamat' => 'Ds. Mekarsari RT 03/02', 'jabatan' => 'Petugas Kandang', 'tanggal_masuk' => '2021-06-01', 'status' => 'aktif'],
            ['nik' => '3201010411920004', 'nama' => 'Hendra Gunawan', 'no_hp' => '081345678901', 'alamat' => 'Ds. Pasir Muncang RT 04/01', 'jabatan' => 'Teknisi', 'tanggal_masuk' => '2022-01-10', 'status' => 'aktif'],
            ['nik' => '3201010515950005', 'nama' => 'Yanto Setiawan', 'no_hp' => '087890123456', 'alamat' => 'Ds. Sukamaju RT 01/05', 'jabatan' => 'Petugas Kandang', 'tanggal_masuk' => '2022-08-15', 'status' => 'aktif'],
            ['nik' => '3201010608880006', 'nama' => 'Slamet Riyadi', 'no_hp' => '082345678902', 'alamat' => 'Ds. Cijeruk RT 05/03', 'jabatan' => 'Petugas Kandang', 'tanggal_masuk' => '2023-03-01', 'status' => 'aktif'],
            ['nik' => '3201010710830007', 'nama' => 'Nurhasanah', 'no_hp' => '081567890123', 'alamat' => 'Ds. Cibungbulang RT 02/06', 'jabatan' => 'Admin Gudang', 'tanggal_masuk' => '2023-07-01', 'status' => 'aktif'],
            ['nik' => '3201010805860008', 'nama' => 'Mulyadi', 'no_hp' => '085123456789', 'alamat' => 'Ds. Ciampea RT 03/01', 'jabatan' => 'Petugas Kandang', 'tanggal_masuk' => '2024-01-15', 'status' => 'aktif'],
        ];

        foreach ($karyawans as $k) {
            Karyawan::create($k);
            $this->karyawanIds[] = Karyawan::where('nik', $k['nik'])->first()->id;
        }
        echo "Karyawan: " . Karyawan::count() . " created\n";
    }

    private function seedPenugasan(): void
    {
        $assignments = [
            ['kandang' => 0, 'karyawan' => 0, 'mulai' => '2024-01-01'],
            ['kandang' => 0, 'karyawan' => 4, 'mulai' => '2024-06-01'],
            ['kandang' => 1, 'karyawan' => 1, 'mulai' => '2024-01-01'],
            ['kandang' => 2, 'karyawan' => 2, 'mulai' => '2024-01-01'],
            ['kandang' => 2, 'karyawan' => 7, 'mulai' => '2024-06-01'],
            ['kandang' => 3, 'karyawan' => 3, 'mulai' => '2024-01-01'],
            ['kandang' => 3, 'karyawan' => 4, 'mulai' => '2024-03-01'],
            ['kandang' => 4, 'karyawan' => 5, 'mulai' => '2024-01-01'],
            ['kandang' => 4, 'karyawan' => 1, 'mulai' => '2024-08-01'],
        ];

        foreach ($assignments as $a) {
            \DB::table('kandang_karyawan')->insert([
                'kandang_id' => $this->kandangIds[$a['kandang']],
                'karyawan_id' => $this->karyawanIds[$a['karyawan']],
                'tanggal_mulai' => $a['mulai'],
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        echo "Penugasan: " . count($assignments) . " created\n";
    }

    private function seedPopulasi(): void
    {
        $start = Carbon::now()->subDays(45);
        $userId = $this->userIds['petugas_kandang'][0];

        foreach ($this->kandangIds as $kid) {
            $kandang = Kandang::find($kid);
            $awal = (int) ($kandang->kapasitas * 0.95);

            PopulasiAyam::create([
                'kandang_id' => $kid, 'tanggal' => $start->format('Y-m-d'),
                'jumlah_masuk' => $awal, 'jumlah_mati' => 0, 'jumlah_afkir' => 0,
                'keterangan' => 'Populasi awal/DOC masuk', 'created_by' => $userId,
            ]);

            for ($d = 1; $d <= 44; $d++) {
                $tgl = $start->copy()->addDays($d);
                $mati = random_int(0, 5);
                $afkir = $tgl->dayOfWeek == Carbon::MONDAY ? random_int(0, 3) : random_int(0, 1);
                $masuk = ($d % 30 == 0) ? random_int(100, 300) : 0;

                if ($mati > 0 || $afkir > 0 || $masuk > 0) {
                    PopulasiAyam::create([
                        'kandang_id' => $kid, 'tanggal' => $tgl->format('Y-m-d'),
                        'jumlah_masuk' => $masuk, 'jumlah_mati' => $mati, 'jumlah_afkir' => $afkir,
                        'keterangan' => $afkir > 0 ? 'Afkir mingguan' : ($masuk > 0 ? 'DOC tambahan' : null),
                        'created_by' => $userId,
                    ]);
                }
            }
        }
        echo "Populasi Ayam: " . PopulasiAyam::count() . " records\n";
    }

    private function seedProduksi(): void
    {
        $start = Carbon::now()->subDays(45);
        $userId = $this->userIds['petugas_kandang'][0];
        $gradeDist = ['besar' => 0.25, 'sedang' => 0.45, 'kecil' => 0.20, 'retak' => 0.10];
            $shifts = ['Siang', 'Sore', null];

        foreach ($this->kandangIds as $kid) {
            $kandang = Kandang::find($kid);
            $populasi = $kandang->populasiSekarang();

            if ($populasi <= 0) $populasi = (int) ($kandang->kapasitas * 0.9);

            for ($d = 0; $d <= 44; $d++) {
                $tgl = $start->copy()->addDays($d);
                $hdpRate = 0.85 + (random_int(-8, 8) / 100);
                $totalButir = (int) ($populasi * $hdpRate);

                foreach ($this->gradeMap as $grade) {
                    $butir = (int) ($totalButir * $gradeDist[$grade]);
                    $avgWeight = match ($grade) {
                        'besar' => 0.063, 'sedang' => 0.055, 'kecil' => 0.048, 'retak' => 0.050,
                    };
                    $berat = round($butir * $avgWeight, 2);

                    if ($butir > 0) {
                        ProduksiTelur::create([
                            'kandang_id' => $kid, 'tanggal' => $tgl->format('Y-m-d'),
                            'jumlah_butir' => $butir, 'berat_kg' => $berat,
                            'grade' => $grade, 'shift' => $shifts[array_rand($shifts)],
                            'input_by' => $userId,
                        ]);
                    }
                }
            }
        }
        echo "Produksi Telur: " . ProduksiTelur::count() . " records\n";
    }

    private function seedJenisPakan(): void
    {
        $pakans = [
            ['nama' => 'Pakan Layer 105', 'kategori' => 'pakan', 'satuan' => 'kg', 'stok_minimal' => 500],
            ['nama' => 'Pakan Layer 106', 'kategori' => 'pakan', 'satuan' => 'kg', 'stok_minimal' => 300],
            ['nama' => 'Jagung Giling', 'kategori' => 'pakan', 'satuan' => 'kg', 'stok_minimal' => 200],
            ['nama' => 'Vitachick', 'kategori' => 'vitamin', 'satuan' => 'sachet', 'stok_minimal' => 50],
            ['nama' => 'Neobro Oral', 'kategori' => 'obat', 'satuan' => 'botol', 'stok_minimal' => 10],
        ];

        foreach ($pakans as $p) {
            JenisPakan::create($p);
            $this->jenisPakanIds[] = JenisPakan::where('nama', $p['nama'])->first()->id;
        }
        echo "Jenis Pakan: " . JenisPakan::count() . " created\n";
    }

    private function seedStokPakan(): void
    {
        $start = Carbon::now()->subDays(45);
        $userId = $this->userIds['petugas_kandang'][0];

        foreach ($this->jenisPakanIds as $idx => $jid) {
            $masukAwal = $idx == 0 ? 2000 : ($idx == 1 ? 1500 : ($idx == 2 ? 800 : 200));

            StokPakan::create([
                'kandang_id' => null, 'jenis_pakan_id' => $jid,
                'tipe' => 'masuk', 'jumlah_kg' => $masukAwal, 'tanggal' => $start->format('Y-m-d'),
                'keterangan' => 'Pembelian awal', 'created_by' => $userId,
            ]);

            for ($d = 0; $d <= 44; $d += random_int(1, 3)) {
                $tgl = $start->copy()->addDays($d);
                $keluar = round($masukAwal * (random_int(2, 8) / 100), 1);
                $kid = $this->kandangIds[array_rand($this->kandangIds)];

                StokPakan::create([
                    'kandang_id' => $kid, 'jenis_pakan_id' => $jid,
                    'tipe' => 'keluar', 'jumlah_kg' => $keluar, 'tanggal' => $tgl->format('Y-m-d'),
                    'keterangan' => 'Pemakaian harian', 'created_by' => $userId,
                ]);
            }

            if ($d % 10 == 0) {
                StokPakan::create([
                    'kandang_id' => null, 'jenis_pakan_id' => $jid,
                    'tipe' => 'masuk', 'jumlah_kg' => $masukAwal * 0.5, 'tanggal' => $start->copy()->addDays($d)->format('Y-m-d'),
                    'keterangan' => 'Restock bulanan', 'created_by' => $userId,
                ]);
            }
        }
        echo "Stok Pakan: " . StokPakan::count() . " records\n";
    }

    private function seedCustomer(): void
    {
        $customers = [
            ['nama_customer' => 'UD Telur Makmur', 'tipe_customer' => 'agen', 'alamat' => 'Pasar Induk Bogor Blok C', 'no_hp' => '081212345678', 'kontak_person' => 'Haji Soleh'],
            ['nama_customer' => 'CV Sumber Rezeki', 'tipe_customer' => 'korporat', 'alamat' => 'Jl. Raya Jakarta No. 45, Bogor', 'no_hp' => '021-8765432', 'kontak_person' => 'Ibu Yanti'],
            ['nama_customer' => 'Warung Bu Nani', 'tipe_customer' => 'retail', 'alamat' => 'Pasar Anyar Bogor', 'no_hp' => '085698765432', 'kontak_person' => 'Bu Nani'],
            ['nama_customer' => 'Pengepul Pak Dedi', 'tipe_customer' => 'pengepul', 'alamat' => 'Ds. Ciampea RT 04/02', 'no_hp' => '082178901234', 'kontak_person' => 'Pak Dedi'],
            ['nama_customer' => 'Agen Telur Segar', 'tipe_customer' => 'agen', 'alamat' => 'Pasar Cibungbulang', 'no_hp' => '081356789012', 'kontak_person' => 'Mas Agus'],
            ['nama_customer' => 'PT Resto Nusantara', 'tipe_customer' => 'korporat', 'alamat' => 'Jl. Sudirman No. 101, Jakarta', 'no_hp' => '021-7654321', 'kontak_person' => 'Bapak Adi'],
        ];

        foreach ($customers as $c) {
            Customer::create($c);
            $this->customerIds[] = Customer::where('nama_customer', $c['nama_customer'])->first()->id;
        }
        echo "Customer: " . Customer::count() . " created\n";
    }

    private function seedHargaTelur(): void
    {
        $prices = [
            'besar' => [2400, 2500, 2600],
            'sedang' => [2100, 2200, 2300],
            'kecil' => [1800, 1900, 2000],
            'retak' => [1200, 1300, 1400],
        ];

        $dates = [Carbon::now()->subDays(60)->format('Y-m-d'), Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->subDays(7)->format('Y-m-d')];
        $userId = $this->userIds['super_admin'][0];

        foreach ($prices as $grade => $history) {
            foreach ($history as $i => $harga) {
                HargaTelur::create([
                    'grade' => $grade, 'harga' => $harga,
                    'satuan' => 'per_butir', 'customer_id' => null,
                    'tanggal_mulai_berlaku' => $dates[$i], 'created_by' => $userId,
                ]);
            }
        }

        HargaTelur::create([
            'grade' => 'besar', 'harga' => 2450, 'satuan' => 'per_butir',
            'customer_id' => $this->customerIds[1],
            'tanggal_mulai_berlaku' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'created_by' => $userId,
        ]);

        echo "Harga Telur: " . HargaTelur::count() . " records\n";
    }

    private function seedStokTelurDanPenjualan(): void
    {
        $start = Carbon::now()->subDays(30);
        $userId = $this->userIds['petugas_kandang'][0];

        for ($d = 0; $d <= 30; $d += random_int(1, 2)) {
            $tgl = $start->copy()->addDays($d);
            $numTrans = random_int(1, 3);

            for ($t = 0; $t < $numTrans; $t++) {
                $grade = $this->gradeMap[array_rand($this->gradeMap)];
                $butir = random_int(100, 2000);
                $berat = round($butir * match ($grade) {
                    'besar' => 0.063, 'sedang' => 0.055, 'kecil' => 0.048, 'retak' => 0.050,
                }, 2);

                $custIdx = array_rand($this->customerIds);
                $cust = Customer::find($this->customerIds[$custIdx]);

                $stok = StokTelurKeluar::create([
                    'tanggal' => $tgl->format('Y-m-d'),
                    'kandang_id' => $this->kandangIds[array_rand($this->kandangIds)],
                    'jumlah_butir' => $butir, 'berat_kg' => $berat, 'grade' => $grade,
                    'tujuan' => $cust->nama_customer,
                    'no_referensi' => 'SJ-' . $tgl->format('Ymd') . '-' . str_pad($t + 1, 3, '0', STR_PAD_LEFT),
                    'keterangan' => null, 'input_by' => $userId,
                ]);

                $harga = HargaTelur::hargaBerlaku($cust->id, 'per_butir', $tgl->format('Y-m-d'));
                $hargaPerButir = $harga ? $harga->harga : random_int(1500, 2800);
                $total = $hargaPerButir * $butir;
                $statusBayar = random_int(0, 100) < 70 ? 'lunas' : 'belum_lunas';

                TransaksiPenjualan::create([
                    'tanggal' => $tgl->format('Y-m-d'),
                    'customer_id' => $cust->id,
                    'stok_telur_keluar_id' => $stok->id,
                    'grade' => $grade,
                    'jumlah_butir' => $butir,
                    'berat_kg' => $berat,
                    'harga_per_satuan' => $hargaPerButir,
                    'total_harga' => $total,
                    'status_pembayaran' => $statusBayar,
                    'no_invoice' => 'INV-' . $tgl->format('Ymd') . '-' . str_pad(StokTelurKeluar::count(), 4, '0', STR_PAD_LEFT),
                    'input_by' => $userId,
                ]);
            }
        }
        echo "Stok Telur Keluar: " . StokTelurKeluar::count() . " records\n";
        echo "Transaksi Penjualan: " . TransaksiPenjualan::count() . " records\n";
    }
}
