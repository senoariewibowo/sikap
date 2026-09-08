<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasirPayment extends Model
{
    protected $table = 'kasir_payments';

    protected $fillable = [
        'kasir_transaction_id',
        'tanggal',
        'metode_pembayaran',
        'jumlah',
        'keterangan',
        'created_by',
    ];

    public function transaksi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KasirTransaksi::class, 'kasir_transaction_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
