<x-layout title="Anggota" topbarTitle="Anggota">
    <x-page-header title="Anggota" subtitle="Daftar anggota mess/kost">
        <x-slot:actions>
            <x-button :href="route('members.create')">+ Tambah Anggota</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($members->isEmpty())
        <x-empty-state icon="👥" title="Belum ada anggota" description="Tambahkan anggota mess Anda untuk mulai mencatat iuran.">
            <x-slot:action>
                <x-button :href="route('members.create')">+ Tambah Anggota</x-button>
            </x-slot:action>
        </x-empty-state>
    @else
        <x-card title="Daftar Anggota" :subtitle="$members->total() .' anggota'">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Anggota</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Catatan</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($members as $member)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-8 w-8 place-items-center rounded-full bg-navy-100 text-xs font-bold text-navy-700">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                                        <span class="font-medium text-navy-900">{{ $member->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    @if ($member->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500 ring-1 ring-inset ring-slate-200">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-500">{{ $member->notes ?: '—' }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('members.edit', $member) }}" class="text-sm font-medium text-navy-700 hover:text-navy-900">Edit</a>
                                        <x-confirm-delete :action="route('members.destroy', $member)" label="Hapus" :message="'Hapus anggota &quot;'.e($member->name).'&quot;?'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $members->links() }}
            </div>
        </x-card>
    @endif
</x-layout>
