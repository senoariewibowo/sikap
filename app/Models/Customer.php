<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customer';

    protected $fillable = [
        'nama_customer', 'tipe_customer', 'alamat',
        'no_hp', 'kontak_person', 'status',
    ];

    protected $dates = ['deleted_at'];

    public function hargaTelur(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HargaTelur::class);
    }

    public function transaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransaksiPenjualan::class);
    }
}
