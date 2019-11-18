<?php

namespace App\Models;

use App\Models\Traits\HasPrice;
use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Product extends Model
{
    use HasPrice;

    public function getRouteKeyName()
    {
        // api/products/{slug}
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

    public function stockCount()
    {
        return $this->variations->sum(function ($variation) {
            return $variation->stockCount();
        });
    }

    public function inStock()
    {
        return $this->stockCount() > 0;
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
        foreach ($this->limitScopes($scopes) as $name => $scope) {
            $value = request()->input($name);
            if (!$scope instanceof Scope) {
                continue;
            }
            $scope->apply($builder, $value);
        }
        return $builder;
    }

    protected function limitScopes(array $scopes)
    {
        return Arr::only($scopes, array_keys(request()->all()));
    }
}
