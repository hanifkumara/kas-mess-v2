<x-layout :title="$batch->exists ? 'Edit Batch' : 'Tambah Batch'" :topbarTitle="$batch->exists ? 'Edit Batch' : 'Tambah Batch'">
    <x-page-header :title="$batch->exists ? 'Edit Batch' : 'Tambah Batch'" :subtitle="'Periode '. $period->name" />

    <x-card title="Detail Batch" class="max-w-2xl">
        <form method="POST" action="{{ $batch->exists ? route('periods.batches.update', [$period, $batch]) : route('periods.batches.store', $period) }}">
            @csrf
            @if ($batch->exists)
                @method('PUT')
            @endif

            <div class="divide-y divide-slate-100">
                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="title">Judul Batch</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $batch->title) }}" placeholder="contoh: Batch 1" required autofocus
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                    @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 px-5 py-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="batch_date">Tanggal</label>
                        <input id="batch_date" type="date" name="batch_date" value="{{ old('batch_date', optional($batch->batch_date)->toDateString()) }}"
                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        @error('batch_date') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="sort_order">Urutan</label>
                        <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $batch->sort_order ?? 0) }}" min="0"
                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        <p class="mt-1 text-xs text-slate-400">Angka kecil = tampil lebih awal</p>
                        @error('sort_order') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="notes">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">{{ old('notes', $batch->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 px-5 py-4">
                <x-button as="button" type="button" variant="secondary" :href="route('periods.batches.index', $period)">Batal</x-button>
                <x-button as="button" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-card>
</x-layout>
