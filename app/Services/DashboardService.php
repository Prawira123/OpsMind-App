<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DashboardService extends BaseService
{
    public function __construct()
    {
        //
    }

    public function getIncomeTotal(){
        $tenantId = Auth::user()->tenant_id;
        return Cache::remember("tenant_{$tenantId}:dashboard:getIncomeTotal", now()->addDay(), function () {
            $data = Transaction::where('type', 'income')
            ->selectRaw('
                YEAR(date) as year,
                MONTH(date) as month,
                SUM(amountTotal) as total_income
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

            $labels = [];
            $totals = [];

            foreach($data as $item){
                $labels[] = date('F Y', mktime(0, 0, 0, $item->month, 1, $item->year));
                $totals[] = (float) $item->total_income;
            }

            return [
                'labels' => $labels,
                'totals' => $totals
            ];
        });
    }

    public function getRecentTransaction(){
        $tenantId = Auth::user()->tenant_id;
        return Cache::remember("tenant_{$tenantId}:dashboard:getRecentTransaction", now()->addDay(), function () {
            $transactions = Transaction::with('client')
            ->orderBy('date', 'desc')
            ->paginate(5);

            return $transactions;
        });
    }

    public function getTransactionThisMonth(){
        $tenantId = Auth::user()->tenant_id;
        return Cache::remember("tenant_{$tenantId}:dashboard:getTransactionThisMonth", now()->addDay(), function () {
            $transaction = Transaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

            $income = $transaction->where('type', 'income')->sum('amountTotal');
            $expense = $transaction->where('type', 'expense')->sum('amountTotal');

            return [
                'income' => $income,
                'expense' => $expense
            ];
        });
    }

    public function getInvoicePending(){
        $tenantId = Auth::user()->tenant_id;
        return Cache::remember("tenant_{$tenantId}:dashboard:getInvoicePending", now()->addDay(), function () {
            $invoice = Invoice::whereIn('status', ['send', 'draft'])
            ->count();

            return $invoice;
        });
    }

    public function getTotalBalance(){
        $tenantId = Auth::user()->tenant_id;
        return Cache::remember("tenant_{$tenantId}:dashboard:getTotalBalance", now()->addDay(), function () {
            // Ensure we sum balance from active accounts for the current tenant
            return Account::where('is_active', true)->sum('balance') ?: 0;
        });
    }

    public function getTopClient(){
        $tenantId = Auth::user()->tenant_id;
        return Cache::remember("tenant_{$tenantId}:dashboard:getTopClient", now()->addDay(), function () {
            $transactions = Transaction::with('client')
            ->selectRaw('client_id, SUM(amountTotal) as total_spent')
            ->groupBy('client_id')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

            return $transactions;
        });
    }
}