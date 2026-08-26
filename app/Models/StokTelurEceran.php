<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokTelurEceran extends Model
{
    protected $table = 'stok_telur_eceran';

    protected $fillable = [
        'tanggal', 'gudang_id', 'sortasi_telur_detail_id', 'jumlah_butir', 'unit_jual', 'berat_kg',
        'karpet', 'peti', 'no_referensi', 'keterangan', 'input_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_butir' => 'integer',
        'berat_kg' => 'float',
        'karpet' => 'integer',
        'peti' => 'integer',
    ];

    public function gudang(): BelongsTo { return $this->belongsTo(Gudang::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'input_by'); }
    public function transaksis(): HasMany { return $this->hasMany(TransaksiEceranDetail::class, 'stok_telur_eceran_id'); }
    public function sortasiDetail(): BelongsTo { return $this->belongsTo(SortasiTelurDetail::class, 'sortasi_telur_detail_id'); }
}
