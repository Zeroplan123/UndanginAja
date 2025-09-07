<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Broadcast;
use Carbon\Carbon;

class SendScheduledBroadcasts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'broadcasts:send-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Send scheduled broadcasts that are due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled broadcasts...');

        $scheduledBroadcasts = Broadcast::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->where('is_active', true)
            ->get();

        if ($scheduledBroadcasts->isEmpty()) {
            $this->info('No scheduled broadcasts found.');
            return 0;
        }

        $sentCount = 0;

        foreach ($scheduledBroadcasts as $broadcast) {
            try {
                $broadcast->update([
                    'status' => 'sent',
                    'sent_at' => now()
                ]);

                $sentCount++;
                $this->info("Sent broadcast: {$broadcast->title}");

            } catch (\Exception $e) {
                $this->error("Failed to send broadcast {$broadcast->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$sentCount} scheduled broadcasts.");
        return 0;
    }
}
