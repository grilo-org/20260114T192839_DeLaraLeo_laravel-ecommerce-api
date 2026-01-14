<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clean expired password reset tokens daily at 2:00 AM
Schedule::command('tokens:clean-expired --hours=24')
    ->dailyAt('02:00')
    ->timezone('UTC')
    ->description('Clean expired password reset tokens');
