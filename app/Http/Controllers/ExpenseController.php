<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\CashPeriod;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request, CashPeriod $period): View
    {
        $expenses = $period->expenses()
            ->with('batch')
            ->when($request->filled('batch'), fn ($q) => $q->where('expense_batch_id', $request->integer('batch')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->latest('expense_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('expenses.index', [
            'period' => $period,
            'expenses' => $expenses,
            'batches' => $period->expenseBatches()->orderBy('sort_order')->get(),
            'categories' => Expense::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function create(CashPeriod $period): View
    {
        return view('expenses.form', [
            'period' => $period,
            'expense' => new Expense,
            'batches' => $period->expenseBatches()->orderBy('sort_order')->get(['id', 'title']),
        ]);
    }

    public function store(ExpenseRequest $request, CashPeriod $period): RedirectResponse
    {
        $period->expenses()->create($request->validated());

        return to_route('periods.expenses.index', $period)
            ->with('toast', ['type' => 'success', 'message' => 'Pengeluaran ditambahkan.']);
    }

    public function edit(CashPeriod $period, Expense $expense): View
    {
        return view('expenses.form', [
            'period' => $period,
            'expense' => $expense,
            'batches' => $period->expenseBatches()->orderBy('sort_order')->get(['id', 'title']),
        ]);
    }

    public function update(ExpenseRequest $request, CashPeriod $period, Expense $expense): RedirectResponse
    {
        $expense->update($request->validated());

        return to_route('periods.expenses.index', $period)
            ->with('toast', ['type' => 'success', 'message' => 'Pengeluaran diperbarui.']);
    }

    public function destroy(CashPeriod $period, Expense $expense): RedirectResponse
    {
        $expense->delete();

        return to_route('periods.expenses.index', $period)
            ->with('toast', ['type' => 'success', 'message' => 'Pengeluaran dihapus.']);
    }
}
