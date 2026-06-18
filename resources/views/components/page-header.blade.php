@props(['title' => '', 'subtitle' => null])
<div class="mb-6 flex flex-wrap items-end justify-between gap-4 no-print">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-navy-900 sm:text-2xl">{{ $title }}</h2>
        @if ($subtitle)<p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>@endif
    </div>
    @if (isset($actions))<div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>@endif
</div>
