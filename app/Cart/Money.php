<?php

namespace App\Cart;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money as BaseMoney;
use Money\Parser\IntlMoneyParser;
use NumberFormatter;

class Money
{
    protected $money;

    public function __construct($price)
    {
        $this->money = new BaseMoney($price, new Currency('GBP'));
    }

    public function format()
    {
        $formatter = new IntlMoneyFormatter(
            new NumberFormatter('en_GB', NumberFormatter::CURRENCY),
            new ISOCurrencies()
        );
        return $formatter->format($this->money);
    }

    public function amount()
    {
        return $this->money->getAmount();
    }

    public function add(Money $moneyWrapper)
    {
        $this->money = $this->money->add($this->instance($moneyWrapper));
        return $this;
    }

    public function instance(Money $obj)
    {
        return $obj->money;
    }
}
