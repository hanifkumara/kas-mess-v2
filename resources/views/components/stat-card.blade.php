@props(['label' => '', 'value' => '0', 'icon' => '', 'tone' => 'navy', 'hint' => null])
@php
    $tones = [
        'navy'    => 'bg-navy-50 text-navy-700 ring-navy-100',
        'green'   => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'amber'   => 'bg-amber-50 text-amber-700 ring-amber-100',
        'red'     => 'bg-rose-50 text-rose-700 ring-rose-100',
        'sky'     => 'bg-sky-50 text-sky-700 ring-sky-100',
        'slate'   => 'bg-slate-50 text-slate-700 ring-slate-200',
    ];
    $t = $tones[$tone] ?? $tones['navy'];
@endphp
<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-md">
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        @if ($icon)<span class="grid h-9 w-9 place-items-center rounded-xl text-base ring-1 {{ $t }}">{{ $icon }}</span>@endif
    </div>
    <p class="mt-2 text-2xl font-bold tracking-tight text-navy-900">{{ $value }}</p>
    @if ($hint)<p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>@endif
    {{ $slot }}
</div>
