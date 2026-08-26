<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SortasiTelurDetail extends Model
{
    use HasFactory;

    protected $table = 'sortasi_telur_detail';

    protected $fillable = [
        'sortasi_telur_id',
        'butir',
        'karpet',
        'berat',
        'kode_peti',
    ];

    public function sortasiTelur(): BelongsTo
    {
        return $this->belongsTo(SortasiTelur::class, 'sortasi_telur_id');
    }

    public function stokTelurKeluarDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StokTelurKeluarDetail::class, 'sortasi_telur_detail_id');
    }
}
