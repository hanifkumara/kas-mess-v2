<?php

namespace App\Exports;

use App\Models\CashPeriod;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExcelExport
{
    private const NAVY = '16224F';

    private const NAVY_LIGHT = 'EEF2FB';

    private const SLATE = 'F1F5F9';

    private const RED = 'C0392B';

    private const GREEN = '1E8449';

    public function __construct(private readonly Spreadsheet $spreadsheet = new Spreadsheet) {}

    /** Bangun spreadsheet laporan dari data hasil CashReportService. */
    public function build(CashPeriod $period, array $r): Spreadsheet
    {
        $sh = $this->spreadsheet->getActiveSheet();
        $sh->setTitle(substr('Laporan '.$period->name, 0, 31));

        $row = 1;
        $row = $this->writeTitle($sh, $row, $period);
        $row = $this->writeSummary($sh, $row, $r);
        $row = $this->writeIncome($sh, $row, $r);
        $row = $this->writeExpenseBatches($sh, $row, $r);

        $this->sizeColumns($sh);

        return $this->spreadsheet;
    }

    /** Render ke response download .xlsx */
    public function download(CashPeriod $period, array $r): StreamedResponse
    {
        $this->build($period, $r);
        $writer = new Xlsx($this->spreadsheet);
        $filename = 'laporan-kas-'.str($period->name)->slug().'.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function writeTitle(Worksheet $sh, int $row, CashPeriod $period): int
    {
        $sh->setCellValue("A{$row}", 'Laporan Kas Mess — '.$period->name);
        $sh->mergeCells("A{$row}:C{$row}");
        $sh->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14)->getColor()->setRGB(self::NAVY);
        $sh->getRowDimension($row)->setRowHeight(24);

        return $row + 2;
    }

    private function writeSummary(Worksheet $sh, int $row, array $r): int
    {
        $row = $this->sectionHeader($sh, $row, 'Ringkasan');

        $lines = [
            'Kas Awal' => $r['starting_balance'],
            'Iuran Seharusnya' => $r['expected_dues'],
            'Iuran Dibayar' => $r['total_paid'],
            'Belum Dibayar' => $r['total_unpaid'],
            'Total Pengeluaran' => $r['total_expenses'],
        ];
        foreach ($lines as $label => $value) {
            $sh->setCellValue("A{$row}", $label);
            $sh->setCellValue("C{$row}", $value);
            $this->amountCell($sh, "C{$row}");
            $sh->getStyle("A{$row}")->getFont()->getColor()->setRGB('475569');
            $row++;
        }

        // Saldo akhir (highlight)
        $sh->setCellValue("A{$row}", 'Saldo Akhir');
        $sh->setCellValue("C{$row}", $r['ending_balance']);
        $this->amountCell($sh, "C{$row}");
        $sh->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => self::NAVY]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY_LIGHT]],
        ]);

        return $row + 2;
    }

    private function writeIncome(Worksheet $sh, int $row, array $r): int
    {
        $row = $this->tableHeader($sh, $row, ['Sumber', '', 'Nominal']);

        $items = [
            'Kas Awal Periode' => $r['starting_balance'],
            'Iuran Dibayar'.' ('.$r['paid_count'].' anggota)' => $r['total_paid'],
        ];
        foreach ($items as $label => $value) {
            $sh->setCellValue("A{$row}", $label);
            $sh->mergeCells("A{$row}:B{$row}");
            $sh->setCellValue("C{$row}", $value);
            $this->amountCell($sh, "C{$row}");
            $row++;
        }

        $sh->setCellValue("A{$row}", 'Total Pemasukan');
        $sh->mergeCells("A{$row}:B{$row}");
        $sh->setCellValue("C{$row}", $r['total_income']);
        $this->amountCell($sh, "C{$row}");
        $sh->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => self::NAVY]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY_LIGHT]],
        ]);

        return $row + 2;
    }

    private function writeExpenseBatches(Worksheet $sh, int $row, array $r): int
    {
        $row = $this->tableHeader($sh, $row, ['Item', 'Kategori', 'Harga']);

        foreach ($r['batches'] as $batch) {
            // Batch sub-header
            $sh->setCellValue("A{$row}", $batch['title']);
            $sh->setCellValue("B{$row}", 'Saldo sebelum');
            $sh->setCellValue("C{$row}", $batch['balance_before']);
            $sh->getStyle("A{$row}:C{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
            ]);
            $this->amountCell($sh, "C{$row}");
            $row++;

            // Item rows
            foreach ($batch['expenses'] as $exp) {
                $sh->setCellValue("A{$row}", $exp->item_name);
                $sh->setCellValue("B{$row}", $exp->category ?? '-');
                $sh->setCellValue("C{$row}", $exp->amount);
                $this->amountCell($sh, "C{$row}");
                $sh->getStyle("C{$row}")->getFont()->getColor()->setRGB(self::RED);
                $row++;
            }

            // Batch total + running balance
            $sh->setCellValue("A{$row}", 'Total '.$batch['title']);
            $sh->mergeCells("A{$row}:B{$row}");
            $sh->setCellValue("C{$row}", $batch['total']);
            $this->amountCell($sh, "C{$row}");
            $sh->getStyle("A{$row}:C{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::SLATE]],
            ]);
            $row++;

            $sh->setCellValue("A{$row}", 'Sisa saldo setelah '.$batch['title']);
            $sh->mergeCells("A{$row}:B{$row}");
            $sh->setCellValue("C{$row}", $batch['balance_after']);
            $this->amountCell($sh, "C{$row}");
            $color = $batch['balance_after'] < 0 ? self::RED : self::GREEN;
            $sh->getStyle("A{$row}:C{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $color]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY_LIGHT]],
            ]);
            $row++;
        }

        // Grand total footer
        $sh->setCellValue("A{$row}", 'SALDO AKHIR PERIODE');
        $sh->mergeCells("A{$row}:B{$row}");
        $sh->setCellValue("C{$row}", $r['ending_balance']);
        $this->amountCell($sh, "C{$row}");
        $sh->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
        $sh->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return $row + 1;
    }

    private function sectionHeader(Worksheet $sh, int $row, string $title): int
    {
        $sh->setCellValue("A{$row}", $title);
        $sh->mergeCells("A{$row}:C{$row}");
        $sh->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::NAVY]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY_LIGHT]],
        ]);

        return $row + 1;
    }

    private function tableHeader(Worksheet $sh, int $row, array $cols): int
    {
        foreach ($cols as $i => $col) {
            $cell = chr(65 + $i).$row;
            $sh->setCellValue($cell, $col);
        }
        $sh->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        return $row + 1;
    }

    private function amountCell(Worksheet $sh, string $cell): void
    {
        $sh->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0');
        $sh->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    private function sizeColumns(Worksheet $sh): void
    {
        $sh->getColumnDimension('A')->setAutoSize(true);
        $sh->getColumnDimension('B')->setAutoSize(true);
        $sh->getColumnDimension('C')->setWidth(18);
        $sh->getStyle('A1:C'.$sh->getHighestRow())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sh->setShowGridlines(false);
        $sh->getParent()?->getProperties()?->setTitle('Laporan Kas Mess');
    }
}
