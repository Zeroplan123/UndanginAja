<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupTempPdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:cleanup-temp {--hours=24 : Hours after which temp PDFs should be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary PDF files older than specified hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = now()->subHours($hours);
        
        $tempDirs = [
            storage_path('app/temp'),
            public_path('temp'),
        ];
        
        $deletedCount = 0;
        
        foreach ($tempDirs as $dir) {
            if (!File::exists($dir)) {
                continue;
            }
            
            $files = File::files($dir);
            
            foreach ($files as $file) {
                // Check if file is a PDF and older than cutoff time
                if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                    $fileTime = File::lastModified($file);
                    
                    if ($fileTime < $cutoffTime->timestamp) {
                        try {
                            File::delete($file);
                            $deletedCount++;
                            $this->info("Deleted: " . basename($file));
                        } catch (\Exception $e) {
                            $this->error("Failed to delete: " . basename($file) . " - " . $e->getMessage());
                        }
                    }
                }
            }
        }
        
        $this->info("Cleanup completed. Deleted {$deletedCount} temporary PDF files older than {$hours} hours.");
        
        return 0;
    }
}
