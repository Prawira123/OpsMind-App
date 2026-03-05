<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountStoreRequest;
use App\Http\Requests\Account\AccountUpdateRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index(){
        $accounts = Account::all();

        return Inertia::render('Account/index', compact('accounts'));
    }

    public function create(){
        return Inertia::render('Account/create');
    }

    public function store(AccountStoreRequest $request, AccountService $service){
        $request->validated();

        $service->store([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'is_active' => $request->is_active
        ]);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil ditambahkan');
    }

    public function edit(Account $account){
        return Inertia::render('Account/edit', compact('account'));
    }

    public function update(AccountUpdateRequest $request, AccountService $service, $id){
        $request->validated();

        $account = Account::find($id);

        $service->update([
            'id' => $account->id,
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'is_active' => $request->is_active
        ]);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil diubah');
    }

    public function destroy(AccountService $service, $id){
        $service->delete($id);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dihapus');
    }

    public function bulkDestroy(Request $request, AccountService $service) 
    {   
    $ids = $request::input('ids', []);
        
    foreach ($ids as $id) {
        $service->delete($id);
    }

    return redirect()->route('accounts.index')
                        ->with('success', count($ids) . ' rekening berhasil dihapus');
    }
}
