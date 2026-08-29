<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduksiPakanDetail extends Model
{
    protected $table = 'produksi_pakan_detail';

    protected $fillable = [
        'produksi_pakan_id',
        'bahan_pakan_id',
        'jumlah_pakai',
        'harga_satuan',
        'subtotal',
    ];

    public function produksiPakan(): BelongsTo
    {
        return $this->belongsTo(ProduksiPakan::class);
    }

    public function bahanPakan(): BelongsTo
    {
        return $this->belongsTo(BahanPakan::class);
    }
}
