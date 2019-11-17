<?php

namespace App\Models;

use App\Scoping\Contracts\Scope;
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

    // one to many
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    // filter all product with matching category name
    public function scopeOfCategory(Builder $builder, string $categoryName = null)
    {
        if (is_null($categoryName) || !is_string($categoryName)) {
            return $builder;
        }
        return $builder->whereHas('categories', function (Builder $query) use ($categoryName) {
            $query->where('name', $categoryName);
        });
    }

    public function scopeOfScopes(Builder $builder, array $scopes)
    {
        foreach ($scopes as $name => $scope) {
            $value = request()->input($name);
            if (is_null($value) || !$scope instanceof Scope) {
                continue;
            }
            $scope->apply($builder, $value);
        }
        return $builder;
    }
}
