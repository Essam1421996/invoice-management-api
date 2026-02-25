<?php

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice
    {
        return Invoice::find($id);
    }

    public function findByIdAndTenant(int $id, int $tenantId): ?Invoice
    {
        return Invoice::where('id', $id)->where('tenant_id', $tenantId)->first();
    }

    /**
     * @param array{status?: string, from_date?: string, to_date?: string, per_page?: int} $filters
     */
    public function getByContractId(int $contractId, array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Invoice::where('contract_id', $contractId)
            ->with(['payments']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['from_date'])) {
            $query->whereDate('due_date', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('due_date', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice->fresh();
    }

    public function getNextSequenceForTenantAndMonth(int $tenantId, string $yearMonth): int
    {
       
        $prefix = 'INV'
            . str_pad($tenantId, 3, '0', STR_PAD_LEFT)
            . '-' . $yearMonth . '-';

        $last = Invoice::where('invoice_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        if (!$last) {
            return 1;
        }

        $seq = (int) substr($last, strlen($prefix));

        return $seq + 1;

    }
}
