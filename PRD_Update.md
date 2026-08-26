# PRD - Sistem Rekap Telur Per Kandang (SIRETAK)

## 1. Overview

### 1.1 Latar Belakang
Peternakan ayam petelur memiliki banyak kandang yang menghasilkan telur setiap hari. Telur dari tiap kandang disetorkan ke gudang, begitu juga kebutuhan pakan dan obat yang didistribusikan dari gudang ke kandang. Saat ini pencatatan dilakukan manual sehingga rawan selisih data, sulit dipantau real-time, dan tidak ada rekap produksi per kandang yang akurat.

### 1.2 Tujuan
- Mencatat setoran telur dari masing-masing kandang ke gudang secara akurat dan real-time.
- Mencatat distribusi pakan dan obat dari gudang ke kandang (masuk & keluar) dalam tabel terpisah.
- Mencatat data populasi ayam per kandang (ayam masuk, mati, afkir).
- Menyediakan dashboard rekap yang informatif berbasis role.
- Menyediakan modul penjualan telur (khusus admin) di fase lanjutan.

### 1.3 Tech Stack
| Layer | Teknologi |
|---|---|
| Backend | Laravel (terbaru, LTS) |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL |
| Auth | Laravel Breeze/Sanctum (session-based) + Role middleware |
| Chart | Chart.js / ApexCharts (untuk dashboard) |
| Export | Laravel Excel (Maatwebsite) untuk rekap ke Excel/PDF |

---

## 2. User Roles & Hak Akses

| Fitur | Admin | Petugas Gudang | Petugas Kandang |
|---|---|---|---|
| Input setoran telur dari kandang | ✅ | ✅ | ❌ |
| Input pakan masuk (ke gudang) | ✅ | ✅ | ❌ |
| Input pakan keluar (gudang → kandang) | ✅ | ✅ | ❌ |
| Input obat masuk (ke gudang) | ✅ | ✅ | ❌ |
| Input obat keluar (gudang → kandang) | ✅ | ✅ | ❌ |
| Input ayam masuk | ✅ | ❌ | ✅ |
| Input ayam mati | ✅ | ❌ | ✅ |
| Input ayam afkir | ✅ | ❌ | ✅ |
| Input produksi telur (untuk disetor ke gudang) | ✅ | ❌ | ✅ |
| Tambah kandang baru | ✅ | ❌ | ❌ |
| Tambah gudang baru | ✅ | ❌ | ❌ |
| Kelola user & role | ✅ | ❌ | ❌ |
| Modul penjualan telur | ✅ | ❌ | ❌ |
| Lihat dashboard rekap keseluruhan | ✅ | Sebagian (gudang) | Sebagian (kandang sendiri) |
| Export laporan | ✅ | ✅ (scope gudang) | ✅ (scope kandang) |

### Catatan Alur Data Penting
- **Petugas kandang** mencatat *produksi telur harian* di kandangnya → status "belum disetor".
- **Petugas gudang** menerima setoran telur dari kandang → mencatat *penerimaan telur* → status berubah "sudah disetor" dan stok telur di gudang bertambah.
- Dengan kata lain, ada 2 tabel yang saling terhubung: `produksi_telur` (dicatat petugas kandang) dan `setoran_telur` (dicatat petugas gudang, referensi ke produksi_telur).

---

## 3. Flow Proses (Role-based)

### 3.1 Flow Petugas Kandang
```
Login → Pilih Kandang (sesuai assignment) 
     → Input Produksi Telur Harian (jumlah butir, rusak/retak, tanggal)
     → Input Ayam Masuk (jumlah, tanggal, sumber/asal)
     → Input Ayam Mati (jumlah, tanggal, penyebab opsional)
     → Input Ayam Afkir (jumlah, tanggal, alasan)
     → Lihat riwayat & status setoran (sudah/belum disetor ke gudang)
```

### 3.2 Flow Petugas Gudang
```
Login → Pilih Gudang (sesuai assignment)
     → Terima Setoran Telur dari Kandang (pilih kandang → pilih data produksi yang belum disetor → konfirmasi jumlah diterima)
     → Input Pakan Masuk ke Gudang (dari supplier: jenis pakan, jumlah, tanggal, supplier)
     → Input Pakan Keluar ke Kandang (pilih kandang tujuan: jenis pakan, jumlah, tanggal)
     → Input Obat Masuk ke Gudang (dari supplier: jenis obat, jumlah, tanggal, supplier)
     → Input Obat Keluar ke Kandang (pilih kandang tujuan: jenis obat, jumlah, tanggal)
     → Lihat stok telur, pakan, obat real-time di gudang
```

### 3.3 Flow Admin
```
Login → Dashboard Global (semua kandang & gudang)
     → Kelola Master Data: Tambah/Edit/Hapus Kandang
     → Kelola Master Data: Tambah/Edit/Hapus Gudang
     → Kelola User & Role (assign petugas ke kandang/gudang)
     → Semua akses Petugas Gudang & Petugas Kandang (full access)
     → Modul Penjualan Telur (input transaksi jual, harga, buyer, qty)
     → Export/Cetak laporan rekap (harian/mingguan/bulanan) per kandang/gudang
```

---

## 4. Struktur Database (ERD Ringkas)

### 4.1 Master Tables

**users**
| Field | Type | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| email | varchar unique | |
| password | varchar | |
| role | enum('admin','petugas_gudang','petugas_kandang') | |
| kandang_id | FK nullable | jika role petugas_kandang |
| gudang_id | FK nullable | jika role petugas_gudang |
| status | enum('aktif','nonaktif') | |
| timestamps | | |

**kandangs**
| Field | Type | Keterangan |
|---|---|---|
| id | bigint PK | |
| kode_kandang | varchar unique | |
| nama_kandang | varchar | |
| lokasi | varchar | |
| kapasitas | int | |
| status | enum('aktif','nonaktif') | |
| timestamps | | |

**gudangs**
| Field | Type | Keterangan |
|---|---|---|
| id | bigint PK | |
| kode_gudang | varchar unique | |
| nama_gudang | varchar | |
| lokasi | varchar | |
| status | enum('aktif','nonaktif') | |
| timestamps | | |

### 4.2 Tabel Ayam (Petugas Kandang)

**ayam_masuk**
| Field | Type |
|---|---|
| id | bigint PK |
| kandang_id | FK |
| jumlah | int |
| tanggal | date |
| sumber | varchar |
| keterangan | text nullable |
| input_by (user_id) | FK |
| timestamps | |

**ayam_mati**
| Field | Type |
|---|---|
| id | bigint PK |
| kandang_id | FK |
| jumlah | int |
| tanggal | date |
| penyebab | varchar nullable |
| keterangan | text nullable |
| input_by (user_id) | FK |
| timestamps | |

**ayam_afkir**
| Field | Type |
|---|---|
| id | bigint PK |
| kandang_id | FK |
| jumlah | int |
| tanggal | date |
| alasan | varchar nullable |
| keterangan | text nullable |
| input_by (user_id) | FK |
| timestamps | |

### 4.3 Tabel Telur

**produksi_telur** (input petugas kandang)
| Field | Type |
|---|---|
| id | bigint PK |
| kandang_id | FK |
| tanggal | date |
| jumlah_butir | int |
| jumlah_rusak | int default 0 |
| jumlah_retak | int default 0 |
| status_setor | enum('belum_disetor','sudah_disetor') default 'belum_disetor' |
| input_by (user_id) | FK |
| timestamps | |

**setoran_telur** (input petugas gudang, referensi produksi_telur)
| Field | Type |
|---|---|
| id | bigint PK |
| produksi_telur_id | FK |
| gudang_id | FK |
| kandang_id | FK |
| tanggal_setor | date |
| jumlah_diterima | int |
| selisih | int (auto: jumlah_diterima - jumlah_butir produksi) |
| catatan | text nullable |
| input_by (user_id) | FK |
| timestamps | |

### 4.4 Tabel Pakan (Terpisah, Petugas Gudang)

**pakan_masuk**
| Field | Type |
|---|---|
| id | bigint PK |
| gudang_id | FK |
| jenis_pakan | varchar |
| jumlah | decimal (kg/sak) |
| satuan | varchar |
| tanggal | date |
| supplier | varchar nullable |
| no_referensi | varchar nullable |
| input_by (user_id) | FK |
| timestamps | |

**pakan_keluar**
| Field | Type |
|---|---|
| id | bigint PK |
| gudang_id | FK |
| kandang_id | FK (tujuan) |
| jenis_pakan | varchar |
| jumlah | decimal |
| satuan | varchar |
| tanggal | date |
| keterangan | text nullable |
| input_by (user_id) | FK |
| timestamps | |

### 4.5 Tabel Obat (Terpisah, Petugas Gudang)

**obat_masuk**
| Field | Type |
|---|---|
| id | bigint PK |
| gudang_id | FK |
| nama_obat | varchar |
| jumlah | decimal |
| satuan | varchar |
| tanggal | date |
| supplier | varchar nullable |
| no_referensi | varchar nullable |
| input_by (user_id) | FK |
| timestamps | |

**obat_keluar**
| Field | Type |
|---|---|
| id | bigint PK |
| gudang_id | FK |
| kandang_id | FK (tujuan) |
| nama_obat | varchar |
| jumlah | decimal |
| satuan | varchar |
| tanggal | date |
| keterangan | text nullable |
| input_by (user_id) | FK |
| timestamps | |

### 4.6 Tabel Penjualan (Fase Lanjutan, Admin only)

**penjualan_telur**
| Field | Type |
|---|---|
| id | bigint PK |
| gudang_id | FK |
| tanggal | date |
| nama_pembeli | varchar |
| jumlah_butir | int |
| harga_per_butir | decimal |
| total_harga | decimal (auto) |
| status_bayar | enum('lunas','belum_lunas') |
| keterangan | text nullable |
| input_by (user_id) | FK |
| timestamps | |

---

## 5. Modul & Halaman Dashboard

### 5.1 Dashboard Admin
- Ringkasan total kandang aktif & gudang aktif
- Grafik tren produksi telur (harian/mingguan/bulanan, semua kandang)
- Grafik stok pakan & obat per gudang
- Grafik populasi ayam (masuk vs mati vs afkir) per kandang
- Tabel selisih setoran telur (produksi vs diterima gudang)
- Ringkasan penjualan telur (revenue, qty terjual)
- Menu CRUD: Kandang, Gudang, User

### 5.2 Dashboard Petugas Gudang
- Stok telur real-time di gudangnya
- Stok pakan & obat real-time (per jenis)
- Daftar setoran telur pending dari kandang-kandang
- Form cepat: terima setoran, input pakan masuk/keluar, input obat masuk/keluar
- Riwayat transaksi (filter tanggal, kandang, jenis)

### 5.3 Dashboard Petugas Kandang
- Ringkasan populasi ayam saat ini (total - mati - afkir)
- Grafik produksi telur harian kandangnya
- Status setoran (belum/sudah disetor)
- Form cepat: input produksi telur, ayam masuk, ayam mati, ayam afkir
- Riwayat input kandangnya sendiri

---

## 6. Business Rules

1. Setiap user hanya bisa input data untuk kandang/gudang yang menjadi assignment-nya (kecuali admin).
2. Produksi telur yang sudah berstatus `sudah_disetor` tidak bisa diedit oleh petugas kandang (lock), hanya admin yang bisa override.
3. Pakan keluar & obat keluar tidak boleh melebihi stok yang tersedia di gudang (validasi stok).
4. Selisih setoran telur (produksi vs diterima) dihitung otomatis dan ditampilkan sebagai indikator warning jika > toleransi tertentu (misal >2%).
5. Populasi ayam aktif = akumulasi ayam masuk - akumulasi ayam mati - akumulasi ayam afkir, dihitung per kandang.
6. Semua input wajib tercatat `input_by` dan `timestamps` untuk audit trail.
7. Soft delete diterapkan pada seluruh tabel transaksi untuk menjaga histori data.

---

## 7. Non-Functional Requirements

- **Autentikasi & Otorisasi**: Middleware role-based (admin, petugas_gudang, petugas_kandang) di setiap route.
- **Responsive UI**: Tailwind CSS, mobile-friendly untuk input di lapangan (kandang/gudang).
- **Validasi**: Server-side validation (Laravel Form Request) untuk semua input transaksi.
- **Audit Trail**: Log siapa input, kapan, dan histori perubahan (activity log/Spatie Laravel Activitylog opsional).
- **Export**: Excel/PDF export untuk laporan rekap per periode.
- **Performance**: Query rekap menggunakan index pada `kandang_id`, `gudang_id`, `tanggal` untuk performa dashboard.
- **Backup**: Backup database berkala (harian).

---

## 8. Roadmap Pengembangan

**Fase 1 (MVP)**
- Auth & role management
- CRUD kandang & gudang
- Input produksi telur, ayam masuk/mati/afkir (petugas kandang)
- Input setoran telur, pakan masuk/keluar, obat masuk/keluar (petugas gudang)
- Dashboard dasar per role

**Fase 2**
- Modul penjualan telur (admin)
- Export laporan Excel/PDF
- Notifikasi (misal stok pakan/obat menipis)

**Fase 3**
- Notifikasi WhatsApp/email untuk setoran & stok kritis
- Multi-level approval (opsional)
- API untuk integrasi aplikasi mobile

---

## 9. Lampiran: Relasi Antar Tabel (Ringkas)

```
kandangs 1---N produksi_telur
kandangs 1---N ayam_masuk / ayam_mati / ayam_afkir
kandangs 1---N pakan_keluar / obat_keluar (sebagai tujuan)
gudangs  1---N pakan_masuk / obat_masuk
gudangs  1---N pakan_keluar / obat_keluar (sebagai asal)
gudangs  1---N setoran_telur / penjualan_telur
produksi_telur 1---1 setoran_telur
users    1---N (semua tabel transaksi via input_by)
users    N---1 kandangs (untuk petugas_kandang)
users    N---1 gudangs (untuk petugas_gudang)
```
