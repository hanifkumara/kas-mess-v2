@php
    $groups = [
        'Dashboard' => ['dashboard.view'],
        'Anggota' => ['members.view', 'members.manage'],
        'Periode Kas' => ['periods.view', 'periods.manage'],
        'Iuran' => ['payments.view', 'payments.manage'],
        'Pengeluaran' => ['expenses.view', 'expenses.manage'],
        'Laporan' => ['reports.view'],
        'Administrasi' => ['users.manage', 'roles.manage'],
    ];
    $selected = $selected ?? [];
    $isSuperadmin = ($role?->name === 'superadmin');
@endphp
<x-layout :title="isset($role) && $role ? 'Edit Role' : 'Tambah Role'" :topbarTitle="isset($role) && $role ? 'Edit Role' : 'Tambah Role'">
    <x-page-header :title="isset($role) && $role ? 'Edit Role' : 'Tambah Role'" :subtitle="isset($role) && $role ? $role->name : 'Buat role baru beserta permission-nya'" />

    <x-card title="Detail Role" class="max-w-3xl">
        <form method="POST" action="{{ isset($role) && $role ? route('roles.update', $role) : route('roles.store') }}">
            @csrf
            @if (isset($role) && $role)
                @method('PUT')
            @endif

            <div class="px-5 py-4 border-b border-slate-100">
                <label class="block text-sm font-medium text-slate-700" for="name">Nama Role</label>
                <input id="name" type="text" name="name" value="{{ old('name', $role?->name) }}" required autofocus
                    class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                @if($isSuperadmin)
                    <p class="mt-2 rounded-lg bg-navy-50 px-3 py-2 text-xs text-navy-700">Superadmin otomatis memiliki semua permission — daftar di bawah hanya untuk referensi.</p>
                @endif
            </div>

            <div class="px-5 py-4">
                <p class="text-sm font-medium text-slate-700">Permission</p>
                @if($isSuperadmin)
                    <div class="mt-3 space-y-4" aria-disabled="true">
                        @foreach ($groups as $group => $perms)
                            <div>
                                <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-navy-700">{{ $group }}</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($perms as $perm)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1 text-xs text-slate-500">✓ {{ $perm }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-3 space-y-4">
                        @foreach ($groups as $group => $perms)
                            <div>
                                <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-navy-700">{{ $group }}</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($perms as $perm)
                                        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-sm cursor-pointer hover:bg-slate-50">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm }}" @checked(in_array($perm, $selected)) class="h-4 w-4 rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                                            <span class="text-slate-700">{{ $perm }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-2 px-5 py-4 border-t border-slate-100">
                <x-button as="button" type="button" variant="secondary" :href="route('roles.index')">Batal</x-button>
                <x-button as="button" type="submit">Simpan</x-button>
            </div>
        </form>
    </x-card>
</x-layout>
