<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiEceran extends Model
{
    protected $table = 'transaksi_eceran';

    protected $fillable = [
        'tanggal', 'total_butir', 'satuan', 'berat_kg', 'karpet', 'harga_per_butir', 'total_harga', 'keterangan', 'input_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_butir' => 'integer',
        'berat_kg' => 'float',
        'harga_per_butir' => 'float',
        'total_harga' => 'float',
    ];

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(TransaksiEceranDetail::class, 'transaksi_eceran_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'input_by'); }
}
