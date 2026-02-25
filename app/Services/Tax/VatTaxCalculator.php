<?php

namespace App\Services\Tax;

use App\Contracts\TaxCalculatorInterface;

class VatTaxCalculator implements TaxCalculatorInterface
{
    private const RATE = 0.15;

    public function calculate(float $amount): float
    {
        return round($amount * self::RATE, 2);
    }
}
