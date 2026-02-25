<?php

namespace App\DTOs;

use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;

class RecordPaymentDTO
{
    public function __construct(
        public readonly int $invoice_id,
        public readonly float $amount,
        public readonly PaymentMethod $payment_method,
        public readonly ?string $reference_number,
    ) {}

    public static function fromRequest(StorePaymentRequest $request, int $invoiceId): self
    {
        return new self(
            invoice_id: $invoiceId,
            amount: (float) $request->validated('amount'),
            payment_method: PaymentMethod::from($request->validated('payment_method')),
            reference_number: $request->validated('reference_number'),
        );
    }
}
