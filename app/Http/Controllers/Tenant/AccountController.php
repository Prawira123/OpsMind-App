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

        $service->store($request->validated());

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil ditambahkan');
    }

    public function edit(Account $account){
        return Inertia::render('Account/edit', compact('account'));
    }

    public function update(AccountUpdateRequest $request, AccountService $service, $id){
        $request->validated();

        $account = Account::find($id);

        if($account->tenant_id !== Auth::user()->tenant_id){
            return abort(403, 'Kamu tidak Punya Akses');
        }

        $service->update($request->validated(), $id);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil diubah');
    }

    public function destroy(AccountService $service, $id){
        
        $account = Account::find($id);

        if($account->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $service->delete($account->id);

        return redirect()->route('accounts.index')->with('success', 'Rekening berhasil dihapus');
    }

    public function bulkDestroy(Request $request, AccountService $service) 
    {   
        $ids = $request::input('ids', []);
            
        foreach ($ids as $id) {
            $account = Account::find($id);

            if($account->tenant_id !== Auth::user()->tenant_id){
                abort(403, 'Kamu tidak Punya Akses');
            }
            
            $service->delete($account->id);
        }

        return redirect()->route('accounts.index')
        ->with('success', count($ids) . ' rekening berhasil dihapus');
    }
}
