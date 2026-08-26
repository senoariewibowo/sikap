<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaTelur extends Model
{
    protected $table = 'harga_telur';

    protected $fillable = ['harga', 'satuan', 'customer_id', 'tanggal_mulai_berlaku', 'created_by'];

    protected $casts = ['tanggal_mulai_berlaku' => 'date', 'harga' => 'float'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public static function hargaBerlaku(?int $customerId, string $satuan, ?string $tanggal = null): ?self
    {
        $tanggal = $tanggal ?? now()->format('Y-m-d');
        $query = static::where('tanggal_mulai_berlaku', '<=', $tanggal)
            ->where('satuan', $satuan)
            ->orderBy('tanggal_mulai_berlaku', 'desc');
        if ($customerId) {
            $khusus = (clone $query)->where('customer_id', $customerId)->first();
            if ($khusus) return $khusus;
        }
        return (clone $query)->whereNull('customer_id')->first();
    }

    public static function hargaEceran(string $satuan, ?string $tanggal = null): ?self
    {
        $tanggal = $tanggal ?? now()->format('Y-m-d');
        return static::whereNull('customer_id')
            ->where('satuan', $satuan)
            ->where('tanggal_mulai_berlaku', '<=', $tanggal)
            ->orderBy('tanggal_mulai_berlaku', 'desc')
            ->first();
    }
}
