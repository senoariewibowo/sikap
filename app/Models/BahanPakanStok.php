<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanPakanStok extends Model
{
    protected $table = 'bahan_pakan_stok';

    protected $fillable = [
        'bahan_pakan_id',
        'gudang_id',
        'jumlah',
        'tanggal',
        'keterangan',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function bahanPakan(): BelongsTo
    {
        return $this->belongsTo(BahanPakan::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BahanPakanStokLog::class)->orderBy('tanggal', 'desc');
    }
}
