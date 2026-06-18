@props([
    'action' => '#',
    'label' => 'Hapus',
    'message' => 'Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'Hapus',
])
<div x-data="{ open: false }" class="inline-block">
    <button
        type="button"
        @click="open = true"
        class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50"
        title="{{ $label }}"
    >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.9 12.1a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
        <span class="hidden sm:inline">{{ $label }}</span>
    </button>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="open = false"
        >
            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-navy-950/50 backdrop-blur-sm" @click="open = false"></div>

            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"
            >
                <div class="flex items-start gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">⚠️</span>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-navy-900">{{ $label }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="open = false" class="rounded-xl px-3.5 py-2 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Batal</button>
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl bg-rose-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700">{{ $confirmText }}</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
