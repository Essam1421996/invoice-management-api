<?php

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;

    public function findByIdAndTenant(int $id, int $tenantId): ?Invoice;

    public function getByContractId(int $contractId, array $filters = []): LengthAwarePaginator|Collection;

    public function create(array $data): Invoice;

    public function update(Invoice $invoice, array $data): Invoice;

    public function getNextSequenceForTenantAndMonth(int $tenantId, string $yearMonth): int;
}
