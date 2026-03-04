<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use PhpOffice\PhpSpreadsheet\Chart\Chart;

class ChartOfAccountService extends BaseService
{
    public function __construct(public ChartOfAccount $chartOfAccount)
    {
        //
    }

    public function store(array $data){
        $this->chartOfAccount->create([
            'account_type_id' => $data['account_type_id'],
            'parent_id' => $data['parent_id'],
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'balance' => $data['balance'],
        ]);

        return $this->chartOfAccount;
    }

    public function update(array $data){
        $this->chartOfAccount = $this->chartOfAccount->find($data['id']);

        $this->chartOfAccount->update([
            'account_type_id' => $data['account_type_id'],
            'parent_id' => $data['parent_id'],
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'balance' => $data['balance'],
        ]);

        return $this->chartOfAccount;
    }

    public function delete($id){
        $this->chartOfAccount->find($id)->delete();
    }
}