<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(){
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

        return Inertia::render('Dashboard', [
            'incomePerMonth' => [
                'labels' => $labels,
                'totals' => $totals
            ]
        ]);
    }
}
