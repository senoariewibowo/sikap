<?php

namespace App\Traits;

trait KandangScope
{
    public function scopeForUser($query, $column = 'kandang_id')
    {
        $user = auth()->user();
        if (!$user || $user->hasRole('super_admin')) {
            return $query;
        }

        $ids = $user->kandangIds();
        if (empty($ids)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($column, $ids);
    }
}
