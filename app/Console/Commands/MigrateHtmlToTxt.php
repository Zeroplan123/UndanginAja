<?php

namespace App\Console\Commands;

use App\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MigrateHtmlToTxt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:migrate-to-txt 
                            {--dry-run : Run without making changes}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing HTML template files to TXT format (base64 encoded)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Template Migration: HTML → TXT (Base64)');
        $this->newLine();

        // Get all templates with HTML files
        $templates = Template::whereNotNull('file_path')
            ->where('file_path', 'like', '%.html')
            ->get();

        if ($templates->isEmpty()) {
            $this->info('✅ No HTML templates found. All templates are already in TXT format.');
            return 0;
        }

        $this->info("Found {$templates->count()} HTML template(s) to migrate:");
        $this->newLine();

        // Show templates to be migrated
        $this->table(
            ['ID', 'Name', 'File Path', 'Size'],
            $templates->map(function ($template) {
                $size = Storage::disk('local')->exists($template->file_path) 
                    ? $this->formatBytes(Storage::disk('local')->size($template->file_path))
                    : 'N/A';
                
                return [
                    $template->id,
                    $template->name,
                    $template->file_path,
                    $size
                ];
            })
        );

        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Confirmation
        if (!$force && !$dryRun) {
            if (!$this->confirm('Do you want to proceed with the migration?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
            $this->newLine();
        }

        // Migration progress
        $bar = $this->output->createProgressBar($templates->count());
        $bar->start();

        $migrated = 0;
        $failed = 0;
        $errors = [];

        foreach ($templates as $template) {
            try {
                if (!Storage::disk('local')->exists($template->file_path)) {
                    $errors[] = "Template #{$template->id}: File not found - {$template->file_path}";
                    $failed++;
                    $bar->advance();
                    continue;
                }

                // Read HTML content
                $htmlContent = Storage::disk('local')->get($template->file_path);
                
                if (empty($htmlContent)) {
                    $errors[] = "Template #{$template->id}: Empty file - {$template->file_path}";
                    $failed++;
                    $bar->advance();
                    continue;
                }

                // Convert to base64 (TXT format)
                $txtContent = base64_encode($htmlContent);

                // Generate new TXT path
                $txtPath = str_replace('.html', '.txt', $template->file_path);

                if (!$dryRun) {
                    // Save as TXT
                    Storage::disk('local')->put($txtPath, $txtContent);

                    // Update template record
                    $template->file_path = $txtPath;
                    $template->save();

                    // Delete old HTML file
                    Storage::disk('local')->delete($template->file_path);

                    Log::info('Template migrated to TXT', [
                        'template_id' => $template->id,
                        'template_name' => $template->name,
                        'old_path' => $template->file_path,
                        'new_path' => $txtPath,
                        'html_size' => strlen($htmlContent),
                        'txt_size' => strlen($txtContent)
                    ]);
                }

                $migrated++;

            } catch (\Exception $e) {
                $errors[] = "Template #{$template->id}: {$e->getMessage()}";
                $failed++;
                
                Log::error('Template migration failed', [
                    'template_id' => $template->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📊 Migration Summary:');
        $this->newLine();
        
        if ($dryRun) {
            $this->line("  Would migrate: <fg=cyan>{$migrated}</> template(s)");
            $this->line("  Would fail: <fg=yellow>{$failed}</> template(s)");
        } else {
            $this->line("  ✅ Migrated: <fg=green>{$migrated}</> template(s)");
            if ($failed > 0) {
                $this->line("  ❌ Failed: <fg=red>{$failed}</> template(s)");
            }
        }

        $this->newLine();

        // Show errors if any
        if (!empty($errors)) {
            $this->error('❌ Errors encountered:');
            foreach ($errors as $error) {
                $this->line("  • {$error}");
            }
            $this->newLine();
        }

        // Final status
        if ($dryRun) {
            $this->info('✨ Dry run complete. Use without --dry-run to perform actual migration.');
        } elseif ($failed === 0) {
            $this->info('✨ Migration completed successfully!');
        } else {
            $this->warn('⚠️  Migration completed with some errors. Check logs for details.');
        }

        return $failed > 0 ? 1 : 0;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
