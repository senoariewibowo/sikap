<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pakan extends Model
{
    protected $table = 'pakan';

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
        return $this->hasMany(PakanStok::class);
    }

    public function distribusi(): HasMany
    {
        return $this->hasMany(PakanDistribusi::class);
    }

    public function pemakaian(): HasMany
    {
        return $this->hasMany(PakanPemakaian::class);
    }

    public function stokGudang(int $gudangId): float
    {
        $stok = $this->stok()->where('gudang_id', $gudangId)->first();
        $masuk = $stok ? (float) $stok->jumlah : 0;
        $distribusi = $this->distribusi()
            ->where('gudang_id', $gudangId)
            ->whereIn('status', ['dikirim', 'diterima'])
            ->sum('jumlah');
        return (float) ($masuk - $distribusi);
    }

    public function stokKandang(int $kandangId): float
    {
        $diterima = $this->distribusi()
            ->where('kandang_id', $kandangId)
            ->where('status', 'diterima')
            ->sum('jumlah');
        $pakai = $this->pemakaian()
            ->where('kandang_id', $kandangId)
            ->sum('jumlah');
        return (float) ($diterima - $pakai);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
