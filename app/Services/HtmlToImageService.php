<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class HtmlToImageService
{
    private $tempDir;
    private $outputDir;
    
    public function __construct()
    {
        $this->tempDir = storage_path('app/temp/html');
        $this->outputDir = storage_path('app/temp/images');
        
        // Create directories if they don't exist
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Convert HTML content to image
     */
    public function convertHtmlToImage(string $htmlContent, string $filename = null): string
    {
        try {
            $filename = $filename ?: 'invitation_' . time() . '_' . uniqid();
            $htmlFile = $this->tempDir . '/' . $filename . '.html';
            $imageFile = $this->outputDir . '/' . $filename . '.png';
            
            // Check if any conversion method is available
            $availableMethods = $this->getAvailableMethods();
            if (empty($availableMethods)) {
                throw new Exception('No image conversion methods available. Please install Chrome, wkhtmltoimage, or Browsershot with Puppeteer.');
            }
            
            // Prepare HTML for image conversion
            $optimizedHtml = $this->prepareHtmlForImage($htmlContent);
            
            // Save HTML to temporary file
            file_put_contents($htmlFile, $optimizedHtml);
            
            // Convert using available method
            $success = $this->convertWithBrowsershot($htmlFile, $imageFile) ||
                      $this->convertWithWkhtmltoimage($htmlFile, $imageFile) ||
                      $this->convertWithChrome($htmlFile, $imageFile);
            
            if (!$success) {
                throw new Exception('All image conversion methods failed. Available methods: ' . implode(', ', $availableMethods));
            }
            
            // Cleanup HTML file
            if (file_exists($htmlFile)) {
                unlink($htmlFile);
            }
            
            Log::info('HTML to image conversion successful', [
                'output_file' => $imageFile,
                'file_size' => filesize($imageFile)
            ]);
            
            return $imageFile;
            
        } catch (Exception $e) {
            Log::error('HTML to image conversion failed', [
                'error' => $e->getMessage(),
                'filename' => $filename ?? 'unknown'
            ]);
            throw $e;
        }
    }
    
    /**
     * Convert using Browsershot (requires Node.js and Puppeteer)
     */
    private function convertWithBrowsershot(string $htmlFile, string $imageFile): bool
    {
        try {
            // Check if Browsershot is available
            if (!class_exists('\Spatie\Browsershot\Browsershot')) {
                return false;
            }
            
            \Spatie\Browsershot\Browsershot::html(file_get_contents($htmlFile))
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
                ->windowSize(1200, 1600)
                ->deviceScaleFactor(2) // For high DPI
                ->format('png')
                ->quality(100)
                ->save($imageFile);
            
            return file_exists($imageFile);
            
        } catch (Exception $e) {
            Log::warning('Browsershot conversion failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Convert using wkhtmltoimage
     */
    private function convertWithWkhtmltoimage(string $htmlFile, string $imageFile): bool
    {
        try {
            $wkhtmltoimage = $this->findWkhtmltoimage();
            if (!$wkhtmltoimage) {
                return false;
            }
            
            $command = sprintf(
                '"%s" --width 1200 --height 1600 --quality 100 --format png "%s" "%s"',
                $wkhtmltoimage,
                $htmlFile,
                $imageFile
            );
            
            exec($command . ' 2>&1', $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($imageFile)) {
                return true;
            }
            
            Log::warning('wkhtmltoimage failed', [
                'command' => $command,
                'output' => implode("\n", $output),
                'return_code' => $returnCode
            ]);
            
            return false;
            
        } catch (Exception $e) {
            Log::warning('wkhtmltoimage conversion failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Convert using Chrome/Chromium headless
     */
    private function convertWithChrome(string $htmlFile, string $imageFile): bool
    {
        try {
            $chrome = $this->findChrome();
            if (!$chrome) {
                return false;
            }
            
            $command = sprintf(
                '"%s" --headless --disable-gpu --no-sandbox --window-size=1200,1600 --screenshot="%s" "file://%s"',
                $chrome,
                $imageFile,
                $htmlFile
            );
            
            exec($command . ' 2>&1', $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($imageFile)) {
                return true;
            }
            
            Log::warning('Chrome headless failed', [
                'command' => $command,
                'output' => implode("\n", $output),
                'return_code' => $returnCode
            ]);
            
            return false;
            
        } catch (Exception $e) {
            Log::warning('Chrome conversion failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Prepare HTML for better image conversion
     */
    private function prepareHtmlForImage(string $html): string
    {
        // Add viewport and styling for better image output
        $imageOptimizedCss = '
        <style>
            body {
                margin: 0;
                padding: 20px;
                background: white;
                font-family: "Arial", "Helvetica", sans-serif;
                width: 1160px; /* Fixed width for consistent output */
                min-height: 1560px;
                overflow: hidden !important;
            }
            
            /* Hide scrollbars completely */
            html, body {
                overflow: hidden !important;
                overflow-x: hidden !important;
                overflow-y: hidden !important;
            }
            
            *::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }
            
            * {
                scrollbar-width: none !important;
                -ms-overflow-style: none !important;
            }
            
            /* Ensure all elements are visible */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Fix for gradients and backgrounds */
            .gradient, [class*="gradient"] {
                background-image: none !important;
                background: linear-gradient(135deg, #ffeef8 0%, #f0e6ff 100%) !important;
            }
            
            /* Ensure text is readable */
            .text-white, .text-light {
                color: #333 !important;
            }
            
            /* Fix for icons - replace with text if needed */
            .fa, .fas, .far, .fab, [class*="icon"] {
                font-family: Arial, sans-serif !important;
            }
            
            /* Remove problematic CSS properties */
            * {
                backdrop-filter: none !important;
                filter: none !important;
                transform: none !important;
                animation: none !important;
                transition: none !important;
            }
            
            /* Ensure proper spacing */
            .container, .card, .invitation-container {
                margin-bottom: 20px;
                page-break-inside: avoid;
            }
        </style>';
        
        // Insert the CSS before closing head tag or at the beginning
        if (strpos($html, '</head>') !== false) {
            $html = str_replace('</head>', $imageOptimizedCss . '</head>', $html);
        } else {
            $html = $imageOptimizedCss . $html;
        }
        
        // Replace problematic elements
        $html = $this->replaceProblematicElements($html);
        
        return $html;
    }
    
    /**
     * Replace elements that don't work well in image conversion
     */
    private function replaceProblematicElements(string $html): string
    {
        // Replace common emoji/icons with text alternatives
        $replacements = [
            '💕' => '♥',
            '💖' => '♥',
            '💗' => '♥',
            '💘' => '♥',
            '💙' => '♥',
            '💚' => '♥',
            '💛' => '♥',
            '💜' => '♥',
            '🤍' => '♥',
            '🖤' => '♥',
            '❤️' => '♥',
            '💍' => '◊',
            '👰' => 'Bride',
            '🤵' => 'Groom',
            '🎉' => '*',
            '🎊' => '*',
            '✨' => '*',
            '⭐' => '*',
            '🌟' => '*',
            '💫' => '*',
        ];
        
        foreach ($replacements as $emoji => $replacement) {
            $html = str_replace($emoji, $replacement, $html);
        }
        
        return $html;
    }
    
    /**
     * Find wkhtmltoimage executable
     */
    private function findWkhtmltoimage(): ?string
    {
        $possiblePaths = [
            'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltoimage.exe',
            'C:\\wkhtmltopdf\\bin\\wkhtmltoimage.exe',
            '/usr/local/bin/wkhtmltoimage',
            '/usr/bin/wkhtmltoimage',
            'wkhtmltoimage' // If in PATH
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) || $this->commandExists($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    /**
     * Find Chrome/Chromium executable
     */
    private function findChrome(): ?string
    {
        $username = get_current_user();
        $userChromePath = 'C:\\Users\\' . $username . '\\AppData\\Local\\Google\\Chrome\\Application\\chrome.exe';
        
        $possiblePaths = [
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            $userChromePath,
            '/usr/bin/google-chrome',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
            'google-chrome', // If in PATH
            'chromium-browser',
            'chromium'
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path) || $this->commandExists($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    /**
     * Check if command exists in PATH
     */
    private function commandExists(string $command): bool
    {
        $whereIsCommand = (PHP_OS_FAMILY === 'Windows') ? 'where' : 'which';
        exec("$whereIsCommand $command 2>&1", $output, $returnCode);
        return $returnCode === 0;
    }
    
    /**
     * Get available conversion methods
     */
    private function getAvailableMethods(): array
    {
        $methods = [];
        
        if (class_exists('\Spatie\Browsershot\Browsershot')) {
            $methods[] = 'Browsershot';
        }
        
        if ($this->findWkhtmltoimage() !== null) {
            $methods[] = 'wkhtmltoimage';
        }
        
        if ($this->findChrome() !== null) {
            $methods[] = 'Chrome/Chromium';
        }
        
        return $methods;
    }
    
    /**
     * Test image conversion capabilities
     */
    public function testConversion(): array
    {
        $testHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Test</title>
        </head>
        <body>
            <div style="padding: 20px; background: linear-gradient(135deg, #ffeef8, #f0e6ff); text-align: center;">
                <h1 style="color: #d63384;">Test Invitation</h1>
                <p>This is a test invitation for image conversion.</p>
                <div style="margin: 20px 0;">💕 Love 💕</div>
            </div>
        </body>
        </html>';
        
        $results = [
            'browsershot' => false,
            'wkhtmltoimage' => false,
            'chrome' => false,
            'available_methods' => []
        ];
        
        try {
            $testFile = $this->convertHtmlToImage($testHtml, 'test_conversion');
            if (file_exists($testFile)) {
                $results['success'] = true;
                $results['test_file'] = $testFile;
                $results['file_size'] = filesize($testFile);
                
                // Cleanup test file
                unlink($testFile);
            }
        } catch (Exception $e) {
            $results['error'] = $e->getMessage();
        }
        
        // Check individual methods
        $results['browsershot'] = class_exists('\Spatie\Browsershot\Browsershot');
        $results['wkhtmltoimage'] = $this->findWkhtmltoimage() !== null;
        $results['chrome'] = $this->findChrome() !== null;
        
        if ($results['browsershot']) $results['available_methods'][] = 'Browsershot';
        if ($results['wkhtmltoimage']) $results['available_methods'][] = 'wkhtmltoimage';
        if ($results['chrome']) $results['available_methods'][] = 'Chrome Headless';
        
        return $results;
    }
    
    /**
     * Cleanup old temporary files
     */
    public function cleanup(int $hoursOld = 24): int
    {
        $deletedCount = 0;
        $cutoffTime = time() - ($hoursOld * 3600);
        
        $directories = [$this->tempDir, $this->outputDir];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) continue;
            
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoffTime) {
                    if (unlink($file)) {
                        $deletedCount++;
                    }
                }
            }
        }
        
        Log::info('Cleaned up temporary image files', [
            'deleted_count' => $deletedCount,
            'hours_old' => $hoursOld
        ]);
        
        return $deletedCount;
    }
}
