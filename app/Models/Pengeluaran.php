<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';

    protected $fillable = [
        'tanggal', 'kategori_pengeluaran_id', 'kandang_id',
        'jumlah', 'keterangan', 'bukti', 'created_by',
    ];

    protected $casts = ['tanggal' => 'date', 'jumlah' => 'float'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
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
