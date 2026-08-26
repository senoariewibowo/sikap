<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gudang extends Model
{
    protected $table = 'gudang';

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'lokasi',
        'status',
    ];

    public function setoranTelur(): HasMany
    {
        return $this->hasMany(SetoranTelur::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function kandangs(): HasMany
    {
        return $this->hasMany(Kandang::class);
    }

    public function karyawan(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Karyawan::class, 'gudang_karyawan')
            ->withPivot('tanggal_mulai', 'tanggal_selesai', 'is_active')
            ->withTimestamps();
    }
}
