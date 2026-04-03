<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\InvoiceStoreRequest;
use App\Http\Requests\Invoice\InvoiceUpdateRequest;
use App\Http\Requests\Invoice\StoreInvoiceFromFormRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService)
    {}

    public function index()
    {
        return Inertia::render('Invoice/index', [
            'invoices' => Invoice::with('client')->latest()->get(),
            'status' => session('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Invoice/create', [
            'clients' => Client::all(),
            'transactions' => Transaction::with('transaction_items')->latest()->get(),
        ]);
    }

    public function store(StoreInvoiceFromFormRequest $request)
    {
        Log::info('store manual data invoice',$request->all());
        
        try {
            $this->invoiceService->storeFromCreate($request->validated());
            return Redirect::route('invoices.index')->with('status', 'Invoice created successfully');
        } catch (\Exception $e) {
            Log::error('error store manual data invoice',$e->getMessage());
            return Redirect::route('invoices.index')->with('error', 'Invoice created failed');
        }
    }

    public function show($id)
    {
        return Inertia::render('Invoice/show', [
            'invoice' => Invoice::with('client', 'items')->findOrFail($id),
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('Invoice/edit', [
            'invoice' => Invoice::with('client', 'items')->findOrFail($id),
            'clients' => Client::all(),
            'transactions' => Transaction::with('transaction_items')->latest()->get(),
        ]);
    }

    public function update(InvoiceUpdateRequest $request, $id)
    {
        $this->invoiceService->update($id, $request->validated());
        return Redirect::route('invoices.index')->with('status', 'Invoice updated successfully');
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->invoiceService->delete($invoice);
        return Redirect::route('invoices.index')->with('status', 'Invoice deleted successfully');
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'paid']);
        return Redirect::back()->with('status', 'Invoice marked as paid');
    }

    public function download($id)
    {
        $invoice = Invoice::with('client', 'items')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'tenant' => auth()->user()->tenant
        ]);
        
        return $pdf->download('Invoice-'.$invoice->number.'.pdf');
    }

    public function publicDownload($token)
    {
        $invoice = Invoice::withoutGlobalScopes()->with('client', 'items', 'tenant')->where('public_token', $token)->firstOrFail();
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'tenant' => $invoice->tenant
        ]);
        
        return $pdf->download('Invoice-'.$invoice->number.'.pdf');
    }
}
