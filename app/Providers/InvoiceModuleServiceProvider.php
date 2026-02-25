<?php

namespace App\Providers;

use App\Contracts\TaxCalculatorInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Eloquent\ContractRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Services\Tax\MunicipalFeeTaxCalculator;
use App\Services\Tax\VatTaxCalculator;
use App\Services\TaxService;
use Illuminate\Support\ServiceProvider;

class InvoiceModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ContractRepositoryInterface::class, ContractRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);

        $this->app->when(TaxService::class)
            ->needs('$calculators')
            ->give(function ($app) {
                return [
                    $app->make(VatTaxCalculator::class),
                    $app->make(MunicipalFeeTaxCalculator::class),
                ];
            });
    }
}
