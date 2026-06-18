<?php

use Illuminate\Support\Carbon;

if (! function_exists('rp')) {
    /**
     * Format angka ke rupiah Indonesia.
     * rp(1359569) -> "Rp 1.359.569"
     */
    function rp(int|float|null $amount, bool $withSymbol = true): string
    {
        $value = (float) ($amount ?? 0);
        $formatted = number_format($value, 0, ',', '.');

        return $withSymbol ? 'Rp '.$formatted : $formatted;
    }
}

if (! function_exists('rp_plain')) {
    /**
     * Format angka dengan dua desimal, tanpa simbol.
     * rp_plain(1359569) -> "1.359.569,00"
     */
    function rp_plain(int|float|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        return number_format($value, 2, ',', '.');
    }
}

if (! function_exists('parse_rp')) {
    /**
     * Ubah input rupiah ("Rp 300.000" / "300000") menjadi integer.
     */
    function parse_rp(string|int|null $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (int) round((float) $value);
        }
        $clean = preg_replace('/[^0-9,\.]/', '', (string) $value);
        // asumsi pemisah ribu = titik, desimal = koma
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);

        return (int) round((float) $clean);
    }
}

if (! function_exists('tgl')) {
    /**
     * Format tanggal ke bahasa Indonesia.
     * tgl('2026-06-18') -> "18 Jun 2026"
     */
    function tgl(\DateTimeInterface|string|null $date, string $format = 'd M Y'): string
    {
        if (blank($date)) {
            return '—';
        }

        try {
            return Carbon::parse($date)->locale('id')->translatedFormat($format);
        } catch (\Throwable $e) {
            return '—';
        }
    }
}
