<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduksiFoto extends Model
{
    protected $table = 'produksi_foto';

    protected $fillable = ['produksi_telur_id', 'path'];

    public function produksiTelur(): BelongsTo
    {
        return $this->belongsTo(ProduksiTelur::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
