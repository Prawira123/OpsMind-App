<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(public DashboardService $dashboardService){
    }

    public function index(){
        return Inertia::render('Dashboard', [
            'financialData'      => $this->dashboardService->getDataFinancial(),
            'invoicePending'     => $this->dashboardService->getInvoicePending(),
            'recentTransactions' => $this->dashboardService->getRecentTransaction(),
            'topClients'         => $this->dashboardService->getTopClient(),
            'userOnline'         => $this->dashboardService->getDataUserOnline(),
        ]);
    }

    public function getDataFinancial(){
        return response()->json([
            'dataFinancial' => $this->dashboardService->getDataFinancial(), 
        ]);
    }

    public function getInvoicePending(){
        return response()->json([
            'invoicePending' => $this->dashboardService->getInvoicePending(),
        ]);
    }

    public function getRecentTransaction(){
        return response()->json([
            'recentTransactions' => $this->dashboardService->getRecentTransaction(),
        ]);
    }

    public function getTopClient(){
        return response()->json([
            'topClients' => $this->dashboardService->getTopClient(),
        ]);
    }

    public function getUserOnline(){
        return response()->json([
            'userOnline' => $this->dashboardService->getDataUserOnline(),
        ]);
    }
}
