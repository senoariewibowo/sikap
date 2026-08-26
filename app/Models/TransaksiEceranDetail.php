<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiEceranDetail extends Model
{
    protected $table = 'transaksi_eceran_detail';

    protected $fillable = ['transaksi_eceran_id', 'stok_telur_eceran_id', 'jumlah_butir'];

    protected $casts = ['jumlah_butir' => 'integer'];

    public function transaksi(): BelongsTo { return $this->belongsTo(TransaksiEceran::class, 'transaksi_eceran_id'); }
    public function stokEceran(): BelongsTo { return $this->belongsTo(StokTelurEceran::class, 'stok_telur_eceran_id'); }
}
