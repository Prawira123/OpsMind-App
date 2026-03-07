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

        $service->store($request->validated());

        return redirect()->route('chart-of-accounts.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit(ChartOfAccount $chartOfAccount){
        $accountTypes = AccountType::all();
        $chartOfAccounts = ChartOfAccount::all();

        return Inertia::render('Tenant/ChartOfAccounts/Edit', compact('chartOfAccount', 'accountTypes', 'chartOfAccounts'));
    }

    public function update(ChartOfAccountService $service, ChartOfAccountUpdateRequest $request, $id){
        $chartOfAccount = ChartOfAccount::find($id);

        if($chartOfAccount->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $service->update($request->validated(), $chartOfAccount->id);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Akun berhasil diubah');
    }

    public function destroy(ChartOfAccountService $service, $id){
        $chartOfAccount = ChartOfAccount::find($id);

        if($chartOfAccount->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $service->delete($id);

        return redirect()->route('chart-of-accounts.index')->with('success', 'Akun berhasil dihapus');
    }
}
