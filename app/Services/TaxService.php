<?php

namespace App\Services;

use App\Contracts\TaxCalculatorInterface;

class TaxService
{
    /**
     * @param array<int, TaxCalculatorInterface> $calculators
     */
    public function __construct(
        private array $calculators
    ) {}

    public function calculateTotalTax(float $amount)
    {
        $total = 0.0;
        foreach ($this->calculators as $calculator) {
            $total += $calculator->calculate($amount);
        }
        return round($total, 2);
    }
}
