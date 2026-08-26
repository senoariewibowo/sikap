<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PakanStok extends Model
{
    protected $table = 'pakan_stok';

    protected $fillable = [
        'pakan_id',
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

    public function pakan(): BelongsTo
    {
        return $this->belongsTo(Pakan::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PakanStokLog::class)->orderBy('tanggal', 'desc');
    }
}
