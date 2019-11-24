<?php

namespace App\Models\Traits;

use App\Cart\Money;

trait HasPrice
{
    public function getFormattedPriceAttribute()
    {
        return (new Money($this->price))->format();
    }

    public function getMoneyObj()
    {
        return new Money($this->price);
    }
}
