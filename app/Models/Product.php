<?php

namespace App\Models;

use App\Scoping\Contracts\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Money\Formatter\IntlMoneyFormatter;
use Money\Currencies\ISOCurrencies;
use Money\Money;
use Money\Currency;
use NumberFormatter;

class Product extends Model
{
    public function getRouteKeyName()
    {
        // api/products/{slug}
        return 'slug';
    }

    public function getFormattedPriceAttribute()
    {
        $formatter = new IntlMoneyFormatter(
            new NumberFormatter('en_GB', NumberFormatter::CURRENCY),
            new ISOCurrencies()
        );
        return $formatter->format(
            new Money($this->price, new Currency('GBP'))
        );
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
