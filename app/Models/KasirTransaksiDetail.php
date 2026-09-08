<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasirTransaksiDetail extends Model
{
    protected $table = 'kasir_transaction_details';

    protected $fillable = ['kasir_transaction_id', 'product_id', 'product_name', 'harga', 'qty', 'subtotal', 'modal'];

    protected $casts = [
        'harga' => 'float',
        'subtotal' => 'float',
        'modal' => 'float',
        'qty' => 'integer',
    ];

    public function transaksi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KasirTransaksi::class, 'kasir_transaction_id');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
