# PRD - Sistem Inventory Kandang Ayam Petelur

**Nama Produk:** SIKAP (Sistem Informasi Kandang Ayam Petelur)
**Versi Dokumen:** 1.0
**Tanggal:** 14 Juli 2026
**Tech Stack:** Laravel (PHP), MySQL/MariaDB, Bootstrap/AdminLTE (opsional), Blade

---

## 1. Latar Belakang

Peternakan ayam petelur membutuhkan pencatatan yang rapi terhadap:
- Data kandang (lokasi/alamat, kapasitas, kondisi)
- Data karyawan yang bertanggung jawab per kandang
- Jumlah populasi ayam per kandang (masuk, mati, afkir)
- Produksi telur harian
- Stok pakan & obat/vitamin
- Laporan performa per kandang/periode

Saat ini pencatatan dilakukan manual (buku/Excel) sehingga rawan hilang data, sulit direkap, dan tidak real-time.

## 2. Tujuan

1. Mendigitalisasi pencatatan inventory kandang, ayam, karyawan, dan hasil produksi.
2. Memudahkan pemilik/manajer memantau performa tiap kandang secara real-time.
3. Menyediakan laporan produksi telur & mortalitas ayam per periode.
4. Mengatur penugasan karyawan ke kandang tertentu.

## 3. Target Pengguna (Role)

| Role | Deskripsi Hak Akses |
|---|---|
| **Super Admin/Owner** | Akses penuh: kelola kandang, karyawan, laporan, master data |
| **Manajer Kandang** | Kelola data kandang & karyawan yang menjadi tanggung jawabnya, input produksi |
| **Karyawan/Petugas Kandang** | Input data harian (produksi telur, pakan, kematian ayam) untuk kandang yang ditugaskan |
| **Owner/Viewer** | Hanya melihat dashboard & laporan (read-only) |

## 4. Ruang Lingkup (Scope)

### In-Scope
- Manajemen data kandang (CRUD, alamat, kapasitas, jumlah kandang per lokasi)
- Manajemen data karyawan (CRUD, penugasan ke kandang, riwayat penugasan)
- Manajemen populasi ayam (stok masuk, mati/afkir, mutasi antar kandang)
- Pencatatan produksi telur harian (jumlah, grade, berat rata-rata)
- Manajemen stok pakan & obat (masuk, keluar, sisa stok)
- Laporan & dashboard (grafik produksi, FCR sederhana, mortalitas)
- Autentikasi & role-based access control

### Out-of-Scope (Fase Berikutnya)
- Integrasi IoT sensor kandang (suhu, kelembaban otomatis)
- Modul penjualan/POS telur
- Modul payroll karyawan
- Aplikasi mobile native (fase awal cukup responsive web)

## 5. Modul & Fitur Detail

### 5.1 Modul Master Kandang
- CRUD data kandang: kode kandang, nama kandang, **alamat lengkap** (jalan, desa/kelurahan, kecamatan, kabupaten/kota, provinsi, kode pos), koordinat (lat/long opsional untuk peta)
- Kapasitas kandang (jumlah maksimal ayam)
- Tipe kandang (baterai/postal/closed house)
- Status kandang (aktif, renovasi, nonaktif)
- **Jumlah kandang per lokasi/cabang** direkap otomatis di dashboard
- Upload foto kandang

### 5.2 Modul Karyawan
- CRUD data karyawan: NIK, nama, no. HP, alamat, tanggal masuk kerja, status (aktif/nonaktif)
- Jabatan (Manajer Kandang, Petugas Kandang, Teknisi, dll.)
- Penugasan karyawan ke satu atau beberapa kandang (tabel relasi `kandang_karyawan`)
- Riwayat mutasi penugasan (histori pindah kandang)
- Foto profil & dokumen (KTP, kontrak kerja) — opsional upload

### 5.3 Modul Populasi Ayam
- Input ayam masuk (jumlah, tanggal, umur/DOC, sumber/supplier) per kandang
- Input ayam mati/afkir harian (jumlah, penyebab)
- Mutasi ayam antar kandang
- Rekap populasi real-time per kandang (populasi awal - mati - afkir + masuk = populasi saat ini)

### 5.4 Modul Produksi Telur
- Input produksi harian per kandang: jumlah telur (butir/kg), grade (besar/sedang/kecil/retak), tanggal, shift petugas
- Perhitungan otomatis **Hen Day Production (HDP%)** = jumlah telur / populasi ayam aktif x 100%
- Grafik tren produksi harian/mingguan/bulanan per kandang

### 5.5 Modul Stok Telur (Panen & Keluar)
Modul ini memisahkan antara **telur hasil panen (masuk ke stok gudang)** dan **telur keluar (dikirim/dijual/didistribusikan)**, sehingga sisa stok telur selalu akurat.

- **Telur Masuk (Panen Harian):** otomatis terisi dari input di Modul Produksi Telur (5.4) setiap kali petugas mencatat hasil panen per kandang per hari.
- **Telur Keluar (Distribusi/Penjualan):** input manual oleh admin/gudang setiap ada telur yang keluar, terdiri dari:
  - Tanggal keluar
  - Jumlah keluar (butir/kg), dipecah per grade (besar/sedang/kecil/retak)
  - Tujuan (nama pembeli/agen/gudang pusat/afkir-buang jika pecah)
  - Kandang asal (opsional, jika stok masih dilacak per kandang) atau dari stok gabungan gudang
  - Nomor referensi/surat jalan (opsional)
  - Petugas yang menginput
- **Stok Akhir Harian (otomatis terhitung):** `stok_akhir = stok_awal + telur_masuk(panen) - telur_keluar`
- **Kartu Stok Telur:** riwayat mutasi masuk/keluar per hari seperti kartu stok gudang, bisa difilter per kandang atau gabungan semua kandang
- **Notifikasi/alert:** jika stok telur menumpuk terlalu lama (indikasi telur belum terjual) atau stok keluar melebihi stok tersedia (validasi agar tidak minus)
- **Laporan rekap panen vs keluar:** grafik perbandingan jumlah panen harian vs jumlah keluar harian per periode, serta laporan stok telur per tanggal (export Excel/PDF)

### 5.6 Modul Pakan & Obat
- Master data jenis pakan/obat/vitamin
- Stok masuk (pembelian) dan stok keluar (pemakaian harian per kandang)
- Kalkulasi FCR (Feed Conversion Ratio) sederhana = total pakan (kg) / total telur (kg)
- Notifikasi stok menipis

### 5.7 Dashboard & Laporan
- Dashboard ringkasan: total kandang, total karyawan aktif, total populasi ayam, total produksi hari ini
- Laporan produksi per kandang/per periode (filter tanggal, export Excel/PDF)
- Laporan mortalitas ayam
- Laporan penggunaan pakan
- Peta sebaran kandang (jika koordinat diisi)

### 5.8 Autentikasi & User Management
- Login dengan role-based access (Laravel Breeze/Jetstream atau custom)
- Manajemen user & hak akses per role
- Log aktivitas (audit trail) untuk input/edit data penting

## 6. Rancangan Skema Database (Awal)

**Nama Database:** `db_sikap_kandang`
**DBMS:** MySQL / MariaDB
**Charset/Collation:** `utf8mb4` / `utf8mb4_unicode_ci`

Contoh konfigurasi koneksi di `.env` Laravel:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sikap_kandang
DB_USERNAME=root
DB_PASSWORD=
```

Contoh perintah pembuatan database:

```sql
CREATE DATABASE db_sikap_kandang
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

```
kandang
- id
- kode_kandang
- nama_kandang
- alamat_jalan
- desa_kelurahan
- kecamatan
- kabupaten_kota
- provinsi
- kode_pos
- latitude
- longitude
- kapasitas
- tipe_kandang
- status (aktif/renovasi/nonaktif)
- created_at, updated_at

karyawan
- id
- nik
- nama
- no_hp
- alamat
- jabatan
- tanggal_masuk
- status (aktif/nonaktif)
- created_at, updated_at

kandang_karyawan (pivot - penugasan)
- id
- kandang_id (FK)
- karyawan_id (FK)
- tanggal_mulai
- tanggal_selesai (nullable, jika masih aktif)
- is_active

populasi_ayam
- id
- kandang_id (FK)
- tanggal
- jumlah_masuk
- jumlah_mati
- jumlah_afkir
- keterangan
- created_by (FK users)

produksi_telur
- id
- kandang_id (FK)
- tanggal
- jumlah_butir
- berat_kg
- grade (besar/sedang/kecil/retak)
- shift
- input_by (FK users)

stok_telur_keluar
- id
- tanggal
- kandang_id (FK, nullable jika keluar dari stok gudang gabungan)
- jumlah_butir
- berat_kg
- grade (besar/sedang/kecil/retak)
- tujuan (nama pembeli/agen/gudang pusat/afkir-buang)
- no_referensi (surat jalan, opsional)
- keterangan
- input_by (FK users)

stok_pakan
- id
- kandang_id (FK, nullable jika stok gudang pusat)
- jenis_pakan_id (FK)
- tipe (masuk/keluar)
- jumlah_kg
- tanggal
- keterangan

jenis_pakan
- id
- nama
- satuan

users
- id
- name
- email
- password
- role_id (FK)
- karyawan_id (FK, nullable)

roles
- id
- nama_role (super_admin, manajer, petugas, viewer)
```

## 7. Alur Utama (User Flow Singkat)

1. Admin membuat data kandang lengkap dengan alamat & kapasitas.
2. Admin menambahkan data karyawan dan menugaskannya ke kandang tertentu.
3. Petugas kandang login harian → input populasi ayam (mati/afkir), produksi telur, dan pemakaian pakan.
4. Manajer kandang memantau dashboard kandang yang menjadi tanggung jawabnya.
5. Owner/Super Admin melihat laporan gabungan seluruh kandang dan mengekspor laporan periodik.

## 8. Kebutuhan Non-Fungsional

- **Framework:** Laravel 11.x, PHP 8.2+
- **Database:** MySQL/MariaDB
- **Frontend:** Blade + Bootstrap 5/AdminLTE atau Tailwind (sesuai preferensi tim)
- **Autentikasi:** Laravel Breeze dengan role-based middleware
- **Export laporan:** Laravel Excel (Maatwebsite) untuk Excel, DomPDF untuk PDF
- **Responsive:** Wajib bisa diakses via smartphone (petugas kandang input dari lapangan)
- **Keamanan:** CSRF protection bawaan Laravel, validasi input, log audit untuk input data produksi
- **Performa:** Dashboard harus tetap cepat meski data historis > 1 tahun (gunakan index pada kolom tanggal & kandang_id)

## 9. Metrik Keberhasilan

- Seluruh kandang dan karyawan tercatat digital dalam sistem
- Input data produksi harian dilakukan tanpa keterlambatan > 1 hari
- Laporan bulanan dapat digenerate otomatis tanpa rekap manual
- Pengurangan waktu pembuatan laporan dari manual (jam) menjadi otomatis (menit)

## 10. Roadmap Pengembangan (Usulan)

| Fase | Fitur | Estimasi |
|---|---|---|
| Fase 1 | Master kandang, karyawan, autentikasi & role | 2 minggu |
| Fase 2 | Modul populasi ayam & produksi telur | 2 minggu |
| Fase 3 | Modul pakan & obat + FCR | 1 minggu |
| Fase 4 | Dashboard, laporan, export Excel/PDF | 2 minggu |
| Fase 5 | Testing, UAT, deployment | 1 minggu |

## 11. Catatan Tambahan

- Struktur alamat kandang dibuat granular (jalan/desa/kecamatan/kabupaten/provinsi) agar mudah difilter per wilayah jika jumlah kandang bertambah banyak.
- Disarankan menambahkan fitur multi-cabang/multi-lokasi peternakan jika skala usaha berkembang ke beberapa daerah.
- Pertimbangkan soft delete pada tabel kandang & karyawan agar histori data tidak hilang saat kandang/karyawan dinonaktifkan.
