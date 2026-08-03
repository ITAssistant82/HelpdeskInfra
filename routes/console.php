<?php

use App\Console\Commands\AutoEscalateTickets;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tickets:auto-escalate')->everyMinute();
Schedule::command('renewal:auto-status')->daily();
