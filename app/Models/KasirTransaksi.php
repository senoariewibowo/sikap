<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasirTransaksi extends Model
{
    protected $table = 'kasir_transactions';

    protected $fillable = [
        'no_struk', 'tanggal', 'total', 'bayar', 'kembalian',
        'metode_pembayaran', 'dp', 'kekurangan', 'status_pembayaran', 'nama_pembeli',
        'input_by'
    ];

    protected $casts = [
        'total' => 'float',
        'bayar' => 'float',
        'kembalian' => 'float',
        'dp' => 'float',
        'kekurangan' => 'float',
    ];

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KasirTransaksiDetail::class, 'kasir_transaction_id');
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KasirPayment::class, 'kasir_transaction_id')->orderBy('id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
