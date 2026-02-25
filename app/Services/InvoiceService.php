<?php

namespace App\Services;

use App\DTOs\CreateInvoiceDTO;
use App\DTOs\RecordPaymentDTO;
use App\Enums\InvoiceStatus;
use App\Exceptions\ContractNotActiveException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvoiceCancelledException;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Enums\ContractStatus;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private ContractRepositoryInterface $contractRepo,
        private InvoiceRepositoryInterface $invoiceRepo,
        private PaymentRepositoryInterface $paymentRepo,
        private TaxService $taxService,
    ) {}

    public function createInvoice(CreateInvoiceDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $contract = $this->contractRepo->findById($dto->contract_id);
            if (!$contract) {
                abort(404, 'Contract not found.');
            }
            if ($contract->status !== ContractStatus::Active) {
                throw new ContractNotActiveException();
            }

            $subtotal = (float) $contract->rent_amount;
            $taxAmount = $this->taxService->calculateTotalTax($subtotal);
            $total = round($subtotal + $taxAmount, 2);

            $yearMonth = now()->format('Ym');

            $sequence = $this->invoiceRepo
                ->getNextSequenceForTenantAndMonth($dto->tenant_id, $yearMonth);
            
            $invoiceNumber = 'INV'
                . str_pad($dto->tenant_id, 3, '0', STR_PAD_LEFT)
                . '-' . $yearMonth
                . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            return $this->invoiceRepo->create([
                'contract_id' => $dto->contract_id,
                'tenant_id' => $dto->tenant_id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'status' => InvoiceStatus::Pending,
                'due_date' => $dto->due_date,
                'paid_at' => null,
            ]);
        });
    }

    public function recordPayment(RecordPaymentDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $invoice = $this->invoiceRepo->findById($dto->invoice_id);
            if (!$invoice) {
                abort(404, 'Invoice not found.');
            }
            if ($invoice->status === InvoiceStatus::Cancelled) {
                throw new InvoiceCancelledException();
            }

            $remainingBalance = (float) $invoice->remaining_balance;
            if ($dto->amount > $remainingBalance) {
                throw new InsufficientBalanceException();
            }
            if ($dto->amount <= 0) {
                abort(422, 'Payment amount must be greater than zero.');
            }

            $payment = $this->paymentRepo->create([
                'invoice_id' => $dto->invoice_id,
                'amount' => $dto->amount,
                'payment_method' => $dto->payment_method,
                'reference_number' => $dto->reference_number,
                'paid_at' => now(),
            ]);

            $totalPaid = (float) $invoice->payments()->sum('amount') + (float) $dto->amount;
            $invoiceTotal = (float) $invoice->total;

            if ($totalPaid >= $invoiceTotal) {
                $this->invoiceRepo->update($invoice, [
                    'status' => InvoiceStatus::Paid,
                    'paid_at' => now(),
                ]);
            } else {
                $this->invoiceRepo->update($invoice, [
                    'status' => InvoiceStatus::PartiallyPaid,
                ]);
            }

            return $payment->fresh();
        });
    }

    public function getInvoicesByContractId(int $contractId, array $filters = [])
    {
        $contract = $this->contractRepo->findById($contractId);
        if (!$contract) {
            abort(404, 'Contract not found.');
        }
        return $this->invoiceRepo->getByContractId($contractId, $filters);
    }

    public function getContractSummary(int $contractId)
    {
        $contract = $this->contractRepo->findById($contractId);
        if (!$contract) {
            abort(404, 'Contract not found.');
        }

        $invoices = $this->invoiceRepo->getByContractId($contractId, ['per_page' => 1000]);
        $invoices = $invoices instanceof \Illuminate\Pagination\AbstractPaginator ? $invoices->getCollection() : $invoices;

        $totalInvoiced = $invoices->sum('total');
        $totalPaid = 0;
        $latestInvoiceDate = null;

        foreach ($invoices as $invoice) {
            $totalPaid += (float) $invoice->payments->sum('amount');
            if ($latestInvoiceDate === null || $invoice->due_date > $latestInvoiceDate) {
                $latestInvoiceDate = $invoice->due_date;
            }
        }

        return [
            'contract_id' => $contractId,
            'total_invoiced' => round((float) $totalInvoiced, 2),
            'total_paid' => round($totalPaid, 2),
            'outstanding_balance' => round((float) $totalInvoiced - $totalPaid, 2),
            'invoices_count' => $invoices->count(),
            'latest_invoice_date' => $latestInvoiceDate?->format('Y-m-d'),
        ];
    }
}
