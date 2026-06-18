<x-layout :title="'Pengeluaran '. $period->name" :topbarTitle="'Pengeluaran '. $period->name">
    <x-page-header :title="'Pengeluaran — '. $period->name" :subtitle="'Kas awal periode: '. rp($period->starting_balance)">
        <x-slot:actions>
            <x-button :href="route('periods.expenses.importForm', $period)" variant="secondary" size="sm">📥 Import CSV</x-button>
            <x-button :href="route('periods.expenses.create', $period)">+ Tambah Pengeluaran</x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter --}}
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500">Batch</label>
            <select name="batch" class="mt-1 rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                <option value="">Semua batch</option>
                @foreach ($batches as $b)
                    <option value="{{ $b->id }}" @selected(request('batch') == $b->id)>{{ $b->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500">Kategori</label>
            <select name="category" class="mt-1 rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" @selected(request('category') == $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <x-button as="button" type="submit" variant="secondary" size="sm">Filter</x-button>
        <a href="{{ route('periods.expenses.index', $period) }}" class="text-sm font-medium text-slate-500 hover:text-navy-700">Reset</a>
    </form>

    @if ($expenses->isEmpty())
        <x-empty-state icon="🧾" title="Belum ada pengeluaran" description="Catat pengeluaran untuk periode ini.">
            <x-slot:action>
                <x-button :href="route('periods.expenses.create', $period)">+ Tambah Pengeluaran</x-button>
            </x-slot:action>
        </x-empty-state>
    @else
        <x-card title="Daftar Pengeluaran" :subtitle="$expenses->total() .' item'">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Item</th>
                            <th class="px-5 py-3 font-semibold">Kategori</th>
                            <th class="px-5 py-3 font-semibold">Batch</th>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 text-right font-semibold">Harga</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($expenses as $expense)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-3 text-slate-700">{{ $expense->item_name }}</td>
                                <td class="px-5 py-3">
                                    @if ($expense->category)
                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $expense->category }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-500">{{ $expense->batch?->title ?? 'Lainnya' }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ tgl($expense->expense_date) }}</td>
                                <td class="px-5 py-3 text-right font-medium text-rose-600">{{ rp($expense->amount) }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('periods.expenses.edit', [$period, $expense]) }}" class="text-sm font-medium text-navy-700 hover:text-navy-900">Edit</a>
                                        <x-confirm-delete :action="route('periods.expenses.destroy', [$period, $expense])" label="Hapus" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $expenses->links() }}
            </div>
        </x-card>
    @endif
</x-layout>
