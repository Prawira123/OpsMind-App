<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Transaction;

class DashboardService extends BaseService
{
    public function __construct()
    {
        //
    }

    public function getIncomeTotal(){
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
    }

    public function getRecentTransaction(){
        $transactions = Transaction::with('client')
        ->orderBy('date', 'desc')
        ->paginate(5);

        return $transactions;
    }

    public function getTransactionThisMonth(){
        $transaction = Transaction::whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->get();

        $income = $transaction->where('type', 'income')->sum('amountTotal');
        $expense = $transaction->where('type', 'expense')->sum('amountTotal');

        return [
            'income' => $income,
            'expense' => $expense
        ];
    }

    public function getInvoicePending(){
        $invoice = Invoice::whereIn('status', ['send', 'draft'])
        ->count();

        return $invoice;
    }

    public function getTotalBalance(){
        // Ensure we sum balance from active accounts for the current tenant
        return Account::where('is_active', true)->sum('balance') ?: 0;
    }

    public function getTopClient(){
        $transactions = Transaction::with('client')
        ->selectRaw('client_id, SUM(amountTotal) as total_spent')
        ->groupBy('client_id')
        ->orderBy('total_spent', 'desc')
        ->limit(5)
        ->get();

        return $transactions;
    }
}