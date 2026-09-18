<?php

use Carbon\Carbon;

if (!function_exists('parse_date_to_carbon')) {
    /**
     * Helper internal to parse various date formats into Carbon instance with 'id' locale.
     */
    function parse_date_to_carbon($date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        try {
            if ($date instanceof Carbon) {
                return $date->copy()->locale('id');
            }

            if ($date instanceof \DateTimeInterface) {
                return Carbon::instance($date)->locale('id');
            }

            if (is_numeric($date)) {
                return Carbon::createFromTimestamp($date)->locale('id');
            }

            return Carbon::parse($date)->locale('id');
        } catch (\Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('tanggal_indo')) {
    /**
     * Format tanggal ke format Bahasa Indonesia.
     * Contoh:
     * - tanggal_indo('2026-08-24') => '24 Agustus 2026'
     * - tanggal_indo('2026-08-24', true) => 'Senin, 24 Agustus 2026'
     * - tanggal_indo('2026-08-24 17:25:00', true, true) => 'Senin, 24 Agustus 2026 17:25'
     * - tanggal_indo('2026-08-24', false, false, true) => '24 Ags 2026'
     */
    function tanggal_indo($date, bool $withDay = false, bool $withTime = false, bool $shortMonth = false): string
    {
        $carbon = parse_date_to_carbon($date);

        if (!$carbon) {
            return '-';
        }

        $monthFormat = $shortMonth ? 'M' : 'F';
        $dateFormat = "d {$monthFormat} Y";

        if ($withDay) {
            $dateFormat = "l, {$dateFormat}";
        }

        if ($withTime) {
            $dateFormat .= " H:i";
        }

        return $carbon->translatedFormat($dateFormat);
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Format tanggal menggunakan Carbon translatedFormat dengan locale Indonesia.
     * Contoh: format_tanggal(now(), 'l, d F Y H:i') => 'Senin, 24 Agustus 2026 17:25'
     */
    function format_tanggal($date, string $format = 'd F Y'): string
    {
        $carbon = parse_date_to_carbon($date);

        if (!$carbon) {
            return '-';
        }

        return $carbon->translatedFormat($format);
    }
}

if (!function_exists('hari_indo')) {
    /**
     * Mendapatkan nama hari dalam Bahasa Indonesia.
     * Menerima string hari Inggris (e.g. 'Monday'), tanggal string, Carbon instance, atau nomor hari (1=Senin..7=Minggu).
     */
    function hari_indo($dateOrDay = null): string
    {
        if ($dateOrDay === null) {
            $dateOrDay = now();
        }

        $map = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
            1           => 'Senin',
            2           => 'Selasa',
            3           => 'Rabu',
            4           => 'Kamis',
            5           => 'Jumat',
            6           => 'Sabtu',
            7           => 'Minggu',
            0           => 'Minggu',
        ];

        if (is_string($dateOrDay) && isset($map[$dateOrDay])) {
            return $map[$dateOrDay];
        }

        if (is_numeric($dateOrDay) && isset($map[(int)$dateOrDay])) {
            return $map[(int)$dateOrDay];
        }

        $carbon = parse_date_to_carbon($dateOrDay);
        if ($carbon) {
            return $carbon->translatedFormat('l');
        }

        return '-';
    }
}

if (!function_exists('bulan_indo')) {
    /**
     * Mendapatkan nama bulan dalam Bahasa Indonesia.
     * Menerima angka bulan (1-12), string nama bulan Inggris, atau tanggal.
     */
    function bulan_indo($monthOrDate = null, bool $short = false): string
    {
        if ($monthOrDate === null) {
            $monthOrDate = now();
        }

        $monthsLong = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $monthsShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $monthsList = $short ? $monthsShort : $monthsLong;

        if (is_numeric($monthOrDate)) {
            $m = (int) $monthOrDate;
            return $monthsList[$m] ?? '-';
        }

        $carbon = parse_date_to_carbon($monthOrDate);
        if ($carbon) {
            return $monthsList[$carbon->month] ?? '-';
        }

        return '-';
    }
}

if (!function_exists('waktu_lalu')) {
    /**
     * Format selisih waktu dalam Bahasa Indonesia (contoh: "2 jam yang lalu").
     */
    function waktu_lalu($date): string
    {
        $carbon = parse_date_to_carbon($date);

        if (!$carbon) {
            return '-';
        }

        return $carbon->diffForHumans();
    }
}

if (!function_exists('daftar_hari_indo')) {
    /**
     * Mengembalikan daftar nama hari [English => Indonesia] atau list hari Indonesia.
     */
    function daftar_hari_indo(bool $asKeyValue = true): array
    {
        if ($asKeyValue) {
            return [
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu',
                'Sunday'    => 'Minggu',
            ];
        }

        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    }
}

if (!function_exists('daftar_bulan_indo')) {
    /**
     * Mengembalikan daftar nama bulan [1 => 'Januari', ...].
     */
    function daftar_bulan_indo(bool $short = false): array
    {
        if ($short) {
            return [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];
        }

        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }
}
