<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanPakanStokLog extends Model
{
    protected $table = 'bahan_pakan_stok_log';

    protected $fillable = [
        'bahan_pakan_stok_id',
        'jumlah_lama',
        'jumlah_baru',
        'total',
        'tanggal',
        'keterangan',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function bahanPakanStok(): BelongsTo
    {
        return $this->belongsTo(BahanPakanStok::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
