<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class StorageHelper
{
    /**
     * Get image URL with fallback handling
     */
    public static function getImageUrl($disk, $path, $fallback = null)
    {
        if (!$path) {
            return $fallback ?: asset('images/placeholder.svg');
        }

        // Check if file exists
        if (!Storage::disk($disk)->exists($path)) {
            return $fallback ?: asset('images/placeholder.svg');
        }

        // Generate relative URL instead of absolute URL
        return self::getRelativeStorageUrl($path);
    }

    /**
     * Get relative storage URL (without domain)
     */
    public static function getRelativeStorageUrl($path)
    {
        // Remove leading slash if exists
        $path = ltrim($path, '/');
        
        // Return relative URL
        return '/storage/' . $path;
    }

    /**
     * Get gallery image URL with fallback
     */
    public static function getGalleryImageUrl($filePath)
    {
        if (!$filePath) {
            return asset('images/gallery-placeholder.svg');
        }

        // Check if file exists first
        if (!Storage::disk('public')->exists($filePath)) {
            return asset('images/gallery-placeholder.svg');
        }
        
        return self::getRelativeStorageUrl($filePath);
    }

    /**
     * Get template cover URL with fallback
     */
    public static function getTemplateCoverUrl($coverImage)
    {
        if (!$coverImage) {
            return asset('images/template-placeholder.svg');
        }

        $path = 'template_covers/' . $coverImage;
        
        // Check if file exists first
        if (!Storage::disk('public')->exists($path)) {
            return asset('images/template-placeholder.svg');
        }
        
        return self::getRelativeStorageUrl($path);
    }

    /**
     * Check if storage system is properly configured
     */
    public static function isStorageConfigured()
    {
        $publicStoragePath = public_path('storage');
        $storagePublicPath = storage_path('app/public');

        // Check if symbolic link exists
        if (!file_exists($publicStoragePath)) {
            return false;
        }

        // Check if required directories exist
        $requiredDirs = ['galleries', 'template_covers'];
        foreach ($requiredDirs as $dir) {
            if (!file_exists(storage_path("app/public/$dir"))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Fix storage system automatically
     */
    public static function fixStorageSystem()
    {
        $fixed = [];

        // Create symbolic link if missing
        $publicStoragePath = public_path('storage');
        $storagePublicPath = storage_path('app/public');

        if (!file_exists($publicStoragePath)) {
            try {
                if (PHP_OS_FAMILY === 'Windows') {
                    // For Windows, create junction
                    $command = "mklink /J \"$publicStoragePath\" \"$storagePublicPath\"";
                    exec($command, $output, $returnCode);
                    
                    if ($returnCode !== 0) {
                        // Fallback: copy directory
                        File::copyDirectory($storagePublicPath, $publicStoragePath);
                    }
                } else {
                    symlink($storagePublicPath, $publicStoragePath);
                }
                $fixed[] = 'Created symbolic link';
            } catch (\Exception $e) {
                $fixed[] = 'Failed to create symbolic link: ' . $e->getMessage();
            }
        }

        // Create required directories
        $requiredDirs = ['galleries', 'galleries/thumbnails', 'template_covers'];
        foreach ($requiredDirs as $dir) {
            $fullPath = storage_path("app/public/$dir");
            if (!file_exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $fixed[] = "Created directory: $dir";
            }
        }

        return $fixed;
    }

    /**
     * Generate placeholder images if they don't exist
     */
    public static function createPlaceholderImages()
    {
        $placeholders = [
            'images/placeholder.jpg' => self::generatePlaceholderImage('No Image', 400, 300),
            'images/gallery-placeholder.jpg' => self::generatePlaceholderImage('Gallery Image', 400, 400),
            'images/template-placeholder.jpg' => self::generatePlaceholderImage('Template Cover', 400, 300),
        ];

        foreach ($placeholders as $path => $content) {
            $fullPath = public_path($path);
            if (!file_exists($fullPath)) {
                File::ensureDirectoryExists(dirname($fullPath));
                file_put_contents($fullPath, $content);
            }
        }
    }

    /**
     * Generate a simple placeholder image
     */
    private static function generatePlaceholderImage($text, $width = 400, $height = 300)
    {
        // Create a simple SVG placeholder
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="#f3f4f6"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="16" fill="#6b7280" text-anchor="middle" dominant-baseline="middle">' . $text . '</text>
</svg>';

        return $svg;
    }

    /**
     * Test storage system functionality
     */
    public static function testStorageSystem()
    {
        $results = [];

        // Test 1: Check symbolic link
        $publicStoragePath = public_path('storage');
        $results['symbolic_link'] = file_exists($publicStoragePath);

        // Test 2: Check directories
        $requiredDirs = ['galleries', 'template_covers'];
        $results['directories'] = [];
        foreach ($requiredDirs as $dir) {
            $results['directories'][$dir] = file_exists(storage_path("app/public/$dir"));
        }

        // Test 3: Test file operations
        try {
            $testPath = 'test-storage.txt';
            Storage::disk('public')->put($testPath, 'test');
            $results['file_operations'] = Storage::disk('public')->exists($testPath);
            Storage::disk('public')->delete($testPath);
        } catch (\Exception $e) {
            $results['file_operations'] = false;
            $results['file_operations_error'] = $e->getMessage();
        }

        // Test 4: Test URL generation
        try {
            $testUrl = Storage::disk('public')->url('test.jpg');
            $results['url_generation'] = str_contains($testUrl, '/storage/test.jpg');
        } catch (\Exception $e) {
            $results['url_generation'] = false;
            $results['url_generation_error'] = $e->getMessage();
        }

        return $results;
    }
}
