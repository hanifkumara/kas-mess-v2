<x-layout :title="'Laporan '. $period->name" :topbar-title="'Laporan '. $period->name">
    <x-page-header :title="'Report Kas Mess — '. $period->name" :subtitle="'Laporan pemasukan, pengeluaran, dan saldo berjalan'">
        <x-slot:actions>
            <x-button :href="route('periods.report.csv', $period)" variant="secondary" size="sm">⬇ CSV</x-button>
            <x-button :href="route('periods.report.excel', $period)" variant="secondary" size="sm">📊 Excel</x-button>
            <x-button :href="route('periods.report.pdf', $period)" variant="secondary" size="sm">📄 PDF</x-button>
            <x-button as="button" type="button" variant="primary" size="sm" onclick="window.print()">🖨️ Print</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="print-area space-y-6">
        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-stat-card label="Kas Awal" :value="rp($report['starting_balance'])" icon="🏦" tone="navy" />
            <x-stat-card label="Pemasukan" :value="rp($report['total_paid'])" icon="📈" tone="green" :hint="'Iuran '. $report['paid_count'] .' anggota'" />
            <x-stat-card label="Pengeluaran" :value="rp($report['total_expenses'])" icon="🧾" tone="red" />
            <x-stat-card label="Saldo Akhir" :value="rp($report['ending_balance'])" icon="💰" tone="navy" />
        </div>

        {{-- Tabel Pemasukan --}}
        <x-card title="Pemasukan">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-navy-800 text-xs uppercase tracking-wide text-navy-100">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Sumber</th>
                            <th class="px-5 py-3 text-right font-semibold">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50/70"><td class="px-5 py-3 text-slate-700">Kas Awal Periode</td><td class="px-5 py-3 text-right font-medium text-slate-700">{{ rp($report['starting_balance']) }}</td></tr>
                        <tr class="hover:bg-slate-50/70"><td class="px-5 py-3 text-slate-700">Iuran Dibayar ({{ $report['paid_count'] }} anggota)</td><td class="px-5 py-3 text-right font-medium text-emerald-600">{{ rp($report['total_paid']) }}</td></tr>
                        <tr class="bg-navy-50">
                            <td class="px-5 py-3 font-semibold text-navy-900">Total Pemasukan</td>
                            <td class="px-5 py-3 text-right text-base font-bold text-navy-900">{{ rp($report['total_income']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Pengeluaran per Batch --}}
        <x-card title="Pengeluaran per Batch" subtitle="Running balance dihitung otomatis dari pemasukan dikurangi tiap batch">
            <div class="space-y-6 p-4 sm:p-5">
                @forelse ($report['batches'] as $batch)
                    <div class="overflow-hidden rounded-xl ring-1 ring-slate-200">
                        {{-- Header batch --}}
                        <div class="flex flex-wrap items-center justify-between gap-2 bg-navy-800 px-5 py-3 text-white">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">{{ $batch['title'] }}</span>
                                @if ($batch['batch_date'])<span class="text-xs text-navy-200">· {{ tgl($batch['batch_date']) }}</span>@endif
                            </div>
                            <span class="text-xs text-navy-200">Saldo sebelum: <span class="font-semibold text-white">{{ rp($batch['balance_before']) }}</span></span>
                        </div>

                        {{-- Tabel item --}}
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-2.5 font-semibold">Item</th>
                                    <th class="px-5 py-2.5 font-semibold">Kategori</th>
                                    <th class="px-5 py-2.5 text-right font-semibold">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($batch['expenses'] as $exp)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-5 py-2.5 text-slate-700">{{ $exp->item_name }}</td>
                                        <td class="px-5 py-2.5">
                                            @if ($exp->category)
                                                <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $exp->category }}</span>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2.5 text-right font-medium text-rose-600">{{ rp($exp->amount) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-5 py-3 text-center text-slate-400">Tidak ada item.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td colspan="2" class="px-5 py-2.5 text-right font-semibold text-slate-600">Total {{ $batch['title'] }}</td>
                                    <td class="px-5 py-2.5 text-right font-bold text-rose-600">{{ rp($batch['total']) }}</td>
                                </tr>
                                <tr class="bg-navy-50">
                                    <td colspan="2" class="px-5 py-2.5 text-right font-semibold text-navy-900">Sisa saldo setelah {{ $batch['title'] }}</td>
                                    <td class="px-5 py-2.5 text-right font-bold @class(['text-rose-600' => $batch['balance_after'] < 0, 'text-emerald-600' => $batch['balance_after'] >= 0])">{{ rp($batch['balance_after']) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @empty
                    <x-empty-state icon="🧾" title="Belum ada pengeluaran" description="Tambahkan pengeluaran pada periode ini." />
                @endforelse

                {{-- Footer total keseluruhan --}}
                @if ($report['batches']->isNotEmpty())
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-navy-900 px-5 py-4 text-white">
                    <div>
                        <p class="text-xs text-navy-200">Saldo Akhir Periode</p>
                        <p class="text-sm text-navy-300">{{ rp($report['total_income']) }} pemasukan − {{ rp($report['total_expenses']) }} pengeluaran</p>
                    </div>
                    <p class="text-2xl font-bold">{{ rp($report['ending_balance']) }}</p>
                </div>
                @endif
            </div>
        </x-card>
    </div>
</x-layout>
