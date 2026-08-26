<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPengeluaran extends Model
{
    protected $table = 'kategori_pengeluaran';
    protected $fillable = ['nama'];

    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
