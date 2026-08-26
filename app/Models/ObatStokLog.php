<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObatStokLog extends Model
{
    protected $table = 'obat_stok_log';

    protected $fillable = [
        'obat_stok_id',
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

    public function obatStok(): BelongsTo
    {
        return $this->belongsTo(ObatStok::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
