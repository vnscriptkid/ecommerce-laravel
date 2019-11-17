<?php

namespace App\Models;

use App\Models\Traits\HasPrice;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasPrice;

    // accessor
    public function getPriceAttribute($value)
    {
        return is_null($value) ? $this->product->price : $value;
    }

    // many to one
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function type()
    {
        return $this->hasOne(ProductVariationType::class, 'id', 'product_variation_type_id');
    }
}
