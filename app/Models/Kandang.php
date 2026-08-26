<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kandang extends Model
{
    use SoftDeletes;

    protected $table = 'kandang';

    protected $fillable = [
        'kode_kandang',
        'nama_kandang',
        'initial',
        'gudang_id',
        'alamat_jalan',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'latitude',
        'longitude',
        'kapasitas',
        'tipe_kandang',
        'status',
        'foto',
    ];

    protected $dates = ['deleted_at'];

    public function karyawan(): BelongsToMany
    {
        return $this->belongsToMany(Karyawan::class, 'kandang_karyawan')
            ->withPivot('tanggal_mulai', 'tanggal_selesai', 'is_active')
            ->withTimestamps();
    }

    public function alamatLengkap(): string
    {
        return implode(', ', array_filter([
            $this->alamat_jalan,
            $this->desa_kelurahan,
            $this->kecamatan,
            $this->kabupaten_kota,
            $this->provinsi,
            $this->kode_pos,
        ]));
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function populasiAyam(): HasMany
    {
        return $this->hasMany(PopulasiAyam::class);
    }

    public function produksiTelur(): HasMany
    {
        return $this->hasMany(ProduksiTelur::class);
    }

    public function populasiSekarang(): int
    {
        return $this->populasiAyam()->sum('jumlah_masuk')
            - $this->populasiAyam()->sum('jumlah_mati')
            - $this->populasiAyam()->sum('jumlah_afkir');
    }
}
