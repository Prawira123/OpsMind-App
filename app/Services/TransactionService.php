<?php

namespace App\Services;

use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;

class TransactionService extends BaseService
{
    public function __construct( public Transaction $transaction, public TransactionItem $transaction_items, public JournalService $journalService)
    {
    }
    
    public function storeTransactionHeader(array $data){
        
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
            'client_id' => $data['client_id'],
            'created_by' => Auth::user()->id,
            ]);

            foreach($data['items'] as $item) {
                $this->storeTransactionItem([
                    ...$item, 'transaction_id' => $transaction->id
                ]);
            }

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

            return $this->transaction;
    }
    
    public function updateTransactionHeader(array $data, $id){

        $this->transaction = $this->transaction->find($id);

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
            ]);

        $this->transaction_items->where('transaction_id', $this->transaction->id)->delete();

        foreach($data['items'] as $item) {
            $this->storeTransactionItem([
                ...$item, 'transaction_id' => $this->transaction->id
            ]);
        }
        $this->journalService->updateJournalEntry([
            'transaction_id' => $this->transaction->id,
            'description' => $data['description'],
            'date' => $data['date'],
            'status' => 'posted',
            'source' => 'transaction',
            'debit_account_id' => $data['debit_account_id'],
            'credit_account_id' => $data['credit_account_id'],
            'amountTotal' => $amountTotal,
        ], $this->transaction->id);

        return $this->transaction;
    }
    
    public function storeTransactionItem(array $data){
        
        $this->transaction_items->create([
            'transaction_id' => $data['transaction_id'],
            'description' => $data['description'],
            'unit_price' => $data['unit_price'],
            'qty' => $data['qty'],
            'amount_per_item' => $data['amount_per_item'],
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
        $transaction->delete();

        return $transaction;
    }

}