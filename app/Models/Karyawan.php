<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use SoftDeletes;

    protected $table = 'karyawan';

    protected $fillable = [
        'nik',
        'nama',
        'no_hp',
        'alamat',
        'jabatan',
        'tanggal_masuk',
        'status',
        'foto',
    ];

    protected $dates = ['deleted_at', 'tanggal_masuk'];

    public function kandang(): BelongsToMany
    {
        return $this->belongsToMany(Kandang::class, 'kandang_karyawan')
            ->withPivot('tanggal_mulai', 'tanggal_selesai', 'is_active')
            ->withTimestamps();
    }

    public function kandangAktif()
    {
        return $this->kandang()->wherePivot('is_active', true);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function gudang(): BelongsToMany
    {
        return $this->belongsToMany(Gudang::class, 'gudang_karyawan')
            ->withPivot('tanggal_mulai', 'tanggal_selesai', 'is_active')
            ->withTimestamps();
    }

    public function gudangAktif()
    {
        return $this->gudang()->wherePivot('is_active', true);
    }
}
