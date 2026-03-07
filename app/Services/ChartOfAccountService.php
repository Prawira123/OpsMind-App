<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Chart\Chart;

class ChartOfAccountService extends BaseService
{
    public function __construct(public ChartOfAccount $chartOfAccount)
    {
        //
    }

    public function store(array $data){
        
        $this->chartOfAccount->create([
            'tenant_id' => Auth::user()->tenant_id,
            'account_type_id' => $data['account_type_id'],
            'parent_id' => $data['parent_id'],
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'balance' => $data['balance'],
        ]);

        return $this->chartOfAccount;
    }

    public function update(array $data, $id){
        $chartOfAccount = $this->chartOfAccount->find($id);

        $chartOfAccount->update([
            'account_type_id' => $data['account_type_id'],
            'parent_id' => $data['parent_id'],
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'balance' => $data['balance'],
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