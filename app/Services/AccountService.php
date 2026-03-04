<?php

namespace App\Services;

use App\Http\Requests\Account\AccountStoreRequest;
use App\Http\Requests\Account\AccountUpdateRequest;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountService extends BaseService
{
    public function __construct(public Account $account)
    {
    }

    public function store(array $data){

        $this->account->create([
            'tenant_id' => $data['tenant_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'is_active' => true,
            'balance' => $data['balance'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
        ]);

        return $this->account;
    }

    public function update(array $data){

        $this->account = $this->account->find($data['id']);

        $this->account->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'is_active' => true,
            'balance' => $data['balance'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
        ]);

        return $this->account;
    }

    public function delete($id){
        $this->account->find($id)->delete();
    }
}