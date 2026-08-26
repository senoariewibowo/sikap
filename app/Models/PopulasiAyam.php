<?php

namespace App\Models;

use App\Traits\KandangScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopulasiAyam extends Model
{
    use KandangScope;
    protected $table = 'populasi_ayam';

    protected $fillable = [
        'kandang_id',
        'tanggal',
        'jumlah_masuk',
        'jumlah_mati',
        'jumlah_afkir',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_masuk' => 'integer',
        'jumlah_mati' => 'integer',
        'jumlah_afkir' => 'integer',
    ];

    public function kandang(): BelongsTo
    {
        return $this->belongsTo(Kandang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
