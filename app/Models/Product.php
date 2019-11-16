<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // many to many
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    // filter all product with matching category name
    public function scopeWithCategory(Builder $builder, string $categoryName = null)
    {
        if (is_null($categoryName) || !is_string($categoryName)) {
            return $builder;
        }
        return $builder->whereHas('categories', function (Builder $query) use ($categoryName) {
            $query->where('name', $categoryName);
        });
    }
}
