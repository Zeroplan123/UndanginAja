<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\UserGallery;
use App\Models\Template;

class FixStorageSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'storage:fix {--force : Force fix even if some checks pass}';

    /**
     * The console command description.
     */
    protected $description = 'Fix storage system issues for UndanginAja (images not displaying)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing UndanginAja Storage System...');
        $this->newLine();

        $force = $this->option('force');
        $fixed = 0;

        // Fix 1: Create/Fix symbolic link
        $fixed += $this->fixSymbolicLink($force);

        // Fix 2: Create required directories
        $fixed += $this->createRequiredDirectories();

        // Fix 3: Fix permissions
        $fixed += $this->fixPermissions();

        // Fix 4: Update model accessors if needed
        $fixed += $this->checkModelAccessors();

        // Fix 5: Test the fixes
        $this->testFixes();

        $this->newLine();
        if ($fixed > 0) {
            $this->info("✅ Fixed $fixed storage issues!");
            $this->info('🚀 Images should now display correctly.');
        } else {
            $this->info('✅ Storage system is already working correctly!');
        }

        return 0;
    }

    private function fixSymbolicLink($force = false): int
    {
        $this->info('1. Checking symbolic link...');
        
        $publicStoragePath = public_path('storage');
        $storagePublicPath = storage_path('app/public');

        // Remove existing if it's not a proper symlink
        if (file_exists($publicStoragePath) && !is_link($publicStoragePath)) {
            $this->warn('   🗑️  Removing invalid storage directory...');
            if (is_dir($publicStoragePath)) {
                File::deleteDirectory($publicStoragePath);
            } else {
                unlink($publicStoragePath);
            }
        }

        // Create symlink if it doesn't exist
        if (!file_exists($publicStoragePath) || $force) {
            try {
                // For Windows, we need to handle this differently
                if (PHP_OS_FAMILY === 'Windows') {
                    // Use junction for Windows
                    $command = "mklink /J \"$publicStoragePath\" \"$storagePublicPath\"";
                    exec($command, $output, $returnCode);
                    
                    if ($returnCode !== 0) {
                        // Fallback: copy directory structure
                        $this->warn('   ⚠️  Symlink failed, creating directory copy...');
                        File::copyDirectory($storagePublicPath, $publicStoragePath);
                    }
                } else {
                    // Unix/Linux symlink
                    symlink($storagePublicPath, $publicStoragePath);
                }
                
                $this->info('   ✅ Symbolic link created successfully');
                return 1;
            } catch (\Exception $e) {
                $this->error('   ❌ Failed to create symbolic link: ' . $e->getMessage());
                $this->warn('   💡 Try running as administrator or use: php artisan storage:link');
                return 0;
            }
        }

        $this->info('   ✅ Symbolic link already exists');
        return 0;
    }

    private function createRequiredDirectories(): int
    {
        $this->info('2. Creating required directories...');
        
        $directories = [
            'galleries',
            'galleries/thumbnails',
            'template_covers',
        ];

        $created = 0;
        foreach ($directories as $dir) {
            $fullPath = storage_path("app/public/$dir");
            if (!file_exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $this->line("   📁 Created: $dir");
                $created++;
            }
        }

        if ($created > 0) {
            $this->info("   ✅ Created $created directories");
        } else {
            $this->info('   ✅ All directories already exist');
        }

        return $created > 0 ? 1 : 0;
    }

    private function fixPermissions(): int
    {
        $this->info('3. Fixing permissions...');
        
        $paths = [
            storage_path('app/public'),
            storage_path('app/public/galleries'),
            storage_path('app/public/template_covers'),
            public_path('storage'),
        ];

        $fixed = 0;
        foreach ($paths as $path) {
            if (file_exists($path) && !is_writable($path)) {
                try {
                    chmod($path, 0755);
                    $this->line("   🔧 Fixed permissions: $path");
                    $fixed++;
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  Could not fix permissions for: $path");
                }
            }
        }

        if ($fixed > 0) {
            $this->info("   ✅ Fixed permissions for $fixed paths");
        } else {
            $this->info('   ✅ All permissions are correct');
        }

        return $fixed > 0 ? 1 : 0;
    }

    private function checkModelAccessors(): int
    {
        $this->info('4. Checking model accessors...');
        
        try {
            // Test UserGallery accessor
            $gallery = new UserGallery();
            $gallery->file_path = 'galleries/1/test.jpg';
            $url = $gallery->file_url;
            
            if (!str_contains($url, '/storage/galleries/1/test.jpg')) {
                $this->error('   ❌ UserGallery accessor issue detected');
                return 0;
            }

            // Test Template accessor
            $template = new Template();
            $template->cover_image = 'test.jpg';
            $coverUrl = $template->cover_image_url;
            
            if (!str_contains($coverUrl, '/storage/template_covers/test.jpg')) {
                $this->error('   ❌ Template accessor issue detected');
                return 0;
            }

            $this->info('   ✅ Model accessors working correctly');
            return 0;

        } catch (\Exception $e) {
            $this->error('   ❌ Model accessor test failed: ' . $e->getMessage());
            return 0;
        }
    }

    private function testFixes(): void
    {
        $this->info('5. Testing fixes...');
        
        try {
            // Test file creation
            $testContent = 'Storage test - ' . now();
            $testPath = 'test-storage-fix.txt';
            
            Storage::disk('public')->put($testPath, $testContent);
            
            if (Storage::disk('public')->exists($testPath)) {
                $url = Storage::disk('public')->url($testPath);
                $this->line("   🔗 Test URL: $url");
                
                // Clean up
                Storage::disk('public')->delete($testPath);
                
                $this->info('   ✅ Storage system is working!');
            } else {
                $this->error('   ❌ Storage test failed');
            }

        } catch (\Exception $e) {
            $this->error('   ❌ Storage test error: ' . $e->getMessage());
        }
    }
}
