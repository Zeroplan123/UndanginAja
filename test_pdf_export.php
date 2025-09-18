<?php

/**
 * Quick PDF Export Test Script
 * Run this script to test the PDF export functionality
 * 
 * Usage: php test_pdf_export.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\PdfExportService;
use Barryvdh\DomPDF\Facade\Pdf;

echo "=== PDF Export Test Script ===\n\n";

// Test 1: Basic HTML to PDF conversion
echo "Test 1: Basic HTML to PDF conversion\n";
echo "-----------------------------------\n";

$testHtml = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; padding: 20px; }
        .header { text-align: center; color: #8b4513; font-size: 24px; margin-bottom: 20px; }
        .content { line-height: 1.6; }
        .emoji-test { font-size: 18px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="header">🌸 Test Wedding Invitation 🌸</div>
    <div class="content">
        <p>This is a test PDF to verify the export functionality.</p>
        <div class="emoji-test">Emoji Test: 💐 🌹 💕 🤵 👰</div>
        <p>Font Test: <strong>Bold Text</strong> and <em>Italic Text</em></p>
        <p>Date: ' . date('d F Y') . '</p>
        <p>Time: ' . date('H:i') . '</p>
    </div>
</body>
</html>';

try {
    // Create a mock PdfExportService instance
    $pdfService = new class {
        public function processPdfHtml($html) {
            // Simulate the processing
            $html = str_replace('🌸', '❀', $html);
            $html = str_replace('💐', '❀', $html);
            $html = str_replace('🌹', '❀', $html);
            $html = str_replace('💕', '♥', $html);
            $html = str_replace('🤵', 'Mempelai Pria', $html);
            $html = str_replace('👰', 'Mempelai Wanita', $html);
            return $html;
        }
    };
    
    $processedHtml = $pdfService->processPdfHtml($testHtml);
    
    echo "✅ HTML processing successful\n";
    echo "Original HTML size: " . round(strlen($testHtml) / 1024, 2) . " KB\n";
    echo "Processed HTML size: " . round(strlen($processedHtml) / 1024, 2) . " KB\n";
    
    // Test PDF generation
    $pdf = Pdf::loadHTML($processedHtml)
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultMediaType' => 'print',
            'dpi' => 150,
        ]);
    
    $testFile = __DIR__ . '/storage/app/temp/test_pdf_' . time() . '.pdf';
    
    // Create directory if it doesn't exist
    $dir = dirname($testFile);
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $pdf->save($testFile);
    
    if (file_exists($testFile)) {
        $fileSize = round(filesize($testFile) / 1024, 2);
        echo "✅ PDF generation successful\n";
        echo "File saved to: $testFile\n";
        echo "File size: {$fileSize} KB\n";
    } else {
        echo "❌ PDF file was not created\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n";

// Test 2: Font availability check
echo "Test 2: Font Availability Check\n";
echo "-------------------------------\n";

$fontDirs = [
    storage_path('fonts'),
    base_path('vendor/dompdf/dompdf/lib/fonts'),
];

foreach ($fontDirs as $dir) {
    echo "Checking directory: $dir\n";
    if (file_exists($dir)) {
        $fonts = glob($dir . '/*.{ttf,otf,afm}', GLOB_BRACE);
        echo "Found " . count($fonts) . " font files\n";
        
        // Check for DejaVu Sans specifically
        $dejavuFiles = array_filter($fonts, function($font) {
            return stripos(basename($font), 'dejavu') !== false;
        });
        
        if (!empty($dejavuFiles)) {
            echo "✅ DejaVu fonts available\n";
        } else {
            echo "⚠️  DejaVu fonts not found\n";
        }
    } else {
        echo "❌ Directory does not exist\n";
    }
    echo "\n";
}

// Test 3: System requirements
echo "Test 3: System Requirements\n";
echo "---------------------------\n";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "\n";

// Check required extensions
$requiredExtensions = ['gd', 'mbstring', 'dom', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extension '$ext' loaded\n";
    } else {
        echo "❌ Extension '$ext' not loaded\n";
    }
}

echo "\n";

// Test 4: Directory permissions
echo "Test 4: Directory Permissions\n";
echo "-----------------------------\n";

$directories = [
    storage_path('app/temp'),
    storage_path('fonts'),
    storage_path('logs'),
    public_path('temp'),
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created directory: $dir\n";
        } else {
            echo "❌ Failed to create directory: $dir\n";
        }
    } else {
        echo "✅ Directory exists: $dir\n";
    }
    
    if (is_writable($dir)) {
        echo "✅ Directory writable: $dir\n";
    } else {
        echo "❌ Directory not writable: $dir\n";
    }
    echo "\n";
}

echo "=== Test Complete ===\n";
echo "\nNext Steps:\n";
echo "1. Run: php artisan pdf:test (if you have invitations in database)\n";
echo "2. Test PDF export from the web interface\n";
echo "3. Check logs in storage/logs/laravel.log for any errors\n";
echo "4. Run: php artisan pdf:cleanup-temp to clean old files\n";
