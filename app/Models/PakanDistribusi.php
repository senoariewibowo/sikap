<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PakanDistribusi extends Model
{
    protected $table = 'pakan_distribusi';

    protected $fillable = [
        'pakan_id',
        'gudang_id',
        'kandang_id',
        'jumlah',
        'tanggal_kirim',
        'status',
        'created_by',
        'diterima_oleh',
        'tanggal_diterima',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kirim' => 'date',
            'tanggal_diterima' => 'date',
        ];
    }

    public function pakan(): BelongsTo
    {
        return $this->belongsTo(Pakan::class);
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }
}
