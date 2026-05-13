<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupExpiredRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup roles that have expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredRoles = \App\Models\Role::where('expires_at', '<=', now())->get();

        if ($expiredRoles->isEmpty()) {
            $this->info('No expired roles found.');
            return;
        }

        foreach ($expiredRoles as $role) {
            $this->info("Deleting expired role: {$role->name} (Dealer ID: {$role->dealer_id})");
            $role->delete();
        }

        $this->info('Expired roles cleanup completed.');
    }
}
