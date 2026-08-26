<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PakanPemakaian extends Model
{
    protected $table = 'pakan_pemakaian';

    protected $fillable = [
        'pakan_id',
        'kandang_id',
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

    public function kandang(): BelongsTo
    {
        return $this->belongsTo(Kandang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
