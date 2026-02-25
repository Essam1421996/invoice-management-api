<?php

namespace App\Policies;

use App\Enums\InvoiceStatus;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function create(User $user, Contract $contract)
    {
        return (int) $user->tenant_id === (int) $contract->tenant_id;
    }

    public function view(User $user, Invoice $invoice)
    {
        return (int) $user->tenant_id === (int) $invoice->tenant_id;
    }

    public function recordPayment(User $user, Invoice $invoice)
    {
        if ((int) $user->tenant_id !== (int) $invoice->tenant_id) {
            return false;
        }
        if ($invoice->status === InvoiceStatus::Cancelled) {
            return false;
        }
        return true;
    }
}
