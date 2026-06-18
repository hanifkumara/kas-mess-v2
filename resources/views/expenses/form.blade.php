@php
    $categories = ['Air', 'Listrik', 'Beras', 'Gas', 'Sabun', 'IPL', 'Lainnya'];
@endphp
<x-layout :title="$expense->exists ? 'Edit Pengeluaran' : 'Tambah Pengeluaran'" :topbarTitle="$expense->exists ? 'Edit Pengeluaran' : 'Tambah Pengeluaran'">
    <x-page-header :title="$expense->exists ? 'Edit Pengeluaran' : 'Tambah Pengeluaran'" :subtitle="'Periode '. $period->name" />

    <x-card title="Detail Pengeluaran" class="max-w-2xl">
        <form method="POST" action="{{ $expense->exists ? route('periods.expenses.update', [$period, $expense]) : route('periods.expenses.store', $period) }}">
            @csrf
            @if ($expense->exists)
                @method('PUT')
            @endif

            <div class="divide-y divide-slate-100">
                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="item_name">Nama Item</label>
                    <input id="item_name" type="text" name="item_name" value="{{ old('item_name', $expense->item_name) }}" placeholder="contoh: Air Galon 2" required autofocus
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                    @error('item_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 px-5 py-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="category">Kategori</label>
                        <select id="category" name="category" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                            <option value="">— Pilih —</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c }}" @selected(old('category', $expense->category) === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="amount">Harga</label>
                        <input id="amount" type="number" name="amount" value="{{ old('amount', $expense->amount) }}" min="0" step="100" required
                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        <p class="mt-1 text-xs text-slate-400">Dalam rupiah</p>
                        @error('amount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 px-5 py-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="expense_date">Tanggal</label>
                        <input id="expense_date" type="date" name="expense_date" value="{{ old('expense_date', optional($expense->expense_date)->toDateString() ?? date('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        @error('expense_date') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="expense_batch_id">Batch</label>
                        <select id="expense_batch_id" name="expense_batch_id" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                            <option value="">Tanpa batch (Lainnya)</option>
                            @foreach ($batches as $b)
                                <option value="{{ $b->id }}" @selected(old('expense_batch_id', $expense->expense_batch_id) == $b->id)>{{ $b->title }}</option>
                            @endforeach
                        </select>
                        @error('expense_batch_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="notes">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">{{ old('notes', $expense->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 px-5 py-4">
                <x-button as="button" type="button" variant="secondary" :href="route('periods.expenses.index', $period)">Batal</x-button>
                <x-button as="button" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-card>
</x-layout>
