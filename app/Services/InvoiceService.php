<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Notifications\InvoiceMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceService extends BaseService
{
    public function __construct(public Invoice $model, public InvoiceItem $itemModel)
    {
    }

    public function store(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {

            if ($data['status'] == 'paid') {
                $status = 'paid';
            } elseif ($data['status'] == 'unpaid') {
                $status = 'draft';
            } else {
                $status = $data['status'];
            }

            $due_date = isset($data['due_date']) && $data['due_date']
                ? $data['due_date']
                : Carbon::parse($data['issue_date'])->addDays(14)->format('Y-m-d');

            $number_invoice = 'INV-' . date('Ymd');

            $invoice = $this->model->create([
                'tenant_id' => Auth::user()->tenant_id,
                'client_id' => $data['client_id'],
                'created_by' => Auth::user()->id,
                'number' => $number_invoice,
                'status' => $status,
                'issue_date' => $data['issue_date'],
                'due_date' => $due_date,
                'subtotal' => $data['subtotal'],
                'tax' => $data['tax'],
                'total' => $data['total'],
                'notes' => $data['notes'] ?? null,
                'public_token' => Str::random(32),
                'transaction_id' => $data['transaction_id'],
            ]);

            foreach ($data['items'] as $item) {
                $this->itemModel->create([
                    'invoice_id' => $invoice->id,
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['qty'],
                    'price' => $item['unit_price'],
                    'total' => $item['amount'],
                ]);
            }

            $invoice->load('client');
            if ($invoice->client && $invoice->client->email) {
                $invoice->client->notify(new InvoiceMail($invoice));
            }

            return $invoice->load('client', 'items');
        });
    }

    public function storeFromCreate(array $data)
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::where('id', $data['transaction_id'])->first();
            Log::info('dapat data transaction');

            if (!$transaction) {
                return abort(403, 'Transaction not found');
            }

            $number_invoice = 'INV-' . date('Ymd');

            $due_date = isset($data['due_date']) && $data['due_date']
                ? $data['due_date']
                : Carbon::parse($transaction->date)->addDays(14)->format('Y-m-d');
            $amount_per_item = TransactionItem::where('transaction_id', $transaction->id)->get();
            $total_amount_per_item = $amount_per_item->sum('amount');

            $invoice = $this->model->create([
                'tenant_id' => Auth::user()->tenant_id,
                'client_id' => $transaction->client_id,
                'created_by' => Auth::user()->id,
                'number' => $number_invoice,
                'status' => $data['status'],
                'issue_date' => $transaction->date,
                'due_date' => $due_date,
                'subtotal' => $total_amount_per_item,
                'tax' => $transaction->tax_percent ?? 0,
                'total' => $transaction->amountTotal,
                'notes' => $data['notes'] ?? null,
                'public_token' => Str::random(32),
                'transaction_id' => $data['transaction_id'],
            ]);

            Log::info('buat data invoice');

            // Migrate Items
            foreach ($amount_per_item as $item) {
                $this->itemModel->create([
                    'invoice_id' => $invoice->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => $item->qty,
                    'price' => $item->unit_price,
                    'total' => $item->amount,
                ]);
            }

            Log::info('buat data items');

            if ($invoice->status == 'send') {
                $invoice->load('client');
                if ($invoice->client && $invoice->client->email) {
                    $invoice->client->notify(new InvoiceMail($invoice));
                }
                Log::info('kirim email invoice');
            }

            return $invoice->load('client', 'items');
        });
    }

    public function update($transaction_id, array $data): Invoice
    {
        return DB::transaction(function () use ($transaction_id, $data) {
            $invoice = $this->model->where('transaction_id', $transaction_id)->first();

            // Jika dipanggil dari Controller manual, transaction_id mungkin id invoice itu sendiri
            if (!$invoice) {
                $invoice = $this->model->find($transaction_id);
            }

            if ($data['status'] == 'paid') {
                $status = 'paid';
            } elseif ($data['status'] == 'unpaid') {
                $status = 'draft';
            } else {
                $status = $data['status'];
            }

            $due_date = isset($data['due_date']) && $data['due_date']
                ? $data['due_date']
                : Carbon::parse($data['issue_date'])->addDays(14)->format('Y-m-d');

            $number_invoice = 'INV-' . date('Ymd');

            $invoice->update([
                'client_id' => $data['client_id'],
                'created_by' => Auth::user()->id,
                'number' => $number_invoice,
                'status' => $status,
                'issue_date' => $data['issue_date'],
                'due_date' => $due_date,
                'subtotal' => $data['subtotal'],
                'tax' => $data['tax'],
                'total' => $data['total'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Update items
            $this->itemModel->where('invoice_id', $invoice->id)->delete();
            foreach ($data['items'] as $item) {
                $this->itemModel->create([
                    'invoice_id' => $invoice->id,
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['qty'],
                    'price' => $item['unit_price'],
                    'total' => $item['amount'],
                ]);
            }

            $invoice->load('client');
            if ($invoice->client && $invoice->client->email) {
                $invoice->client->notify(new InvoiceMail($invoice));
            }
            Log::info('kirim email invoice');

            return $invoice->load('client', 'items');
        });
    }

    public function delete($transaction_id): bool
    {
        return DB::transaction(function () use ($transaction_id) {
            $invoice = $this->model->where('transaction_id', $transaction_id)->first();
            $this->itemModel->where('invoice_id', $invoice->id)->delete();
            return $invoice->delete();
        });
    }
}