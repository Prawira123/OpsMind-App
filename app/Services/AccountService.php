<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountService extends BaseService
{
    public function __construct(public Account $account)
    {
    }

    public function store(array $data){

        $this->account->create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $data['name'],
            'type' => $data['type'],
            'is_active' => $data['is_active'],
            'balance' => $data['balance'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
        ]);

        return $this->account;
    }

    public function update(array $data, $id){

        $this->account = $this->account->find($id);

        $this->account->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'is_active' => $data['is_active'],
            'balance' => $data['balance'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
        ]);

        return $this->account;
    }

    public function delete($id){
        $account = $this->account->find((int) $id);

        if (!$account) {
            throw new \Exception("Rekening dengan id {$id} tidak ditemukan");
        }

        if($account->balance > 0){
            abort(403, 'Rekening masih memiliki saldo, Transfer Saldo ke Rekening Lain agar bisa menghapus Rekening ini');
        }

        $account->delete();
    }
}