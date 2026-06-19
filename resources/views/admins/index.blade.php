<x-layout title="Pengguna" topbarTitle="Pengguna">
    <x-page-header title="Pengguna" subtitle="Manajemen akun admin & role-nya">
        <x-slot:actions>
            <x-button :href="route('admins.create')">+ Tambah Pengguna</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card title="Daftar Pengguna" :subtitle="$admins->count().' akun'">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nama</th>
                        <th class="px-5 py-3 font-semibold">Email</th>
                        <th class="px-5 py-3 font-semibold">Role</th>
                        <th class="px-5 py-3 font-semibold">Passkey</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($admins as $admin)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-8 w-8 place-items-center rounded-full bg-navy-100 text-xs font-bold text-navy-700">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                                    <span class="font-medium text-navy-900">{{ $admin->name }}
                                        @if($admin->is(auth()->user()))<span class="text-xs text-slate-400">(Anda)</span>@endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $admin->email }}</td>
                            <td class="px-5 py-3">
                                @foreach ($admin->roles as $role)
                                    <span class="inline-flex rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-semibold capitalize text-navy-700 ring-1 ring-inset ring-navy-100">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-3">
                                @if($admin->passkeys()->count() > 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">🔑 {{ $admin->passkeys()->count() }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admins.edit', $admin) }}" class="text-sm font-medium text-navy-700 hover:text-navy-900">Edit</a>
                                    @if(! $admin->is(auth()->user()))
                                        <x-confirm-delete :action="route('admins.destroy', $admin)" label="Hapus" :message="'Hapus pengguna &quot;'.e($admin->name).'&quot;?'" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-layout>
