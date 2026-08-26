<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokTelurKeluar extends Model
{
    protected $table = 'stok_telur_keluar';

    protected $fillable = [
        'tanggal', 'gudang_id', 'customer_id', 'driver', 'jumlah_butir', 'berat_kg',
        'unit_jual', 'karpet', 'peti', 'no_referensi', 'keterangan', 'carryover_sisa', 'input_by',
        'ttd_pengirim', 'ttd_pengirim_at', 'ttd_pengirim_img',
        'ttd_mengetahui', 'ttd_mengetahui_at', 'ttd_mengetahui_img',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_butir' => 'integer',
        'berat_kg' => 'float',
        'karpet' => 'integer',
        'peti' => 'integer',
        'carryover_sisa' => 'boolean',
    ];

    public function gudang(): BelongsTo { return $this->belongsTo(Gudang::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'input_by'); }
    public function transaksi(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(TransaksiPenjualan::class); }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StokTelurKeluarDetail::class, 'stok_telur_keluar_id');
    }

    public function penjualanStokDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenjualanStok::class, 'stok_telur_keluar_id');
    }

    public function ttdPengirim(): BelongsTo { return $this->belongsTo(User::class, 'ttd_pengirim'); }
    public function ttdMengetahui(): BelongsTo { return $this->belongsTo(User::class, 'ttd_mengetahui'); }

    public function availableDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StokTelurKeluarDetail::class, 'stok_telur_keluar_id')->whereNull('carried_over_to_id');
    }

    public function recalcCarryoverFlag(): void
    {
        $availableDetails = $this->availableDetails()->select('id', 'jumlah_butir')->get();
        $total = $availableDetails->sum('jumlah_butir');
        $sold = $this->penjualanStokDetails()->whereIn('stok_telur_keluar_detail_id', $availableDetails->pluck('id'))->sum('jumlah_butir');
        $sisa = max(0, $total - $sold);
        $this->update(['carryover_sisa' => $this->driver && $sisa > 0]);
    }
}
