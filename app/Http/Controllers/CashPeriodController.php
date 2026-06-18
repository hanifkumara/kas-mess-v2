<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashPeriodRequest;
use App\Models\CashPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CashPeriodController extends Controller
{
    public function index(): View
    {
        $periods = CashPeriod::orderByDesc('year')->orderByDesc('month')->paginate(15);

        return view('periods.index', ['periods' => $periods]);
    }

    public function create(): View
    {
        return view('periods.form', ['period' => new CashPeriod]);
    }

    public function store(CashPeriodRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (! empty($data['is_active'])) {
            CashPeriod::active()->update(['is_active' => false]);
        }

        $period = CashPeriod::create($data);

        return to_route('periods.index')
            ->with('toast', ['type' => 'success', 'message' => "Periode \"{$period->name}\" dibuat."]);
    }

    public function edit(CashPeriod $period): View
    {
        return view('periods.form', ['period' => $period]);
    }

    public function update(CashPeriodRequest $request, CashPeriod $period): RedirectResponse
    {
        $data = $request->validated();
        if (! empty($data['is_active']) && ! $period->is_active) {
            CashPeriod::whereKeyNot($period)->active()->update(['is_active' => false]);
        }

        $period->update($data);

        return to_route('periods.index')
            ->with('toast', ['type' => 'success', 'message' => "Periode \"{$period->name}\" diperbarui."]);
    }

    public function destroy(CashPeriod $period): RedirectResponse
    {
        $name = $period->name;
        $period->delete();

        return to_route('periods.index')
            ->with('toast', ['type' => 'success', 'message' => "Periode \"{$name}\" dihapus."]);
    }

    public function activate(CashPeriod $period): RedirectResponse
    {
        CashPeriod::whereKeyNot($period)->update(['is_active' => false]);
        $period->update(['is_active' => true]);

        return back()->with('toast', ['type' => 'success', 'message' => "\"{$period->name}\" dijadikan periode aktif."]);
    }
}
