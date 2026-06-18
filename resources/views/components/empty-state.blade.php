@props(['title' => 'Belum ada data', 'description' => null, 'icon' => '🗂️', 'action' => null])
<div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white/50 px-6 py-16 text-center">
    <div class="grid h-16 w-16 place-items-center rounded-2xl bg-slate-100 text-3xl">{{ $icon }}</div>
    <h3 class="mt-4 text-base font-semibold text-navy-900">{{ $title }}</h3>
    @if ($description)<p class="mt-1 max-w-sm text-sm text-slate-500">{{ $description }}</p>@endif
    @if ($action)<div class="mt-5">{{ $action }}</div>@endif
    {{ $slot }}
</div>
