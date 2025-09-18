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
        
        // Set timezone to Asia/Jakarta for accurate time comparison
        $currentTime = now()->setTimezone('Asia/Jakarta');
        $this->info("Current time (Jakarta): {$currentTime->format('Y-m-d H:i:s')}");

        $scheduledBroadcasts = Broadcast::where('status', 'scheduled')
            ->where('scheduled_at', '<=', $currentTime)
            ->where('is_active', true)
            ->get();

        if ($scheduledBroadcasts->isEmpty()) {
            $this->info('No scheduled broadcasts found.');
            return 0;
        }

        $sentCount = 0;

        foreach ($scheduledBroadcasts as $broadcast) {
            try {
                $scheduledTime = $broadcast->scheduled_at->setTimezone('Asia/Jakarta');
                $this->info("Processing broadcast '{$broadcast->title}' scheduled for: {$scheduledTime->format('Y-m-d H:i:s')}");
                
                $broadcast->update([
                    'status' => 'sent',
                    'sent_at' => $currentTime
                ]);

                $sentCount++;
                $this->info("✓ Sent broadcast: {$broadcast->title}");

            } catch (\Exception $e) {
                $this->error("✗ Failed to send broadcast {$broadcast->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$sentCount} scheduled broadcasts.");
        return 0;
    }
}
