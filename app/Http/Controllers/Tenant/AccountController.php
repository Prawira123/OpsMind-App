<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountStoreRequest;
use App\Http\Requests\Account\AccountUpdateRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(){
        $accounts = Account::all();

        return redirect()->route('accounts.index', compact('accounts'));
    }

    public function create(){
        return view('tenant.accounts.create');
    }

    public function store(AccountStoreRequest $request, AccountService $service){
        $request->validated($request->all());

        $service->store([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil ditambahkan');
    }

    public function edit(Account $account){
        return view('tenant.accounts.edit', compact('account'));
    }

    public function update(AccountUpdateRequest $request, AccountService $service, $id){
        $request->validated($request->all());

        $account = Account::find($id);

        $service->update([
            'id' => $account->id,
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil diubah');
    }

    public function destroy(AccountService $service, $id){
        $service->delete($id);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dihapus');
    }
}
