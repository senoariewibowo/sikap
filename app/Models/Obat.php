<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'satuan',
        'stok_minimal',
        'status',
    ];

    public function stok(): HasMany
    {
        return $this->hasMany(ObatStok::class);
    }

    public function pemakaian(): HasMany
    {
        return $this->hasMany(ObatPemakaian::class);
    }

    public function stokGudang(int $gudangId): float
    {
        $stok = $this->stok()->where('gudang_id', $gudangId)->first();
        return $stok ? (float) $stok->jumlah : 0;
    }

    public function stokKandang(int $kandangId): float
    {
        $kandang = \App\Models\Kandang::find($kandangId);
        if (!$kandang || !$kandang->gudang_id) return 0;

        $masuk = $this->stokGudang($kandang->gudang_id);
        $pakai = $this->pemakaian()->where('kandang_id', $kandangId)->sum('jumlah');

        $otherKandangPakai = $this->pemakaian()
            ->where('kandang_id', '!=', $kandangId)
            ->whereHas('kandang', fn($q) => $q->where('gudang_id', $kandang->gudang_id))
            ->sum('jumlah');

        return (float) ($masuk - $pakai - $otherKandangPakai);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
