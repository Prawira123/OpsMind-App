<?php

namespace App\Services;

use App\Models\Client;

class ClientService extends BaseService
{
    public function __construct(public Client $client)
    {
    }

    public function store(array $data){
        $this->client->create([
            'tenant_id' => $data['tenant_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'company' => $data['company'],
        ]);

        return $this->client;
    }

    public function update(array $data){
        $this->client = $this->client->find($data['id']);

        $this->client->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'company' => $data['company'],
        ]);

        return $this->client;
    }

    public function delete($id){
        return $this->client->find($id)->delete();
    }
    
}