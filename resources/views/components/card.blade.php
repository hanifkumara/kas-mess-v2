@props(['title' => null, 'subtitle' => null, 'class' => ''])
<section {{ $attributes->merge(['class' => 'rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 '.$class]) }}>
    @if ($title || $subtitle || isset($actions))
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
        <div>
            @if ($title)<h3 class="font-semibold text-navy-900">{{ $title }}</h3>@endif
            @if ($subtitle)<p class="mt-0.5 text-sm text-slate-500">{{ $subtitle }}</p>@endif
        </div>
        @if (isset($actions))<div class="flex items-center gap-2">{{ $actions }}</div>@endif
    </div>
    @endif
    <div {{ $slot->attributes->merge(['class' => '']) }}>
        {{ $slot }}
    </div>
</section>
