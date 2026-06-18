@props(['route' => '#', 'active' => false, 'icon' => ''])
<a
    href="{{ $route }}"
    @class([
        'flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition',
        'bg-white/10 text-white shadow-sm' => $active,
        'text-navy-300 hover:bg-white/5 hover:text-white' => ! $active,
    ])
>
    <span class="grid h-6 w-6 place-items-center text-base">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</a>
