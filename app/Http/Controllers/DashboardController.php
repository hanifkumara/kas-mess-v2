<?php

namespace App\Http\Controllers;

use App\Models\CashPeriod;
use App\Services\CashReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private CashReportService $reportService)
    {
    }

    public function index(Request $request)
    {
        $period = CashPeriod::active()->latest('year')->latest('month')->first()
            ?? CashPeriod::latest('year')->latest('month')->first();

        $periods = CashPeriod::orderByDesc('year')->orderByDesc('month')->get(['id', 'name']);

        if (! $period) {
            return view('dashboard.empty', ['periods' => $periods]);
        }

        // Izinkan memilih periode lewat query ?period=
        if ($request->filled('period')) {
            $found = CashPeriod::find($request->integer('period'));
            if ($found) {
                $period = $found;
            }
        }

        $report = $this->reportService->build($period);

        return view('dashboard.index', [
            'report' => $report,
            'periods' => $periods,
        ]);
    }
}
