<?php

namespace App\Models\Traits;

use Money\Formatter\IntlMoneyFormatter;
use Money\Currencies\ISOCurrencies;
use Money\Money;
use Money\Currency;
use NumberFormatter;

trait HasPrice
{
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
}
