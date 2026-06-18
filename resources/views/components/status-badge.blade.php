@props(['status' => 'unpaid'])
@php
    $paid = $status === 'paid';
@endphp
<span @class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset',
    'bg-emerald-50 text-emerald-700 ring-emerald-200' => $paid,
    'bg-rose-50 text-rose-700 ring-rose-200' => ! $paid,
])>
    @if ($paid)
        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.1 3.1 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
        Lunas
    @else
        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-7.5 4.3a1 1 0 00-1.4 1.4A5 5 0 0010 15a5 5 0 003.9-1.3 1 1 0 10-1.4-1.4A3 3 0 0110 13a3 3 0 01-2.5-1.7z" clip-rule="evenodd"/></svg>
        Belum Lunas
    @endif
</span>
