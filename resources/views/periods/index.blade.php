<x-layout title="Periode Kas" topbarTitle="Periode Kas">
    <x-page-header title="Periode Kas" subtitle="Kelola periode iuran bulanan">
        <x-slot:actions>
            <x-button :href="route('periods.create')">+ Buat Periode</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($periods->isEmpty())
        <x-empty-state icon="🗓️" title="Belum ada periode kas" description="Buat periode pertama, misalnya Juni 2026, lengkap dengan iuran dan kas awal.">
            <x-slot:action>
                <x-button :href="route('periods.create')">+ Buat Periode</x-button>
            </x-slot:action>
        </x-empty-state>
    @else
        <x-card title="Daftar Periode" :subtitle="$periods->total() .' periode'">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Periode</th>
                            <th class="px-5 py-3 font-semibold">Iuran / Anggota</th>
                            <th class="px-5 py-3 font-semibold">Kas Awal</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($periods as $period)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-navy-900">{{ $period->name }}</p>
                                    <p class="text-xs text-slate-400">Bulan {{ $period->month }} / {{ $period->year }}</p>
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ rp($period->monthly_due) }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ rp($period->starting_balance) }}</td>
                                <td class="px-5 py-3">
                                    @if ($period->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500 ring-1 ring-inset ring-slate-200">Arsip</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-x-3 gap-y-1">
                                        @if (! $period->is_active)
                                            <form method="POST" action="{{ route('periods.activate', $period) }}">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-navy-700 hover:text-navy-900">Aktifkan</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('periods.payments.index', $period) }}" class="text-xs font-medium text-slate-600 hover:text-navy-900">Iuran</a>
                                        <a href="{{ route('periods.expenses.index', $period) }}" class="text-xs font-medium text-slate-600 hover:text-navy-900">Pengeluaran</a>
                                        <a href="{{ route('periods.report.show', $period) }}" class="text-xs font-medium text-slate-600 hover:text-navy-900">Laporan</a>
                                        <a href="{{ route('periods.edit', $period) }}" class="text-sm font-medium text-navy-700 hover:text-navy-900">Edit</a>
                                        <x-confirm-delete :action="route('periods.destroy', $period)" label="Hapus" :message="'Hapus periode &quot;'.e($period->name).'&quot;? Semua data iuran & pengeluaran terkait juga akan terhapus.'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $periods->links() }}
            </div>
        </x-card>
    @endif
</x-layout>
