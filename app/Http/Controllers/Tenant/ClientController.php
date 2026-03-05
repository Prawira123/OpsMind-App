<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientStoreRequest;
use App\Http\Requests\Client\ClientUpdateRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(){
        return Inertia::render('Tenant/Clients/Index');
    }

    public function create(){
        return Inertia::render('Tenant/Clients/Create');
    }

    public function store(ClientStoreRequest $request, ClientService $service){
        $request->validated();

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
        return Inertia::render('Tenant/Clients/Edit', compact('client'));
    }

    public function update(ClientUpdateRequest $request, Client $client, ClientService $service){
        $request->validated();

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
