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
        $clients = Client::where('tenant_id', Auth::user()->tenant_id)->get();
        return Inertia::render('Client/index', [
            'status' => session('success'),
            'clients' => $clients
        ]);
    }

    public function create(){
        return Inertia::render('Client/create');
    }

    public function store(ClientStoreRequest $request, ClientService $service){

        $service->store($request->validated());

        return redirect()->route('clients.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function edit(Client $client){
        return Inertia::render('Client/edit', compact('client'));
    }

    public function update(ClientUpdateRequest $request, Client $client, ClientService $service){

        if($client->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $service->update($request->validated(), $client->id);

        return redirect()->route('clients.index')->with('success', 'Pelanggan berhasil diubah');
    }

    public function destroy(ClientService $service, $id){
        
        $client = Client::find($id);

        if($client->tenant_id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $service->delete($id);

        return redirect()->route('clients.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
