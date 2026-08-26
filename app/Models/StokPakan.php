<?php

namespace App\Models;

use App\Traits\KandangScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokPakan extends Model
{
    use KandangScope;
    protected $table = 'stok_pakan';

    protected $fillable = [
        'kandang_id',
        'jenis_pakan_id',
        'tipe',
        'jumlah_kg',
        'tanggal',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_kg' => 'float',
    ];

    public function jenisPakan(): BelongsTo
    {
        return $this->belongsTo(JenisPakan::class);
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
