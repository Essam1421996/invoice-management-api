<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $invoice = Invoice::factory()->create();
        return [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total,
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'reference_number' => 'REF-' . fake()->unique()->numerify('########'),
            'paid_at' => now(),
        ];
    }
}
