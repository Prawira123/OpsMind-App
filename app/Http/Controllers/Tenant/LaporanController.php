<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function __construct(protected LaporanService $laporanService)
    {
    }

    public function getLabaRugi(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $result = $this->laporanService->getLabaRugi($dateFrom, $dateTo);

        return Inertia::render('Reports/LabaRugi', [
            'report' => [
                'revenues' => $result['data']['revenue'],
                'expenses' => $result['data']['expense'],
            ],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function getNeraca(Request $request)
    {
        $dateTo = $request->date_to ?? now()->toDateString();
        $dateFrom = $request->date_from ?? now()->startOfYear()->toDateString();

        $result = $this->laporanService->getNeraca($dateTo);

        return Inertia::render('Reports/Neraca', [
            'report' => [
                'assets' => $result['data']['assets'],
                'liabilities' => $result['data']['liabilities'],
                'equity' => $result['data']['equity'],
            ],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function getArusKas(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $result = $this->laporanService->getArusKas($dateFrom, $dateTo);

        return Inertia::render('Reports/ArusKas', [
            'report' => $result['data'],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function getBukuBesar(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();
        $coaId = (int) $request->coa_id;

        $result = $this->laporanService->getBukuBesar($coaId ?: null, $dateFrom, $dateTo);
        $ledger = $result['data']['ledger'];
        $openingBal = (float) $result['data']['opening_balance'];

        return Inertia::render('Reports/BukuBesar', [
            'accounts' => ChartOfAccount::orderBy('code')->get(),
            'selectedCoa' => $coaId,
            'ledger' => $ledger,
            'openingBal' => (float) $openingBal,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }
}
