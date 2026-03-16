<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:cleanup-stale-pending-payments')->hourly();
Schedule::command('app:cleanup-old-notifications')->daily();
