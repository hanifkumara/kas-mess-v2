<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseBatchRequest;
use App\Models\CashPeriod;
use App\Models\ExpenseBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseBatchController extends Controller
{
    public function index(CashPeriod $period): View
    {
        $batches = $period->expenseBatches()->orderBy('sort_order')->get();

        return view('batches.index', ['period' => $period, 'batches' => $batches]);
    }

    public function create(CashPeriod $period): View
    {
        return view('batches.form', ['period' => $period, 'batch' => new ExpenseBatch]);
    }

    public function store(ExpenseBatchRequest $request, CashPeriod $period): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? (ExpenseBatch::where('cash_period_id', $period->id)->max('sort_order') + 1 ?? 1);

        $period->expenseBatches()->create($data);

        return to_route('periods.batches.index', $period)
            ->with('toast', ['type' => 'success', 'message' => 'Batch pengeluaran dibuat.']);
    }

    public function edit(CashPeriod $period, ExpenseBatch $batch): View
    {
        return view('batches.form', ['period' => $period, 'batch' => $batch]);
    }

    public function update(ExpenseBatchRequest $request, CashPeriod $period, ExpenseBatch $batch): RedirectResponse
    {
        $batch->update($request->validated());

        return to_route('periods.batches.index', $period)
            ->with('toast', ['type' => 'success', 'message' => 'Batch diperbarui.']);
    }

    public function destroy(CashPeriod $period, ExpenseBatch $batch): RedirectResponse
    {
        $batch->expenses()->update(['expense_batch_id' => null]);
        $batch->delete();

        return to_route('periods.batches.index', $period)
            ->with('toast', ['type' => 'success', 'message' => 'Batch dihapus. Item dipindah ke "Lainnya".']);
    }
}
