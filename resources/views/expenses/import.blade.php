<x-layout :title="'Import Pengeluaran — '. $period->name" :topbarTitle="'Import '. $period->name">
    <x-page-header :title="'Import Pengeluaran'" :subtitle="'Upload file CSV untuk mengimpor banyak pengeluaran — periode '. $period->name">
        <x-slot:actions>
            <x-button :href="route('periods.expenses.template', $period)" variant="secondary" size="sm">⬇ Template CSV</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card title="Upload File CSV" class="max-w-2xl">
        <div class="px-5 py-5">
            <div class="mb-5 rounded-xl bg-navy-50 px-4 py-3 text-sm text-navy-800 ring-1 ring-navy-100">
                <p class="font-semibold mb-1">Format kolom (header wajib):</p>
                <code class="text-xs">date, item_name, category, amount</code>
                <p class="mt-2 text-navy-700">Contoh baris: <code class="text-xs">2026-06-05, Air Galon 2, Air, 43000</code></p>
                <p class="mt-2 text-navy-700">Tiap tanggal unik akan dibuat menjadi satu batch pengeluaran.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                    @foreach ($errors->all() as $err)<p>{{ $err }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('periods.expenses.import', $period) }}" enctype="multipart/form-data">
                @csrf
                <label class="block">
                    <span class="block text-sm font-medium text-slate-700">Pilih file CSV</span>
                    <input type="file" name="file" accept=".csv,.txt,.xls,.xlsx" required
                        class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-navy-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-navy-800">
                </label>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button as="button" type="button" variant="secondary" :href="route('periods.expenses.index', $period)">Batal</x-button>
                    <x-button as="button" type="submit">Import</x-button>
                </div>
            </form>
        </div>
    </x-card>
</x-layout>
