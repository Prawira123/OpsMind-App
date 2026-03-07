<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Illuminate\Support\Facades\Auth;

class JournalService extends BaseService
{
    public function __construct(public JournalEntry $journalEntry, public JournalEntryDetail $journalEntryDetail)
    {
        //
    }

    public function storeJournalEntry(array $data){
        
        $entry_number = $this->createEntryNumber($data['transaction_id']);

        $journal_entry = $this->journalEntry->create([
            'tenant_id' => Auth::user()->tenant_id,
            'transaction_id' => $data['transaction_id'],
            'entry_number' => $entry_number,
            'description' => $data['description'],
            'date' => $data['date'],
            'status' => $data['status'],
            'source' => $data['source'],
        ]);

        $this->storeJournalEntryDetail([
            [
                'journal_entry_id' => $journal_entry->id,
                'account_id' => $data['debit_account_id'],
                'amount' => $data['amountTotal'],
                'description' => $data['description'],
                'type' => 'debit',
            ],
            [
                'journal_entry_id' => $journal_entry->id,
                'account_id' => $data['credit_account_id'],
                'amount' => $data['amountTotal'],
                'description' => $data['description'],
                'type' => 'credit',
            ]
        ]);

        return $this->journalEntry;
    }

    public function updateJournalEntry(array $data, $id){
        
        $journal_entry = $this->journalEntry->where('transaction_id', $id)->first();
        
        $journal_entry->update([
            'tenant_id' => Auth::user()->tenant_id,
            'transaction_id' => $data['transaction_id'],
            'description' => $data['description'],
            'date' => $data['date'],
            'status' => $data['status'],
            'source' => $data['source'],
        ]);

        $this->journalEntryDetail->where('journal_entry_id', $journal_entry->id)->delete();

        $this->storeJournalEntryDetail([
            [
                'journal_entry_id' => $journal_entry->id,
                'account_id' => $data['debit_account_id'],
                'amount' => $data['amountTotal'],
                'description' => $data['description'],
                'type' => 'debit',
            ],
            [
                'journal_entry_id' => $journal_entry->id,
                'account_id' => $data['credit_account_id'],
                'amount' => $data['amountTotal'],
                'description' => $data['description'],
                'type' => 'credit',
            ]
        ]);

        return $this->journalEntry;
    }

    

    public function storeJournalEntryDetail(array $data){
        
        foreach($data as $item) {
            $this->journalEntryDetail->create([
                'journal_entry_id' => $item['journal_entry_id'],
                'account_id' => $item['account_id'],
                'amount' => $item['amount'],
                'description' => $item['description'],
                'type' => $item['type'],
            ]);
        }

        return $this->journalEntryDetail;
    }

    public function deleteJournalEntry($id){

        $journal_entry = $this->journalEntry->where('transaction_id', $id)->first();
        $this->journalEntryDetail->where('journal_entry_id', $journal_entry->id)->delete();

        $journal_entry->delete();

        return $this->journalEntry;

    }

    public function createEntryNumber($transaction_id){
        return "JE-" . date('Y') . "-" . date('m') . "-" . sprintf("%04d", $transaction_id);
    }
}