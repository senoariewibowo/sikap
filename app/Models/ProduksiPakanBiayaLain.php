<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduksiPakanBiayaLain extends Model
{
    protected $table = 'produksi_pakan_biaya_lain';

    protected $fillable = [
        'produksi_pakan_id',
        'nama_biaya',
        'jumlah',
    ];

    public function produksiPakan(): BelongsTo
    {
        return $this->belongsTo(ProduksiPakan::class);
    }
}
