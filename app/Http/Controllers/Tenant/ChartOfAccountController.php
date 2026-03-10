<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChartOfAccount\ChartOfAccountStoreRequest;
use App\Http\Requests\ChartOfAccount\ChartOfAccountUpdateRequest;
use App\Models\ChartOfAccount;
use App\Services\ChartOfAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ChartOfAccountController extends Controller
{
    public function index(){
        $chartOfAccounts = ChartOfAccount::with('accountType')->get();

        return Inertia::render('CoA/index', [
            'status' => session('success'),
            'chartOfAccounts' => $chartOfAccounts
        ]);
    }

    public function create(){

        $chartOfAccounts = ChartOfAccount::with('accountType')->get();

        return Inertia::render('CoA/create', compact( 'chartOfAccounts'));
    }

    public function store(ChartOfAccountStoreRequest $request, ChartOfAccountService $service){

        $service->store($request->validated());
        
        Log::info($request->validated());

        return redirect()->route('chart-of-accounts.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit($id){
        $chartOfAccount = ChartOfAccount::with('accountType')->findOrFail($id);

        if($chartOfAccount->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $chartOfAccounts = ChartOfAccount::all();

        return Inertia::render('CoA/edit', compact('chartOfAccount', 'chartOfAccounts'));
    }

    public function update(ChartOfAccountService $service, ChartOfAccountUpdateRequest $request, $id){
        $chartOfAccount = ChartOfAccount::findOrFail($id);

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

    public function bulkDestroy(Request $request, ChartOfAccountService $service) 
    {   
        $ids = $request->input('ids', []);
            
        foreach ($ids as $id) {
            $CoA = ChartOfAccount::find($id);

            if($CoA->tenant_id !== Auth::user()->tenant_id){
                abort(403, 'Kamu tidak Punya Akses');
            }

            $service->delete($CoA->id);
        }

        return redirect()->route('chart-of-accounts.index')
        ->with('success', count($ids) . ' Akun ini berhasil dihapus');
    }
}
