<x-layout :title="$status ?? 'Error'" :topbarTitle="($status ?? 'Error').' · Terjadi kesalahan'">
    <div class="flex min-h-[60vh] flex-col items-center justify-center text-center">
        <p class="text-7xl font-extrabold tracking-tight text-navy-800">{{ $status ?? '!' }}</p>
        <h2 class="mt-4 text-xl font-bold text-navy-900">{{ $title ?? 'Terjadi kesalahan' }}</h2>
        <p class="mt-2 max-w-md text-sm text-slate-500">{{ $exception->getMessage() ?? 'Maaf, terjadi kesalahan saat memproses permintaan Anda.' }}</p>
        <div class="mt-6 flex gap-2">
            <x-button :href="route('dashboard')">← Kembali ke Dashboard</x-button>
        </div>
    </div>
</x-layout>
