<?php

namespace App\Contracts;

interface TaxCalculatorInterface
{
    public function calculate(float $amount): float;
}
