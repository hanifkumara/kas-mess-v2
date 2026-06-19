<?php

namespace App\Http\Controllers;

use App\Exports\ReportExcelExport;
use App\Models\CashPeriod;
use App\Services\CashReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private CashReportService $reportService) {}

    public function show(Request $request, CashPeriod $period): View
    {
        $report = $this->reportService->build($period);

        return view('reports.show', ['report' => $report, 'period' => $period]);
    }

    public function exportCsv(CashPeriod $period): StreamedResponse
    {
        $report = $this->reportService->build($period);
        $periodName = Str::slug($period->name);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"laporan-kas-{$periodName}.csv\"",
        ];

        $callback = function () use ($report) {
            $out = fopen('php://output', 'wb');
            // BOM agar Excel membaca UTF-8
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Laporan Kas Mess - '.$report['period']->name]);
            fputcsv($out, []);

            fputcsv($out, ['Ringkasan']);
            fputcsv($out, ['Kas Awal', $report['starting_balance']]);
            fputcsv($out, ['Total Iuran Seharusnya', $report['expected_dues']]);
            fputcsv($out, ['Total Iuran Dibayar', $report['total_paid']]);
            fputcsv($out, ['Total Belum Dibayar', $report['total_unpaid']]);
            fputcsv($out, ['Total Pengeluaran', $report['total_expenses']]);
            fputcsv($out, ['Saldo Akhir', $report['ending_balance']]);
            fputcsv($out, []);

            fputcsv($out, ['Pemasukan']);
            fputcsv($out, ['Sumber', 'Nominal']);
            fputcsv($out, ['Kas Awal', $report['starting_balance']]);
            fputcsv($out, ['Iuran Dibayar', $report['total_paid']]);
            fputcsv($out, []);

            fputcsv($out, ['Pengeluaran per Batch']);
            foreach ($report['batches'] as $batch) {
                fputcsv($out, [$batch['title']]);
                fputcsv($out, ['Item', 'Kategori', 'Harga']);
                foreach ($batch['expenses'] as $exp) {
                    fputcsv($out, [$exp->item_name, $exp->category ?? '-', $exp->amount]);
                }
                fputcsv($out, ['Total '.$batch['title'], $batch['total']]);
                fputcsv($out, ['Saldo Sebelum', $batch['balance_before']]);
                fputcsv($out, ['Sisa Saldo', $batch['balance_after']]);
                fputcsv($out, []);
            }

            fclose($out);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel(CashPeriod $period): StreamedResponse
    {
        $report = $this->reportService->build($period);

        return (new ReportExcelExport)->download($period, $report);
    }

    public function exportPdf(CashPeriod $period)
    {
        $report = $this->reportService->build($period);
        $filename = 'laporan-kas-'.Str::slug($period->name).'.pdf';

        $pdf = Pdf::loadView('reports.pdf', ['report' => $report])
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
