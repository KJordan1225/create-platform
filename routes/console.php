<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('subscriptions:expire-creators')->hourly();
Schedule::command('subscriptions:notify-creators')->twiceDaily(9, 18);

Schedule::command('app:cleanup-stale-pending-payments')->hourly();
Schedule::command('app:cleanup-old-notifications')->daily();
Schedule::command('app:generate-monthly-payout-reports')->monthlyOn(1, '01:00');
