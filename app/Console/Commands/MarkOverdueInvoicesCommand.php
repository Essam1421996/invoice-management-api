<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkOverdueInvoicesCommand extends Command
{
    protected $signature = 'invoices:mark-overdue';

    protected $description = 'Mark invoices as overdue where due_date < today and status is still pending';

    public function handle(): int
    {
        $count = Invoice::withoutGlobalScopes()
            ->where('status', InvoiceStatus::Pending)
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => InvoiceStatus::Overdue]);

        $this->info("Marked {$count} invoice(s) as overdue.");
        return self::SUCCESS;
    }
}
