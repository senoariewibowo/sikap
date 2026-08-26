# PRD - Modul Penjualan Telur (Customer & Harga Telur)

**Nama Produk:** SIKAP (Sistem Informasi Kandang Ayam Petelur) - Modul Penjualan
**Versi Dokumen:** 1.0
**Tanggal:** 14 Juli 2026
**Status:** Fase 2 - Pengembangan Lanjutan (terintegrasi dengan PRD utama SIKAP)
**Tech Stack:** Laravel (PHP), MySQL/MariaDB (`db_sikap_kandang`)

---

## 1. Latar Belakang

Modul inventory utama SIKAP sudah mencatat telur yang **keluar dari stok** (`stok_telur_keluar`), namun belum mencatat:
- Ke customer/pelanggan mana telur tersebut dijual
- Berapa harga jual per grade telur saat itu
- Berapa total nilai transaksi penjualan

Modul ini melengkapi bagian tersebut agar SIKAP bisa menjadi sistem pencatatan penjualan telur yang lengkap, bukan hanya pencatatan stok keluar.

## 2. Tujuan

1. Mencatat data pelanggan/customer secara terstruktur.
2. Mencatat histori harga jual telur per grade yang bisa berubah dari waktu ke waktu.
3. Menghubungkan setiap telur yang keluar dengan customer & harga, sehingga otomatis menghasilkan nilai transaksi penjualan.
4. Menyediakan laporan omzet penjualan per customer, per periode, dan per grade telur.

## 3. Ruang Lingkup (Scope)

### In-Scope
- Master data customer/pelanggan
- Master data harga telur per grade (dengan histori perubahan harga)
- Transaksi penjualan yang terhubung dengan stok telur keluar
- Laporan penjualan & omzet

### Out-of-Scope (Fase Berikutnya)
- Invoice/kwitansi cetak otomatis
- Pembayaran/piutang customer (modul keuangan)
- Diskon/promo otomatis

## 4. Fitur Detail

### 4.1 Master Customer/Pelanggan
- CRUD data customer:
  - Nama customer/perusahaan
  - Tipe customer (Agen, Pengepul, Retail, Perusahaan/Korporat)
  - Alamat
  - No. HP/telepon & kontak person
  - Harga khusus per customer (opsional, jika ada kesepakatan harga beda dari harga umum)
  - Status (aktif/nonaktif)

### 4.2 Master Harga Telur
- CRUD harga jual telur per grade (besar/sedang/kecil/retak)
- Setiap perubahan harga tersimpan sebagai **histori harga baru** (bukan menimpa harga lama), sehingga transaksi lama tetap memakai harga yang berlaku saat itu
- Field: grade, harga per butir/kg, tanggal mulai berlaku, dibuat oleh (user)
- Bisa diset harga umum (default) dan harga khusus per customer

### 4.3 Transaksi Penjualan
- Dibuat berdasarkan data `stok_telur_keluar` yang sudah tercatat di modul inventory, ditambah:
  - Customer tujuan (pilih dari master customer)
  - Harga yang dipakai (otomatis ambil dari harga berlaku pada tanggal transaksi, atau harga khusus customer jika ada)
  - Total nilai transaksi = jumlah keluar x harga per grade
  - Status pembayaran sederhana (Lunas/Belum Lunas) - opsional untuk pelacakan awal, bukan modul keuangan penuh
  - Nomor invoice/referensi (opsional)

### 4.4 Laporan Penjualan
- Omzet penjualan per customer (per periode: harian/mingguan/bulanan)
- Omzet penjualan per grade telur
- Grafik tren penjualan dari waktu ke waktu
- Rekap piutang/belum lunas (jika status pembayaran diaktifkan)
- Export laporan ke Excel/PDF

## 5. Rancangan Skema Database Tambahan

```
customer
- id
- nama_customer
- tipe_customer (agen/pengepul/retail/korporat)
- alamat
- no_hp
- kontak_person
- status (aktif/nonaktif)
- created_at, updated_at

harga_telur
- id
- grade (besar/sedang/kecil/retak)
- harga
- satuan (per_butir/per_kg)
- customer_id (FK, nullable — jika diisi berarti harga khusus untuk customer tsb)
- tanggal_mulai_berlaku
- created_by (FK users)
- created_at, updated_at

transaksi_penjualan
- id
- tanggal
- customer_id (FK)
- stok_telur_keluar_id (FK, relasi ke data telur keluar di modul inventory)
- grade
- jumlah_butir / jumlah_kg
- harga_per_satuan (snapshot harga saat transaksi, bukan referensi live ke master harga)
- total_harga
- status_pembayaran (lunas/belum_lunas)
- no_invoice (opsional)
- input_by (FK users)
- created_at, updated_at
```

> **Catatan teknis:** `harga_per_satuan` di tabel `transaksi_penjualan` sengaja disimpan sebagai snapshot (bukan hanya foreign key ke `harga_telur`), agar jika harga di master berubah di kemudian hari, data transaksi lama tidak ikut berubah.

## 6. Alur Utama (User Flow)

1. Admin/owner mengatur harga telur per grade di Master Harga Telur (harga umum atau harga khusus per customer).
2. Admin mendaftarkan customer baru di Master Customer.
3. Saat ada telur keluar (dari modul inventory `stok_telur_keluar`), admin/petugas gudang memilih customer tujuan dan sistem otomatis mengambil harga yang berlaku → membentuk transaksi penjualan.
4. Owner memantau laporan omzet penjualan per customer/periode/grade di dashboard.

## 7. Ketergantungan (Dependencies)

Modul ini **bergantung** pada modul inventory utama SIKAP, khususnya tabel `stok_telur_keluar` (lihat PRD utama: `PRD.md`, bagian 5.5 Modul Stok Telur). Modul penjualan tidak bisa berdiri sendiri tanpa data telur keluar dari modul tersebut.

## 8. Metrik Keberhasilan

- Setiap telur yang keluar dari stok tercatat dengan customer & harga yang jelas
- Owner bisa melihat omzet penjualan real-time tanpa rekap manual
- Histori harga tidak menyebabkan data transaksi lama berubah retroaktif

## 9. Roadmap

| Fase | Fitur | Estimasi |
|---|---|---|
| Fase 2.1 | Master customer & master harga telur | 1 minggu |
| Fase 2.2 | Transaksi penjualan terhubung stok keluar | 1 minggu |
| Fase 2.3 | Laporan omzet & export | 1 minggu |
