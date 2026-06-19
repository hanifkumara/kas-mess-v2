<x-layout title="403 · Dilarang" topbarTitle="Akses Ditolak">
    @php $title = 'Akses Ditolak'; @endphp
    <div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
        <span class="text-6xl">🔒</span>
        <p class="mt-4 text-7xl font-extrabold tracking-tight text-navy-800">403</p>
        <h2 class="mt-4 text-xl font-bold text-navy-900">Anda tidak punya akses ke halaman ini</h2>
        <p class="mt-2 max-w-md text-sm text-slate-500">Role Anda tidak memiliki permission untuk mengakses fitur ini. Hubungi bendahara/superadmin jika Anda merasa perlu akses.</p>
        <div class="mt-6 flex gap-2">
            <x-button :href="route('dashboard')">← Kembali ke Dashboard</x-button>
        </div>
    </div>
</x-layout>
