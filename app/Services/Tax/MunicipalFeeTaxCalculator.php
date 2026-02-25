<?php

namespace App\Services\Tax;

use App\Contracts\TaxCalculatorInterface;

class MunicipalFeeTaxCalculator implements TaxCalculatorInterface
{
    private const RATE = 0.025;

    public function calculate(float $amount): float
    {
        return round($amount * self::RATE, 2);
    }
}
