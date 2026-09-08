<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = ['barcode', 'product_name', 'modal', 'group_id', 'is_active'];

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductGroup::class, 'group_id');
    }

    public function prices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id')->orderBy('tier');
    }

    public function priceForQty(int $qty): ?float
    {
        $price = $this->prices()
            ->where('min_qty', '<=', $qty)
            ->orderBy('min_qty', 'desc')
            ->first();

        return $price ? (float) $price->harga : null;
    }
}
