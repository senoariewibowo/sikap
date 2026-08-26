<?php

namespace App\Models;

use App\Traits\KandangScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProduksiTelur extends Model
{
    use KandangScope;

    protected $table = 'produksi_telur';

    protected $fillable = [
        'kandang_id', 'tanggal', 'jumlah_butir',
        'karpet', 'sisa',
        'shift', 'status_setor', 'input_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_butir' => 'integer',
        'karpet' => 'integer',
        'sisa' => 'integer',
    ];

    public function kandang(): BelongsTo
    {
        return $this->belongsTo(Kandang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function setoran(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SetoranTelur::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(ProduksiFoto::class);
    }
}
