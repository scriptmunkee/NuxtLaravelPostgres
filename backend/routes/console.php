<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Poll WhatDoTheyKnow for FOI request status updates every hour
Schedule::command('wdtk:poll')->hourly()->withoutOverlapping();
