<x-layout :title="'Batch Pengeluaran '. $period->name" :topbarTitle="'Batch '. $period->name">
    <x-page-header :title="'Batch Pengeluaran — '. $period->name" subtitle="Kelompokkan pengeluaran per batch/minggu">
        <x-slot:actions>
            <x-button :href="route('periods.batches.create', $period)">+ Tambah Batch</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($batches->isEmpty())
        <x-empty-state icon="📦" title="Belum ada batch" description="Buat batch seperti Batch 1, Batch 2, dst. untuk mengelompokkan pengeluaran.">
            <x-slot:action>
                <x-button :href="route('periods.batches.create', $period)">+ Tambah Batch</x-button>
            </x-slot:action>
        </x-empty-state>
    @else
        <x-card title="Daftar Batch" :subtitle="$batches->count() .' batch'">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Batch</th>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold">Urutan</th>
                            <th class="px-5 py-3 font-semibold">Jumlah Item</th>
                            <th class="px-5 py-3 text-right font-semibold">Total</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($batches as $batch)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-navy-900">{{ $batch->title }}</p>
                                    @if ($batch->notes)<p class="text-xs text-slate-400">{{ $batch->notes }}</p>@endif
                                </td>
                                <td class="px-5 py-3 text-slate-500">{{ tgl($batch->batch_date) }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $batch->sort_order }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $batch->expenses->count() }} item</td>
                                <td class="px-5 py-3 text-right font-medium text-rose-600">{{ rp($batch->expenses->sum('amount')) }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('periods.expenses.index', $period).'?batch='.$batch->id }}" class="text-xs font-medium text-slate-600 hover:text-navy-900">Item</a>
                                        <a href="{{ route('periods.batches.edit', [$period, $batch]) }}" class="text-sm font-medium text-navy-700 hover:text-navy-900">Edit</a>
                                        <x-confirm-delete :action="route('periods.batches.destroy', [$period, $batch])" label="Hapus" message="Hapus batch ini? Item di dalamnya akan dipindahkan ke 'Lainnya'." />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</x-layout>
