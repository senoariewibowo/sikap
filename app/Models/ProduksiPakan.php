<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProduksiPakan extends Model
{
    protected $table = 'produksi_pakan';

    protected $fillable = [
        'kode',
        'tanggal',
        'pakan_id',
        'resep_pakan_id',
        'gudang_id',
        'jumlah',
        'hpp_bahan',
        'biaya_lain',
        'hpp_total',
        'hpp_per_satuan',
        'keterangan',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function pakan(): BelongsTo
    {
        return $this->belongsTo(Pakan::class);
    }

    public function resepPakan(): BelongsTo
    {
        return $this->belongsTo(ResepPakan::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ProduksiPakanDetail::class);
    }

    public function biayaLain(): HasMany
    {
        return $this->hasMany(ProduksiPakanBiayaLain::class);
    }
}
