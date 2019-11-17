<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    // many to one
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
