<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\CashPeriod;
use App\Models\Expense;
use App\Models\ExpenseBatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /** Form upload CSV untuk import pengeluaran. */
    public function importForm(CashPeriod $period): View
    {
        return view('expenses.import', ['period' => $period]);
    }

    /** Download template CSV untuk import. */
    public function downloadTemplate(CashPeriod $period): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-pengeluaran.csv"',
        ];

        $callback = function () {
            $out = fopen('php://output', 'wb');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel baca UTF-8
            fputcsv($out, ['date', 'item_name', 'category', 'amount']);
            fputcsv($out, ['2026-06-05', 'Air Galon 2', 'Air', '43000']);
            fputcsv($out, ['2026-06-05', 'Listrik Monthly', 'Listrik', '1020370']);
            fclose($out);
        };

        return Response::stream($callback, 200, $headers);
    }

    /** Proses upload CSV pengeluaran. Header wajib: date, item_name, amount. */
    public function import(Request $request, CashPeriod $period): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:5120'],
        ]);

        $rows = $this->readCsv($request->file('file')->getRealPath());

        if (empty($rows)) {
            return back()->withErrors(['file' => 'File CSV kosong.']);
        }

        $errors = [];
        $valid = [];
        foreach ($rows as $i => $row) {
            $line = $i + 2; // header is line 1
            $date = trim($row['date'] ?? '');
            $itemName = trim($row['item_name'] ?? '');
            $category = trim($row['category'] ?? '');
            $amount = parse_rp($row['amount'] ?? 0);

            if ($date === '' || $itemName === '') {
                $errors[] = "Baris {$line}: tanggal & nama item wajib diisi.";

                continue;
            }
            try {
                $date = Carbon::parse($date)->toDateString();
            } catch (\Throwable $e) {
                $errors[] = "Baris {$line}: format tanggal tidak valid ({$date}).";

                continue;
            }
            if ($amount <= 0) {
                $errors[] = "Baris {$line}: nominal harus lebih dari 0.";

                continue;
            }

            $valid[] = compact('date', 'itemName', 'category', 'amount') + ['title' => trim($row['title'] ?? '')];
        }

        if (! empty($errors)) {
            return back()->withErrors(['file' => implode(' ', array_slice($errors, 0, 10))]);
        }

        // Kelompokkan per tanggal -> satu batch per tanggal
        $imported = 0;
        foreach (collect($valid)->groupBy('date') as $date => $group) {
            $sortOrder = ExpenseBatch::where('cash_period_id', $period->id)->max('sort_order') ?? 0;
            $batch = $period->expenseBatches()->create([
                'title' => 'Import '.$date,
                'batch_date' => $date,
                'sort_order' => $sortOrder + 1,
            ]);
            foreach ($group as $row) {
                $batch->expenses()->create([
                    'cash_period_id' => $period->id,
                    'item_name' => $row['itemName'],
                    'category' => $row['category'] !== '' ? $row['category'] : null,
                    'amount' => $row['amount'],
                    'expense_date' => $date,
                ]);
            }
            $imported++;
        }

        return to_route('periods.expenses.index', $period)
            ->with('toast', ['type' => 'success', 'message' => "Berhasil import {$imported} batch pengeluaran (".count($valid).' item).']);
    }

    /** Baca CSV (dengan header) menjadi array asosiatif. */
    protected function readCsv(string $path): array
    {
        if (($handle = fopen($path, 'r')) === false) {
            return [];
        }
        // deteksi delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = str_contains($firstLine, ';') && ! str_contains($firstLine, ',') ? ';' : ',';

        $rows = [];
        $header = null;
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $data = array_map(fn ($v) => is_string($v) ? trim($v, "\xEF\xBB\xBF\"") : $v, $data);
            if ($header === null) {
                $header = array_map(fn ($h) => Str::slug($h, '_'), array_filter($data, fn ($h) => $h !== null && $h !== ''));
                if (empty($header)) {
                    break;
                }

                continue;
            }
            if (count($data) === 1 && $data[0] === null) {
                continue; // skip baris kosong
            }
            $rows[] = array_combine($header, array_pad($data, count($header), ''));
        }
        fclose($handle);

        return $rows;
    }
}
