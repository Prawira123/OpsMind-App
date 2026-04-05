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
            'totalBalance'       => $this->dashboardService->getTotalBalance(),
            'monthlyStats'       => $this->dashboardService->getTransactionThisMonth(),
            'invoicePending'     => $this->dashboardService->getInvoicePending(),
            'recentTransactions' => $this->dashboardService->getRecentTransaction(),
            'topClients'        => $this->dashboardService->getTopClient(),
            'incomePerMonth'     => $this->dashboardService->getIncomeTotal(),
        ]);
    }
}
