<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanPakan extends Model
{
    protected $table = 'bahan_pakan';

    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'harga',
        'stok_minimal',
        'status',
    ];

    public function stok(): HasMany
    {
        return $this->hasMany(BahanPakanStok::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
