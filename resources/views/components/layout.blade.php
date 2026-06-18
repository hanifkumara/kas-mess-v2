<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title.' · Kas Mess' : 'Kas Mess' }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💰</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="h-full text-slate-800">
    <div class="min-h-full lg:flex" x-data="{ sidebarOpen: false }">
        {{-- Mobile overlay --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-navy-950/50 backdrop-blur-sm lg:hidden"
        ></div>

        {{-- Sidebar --}}
        <aside
            x-cloak
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-64 transform bg-navy-900 text-navy-100 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 flex flex-col"
        >
            <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 text-lg">💰</span>
                <div class="leading-tight">
                    <p class="font-semibold text-white">Kas Mess</p>
                    <p class="text-xs text-navy-300">Pengelolaan Kas Bulanan</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm">
                <x-layout.nav-link :route="route('dashboard')" :active="request()->routeIs('dashboard')" icon="▦">Dashboard</x-layout.nav-link>
                <x-layout.nav-link :route="route('members.index')" :active="request()->routeIs('members.*')" icon="👥">Anggota</x-layout.nav-link>
                <x-layout.nav-link :route="route('periods.index')" :active="request()->routeIs('periods.*') && !request()->routeIs('periods.payments.*','periods.expenses.*','periods.batches.*','periods.report.*')" icon="🗓️">Periode Kas</x-layout.nav-link>

                @if ($sharedActivePeriod)
                <p class="px-3 pt-5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-navy-400">
                    {{ $sharedActivePeriod->name }}
                </p>
                <x-layout.nav-link :route="route('periods.payments.index', $sharedActivePeriod)" :active="request()->routeIs('periods.payments.*')" icon="✅">Pembayaran Iuran</x-layout.nav-link>
                <x-layout.nav-link :route="route('periods.expenses.index', $sharedActivePeriod)" :active="request()->routeIs('periods.expenses.*')" icon="🧾">Pengeluaran</x-layout.nav-link>
                <x-layout.nav-link :route="route('periods.batches.index', $sharedActivePeriod)" :active="request()->routeIs('periods.batches.*')" icon="📦">Batch Pengeluaran</x-layout.nav-link>
                <x-layout.nav-link :route="route('periods.report.show', $sharedActivePeriod)" :active="request()->routeIs('periods.report.*')" icon="📊">Laporan Bulanan</x-layout.nav-link>
                @endif
            </nav>

            <div class="border-t border-white/10 p-4 text-xs text-navy-400">
                <p class="text-navy-300 font-medium">{{ config('app.name') }}</p>
                <p>Laravel {{ app()->version() }} · SQLite</p>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar --}}
            <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 no-print">
                <button @click="sidebarOpen = true" class="grid h-9 w-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100 lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex items-center gap-2">
                    @isset($topbarTitle)
                        <h1 class="text-base font-semibold text-navy-900 sm:text-lg">{{ $topbarTitle }}</h1>
                    @endisset
                </div>

                <div class="ml-auto flex items-center gap-3">
                    @if ($sharedActivePeriod)
                        <span class="hidden items-center gap-2 rounded-full bg-navy-50 px-3 py-1.5 text-xs font-medium text-navy-700 ring-1 ring-navy-100 sm:inline-flex">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Periode aktif: {{ $sharedActivePeriod->name }}
                        </span>
                    @endif

                    @if ($sharedPeriods?->isNotEmpty())
                    @php $selPeriodId = is_numeric(request('period')) ? (int) request('period') : ($sharedActivePeriod->id ?? null); @endphp
                    <form method="GET" action="{{ route('dashboard') }}" class="hidden sm:block">
                        @csrf
                        <select name="period" onchange="this.form.submit()" class="rounded-lg border-slate-200 bg-white text-sm text-slate-700 focus:border-navy-500 focus:ring-navy-500">
                            @foreach ($sharedPeriods as $p)
                                <option value="{{ $p->id }}" @selected($p->id === $selPeriodId)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto w-full max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Toast notifications --}}
    @if (session('toast'))
        @php $toast = session('toast'); @endphp
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3500)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-y-3"
            class="fixed bottom-5 right-5 z-50 flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg ring-1 no-print {{
                ($toast['type'] ?? 'success') === 'success' ? 'bg-emerald-600 text-white ring-emerald-700/20' :
                (($toast['type'] ?? 'success') === 'info' ? 'bg-navy-700 text-white ring-navy-800/20' : 'bg-rose-600 text-white ring-rose-700/20')
            }}"
        >
            <span class="mt-0.5">{{ ($toast['type'] ?? 'success') === 'success' ? '✓' : (($toast['type'] ?? 'success') === 'info' ? 'ℹ' : '✕') }}</span>
            <p class="text-sm font-medium">{{ $toast['message'] ?? '' }}</p>
        </div>
    @endif

    @yield('modals')
</body>
</html>
