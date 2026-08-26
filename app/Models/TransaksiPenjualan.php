<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiPenjualan extends Model
{
    protected $table = 'transaksi_penjualan';

    protected $fillable = [
        'tanggal', 'customer_id', 'stok_telur_keluar_id',
        'satuan', 'jumlah_satuan',
        'jumlah_butir', 'berat_kg', 'harga_per_satuan', 'total_harga',
        'dp', 'status_pembayaran', 'metode_pembayaran', 'catatan_pembayaran', 'no_invoice', 'input_by',
        'ttd_petugas', 'ttd_petugas_at', 'ttd_petugas_img',
    ];

    protected $casts = [
        'tanggal' => 'date', 'jumlah_butir' => 'integer', 'berat_kg' => 'float',
        'harga_per_satuan' => 'float', 'total_harga' => 'float',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function stokTelurKeluar(): BelongsTo { return $this->belongsTo(StokTelurKeluar::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'input_by'); }
    public function ttdPetugas(): BelongsTo { return $this->belongsTo(User::class, 'ttd_petugas'); }

    public function stokDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenjualanStok::class, 'transaksi_penjualan_id');
    }
}
