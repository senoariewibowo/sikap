<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanStok extends Model
{
    protected $table = 'penjualan_stok';

    protected $fillable = ['transaksi_penjualan_id', 'stok_telur_keluar_id', 'stok_telur_keluar_detail_id', 'jumlah_butir', 'berat_kg'];

    protected $casts = ['jumlah_butir' => 'integer', 'berat_kg' => 'float'];

    public function transaksi(): BelongsTo { return $this->belongsTo(TransaksiPenjualan::class, 'transaksi_penjualan_id'); }
    public function stokTelurKeluar(): BelongsTo { return $this->belongsTo(StokTelurKeluar::class); }
    public function stokTelurKeluarDetail(): BelongsTo { return $this->belongsTo(StokTelurKeluarDetail::class); }
}
