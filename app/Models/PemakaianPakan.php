<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianPakan extends Model
{
    protected $table = 'pemakaian_pakan';

    protected $fillable = [
        'jenis_pakan_id', 'kandang_id', 'jumlah', 'tanggal',
        'keterangan', 'created_by',
    ];

    protected $casts = ['tanggal' => 'date', 'jumlah' => 'float'];

    public function jenisPakan(): BelongsTo { return $this->belongsTo(JenisPakan::class); }
    public function kandang(): BelongsTo { return $this->belongsTo(Kandang::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
