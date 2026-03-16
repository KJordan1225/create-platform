<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:cleanup-stale-pending-payments')->hourly();
Schedule::command('app:cleanup-old-notifications')->daily();
Schedule::command('app:generate-monthly-payout-reports')->monthlyOn(1, '01:00');
