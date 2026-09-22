<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat Presensi Otomatis Pagi (Check-in) & Sore (Check-out)
\Illuminate\Support\Facades\Schedule::command('attendance:send-reminders --session=morning')
    ->dailyAt('06:50')
    ->name('attendance-morning-reminder');

\Illuminate\Support\Facades\Schedule::command('attendance:send-reminders --session=afternoon')
    ->dailyAt('15:45')
    ->name('attendance-afternoon-reminder');
