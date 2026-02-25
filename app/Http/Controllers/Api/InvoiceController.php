<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateInvoiceDTO;
use App\DTOs\RecordPaymentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\ContractSummaryResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\PaymentResource;
use App\Models\Contract;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    public function store(StoreInvoiceRequest $request, Contract $contract)
    {
        $this->authorize('create', [Invoice::class, $contract]);
        $dto = CreateInvoiceDTO::fromRequest($request, $contract);
        $invoice = $this->invoiceService->createInvoice($dto);
        return InvoiceResource::make($invoice)
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request, Contract $contract)
    {
        $this->authorize('view', $contract);
        $filters = [
            'status' => $request->query('status'),
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
            'per_page' => $request->query('per_page', 15),
        ];
        $invoices = $this->invoiceService->getInvoicesByContractId($contract->id, array_filter($filters));
        return InvoiceResource::collection($invoices);
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['contract', 'payments']);
        return InvoiceResource::make($invoice);
    }

    public function recordPayment(StorePaymentRequest $request, Invoice $invoice)
    {
        $this->authorize('recordPayment', $invoice);
        $dto = RecordPaymentDTO::fromRequest($request, $invoice->id);
        $payment = $this->invoiceService->recordPayment($dto);
        return PaymentResource::make($payment)
            ->response()
            ->setStatusCode(201);
    }

    public function summary(Contract $contract)
    {
        $this->authorize('view', $contract);
        $summary = $this->invoiceService->getContractSummary($contract->id);
        return ContractSummaryResource::make($summary);
    }
}
