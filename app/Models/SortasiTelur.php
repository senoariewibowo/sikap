<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SortasiTelur extends Model
{
    use HasFactory;

    protected $table = 'sortasi_telur';

    protected $fillable = [
        'gudang_id',
        'kandang_id',
        'tanggal',
        'shift',
        'pecah',
        'retak',
        'kopong',
        'sisa',
        'catatan',
        'input_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

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

    public function detail(): HasMany
    {
        return $this->hasMany(SortasiTelurDetail::class, 'sortasi_telur_id');
    }

    public function butirMasuk(): int
    {
        return SetoranTelur::whereHas('produksiTelur', function ($q) {
            $q->whereDate('tanggal', $this->tanggal)->where('shift', $this->shift);
        })->where('gudang_id', $this->gudang_id)
          ->where('kandang_id', $this->kandang_id)
          ->sum('butir');
    }
}
