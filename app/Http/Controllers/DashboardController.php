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
            'totalBalance'=> Inertia::defer(fn () => $this->dashboardService->getTotalBalance()),
            'monthlyStats'       => Inertia::defer(fn () => $this->dashboardService->getTransactionThisMonth()),
            'invoicePending'     => Inertia::defer(fn () => $this->dashboardService->getInvoicePending()),
            'recentTransactions' => Inertia::defer(fn () => $this->dashboardService->getRecentTransaction()),
            'topClients'        => Inertia::defer(fn () => $this->dashboardService->getTopClient()),
            'incomePerMonth'     => Inertia::defer(fn () => $this->dashboardService->getIncomeTotal()),
        ]);
    }
}
