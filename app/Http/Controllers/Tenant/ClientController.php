<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientStoreRequest;
use App\Http\Requests\Client\ClientUpdateRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(){
        return redirect()->route('clients.index');
    }

    public function create(){
        return redirect()->route('clients.create');
    }

    public function store(ClientStoreRequest $request, ClientService $service){
        $request->validated($request->all());

        $service->store([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company' => $request->company
        ]);

        return redirect()->route('clients.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function edit(Client $client){
        return redirect()->route('clients.edit', compact('client'));
    }

    public function update(ClientUpdateRequest $request, Client $client, ClientService $service){
        $request->validated($request->all());

        $service->update([
            'id' => $client->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company' => $request->company
        ]);

        return redirect()->route('clients.index')->with('success', 'Pelanggan berhasil diubah');
    }

    public function destroy(ClientService $service, $id){
        $service->delete($id);

        return redirect()->route('clients.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
