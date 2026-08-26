<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPakan extends Model
{
    protected $table = 'jenis_pakan';

    protected $fillable = ['nama', 'kategori', 'satuan', 'stok_minimal', 'harga'];

    public function stokPakan(): HasMany
    {
        return $this->hasMany(StokPakan::class);
    }

    public function stokSekarang(): float
    {
        $masuk = $this->stokPakan()->where('tipe', 'masuk')->sum('jumlah_kg');
        $keluar = $this->stokPakan()->where('tipe', 'keluar')->sum('jumlah_kg');
        $pemakaian = PemakaianPakan::where('jenis_pakan_id', $this->id)->sum('jumlah');
        return $masuk - $keluar - $pemakaian;
    }

    public function isStokMenipis(): bool
    {
        return $this->stok_minimal !== null && $this->stokSekarang() <= $this->stok_minimal;
    }

    public function nilaiStok(): float
    {
        return $this->harga > 0 ? $this->stokSekarang() * $this->harga : 0;
    }
}
