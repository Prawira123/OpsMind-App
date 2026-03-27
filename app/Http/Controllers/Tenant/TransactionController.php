<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Account;

use Inertia\Inertia;

class TransactionController extends Controller
{

    public function index(){
        $transaction = Transaction::with('category')->get();

        return Inertia::render('Transaction/index', [
            'status' => session('success'),
            'transactions' => $transaction
        ]);
    }

    public function create(){
        $categories = Category::with('tenant')->get();
        $accounts = ChartOfAccount::with('tenant')->get();
        $clients = Client::with('tenant')->get();
        $rekenings = Account::with('tenant')->get();

        return Inertia::render('Transaction/create', [ 
            'categories' => $categories,
            'accounts' => $accounts,
            'clients' => $clients,
            'rekenings' => $rekenings,
        ]);
    }

    public function show($id){
        $transaction = Transaction::with('category', 'client', 'debit_account', 'credit_account', 'transaction_items', 'createdBy')->find($id);

        Log::info("langkah 1", ['id' => $id, 'transaction' => $transaction]);

        return Inertia::render('Transaction/show', [
            'transaction' => $transaction
        ]);
    }

    public function store(TransactionStoreRequest $request, TransactionService $transactionService){
        
        $transactionService->storeTransactionHeader($request->validated());

        return redirect()->route('transactions.index')
                     ->with('success', 'Transaksi berhasil disimpan');
    }

    public function edit($id){
        $transaction = Transaction::with('category', 'client')->find($id);
        $category = Category::with('tenant')->get();
        $transactionItems = TransactionItem::where('transaction_id', $transaction->id)->get();
        $clients = Client::with('tenant')->get();
        $accounts = ChartOfAccount::with('tenant')->get();
        $rekenings = Account::with('tenant')->get();


        Log::info("langkah 1", ['id' => $id, 'transaction' => $transaction]);

        return Inertia::render('Transaction/edit', [
            'transaction' => $transaction,
            'categories' => $category,
            'transaction_items' => $transactionItems,
            'clients' => $clients,
            'accounts' => $accounts,
            'rekenings' => $rekenings,
        ]);
        
    }

    public function update(TransactionStoreRequest $request, TransactionService $transactionService, $id){

        $transaction = Transaction::find($id);
        Log::info("langkah 1");

        if($transaction->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu Tidak Punya Akses');
        }

        $transactionService->updateTransactionHeader( $request->validated(), $transaction->id);
        Log::info("langkah 2");

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
