<?php

namespace App\Services;

use App\Models\CashPeriod;
use App\Models\ExpenseBatch;
use App\Models\Member;
use Illuminate\Support\Collection;

class CashReportService
{
    /**
     * Bangun seluruh struktur laporan & ringkasan untuk sebuah periode kas.
     * Semua angka dihitung dari data (tidak hardcoded).
     */
    public function build(CashPeriod $period): array
    {
        $period->load([
            'payments.member',
            'expenseBatches.expenses',
            'expenses',
        ]);

        $activeMembers = Member::active()->orderBy('name')->get();
        $monthlyDue = (int) $period->monthly_due;
        $startingBalance = (int) $period->starting_balance;

        // Peta pembayaran per member untuk periode ini
        $paymentByMember = $period->payments->keyBy('member_id');

        // Susun baris anggota dengan status pembayarannya
        $memberRows = $activeMembers->map(function (Member $member) use ($paymentByMember, $monthlyDue) {
            $payment = $paymentByMember->get($member->id);

            return [
                'member' => $member,
                'payment' => $payment,
                'status' => $payment?->status ?? 'unpaid',
                'amount' => (int) ($payment?->amount ?? 0),
                'expected' => $monthlyDue,
                'paid_at' => $payment?->paid_at,
            ];
        });

        $paidCount = $memberRows->where('status', 'paid')->count();
        $totalCount = $memberRows->count();
        $unpaidCount = $totalCount - $paidCount;

        $totalPaid = (int) $period->payments->where('status', 'paid')->sum('amount');
        $expectedDues = $totalCount * $monthlyDue;
        $totalUnpaid = max(0, $expectedDues - $totalPaid);

        $totalExpenses = (int) $period->expenses->sum('amount');
        $totalIncome = $startingBalance + $totalPaid;
        $endingBalance = $startingBalance + $totalPaid - $totalExpenses;

        $paymentProgress = $expectedDues > 0 ? round(($totalPaid / $expectedDues) * 100, 1) : 0;

        // Batch pengeluaran + running balance
        $batches = $this->buildBatches($period, $totalIncome);

        return [
            'period' => $period,
            'monthly_due' => $monthlyDue,
            'starting_balance' => $startingBalance,

            'active_members_count' => $totalCount,
            'paid_count' => $paidCount,
            'unpaid_count' => $unpaidCount,

            'expected_dues' => $expectedDues,
            'total_paid' => $totalPaid,
            'total_unpaid' => $totalUnpaid,
            'payment_progress' => $paymentProgress,

            'total_expenses' => $totalExpenses,
            'total_income' => $totalIncome,
            'ending_balance' => $endingBalance,

            'member_rows' => $memberRows,
            'batches' => $batches,
        ];
    }

    /**
     * Susun batch pengeluaran dengan running balance per batch.
     * Pemasukan (kas awal + iuran) diterapkan di awal, lalu setiap batch
     * mengurangi saldo secara berurutan (urut sort_order).
     * Pengeluaran tanpa batch digabung sebagai grup "Lainnya".
     */
    protected function buildBatches(CashPeriod $period, int $openingBalance): Collection
    {
        $running = $openingBalance;

        $batchedExpenseIds = $period->expenseBatches->flatMap->expenses->pluck('id')->all();
        $unbatched = $period->expenses->whereNull('expense_batch_id')
            ->reject(fn ($e) => in_array($e->id, $batchedExpenseIds));

        $nodes = collect();

        foreach ($period->expenseBatches as $batch) {
            $total = (int) $batch->expenses->sum('amount');
            $before = $running;
            $running -= $total;

            $nodes->push([
                'id' => $batch->id,
                'title' => $batch->title,
                'batch_date' => $batch->batch_date,
                'notes' => $batch->notes,
                'is_synthetic' => false,
                'expenses' => $batch->expenses,
                'total' => $total,
                'balance_before' => $before,
                'balance_after' => $running,
            ]);
        }

        if ($unbatched->isNotEmpty()) {
            $total = (int) $unbatched->sum('amount');
            $before = $running;
            $running -= $total;

            $nodes->push([
                'id' => null,
                'title' => 'Pengeluaran Lainnya',
                'batch_date' => null,
                'notes' => null,
                'is_synthetic' => true,
                'expenses' => $unbatched->values(),
                'total' => $total,
                'balance_before' => $before,
                'balance_after' => $running,
            ]);
        }

        return $nodes;
    }
}
