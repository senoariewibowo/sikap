<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepPakanDetail extends Model
{
    protected $table = 'resep_pakan_detail';

    protected $fillable = [
        'resep_pakan_id',
        'bahan_pakan_id',
        'jumlah',
        'catatan',
    ];

    public function resepPakan(): BelongsTo
    {
        return $this->belongsTo(ResepPakan::class);
    }

    public function bahanPakan(): BelongsTo
    {
        return $this->belongsTo(BahanPakan::class);
    }
}
