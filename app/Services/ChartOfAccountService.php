<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChartOfAccountService extends BaseService
{
    public function __construct(public ChartOfAccount $chartOfAccount)
    {
        //
    }

    public function store(array $data){
        
        $coa = ChartOfAccount::where('tenant_id', Auth::user()->tenant_id)
        ->where('account_type_id', $data['type'])
        ->orderBy('code', 'desc')->first();
        
        if($coa){
            $code = $coa->code + 1;
        }else{
            $code = 1;
        }

        $createdCoA = $this->chartOfAccount->create([
            'tenant_id' => Auth::user()->tenant_id,
            'account_type_id' => $data['type'],
            'parent_id' => $data['parent_id'],
            'code' => $code,
            'name' => $data['name'],
            'description' => $data['description'],
            'balance' => $data['balance'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
            'is_locked' => $data['is_locked'] ?? false,
        ]);

        Log::info("data masuk", [
            'chartOfAccount' => $createdCoA
        ]);

        return $createdCoA;
    }

    public function update(array $data, $id){
        $chartOfAccount = $this->chartOfAccount->find($id);

        $chartOfAccount->update([
            'account_type_id' => $data['type'],
            'parent_id' => $data['parent_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'balance' => $data['balance'],
            'is_active' => $data['is_active'] ?? $chartOfAccount->is_active,
            'is_locked' => $data['is_locked'] ?? $chartOfAccount->is_locked,
        ]);

        return $chartOfAccount;
    }

    public function delete($id){
       
        $chartOfAccount = ChartOfAccount::find($id);

        if($chartOfAccount->is_default || $chartOfAccount->is_locked){
            abort(403, 'Tidak dapat menghapus akun default atau terkunci');
        }

        $chartOfAccount->delete();

    }
}