<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\CashPeriod;
use App\Models\Member;
use App\Models\Payment;
use App\Services\CashReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private CashReportService $reportService) {}

    public function index(CashPeriod $period): View
    {
        $report = $this->reportService->build($period);

        return view('payments.index', [
            'period' => $period,
            'report' => $report,
        ]);
    }

    public function update(PaymentRequest $request, CashPeriod $period, Payment $payment): RedirectResponse
    {
        $data = $request->validated();
        $data['paid_at'] = ($data['status'] ?? $payment->status) === 'paid'
            ? ($data['paid_at'] ?? now()->toDateString())
            : null;

        $payment->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Pembayaran diperbarui.']);
    }

    /** Tombol cepat: tandai anggota lunas (buat record bila belum ada). */
    public function markPaid(CashPeriod $period, Member $member): RedirectResponse
    {
        $payment = $this->findOrCreatePayment($period, $member);
        $payment->update([
            'status' => 'paid',
            'amount' => $payment->amount > 0 ? $payment->amount : $period->monthly_due,
            'paid_at' => $payment->paid_at ?? now()->toDateString(),
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => "Iuran {$member->name} ditandai lunas."]);
    }

    /** Tombol cepat: batalkan lunas. */
    public function markUnpaid(CashPeriod $period, Member $member): RedirectResponse
    {
        $payment = $this->findOrCreatePayment($period, $member);
        $payment->update([
            'status' => 'unpaid',
            'paid_at' => null,
        ]);

        return back()->with('toast', ['type' => 'info', 'message' => "Iuran {$member->name} dibatalkan lunas."]);
    }

    /** Tombol cepat: tandai semua anggota aktif lunas. */
    public function markAllPaid(CashPeriod $period): RedirectResponse
    {
        $memberIds = Member::active()->pluck('id');

        foreach ($memberIds as $memberId) {
            $payment = $this->findOrCreatePaymentByIds($period, $memberId);
            $payment->update([
                'status' => 'paid',
                'amount' => $period->monthly_due,
                'paid_at' => now()->toDateString(),
            ]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Semua anggota aktif ditandai lunas.']);
    }

    protected function findOrCreatePayment(CashPeriod $period, Member $member): Payment
    {
        return $this->findOrCreatePaymentByIds($period, $member->id);
    }

    protected function findOrCreatePaymentByIds(CashPeriod $period, int $memberId): Payment
    {
        return Payment::firstOrCreate(
            ['cash_period_id' => $period->id, 'member_id' => $memberId],
            ['amount' => 0, 'status' => 'unpaid'],
        );
    }
}
