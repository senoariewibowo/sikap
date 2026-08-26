<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokTelurKeluarDetail extends Model
{
    protected $table = 'stok_telur_keluar_details';

    protected $fillable = [
        'stok_telur_keluar_id',
        'sortasi_telur_detail_id',
        'jumlah_butir',
        'berat_kg',
        'karpet',
        'peti',
        'keterangan',
        'carried_over_to_id',
    ];

    protected $casts = [
        'jumlah_butir' => 'integer',
        'berat_kg' => 'float',
        'karpet' => 'integer',
        'peti' => 'integer',
    ];

    public function stokTelurKeluar(): BelongsTo
    {
        return $this->belongsTo(StokTelurKeluar::class, 'stok_telur_keluar_id');
    }

    public function sortasiDetail(): BelongsTo
    {
        return $this->belongsTo(SortasiTelurDetail::class, 'sortasi_telur_detail_id');
    }

    public function sortasiTelurDetail(): BelongsTo
    {
        return $this->belongsTo(SortasiTelurDetail::class, 'sortasi_telur_detail_id');
    }

    public function carriedOverTo(): BelongsTo
    {
        return $this->belongsTo(StokTelurKeluar::class, 'carried_over_to_id');
    }

    public function penjualanStok(): HasMany
    {
        return $this->hasMany(PenjualanStok::class, 'stok_telur_keluar_detail_id');
    }
}
