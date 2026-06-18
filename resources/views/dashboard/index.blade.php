<x-layout :title="'Dashboard · '. $report['period']->name" :topbar-title="'Kas '. $report['period']->name">
    <x-page-header :title="'Kas '. $report['period']->name" :subtitle="'Ringkasan keuangan periode berjalan'">
        <x-slot:actions>
            <x-button :href="route('periods.report.show', $report['period'])" variant="subtle">📊 Laporan</x-button>
            <x-button :href="route('periods.payments.index', $report['period'])" variant="secondary">✅ Kelola Iuran</x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-stat-card label="Kas Awal Bulan" :value="rp($report['starting_balance'])" icon="🏦" tone="navy" />
        <x-stat-card label="Iuran Seharusnya" :value="rp($report['expected_dues'])" icon="🎯" tone="sky" :hint="$report['active_members_count'].' anggota × '.rp($report['monthly_due'])" />
        <x-stat-card label="Sudah Dibayar" :value="rp($report['total_paid'])" icon="✅" tone="green" />
        <x-stat-card label="Belum Dibayar" :value="rp($report['total_unpaid'])" icon="⏳" tone="amber" :hint="$report['unpaid_count'].' anggota'" />
        <x-stat-card label="Total Pengeluaran" :value="rp($report['total_expenses'])" icon="🧾" tone="red" />
        <x-stat-card label="Saldo Akhir" :value="rp($report['ending_balance'])" icon="💰" tone="navy" />
    </div>

    {{-- Progress + anggota lunas --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1 space-y-6">
            <x-card title="Status Pembayaran" :subtitle="$report['paid_count'].' dari '.$report['active_members_count'].' anggota lunas'">
                <div class="px-5 py-5">
                    <div class="flex items-end justify-between">
                        <p class="text-3xl font-bold text-navy-900">{{ $report['payment_progress'] }}%</p>
                        <p class="text-sm text-slate-500">terkumpul</p>
                    </div>
                    <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all" style="width: {{ min(100, $report['payment_progress']) }}%"></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-xl bg-emerald-50 py-3 ring-1 ring-emerald-100">
                            <p class="text-xl font-bold text-emerald-700">{{ $report['paid_count'] }}</p>
                            <p class="text-xs text-emerald-700/80">Lunas</p>
                        </div>
                        <div class="rounded-xl bg-rose-50 py-3 ring-1 ring-rose-100">
                            <p class="text-xl font-bold text-rose-700">{{ $report['unpaid_count'] }}</p>
                            <p class="text-xs text-rose-700/80">Belum</p>
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card title="Ringkasan Saldo">
                <div class="divide-y divide-slate-100 px-5 text-sm">
                    <div class="flex items-center justify-between py-3">
                        <span class="text-slate-500">Kas Awal</span>
                        <span class="font-semibold text-slate-700">{{ rp($report['starting_balance']) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-emerald-600">(+) Iuran Dibayar</span>
                        <span class="font-semibold text-emerald-600">{{ rp($report['total_paid']) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="text-rose-600">(−) Pengeluaran</span>
                        <span class="font-semibold text-rose-600">{{ rp($report['total_expenses']) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <span class="font-semibold text-navy-900">Saldo Akhir</span>
                        <span class="text-lg font-bold text-navy-900">{{ rp($report['ending_balance']) }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Tabel anggota --}}
        <div class="lg:col-span-2">
            <x-card title="Status Iuran Anggota" :subtitle="$report['period']->name">
                <x-slot:actions>
                    <a href="{{ route('periods.payments.index', $report['period']) }}" class="text-sm font-semibold text-navy-700 hover:text-navy-900">Kelola →</a>
                </x-slot:actions>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Anggota</th>
                                <th class="px-5 py-3 font-semibold">Iuran</th>
                                <th class="px-5 py-3 font-semibold">Dibayar</th>
                                <th class="px-5 py-3 font-semibold">Tanggal</th>
                                <th class="px-5 py-3 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($report['member_rows'] as $row)
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-8 w-8 place-items-center rounded-full bg-navy-100 text-xs font-bold text-navy-700">{{ strtoupper(substr($row['member']->name, 0, 2)) }}</span>
                                            <span class="font-medium text-navy-900">{{ $row['member']->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ rp($row['expected']) }}</td>
                                    <td class="px-5 py-3 {{ $row['status'] === 'paid' ? 'text-emerald-600 font-medium' : 'text-slate-400' }}">{{ $row['status'] === 'paid' ? rp($row['amount']) : '—' }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ tgl($row['paid_at']) }}</td>
                                    <td class="px-5 py-3 text-right"><x-status-badge :status="$row['status']" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada anggota aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-layout>
