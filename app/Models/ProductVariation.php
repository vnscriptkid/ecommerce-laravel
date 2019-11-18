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

    public function priceVaries()
    {
        return $this->price !== $this->product->price;
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

    // one to many
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stock()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_stock_view')
            ->withPivot(['stock', 'in_stock']);
    }

    public function stockCount()
    {
        return (int) $this->stock->first()->pivot->stock;
    }

    public function inStock()
    {
        return (bool) $this->stock->first()->pivot->in_stock;
    }
}
