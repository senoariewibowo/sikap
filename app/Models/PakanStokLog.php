<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PakanStokLog extends Model
{
    protected $table = 'pakan_stok_log';

    protected $fillable = [
        'pakan_stok_id',
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

    public function pakanStok(): BelongsTo
    {
        return $this->belongsTo(PakanStok::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
