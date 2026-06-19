<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kas Mess — {{ $report['period']->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; color: #1e293b; font-size: 11px; margin: 0; }
        @page { size: A4; margin: 16mm 14mm; }
        .header { border-bottom: 3px solid #16224f; padding-bottom: 10px; margin-bottom: 14px; }
        .header h1 { color: #16224f; font-size: 18px; margin: 0 0 2px 0; }
        .header p { color: #64748b; margin: 0; font-size: 10px; }
        .section-title { color: #16224f; font-size: 12px; font-weight: bold; background: #eef2fb; padding: 6px 10px; border-radius: 4px; margin: 14px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        th { background: #16224f; color: #fff; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .3px; }
        td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        td.right, th.right { text-align: right; }
        .muted { color: #64748b; }
        .green { color: #1e8449; font-weight: bold; }
        .red { color: #c0392b; font-weight: bold; }
        tr.batch-head td { background: #16224f; color: #fff; font-weight: bold; }
        tr.batch-total td { background: #f1f5f9; font-weight: bold; }
        tr.batch-run td { background: #eef2fb; font-weight: bold; color: #16224f; }
        tr.summary-foot td { background: #eef2fb; font-weight: bold; color: #16224f; }
        tr.grand td { background: #16224f; color: #fff; font-weight: bold; font-size: 12px; }
        .pill { display: inline-block; background: #e2e8f0; color: #475569; padding: 1px 6px; border-radius: 8px; font-size: 9px; }
        .stat-row { width: 100%; margin-bottom: 12px; }
        .stat { width: 33%; display: inline-block; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; margin-right: 0.3%; }
        .stat .lbl { color: #64748b; font-size: 9px; text-transform: uppercase; }
        .stat .val { color: #16224f; font-size: 14px; font-weight: bold; }
        .footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kas Mess — {{ $report['period']->name }}</h1>
        <p>Dicetak pada {{ tgl(now(), 'd M Y') }} · Saldo akhir {{ rp($report['ending_balance']) }}</p>
    </div>

    <div class="stat-row">
        <div class="stat"><div class="lbl">Kas Awal</div><div class="val">{{ rp($report['starting_balance']) }}</div></div>
        <div class="stat"><div class="lbl">Pemasukan</div><div class="val green">{{ rp($report['total_paid']) }}</div></div>
        <div class="stat"><div class="lbl">Pengeluaran</div><div class="val red">{{ rp($report['total_expenses']) }}</div></div>
    </div>

    <div class="section-title">Pemasukan</div>
    <table>
        <thead>
            <tr><th>Sumber</th><th class="right">Nominal</th></tr>
        </thead>
        <tbody>
            <tr><td>Kas Awal Periode</td><td class="right">{{ rp($report['starting_balance']) }}</td></tr>
            <tr><td>Iuran Dibayar ({{ $report['paid_count'] }} anggota)</td><td class="right green">{{ rp($report['total_paid']) }}</td></tr>
            <tr class="summary-foot"><td>Total Pemasukan</td><td class="right">{{ rp($report['total_income']) }}</td></tr>
        </tbody>
    </table>

    <div class="section-title">Pengeluaran per Batch</div>
    @foreach ($report['batches'] as $batch)
        <table>
            <thead>
                <tr><th>Item</th><th>Kategori</th><th class="right">Harga</th></tr>
            </thead>
            <tbody>
                <tr class="batch-head">
                    <td>{{ $batch['title'] }}@if ($batch['batch_date']) — {{ tgl($batch['batch_date']) }}@endif</td>
                    <td colspan="2" class="right">Saldo sebelum: {{ rp($batch['balance_before']) }}</td>
                </tr>
                @forelse ($batch['expenses'] as $exp)
                    <tr>
                        <td>{{ $exp->item_name }}</td>
                        <td>@if ($exp->category)<span class="pill">{{ $exp->category }}</span>@else <span class="muted">—</span>@endif</td>
                        <td class="right red">{{ rp($exp->amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="muted">Tidak ada item.</td></tr>
                @endforelse
                <tr class="batch-total"><td colspan="2" class="right">Total {{ $batch['title'] }}</td><td class="right red">{{ rp($batch['total']) }}</td></tr>
                <tr class="batch-run"><td colspan="2" class="right">Sisa saldo setelah {{ $batch['title'] }}</td><td class="right @if($batch['balance_after']<0) red @else green @endif">{{ rp($batch['balance_after']) }}</td></tr>
            </tbody>
        </table>
    @endforeach

    <table>
        <tbody>
            <tr class="grand">
                <td>SALDO AKHIR PERIODE</td>
                <td class="right">{{ rp($report['ending_balance']) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">Kas Mess · Laporan {{ $report['period']->name }} · dibuat otomatis</div>
</body>
</html>
