<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResepPakan extends Model
{
    protected $table = 'resep_pakan';

    protected $fillable = [
        'pakan_id',
        'nama_resep',
        'is_default',
        'keterangan',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function pakan(): BelongsTo
    {
        return $this->belongsTo(Pakan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ResepPakanDetail::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
