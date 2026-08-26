<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObatPemakaian extends Model
{
    protected $table = 'obat_pemakaian';

    protected $fillable = [
        'obat_id',
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

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
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
