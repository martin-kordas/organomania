<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CollectStats;
use App\Console\Commands\UpdateOrganists;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(
    CollectStats::class,
    ['--db']
)->daily();

Schedule::command(
    UpdateOrganists::class
)->daily();
