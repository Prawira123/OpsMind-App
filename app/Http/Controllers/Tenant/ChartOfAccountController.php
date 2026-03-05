<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChartOfAccount\ChartOfAccountStoreRequest;
use App\Http\Requests\ChartOfAccount\ChartOfAccountUpdateRequest;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Services\ChartOfAccountService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChartOfAccountController extends Controller
{
    public function index(){
        $chartOfAccounts = ChartOfAccount::all();

        return Inertia::render('Tenant/ChartOfAccounts/Index', compact('chartOfAccounts'));
    }

    public function create(){
        $accountTypes = AccountType::all();
        $chartOfAccounts = ChartOfAccount::all();

        return Inertia::render('Tenant/ChartOfAccounts/Create', compact('accountTypes', 'chartOfAccounts'));
    }

    public function store(ChartOfAccountStoreRequest $request, ChartOfAccountService $service){
        $request->validated();

        $service->store([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $request->name,
            'account_type_id' => $request->account_type_id,
            'parent_id' => $request->parent_id,
            'code' => $request->code,
            'description' => $request->description,
            'balance' => $request->balance
        ]);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit(ChartOfAccount $chartOfAccount){
        $accountTypes = AccountType::all();
        $chartOfAccounts = ChartOfAccount::all();

        return Inertia::render('Tenant/ChartOfAccounts/Edit', compact('chartOfAccount', 'accountTypes', 'chartOfAccounts'));
    }

    public function update(ChartOfAccountService $service, ChartOfAccountUpdateRequest $request, $id){
        $request->validated();

        $service->update([
            'id' => $id,
            'name' => $request->name,
            'account_type_id' => $request->account_type_id,
            'parent_id' => $request->parent_id,
            'code' => $request->code,
            'description' => $request->description,
            'balance' => $request->balance,
        ]);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Akun berhasil diubah');
    }

    public function destroy(ChartOfAccountService $service, $id){
        $service->delete($id);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Akun berhasil dihapus');
    }
}
