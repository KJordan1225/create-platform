<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldNotifications extends Command
{
    protected $signature = 'app:cleanup-old-notifications';
    protected $description = 'Delete old read notifications';

    public function handle(): int
    {
        $count = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('created_at', '<', now()->subDays(60))
            ->delete();

        $this->info("Deleted {$count} old notifications.");

        return self::SUCCESS;
    }
}
