<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

class LaporanService extends BaseService
{
    public function __construct(
        public JournalEntry $journalEntry,
        public JournalEntryDetail $journalEntryDetail
    )
    {
        //
    }

    /**
     * Laporan Laba Rugi (Profit & Loss)
     * Revenue - Expense
     */
    public function getLabaRugi($startDate, $endDate)
    {
        $data = $this->getAccountBalances($startDate, $endDate, ['revenue', 'expense']);

        $revenue = $data->where('category', 'revenue');
        $expense = $data->where('category', 'expense');

        $totalRevenue = $revenue->sum('balance');
        $totalExpense = $expense->sum('balance');

        return $this->success([
            'revenue' => $revenue->values(),
            'expense' => $expense->values(),
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $totalRevenue - $totalExpense,
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);
    }

    /**
     * Laporan Neraca (Balance Sheet)
     * Assets = Liabilities + Equity
     */
    public function getNeraca($date)
    {
        // Untuk Neraca, kita butuh saldo kumulatif sampai tanggal tersebut
        $data = $this->getAccountBalances(null, $date, ['asset', 'liability', 'equity']);

        $assets = $data->where('category', 'asset');
        $liabilities = $data->where('category', 'liability');
        $equity = $data->where('category', 'equity');

        // Hitung Laba Tahun Berjalan (Revenue - Expense dari awal tahun sampai tanggal neraca)
        $startOfYear = \Carbon\Carbon::parse($date)->startOfYear()->toDateString();
        $profitData = $this->getAccountBalances($startOfYear, $date, ['revenue', 'expense']);
        $currentEarnings = $profitData->where('category', 'revenue')->sum('balance') - $profitData->where('category', 'expense')->sum('balance');

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance') + $currentEarnings;

        return $this->success([
            'assets'       => $assets->values(),
            'liabilities'  => $liabilities->values(),
            'equity'       => $equity->values(),
            'current_earnings' => $currentEarnings,
            'total_assets'     => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity'     => $totalEquity,
            'is_balanced'  => round($totalAssets, 2) == round($totalLiabilities + $totalEquity, 2),
            'date'         => $date
        ]);
    }

    /**
     * Laporan Arus Kas (Cash Flow)
     * Pergerakan berdasarkan cash_flow_type di account_types
     */
    public function getArusKas($startDate, $endDate)
    {
        $query = JournalEntryDetail::with(['journalEntry', 'account.accountType'])
            ->whereHas('account.accountType', function($q) {
                $q->whereIn('cash_flow_type', ['operating', 'investing', 'financing']);
            })
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                  ->where('date', '>=', $startDate)
                  ->where('date', '<=', $endDate);
            });

        // Urutkan berdasarkan tanggal
        $query->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_details.journal_entry_id')
              ->orderBy('journal_entries.date', 'asc')
              ->select('journal_entry_details.*');

        $details = $query->get();

        $operating = [];
        $investing = [];
        $financing = [];

        foreach ($details as $d) {
            $cfType = $d->account->accountType->cash_flow_type;
            
            // Logika Arus Kas: Kas masuk jika Kredit (untuk akun non-kas), Kas keluar jika Debit.
            // Nilai = Kredit - Debit
            $amount = (float)$d->amount_credit - (float)$d->amount_debit;
            
            // Tunggu, JournalEntryDetail menggunakan 'type' (debit/credit) dan 'amount'.
            if ($d->type === 'credit') {
                $amount = (float) $d->amount;
            } else {
                $amount = -(float) $d->amount;
            }

            $item = [
                'id'          => $d->id,
                'date'        => $d->journalEntry->date->toDateString(),
                'description' => $d->description ?: $d->journalEntry->description,
                'account'     => $d->account->name,
                'amount'      => $amount,
            ];

            if ($cfType === 'operating') $operating[] = $item;
            elseif ($cfType === 'investing') $investing[] = $item;
            elseif ($cfType === 'financing') $financing[] = $item;
        }

        return $this->success([
            'operating' => $operating,
            'investing' => $investing,
            'financing' => $financing,
            'period'    => ['start' => $startDate, 'end' => $endDate]
        ]);
    }

    /**
     * Laporan Buku Besar (General Ledger)
     */
    public function getBukuBesar($accountId = null, $startDate = null, $endDate = null)
    {
        // 1. Hitung Saldo Awal (Jumlah semua debit - kredit sebelum startDate)
        $openingBalance = 0;
        if ($startDate) {
            $prevQuery = JournalEntryDetail::whereHas('journalEntry', function($q) use ($startDate) {
                    $q->where('date', '<', $startDate)
                      ->where('status', 'posted');
                });
            
            if ($accountId) {
                $prevQuery->where('account_id', $accountId);
            }

            $prevDetails = $prevQuery->get();
            $openingBalance = $prevDetails->sum(fn($d) => $d->type === 'debit' ? (float)$d->amount : -(float)$d->amount);
        }

        // 2. Ambil Mutasi Periode
        $query = JournalEntryDetail::with(['journalEntry', 'account']);
        
        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $query->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
            $q->where('status', 'posted');
            if ($startDate) $q->where('date', '>=', $startDate);
            if ($endDate) $q->where('date', '<=', $endDate);
        });

        // Urutkan berdasarkan tanggal jurnal
        $query->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_details.journal_entry_id')
              ->orderBy('journal_entries.date', 'asc')
              ->orderBy('journal_entries.created_at', 'asc')
              ->select('journal_entry_details.*');

        $details = $query->get()->map(fn($d) => [
            'id'           => $d->id,
            'date'         => $d->journalEntry->date->toDateString(),
            'entry_number' => $d->journalEntry->entry_number,
            'description'  => ($accountId ? '' : ($d->account->name . ': ')) . ($d->description ?: $d->journalEntry->description),
            'reference'    => $d->journalEntry->transaction_id ? 'TRX-'.$d->journalEntry->transaction_id : null,
            'debit'        => $d->type === 'debit' ? (float) $d->amount : 0,
            'credit'       => $d->type === 'credit' ? (float) $d->amount : 0,
        ]);

        return $this->success([
            'opening_balance' => (float) $openingBalance,
            'ledger'          => $details->values()
        ]);
    }

    /**
     * Helper: Hitung saldo akun berdasarkan kategori dan range tanggal
     */
    private function getAccountBalances($startDate, $endDate, array $categories)
    {
        $query = \Illuminate\Support\Facades\DB::table('journal_entry_details as jed')
            ->join('journal_entries as je', 'je.id', '=', 'jed.journal_entry_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'jed.account_id')
            ->join('account_types as act', 'act.id', '=', 'coa.account_type_id')
            ->whereIn('act.category', $categories)
            ->where('je.status', 'posted');

        if ($startDate) {
            $query->where('je.date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('je.date', '<=', $endDate);
        }

        $results = $query->select(
                'coa.id',
                'coa.name',
                'coa.code',
                'act.category',
                'act.normal_balance',
                \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN jed.type = 'debit' THEN jed.amount ELSE 0 END) as total_debit"),
                \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN jed.type = 'credit' THEN jed.amount ELSE 0 END) as total_credit")
            )
            ->groupBy('coa.id', 'coa.name', 'coa.code', 'act.category', 'act.normal_balance')
            ->get();

        return $results->map(function($row) {
            // Saldo Normal: Debit
            if ($row->normal_balance === 'debit') {
                $row->balance = (float) $row->total_debit - (float) $row->total_credit;
            } else {
                // Saldo Normal: Credit
                $row->balance = (float) $row->total_credit - (float) $row->total_debit;
            }
            return $row;
        });
    }
}