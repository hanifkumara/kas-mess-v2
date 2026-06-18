@props([
    'variant' => 'primary',
    'as' => 'a',
    'href' => null,
    'size' => 'md',
])
@php
    $variants = [
        'primary'   => 'bg-navy-700 text-white hover:bg-navy-800 focus-visible:outline-navy-700 shadow-sm',
        'secondary' => 'bg-white text-navy-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50',
        'success'   => 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm',
        'danger'    => 'bg-rose-600 text-white hover:bg-rose-700 shadow-sm',
        'ghost'     => 'text-slate-600 hover:bg-slate-100',
        'subtle'    => 'bg-navy-50 text-navy-700 hover:bg-navy-100',
    ];
    $sizes = [
        'sm'  => 'px-2.5 py-1.5 text-xs gap-1.5',
        'md'  => 'px-3.5 py-2 text-sm gap-2',
        'lg'  => 'px-5 py-2.5 text-sm gap-2',
    ];
    $base = 'inline-flex items-center justify-center rounded-xl font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    $classes = $base.' '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']);
@endphp
@if ($as === 'button' && $attributes->has('type'))
    <button {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@elseif ($as === 'button')
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'submit']) }}>{{ $slot }}</button>
@else
    <a {{ $attributes->merge(['class' => $classes, 'href' => $href ?? '#']) }}>{{ $slot }}</a>
@endif
