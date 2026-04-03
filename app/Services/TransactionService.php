<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Log;        // ← Ganti ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService extends BaseService
{
    public function __construct( public Transaction $transaction, public TransactionItem $transaction_items, public JournalService $journalService, public ChartOfAccountService $chartOfAccountService, public AccountService $accountService, public InvoiceService $invoiceService)
    {
    }
    
    public function storeTransactionHeader(array $data){

        DB::transaction(function() use ($data){
        
            $amountTotal = collect($data['items'])
                ->sum(fn($item) => $item['qty'] * $item['unit_price']);

            $transaction = $this->transaction->create([
                'tenant_id' => Auth::user()->tenant_id,
                'category_id' => $data['category_id'],
                'amountTotal' => $amountTotal,
                'debit_account_id' => $data['debit_account_id'],
                'credit_account_id' => $data['credit_account_id'],
                'date' => $data['date'],
                'description' => $data['description'],
                'type' => $data['type'],
                'reference_no' => $data['reference_no'],
                'status' => $data['status'],
                'client_id' => $data['client_id'],
                'created_by' => Auth::user()->id,
                'tax_percent' => $data['tax_percent'],
                'other_fee' => $data['other_fee'],
                'discount' => $data['discount'],
                'rekening_id' => $data['rekening_id'],
                ]);

                foreach($data['items'] as $item) {
                    $this->storeTransactionItem([
                        ...$item, 'transaction_id' => $transaction->id
                    ]);
                }

                if($data['type'] == 'income'){
                    $this->invoiceService->store([
                        'client_id' => $data['client_id'],
                        'created_by' => Auth::user()->id,
                        'number' => $data['reference_no'],
                        'status' => $transaction->status,
                        'issue_date' => $data['date'],
                        'subtotal' => $amountTotal,
                        'tax' => $data['tax_percent'],
                        'total' => $amountTotal,
                        'notes' => $data['description'],
                        'transaction_id' => $transaction->id,
                        'items' => $data['items'],
                    ]);
                }

                Log::info('invoice selesai dibuat');

                $this->journalService->storeJournalEntry([
                    'transaction_id' => $transaction->id,
                    'description' => $data['description'],
                    'date' => $data['date'],
                    'status' => 'posted',
                    'source' => 'transaction',
                    'debit_account_id' => $data['debit_account_id'],
                    'credit_account_id' => $data['credit_account_id'],
                    'amountTotal' => $amountTotal,
                ]);

                $this->chartOfAccountService->updateBalance([
                    [
                        'id' => $data['debit_account_id'],
                        'balance' => $amountTotal,
                        'line_type' => 'debit',
                    ],
                    [
                        'id' => $data['credit_account_id'],
                        'balance' => $amountTotal,
                        'line_type' => 'credit',
                    ]
                ]);

                $this->accountService->updateBalance($data['rekening_id'], [
                    'balance' => $amountTotal,
                    'type' => $data['type'],
                ]);

                return $this->transaction;
            });
    }
    
    public function updateTransactionHeader(array $data, $id){

        DB::transaction(function() use ($data, $id){

            $this->transaction = $this->transaction->find($id);

            Log::info("data masuk", [
                'transaction' => $this->transaction,
                'data' => $data
            ]);

            
            $this->chartOfAccountService->restoreBalance([
                [
                    'id' => $this->transaction->debit_account_id,
                    'balance' => $this->transaction->amountTotal,
                    'line_type' => 'debit',
                ],
                [
                    'id' => $this->transaction->credit_account_id,
                    'balance' => $this->transaction->amountTotal,
                    'line_type' => 'credit',
                ]
            ]);

            Log::info("restore balance coa");
                
            $this->accountService->restoreBalance($this->transaction->rekening_id, [
                'balance' => $this->transaction->amountTotal,
                'type' => $this->transaction->type,
            ]);

            Log::info("restore balance account");

            $amountTotal = collect($data['items'])
                ->sum(fn($item) => $item['qty'] * $item['unit_price']);
                
            $this->transaction->update([
                'tenant_id' => Auth::user()->tenant_id,
                'category_id' => $data['category_id'],
                'amountTotal' => $amountTotal,
                'debit_account_id' => $data['debit_account_id'],
                'credit_account_id' => $data['credit_account_id'],
                'date' => $data['date'],
                'description' => $data['description'],
                'type' => $data['type'],
                'reference_no' => $data['reference_no'],
                'client_id' => $data['client_id'],
                'created_by' => Auth::user()->id,
                'tax_percent' => $data['tax_percent'],
                'other_fee' => $data['other_fee'],
                'discount' => $data['discount'],
                'rekening_id' => $data['rekening_id'],
                ]);

            Log::info("data terupdate", []);

            $this->transaction_items->where('transaction_id', $id)->delete();

            Log::info("data terhapus");

            foreach($data['items'] as $item) {
                $this->storeTransactionItem([
                    ...$item, 'transaction_id' => $id
                ]);
            }

            Log::info("data items tersimpan");

            if($data['type'] == 'income'){
                $this->invoiceService->update($this->transaction->id, [
                    'client_id' => $data['client_id'],
                    'created_by' => Auth::user()->id,
                    'number' => $data['reference_no'],
                    'status' => 'send',
                    'issue_date' => $data['date'],
                    'subtotal' => $amountTotal,
                    'tax' => $data['tax_percent'],
                    'total' => $amountTotal,
                    'notes' => $data['description'],
                    'items' => $data['items'],
                ]);
            }
            

            $this->chartOfAccountService->updateBalance([
                [
                    'id' => $data['debit_account_id'],
                    'balance' => $amountTotal,
                    'line_type' => 'debit',
                ],
                [
                    'id' => $data['credit_account_id'],
                    'balance' => $amountTotal,
                    'line_type' => 'credit',
                ]
            ]);

            Log::info("data balance terupdate");

            $this->accountService->updateBalance($data['rekening_id'], [
                'balance' => $amountTotal,
                'type' => $data['type'],
            ]);

            Log::info("data balance account terupdate");

            $this->journalService->updateJournalEntry([
                'transaction_id' => $id,
                'description' => $data['description'],
                'date' => $data['date'],
                'status' => 'posted',
                'source' => 'transaction',
                'debit_account_id' => $data['debit_account_id'],
                'credit_account_id' => $data['credit_account_id'],
                'amountTotal' => $amountTotal,
            ], $id);

            Log::info("data journal terupdate");

            return $this->transaction;
        });
    }
    
    public function storeTransactionItem(array $data){
        
        $this->transaction_items->create([
            'name' => $data['name'],
            'transaction_id' => $data['transaction_id'],
            'description' => $data['description'],
            'unit_price' => $data['unit_price'],
            'qty' => $data['qty'],
            'amount' => $data['amount'],
        ]);

        return $this->transaction_items;
    }

    public function delete($id){
        $transaction = $this->transaction->find($id);

        if (!$transaction) {
            throw new \Exception("Transaksi dengan id {$id} tidak ditemukan");
        }

        $this->journalService->deleteJournalEntry($transaction->id);
        $this->transaction_items->where('transaction_id', $transaction->id)->delete();
        $this->invoiceService->delete($transaction->id);
        $transaction->delete();

        return $transaction;
    }

}