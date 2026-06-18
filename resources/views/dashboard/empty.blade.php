<x-layout title="Dashboard" topbar-title="Dashboard">
    <x-page-header title="Selamat datang di Kas Mess" subtitle="Mulai dengan membuat periode kas pertama Anda." />

    <x-empty-state
        icon="🗓️"
        title="Belum ada periode kas"
        description="Buat periode kas (mis. Juni 2026), isi iuran per anggota dan kas awal, lalu kelola pembayaran serta pengeluaran."
    >
        <x-slot:action>
            <x-button :href="route('periods.create')">+ Buat Periode Kas</x-button>
        </x-slot:action>
    </x-empty-state>
</x-layout>
