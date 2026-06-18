@php
    $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
@endphp
<x-layout :title="$period->exists ? 'Edit Periode' : 'Buat Periode'" :topbarTitle="$period->exists ? 'Edit Periode' : 'Buat Periode'">
    <x-page-header :title="$period->exists ? 'Edit Periode' : 'Buat Periode'" :subtitle="isset($period) && $period->exists ? $period->name : 'Lengkapi detail periode kas baru'" />

    <x-card title="Detail Periode" class="max-w-2xl">
        <form method="POST" action="{{ $period->exists ? route('periods.update', $period) : route('periods.store') }}">
            @csrf
            @if ($period->exists)
                @method('PUT')
            @endif

            <div class="divide-y divide-slate-100">
                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="name">Nama Periode</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $period->name) }}" placeholder="contoh: Juni 2026" required
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 px-5 py-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="month">Bulan</label>
                        <select id="month" name="month" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                            @foreach ($months as $num => $label)
                                <option value="{{ $num }}" @selected(old('month', $period->month) == $num)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('month') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="year">Tahun</label>
                        <input id="year" type="number" name="year" value="{{ old('year', $period->year ?: date('Y')) }}" min="2000" max="2100" required
                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        @error('year') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 px-5 py-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="monthly_due">Iuran per Anggota</label>
                        <input id="monthly_due" type="number" name="monthly_due" value="{{ old('monthly_due', $period->monthly_due) }}" min="0" step="1000" required
                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        <p class="mt-1 text-xs text-slate-400">Dalam rupiah, contoh: 300000</p>
                        @error('monthly_due') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="starting_balance">Kas Awal</label>
                        <input id="starting_balance" type="number" name="starting_balance" value="{{ old('starting_balance', $period->starting_balance) }}" min="0" step="1000" required
                            class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                        <p class="mt-1 text-xs text-slate-400">Saldo awal / manual adjustment</p>
                        @error('starting_balance') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-5 py-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-navy-700 focus:ring-navy-500" @checked(old('is_active', $period->is_active))>
                        <span class="text-sm font-medium text-slate-700">Jadikan periode aktif</span>
                    </label>
                    <p class="mt-1 text-xs text-slate-400">Hanya satu periode yang bisa aktif dalam satu waktu.</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 px-5 py-4">
                <x-button as="button" type="button" variant="secondary" :href="route('periods.index')">Batal</x-button>
                <x-button as="button" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-card>
</x-layout>
