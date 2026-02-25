<?php

namespace App\DTOs;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Contract;

class CreateInvoiceDTO
{
    public function __construct(
        public readonly int $contract_id,
        public readonly string $due_date,
        public readonly int $tenant_id,
    ) {}

    public static function fromRequest(StoreInvoiceRequest $request, Contract $contract): self
    {
        return new self(
            contract_id: $contract->id,
            due_date: $request->validated('due_date'),
            tenant_id: (int) $request->user()->tenant_id,
        );
    }
}
