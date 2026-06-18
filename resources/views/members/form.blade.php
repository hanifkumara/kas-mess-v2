<x-layout :title="$member->exists ? 'Edit Anggota' : 'Tambah Anggota'" :topbarTitle="$member->exists ? 'Edit Anggota' : 'Tambah Anggota'">
    <x-page-header :title="$member->exists ? 'Edit Anggota' : 'Tambah Anggota'" :subtitle="isset($member) && $member->exists ? $member->name : 'Lengkapi data anggota baru'" />

    <x-card title="Data Anggota" class="max-w-2xl">
        <form method="POST" action="{{ $member->exists ? route('members.update', $member) : route('members.store') }}">
            @csrf
            @if ($member->exists)
                @method('PUT')
            @endif

            <div class="divide-y divide-slate-100">
                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="name">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $member->name) }}" autofocus required
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="px-5 py-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-navy-700 focus:ring-navy-500" @checked(old('is_active', $member->is_active ?? true))>
                        <span class="text-sm font-medium text-slate-700">Anggota aktif</span>
                    </label>
                    <p class="mt-1 text-xs text-slate-400">Anggota nonaktif tidak ikut dihitung dalam iuran periode.</p>
                </div>

                <div class="px-5 py-4">
                    <label class="block text-sm font-medium text-slate-700" for="notes">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">{{ old('notes', $member->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 px-5 py-4">
                <x-button as="button" type="button" variant="secondary" :href="route('members.index')">Batal</x-button>
                <x-button as="button" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-card>
</x-layout>
