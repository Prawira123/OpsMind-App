<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function store(TransactionStoreRequest $request, TransactionService $transactionService){
        
        $transactionService->storeTransactionHeader($request->validated());

        return redirect()->route('transactions.index')
                     ->with('success', 'Transaksi berhasil disimpan');
    }

    public function update($id, TransactionStoreRequest $request, TransactionService $transactionService){

        $transaction = Transaction::find($id);

        if($transaction->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu Tidak Punya Akses');
        }

        $transactionService->updateTransactionHeader( $request->validated(), $transaction->id);

        return redirect()->route('transactions.index')
                     ->with('success', 'Transaksi berhasil diubah');
    }

    public function destroy($id, TransactionService $transactionService){
    
        $transaction = Transaction::find($id);

        if($transaction->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu Tidak Punya Akses');
        }
    
        $transactionService->delete($transaction->id);
        
        return redirect()->route('transactions.index')
                     ->with('success', 'Transaksi berhasil dihapus');
    }
}
