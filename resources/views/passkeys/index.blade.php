<x-layout title="Passkey Saya" topbarTitle="Passkey Saya">
    <x-page-header title="Passkey Saya" subtitle="Kelola passkey (WebAuthn) untuk login tanpa password" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3" x-data="passkeyForm()">
        {{-- Register card --}}
        <div class="lg:col-span-1">
            <x-card title="Daftarkan Passkey Baru">
                <div class="px-5 py-5">
                    <div class="mb-4 rounded-xl bg-navy-50 px-4 py-3 text-sm text-navy-800 ring-1 ring-navy-100">
                        Passkey memungkinkan login dengan sidik jari, wajah, PIN perangkat, atau kunci keamanan.
                    </div>

                    <label class="block text-sm font-medium text-slate-700" for="alias">Nama (opsional)</label>
                    <input id="alias" type="text" x-model="alias" placeholder="mis. MacBook, iPhone"
                        class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500">

                    <p x-show="error" x-text="error" class="mt-3 rounded-lg bg-rose-50 px-3 py-2 text-xs text-rose-700"></p>
                    <p x-show="success" class="mt-3 rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-700">Passkey berhasil didaftarkan.</p>

                    <button type="button" @click="register()" :disabled="busy"
                        class="mt-4 w-full justify-center inline-flex items-center justify-center gap-2 rounded-xl bg-navy-700 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!busy">🔑 Daftarkan Passkey</span>
                        <span x-show="busy" style="display:none">Memproses…</span>
                    </button>
                    <p class="mt-2 text-center text-xs text-slate-400" x-text="hint"></p>
                </div>
            </x-card>
        </div>

        {{-- List card --}}
        <div class="lg:col-span-2">
            <x-card title="Passkey Terdaftar" :subtitle="$passkeys->count().' perangkat'">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nama</th>
                                <th class="px-5 py-3 font-semibold">Dibuat</th>
                                <th class="px-5 py-3 font-semibold">Login Terakhir</th>
                                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($passkeys as $pk)
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-navy-50 text-navy-700">🔑</span>
                                            <span class="font-medium text-navy-900">{{ $pk->alias ?: 'Passkey' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ tgl($pk->created_at) }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $pk->last_login_at ? tgl($pk->last_login_at) : '—' }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <x-confirm-delete :action="route('passkeys.destroy', $pk->id)" label="Hapus" :message="'Hapus passkey &quot;'.e($pk->alias ?: 'Passkey').'&quot;?'" />
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-12 text-center text-slate-400">Belum ada passkey terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function passkeyForm() {
            return {
                alias: '',
                busy: false,
                error: '',
                success: false,
                hint: '',
                routes: {
                    options: "{{ route('passkey.options') }}",
                    store: "{{ route('passkey.store') }}",
                },
                async register() {
                    this.error = '';
                    this.success = false;
                    if (!window.passkey.supported()) {
                        this.error = 'Browser ini tidak mendukung passkey.';
                        return;
                    }
                    this.busy = true;
                    this.hint = 'Konfirmasi di perangkat Anda…';
                    try {
                        await window.passkey.register(this.routes, this.alias);
                        this.success = true;
                        setTimeout(() => location.reload(), 900);
                    } catch (e) {
                        this.error = e.message || 'Gagal mendaftarkan passkey.';
                    } finally {
                        this.busy = false;
                        this.hint = '';
                    }
                },
            };
        }
    </script>
    @endpush
</x-layout>
