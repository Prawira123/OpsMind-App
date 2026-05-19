<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;


class TransactionController extends Controller
{

    public function __construct(public TransactionService $transactionService){
    }

    public function index(){
        
        $data = $this->transactionService->getTransactionData();
        
        return Inertia::render('Transaction/index', [
            'status'       => session('success'),
            'transactions' => Inertia::defer(fn () => $data['transactions']),
            'summary'     => Inertia::defer(fn () => $data['summary'])
        ]);
    }

    public function create(){
        $tenantId = Auth::user()->tenant_id;

        $categories = Cache::remember("tenant_{$tenantId}:category:all_with_tenant", now()->addDay(), function () {
            return Category::with('tenant')->get();
        });
        $accounts = Cache::remember("tenant_{$tenantId}:chart_of_account:all_with_tenant", now()->addDay(), function () {
            return ChartOfAccount::with('tenant')->get();
        });
        $clients = Cache::remember("tenant_{$tenantId}:client:all_with_tenant", now()->addDay(), function () {
            return Client::with('tenant')->get();
        });
        $rekenings = Cache::remember("tenant_{$tenantId}:account:all_with_tenant", now()->addDay(), function () {
            return Account::with('tenant')->get();
        });

        return Inertia::render('Transaction/create', [ 
            'categories' => $categories,
            'accounts' => $accounts,
            'clients' => $clients,
            'rekenings' => $rekenings,
        ]);
    }

    public function show($id){
        $transaction = Transaction::with([
            'category', 
            'client', 
            'debit_account', 
            'credit_account', 
            'items', 
            'createdBy', 
            'journalEntry.lines.account'
        ])->find($id);

        Log::info("langkah 1", ['id' => $id, 'transaction' => $transaction]);

        return Inertia::render('Transaction/show', [
            'transaction' => Inertia::defer(fn () => $transaction)
        ]);
    }

    public function store(TransactionStoreRequest $request, TransactionService $transactionService){
        
        $transactionService->storeTransactionHeader($request->validated());

        return redirect()->route('transactions.index')
                     ->with('success', 'Transaksi berhasil disimpan');
    }

    public function edit($id){
        $tenantId = Auth::user()->tenant_id;
        $transaction = Transaction::with('category', 'client')->find($id);
        
        $category = Cache::remember("tenant_{$tenantId}:category:all_with_tenant", now()->addDay(), function () {
            return Category::with('tenant')->get();
        });
        $transactionItems = TransactionItem::where('transaction_id', $transaction->id)->get();
        
        $clients = Cache::remember("tenant_{$tenantId}:client:all_with_tenant", now()->addDay(), function () {
            return Client::with('tenant')->get();
        });
        $accounts = Cache::remember("tenant_{$tenantId}:chart_of_account:all_with_tenant", now()->addDay(), function () {
            return ChartOfAccount::with('tenant')->get();
        });
        $rekenings = Cache::remember("tenant_{$tenantId}:account:all_with_tenant", now()->addDay(), function () {
            return Account::with('tenant')->get();
        });


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

    public function bulkDestroy(Request $request, TransactionService $service) 
    {   
        $ids = $request->input('ids', []);
            
        foreach ($ids as $id) {
            $transaction = Transaction::find($id);

            if($transaction && $transaction->tenant_id !== Auth::user()->tenant_id){
                abort(403, 'Kamu tidak Punya Akses');
            }

            $service->delete($transaction->id); 
        }

        return redirect()->route('transactions.index')
        ->with('success', count($ids) . ' transaksi berhasil dihapus');
    }
}
