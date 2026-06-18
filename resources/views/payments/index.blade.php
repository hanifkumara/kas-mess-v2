<x-layout :title="'Iuran '. $period->name" :topbar-title="'Iuran '. $period->name">
    <x-page-header :title="'Pembayaran Iuran — '. $period->name" :subtitle="'Iuran per anggota: '. rp($period->monthly_due)">
        <x-slot:actions>
            <form method="POST" action="{{ route('periods.payments.markAllPaid', $period) }}" onsubmit="return confirm('Tandai SEMUA anggota aktif lunas?')">
                @csrf
                <x-button as="button">✅ Tandai Semua Lunas</x-button>
            </form>
        </x-slot:actions>
    </x-page-header>

    {{-- Ringkasan --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Terkumpul" :value="rp($report['total_paid'])" icon="✅" tone="green" />
        <x-stat-card label="Belum Dibayar" :value="rp($report['total_unpaid'])" icon="⏳" tone="amber" />
        <x-stat-card label="Lunas" :value="$report['paid_count'] .' / '. $report['active_members_count']" icon="👥" tone="navy" />
        <x-stat-card label="Progress" :value="$report['payment_progress'] .' %'" icon="📊" tone="sky" />
    </div>

    <x-card title="Daftar Iuran Anggota">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Anggota</th>
                        <th class="px-5 py-3 font-semibold">Iuran</th>
                        <th class="px-5 py-3 font-semibold">Dibayar</th>
                        <th class="px-5 py-3 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
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
                            <td class="px-5 py-3 {{ $row['status'] === 'paid' ? 'font-medium text-emerald-600' : 'text-slate-400' }}">{{ $row['status'] === 'paid' ? rp($row['amount']) : '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ tgl($row['paid_at']) }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$row['status']" /></td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($row['status'] === 'paid')
                                        <form method="POST" action="{{ route('periods.payments.markUnpaid', [$period, $row['member']]) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-amber-600 ring-1 ring-inset ring-amber-200 hover:bg-amber-50">↩ Batalkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('periods.payments.markPaid', [$period, $row['member']]) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">✓ Tandai Lunas</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada anggota aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layout>
