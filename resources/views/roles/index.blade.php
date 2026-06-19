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
    $allGrouped = collect($groups)->flatten()->values();
@endphp
<x-layout title="Role & Permission" topbarTitle="Role & Permission">
    <x-page-header title="Role & Permission" subtitle="Kelola role dan hak akses (RBAC)">
        <x-slot:actions>
            <x-button :href="route('roles.create')">+ Tambah Role</x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Matriks Role x Permission --}}
    <x-card title="Matriks Akses" subtitle="Centang menandakan role memiliki permission">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-navy-800 text-xs uppercase tracking-wide text-navy-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold sticky left-0 bg-navy-800">Permission</th>
                        @foreach ($roles as $role)
                            <th class="px-4 py-3 text-center font-semibold capitalize">{{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($groups as $group => $perms)
                        <tr class="bg-slate-50/60">
                            <td colspan="{{ 1 + $roles->count() }}" class="px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-navy-700">{{ $group }}</td>
                        </tr>
                        @foreach ($perms as $perm)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-4 py-2 font-mono text-xs text-slate-600 sticky left-0 bg-white">{{ $perm }}</td>
                                @foreach ($roles as $role)
                                    <td class="px-4 py-2 text-center">
                                        @if($role->hasPermissionTo($perm))
                                            <span class="inline-grid h-5 w-5 place-items-center rounded-full bg-emerald-100 text-emerald-700">✓</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Daftar role --}}
    <div class="mt-6">
        <x-card title="Daftar Role" :subtitle="$roles->count().' role'">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Role</th>
                            <th class="px-5 py-3 font-semibold">Permission</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($roles as $role)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-3">
                                    <span class="font-semibold capitalize text-navy-900">{{ $role->name }}</span>
                                    @if(in_array($role->name, ['superadmin','bendahara','anggota'], true))
                                        <span class="ml-2 text-xs text-slate-400">(bawaan)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($role->permissions as $perm)
                                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $perm->name }}</span>
                                        @empty
                                            <span class="text-xs text-slate-400">—</span>
                                        @endforelse
                                        @if($role->name === 'superadmin')
                                            <span class="inline-flex rounded-full bg-navy-50 px-2 py-0.5 text-xs font-semibold text-navy-700">semua</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('roles.edit', $role) }}" class="text-sm font-medium text-navy-700 hover:text-navy-900">Edit</a>
                                        @if(! in_array($role->name, ['superadmin','bendahara','anggota'], true))
                                            <x-confirm-delete :action="route('roles.destroy', $role)" label="Hapus" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layout>
