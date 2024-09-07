<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:process-campaign-command')->everyFifteenMinutes();
Schedule::command('app:process-mail-command')->everyMinute();
Schedule::command('mysql:restart')->everyMinute();