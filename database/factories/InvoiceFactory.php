<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $contract = Contract::factory()->create();
        $subtotal = (float) $contract->rent_amount;
        $tenantId = $contract->tenant_id;
        return [
            'contract_id' => $contract->id,
            'tenant_id' => $tenantId,
            'invoice_number' => 'INV'
                . str_pad($tenantId, 3, '0', STR_PAD_LEFT)
                . '-' . now()->format('Ym')
                . '-' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'total' => $subtotal,
            'status' => InvoiceStatus::Pending,
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'paid_at' => null,
        ];
    }
}
