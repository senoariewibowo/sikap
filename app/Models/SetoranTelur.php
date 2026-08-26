<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetoranTelur extends Model
{
    protected $table = 'setoran_telur';

    protected $fillable = [
        'produksi_telur_id',
        'gudang_id',
        'kandang_id',
        'tanggal_setor',
        'karpet',
        'peti',
        'berat',
        'butir',
        'selisih',
        'catatan',
        'input_by',
    ];

    protected $casts = [
        'tanggal_setor' => 'date',
        'karpet' => 'integer',
        'peti' => 'integer',
        'berat' => 'float',
        'butir' => 'integer',
        'selisih' => 'integer',
    ];

    public function produksiTelur(): BelongsTo
    {
        return $this->belongsTo(ProduksiTelur::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function kandang(): BelongsTo
    {
        return $this->belongsTo(Kandang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
