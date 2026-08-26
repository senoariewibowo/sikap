<?php

namespace App\Traits;

trait GudangScope
{
    public function scopeForGudang($query, $column = 'gudang_id')
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('petugas_gudang')) {
            return $query;
        }

        $gudangId = $user->gudang_id;
        if (!$gudangId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where($column, $gudangId);
    }
}
