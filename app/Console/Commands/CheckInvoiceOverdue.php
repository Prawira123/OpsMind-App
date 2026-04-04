<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Invoice;
use App\Notifications\InvoiceMail;
use App\Notifications\InvoiceOverdueNotification;
use Illuminate\Console\Command;

class CheckInvoiceOverdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-invoice-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Scheduler jalan: cek invoice overdue');

        $invoices = Invoice::with(['client', 'createdBy'])
        ->where('status', 'send')
        ->where('due_date', '<', now())
        ->get();

        foreach($invoices as $invoice){
            $invoice->update([
                'status' => 'overdue'
            ]);

            // Notifikasi ke Client (Email)
            if ($invoice->client) {
                $invoice->client->notify(new InvoiceMail($invoice));
            }

            // Notifikasi ke Internal User (Database)
            if ($invoice->createdBy) {
                $invoice->createdBy->notify(new InvoiceOverdueNotification($invoice));
            }
        }
    }
}
