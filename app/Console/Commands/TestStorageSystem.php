<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\UserGallery;
use App\Models\Template;
use App\Models\User;

class TestStorageSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'storage:test {--verbose : Show detailed output}';

    /**
     * The console command description.
     */
    protected $description = 'Test storage system functionality for UndanginAja';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing UndanginAja Storage System...');
        $this->newLine();

        $verbose = $this->option('verbose');
        $allPassed = true;

        // Test 1: Check symbolic link
        $allPassed &= $this->testSymbolicLink($verbose);

        // Test 2: Check storage directories
        $allPassed &= $this->testStorageDirectories($verbose);

        // Test 3: Test file operations
        $allPassed &= $this->testFileOperations($verbose);

        // Test 4: Test model accessors
        $allPassed &= $this->testModelAccessors($verbose);

        // Test 5: Check permissions
        $allPassed &= $this->testPermissions($verbose);

        // Test 6: Test URL generation
        $allPassed &= $this->testUrlGeneration($verbose);

        $this->newLine();
        if ($allPassed) {
            $this->info('✅ All storage system tests passed!');
            $this->info('🚀 System is ready for hosting deployment.');
        } else {
            $this->error('❌ Some tests failed. Please check the issues above.');
            return 1;
        }

        return 0;
    }

    private function testSymbolicLink($verbose = false): bool
    {
        $this->info('1. Testing symbolic link...');
        
        $publicStoragePath = public_path('storage');
        $storagePublicPath = storage_path('app/public');

        if (!file_exists($publicStoragePath)) {
            $this->error('   ❌ Symbolic link does not exist at: ' . $publicStoragePath);
            $this->warn('   💡 Run: php artisan storage:link');
            return false;
        }

        if (!is_link($publicStoragePath)) {
            $this->error('   ❌ public/storage exists but is not a symbolic link');
            $this->warn('   💡 Delete public/storage and run: php artisan storage:link');
            return false;
        }

        $linkTarget = readlink($publicStoragePath);
        if ($verbose) {
            $this->line("   📁 Link: $publicStoragePath -> $linkTarget");
        }

        $this->info('   ✅ Symbolic link is properly configured');
        return true;
    }

    private function testStorageDirectories($verbose = false): bool
    {
        $this->info('2. Testing storage directories...');
        
        $directories = [
            'galleries' => storage_path('app/public/galleries'),
            'template_covers' => storage_path('app/public/template_covers'),
        ];

        $allExist = true;
        foreach ($directories as $name => $path) {
            if (!file_exists($path)) {
                $this->error("   ❌ Directory missing: $name ($path)");
                $allExist = false;
            } else {
                if ($verbose) {
                    $this->line("   📁 $name: $path");
                }
            }
        }

        if ($allExist) {
            $this->info('   ✅ All required directories exist');
        }

        return $allExist;
    }

    private function testFileOperations($verbose = false): bool
    {
        $this->info('3. Testing file operations...');
        
        try {
            // Test gallery file operations
            $testContent = 'Test content for storage system';
            $testPath = 'galleries/test/test-file.txt';
            
            // Write test file
            Storage::disk('public')->put($testPath, $testContent);
            
            if (!Storage::disk('public')->exists($testPath)) {
                $this->error('   ❌ Failed to create test file');
                return false;
            }

            // Read test file
            $readContent = Storage::disk('public')->get($testPath);
            if ($readContent !== $testContent) {
                $this->error('   ❌ File content mismatch');
                return false;
            }

            // Test URL generation
            $url = Storage::disk('public')->url($testPath);
            if ($verbose) {
                $this->line("   🔗 Generated URL: $url");
            }

            // Clean up
            Storage::disk('public')->delete($testPath);
            Storage::disk('public')->deleteDirectory('galleries/test');

            $this->info('   ✅ File operations working correctly');
            return true;

        } catch (\Exception $e) {
            $this->error('   ❌ File operation failed: ' . $e->getMessage());
            return false;
        }
    }

    private function testModelAccessors($verbose = false): bool
    {
        $this->info('4. Testing model accessors...');
        
        try {
            // Test UserGallery accessor
            $gallery = new UserGallery();
            $gallery->file_path = 'galleries/1/test-image.jpg';
            
            $url = $gallery->file_url;
            if (!str_contains($url, '/storage/galleries/1/test-image.jpg')) {
                $this->error('   ❌ UserGallery file_url accessor not working');
                return false;
            }

            if ($verbose) {
                $this->line("   🖼️  Gallery URL: $url");
            }

            // Test Template accessor
            $template = new Template();
            $template->cover_image = 'test-cover.jpg';
            
            $coverUrl = $template->cover_image_url;
            if (!str_contains($coverUrl, '/storage/template_covers/test-cover.jpg')) {
                $this->error('   ❌ Template cover_image_url accessor not working');
                return false;
            }

            if ($verbose) {
                $this->line("   🎨 Template URL: $coverUrl");
            }

            $this->info('   ✅ Model accessors working correctly');
            return true;

        } catch (\Exception $e) {
            $this->error('   ❌ Model accessor test failed: ' . $e->getMessage());
            return false;
        }
    }

    private function testPermissions($verbose = false): bool
    {
        $this->info('5. Testing permissions...');
        
        $paths = [
            storage_path('app/public'),
            storage_path('app/public/galleries'),
            storage_path('app/public/template_covers'),
            public_path('storage'),
        ];

        $allWritable = true;
        foreach ($paths as $path) {
            if (!is_writable($path)) {
                $this->error("   ❌ Not writable: $path");
                $allWritable = false;
            } else {
                if ($verbose) {
                    $perms = substr(sprintf('%o', fileperms($path)), -4);
                    $this->line("   📝 $path ($perms)");
                }
            }
        }

        if ($allWritable) {
            $this->info('   ✅ All paths are writable');
        } else {
            $this->warn('   💡 Fix permissions with: chmod -R 755 storage/ public/storage/');
        }

        return $allWritable;
    }

    private function testUrlGeneration($verbose = false): bool
    {
        $this->info('6. Testing URL generation...');
        
        try {
            $baseUrl = config('app.url');
            
            // Test gallery URL
            $galleryUrl = Storage::disk('public')->url('galleries/1/test.jpg');
            $expectedGallery = $baseUrl . '/storage/galleries/1/test.jpg';
            
            if ($galleryUrl !== $expectedGallery) {
                $this->error('   ❌ Gallery URL generation incorrect');
                $this->line("   Expected: $expectedGallery");
                $this->line("   Got: $galleryUrl");
                return false;
            }

            // Test template URL
            $templateUrl = Storage::disk('public')->url('template_covers/test.jpg');
            $expectedTemplate = $baseUrl . '/storage/template_covers/test.jpg';
            
            if ($templateUrl !== $expectedTemplate) {
                $this->error('   ❌ Template URL generation incorrect');
                $this->line("   Expected: $expectedTemplate");
                $this->line("   Got: $templateUrl");
                return false;
            }

            if ($verbose) {
                $this->line("   🔗 Gallery URL: $galleryUrl");
                $this->line("   🔗 Template URL: $templateUrl");
            }

            $this->info('   ✅ URL generation working correctly');
            return true;

        } catch (\Exception $e) {
            $this->error('   ❌ URL generation test failed: ' . $e->getMessage());
            return false;
        }
    }
}
