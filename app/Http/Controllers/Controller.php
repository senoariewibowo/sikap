<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;

abstract class Controller
{
    protected function getUserKandangIds(): array
    {
        return auth()->user()->kandangIds();
    }

    protected function getUserKandangs()
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return \App\Models\Kandang::orderBy('nama_kandang');
        }
        return \App\Models\Kandang::whereIn('id', $this->getUserKandangIds())->orderBy('nama_kandang');
    }

    protected function getUserGudangIds(): array
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return \App\Models\Gudang::pluck('id')->toArray();
        }
        if ($user->hasRole('petugas_gudang') && $user->gudang_id) {
            return [$user->gudang_id];
        }
        return [];
    }

    protected function getUserGudangs()
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return \App\Models\Gudang::orderBy('nama_gudang');
        }
        if ($user->hasRole('petugas_gudang') && $user->gudang_id) {
            return \App\Models\Gudang::where('id', $user->gudang_id)->orderBy('nama_gudang');
        }
        return \App\Models\Gudang::whereRaw('1 = 0');
    }

    protected function scopeByUser(Builder $query, string $column = 'kandang_id'): Builder
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return $query;
        }
        $ids = $this->getUserKandangIds();
        if (empty($ids)) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereIn($column, $ids);
    }
}
