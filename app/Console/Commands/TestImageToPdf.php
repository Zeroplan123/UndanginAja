<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HtmlToImageService;
use App\Services\ImageToPdfService;
use Exception;

class TestImageToPdf extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'image-pdf:test 
                            {--sample : Generate sample test file}
                            {--cleanup : Clean up old temporary files}
                            {--hours=24 : Hours old for cleanup}';

    /**
     * The console command description.
     */
    protected $description = 'Test HTML to Image to PDF conversion system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing HTML to Image to PDF System');
        $this->newLine();

        if ($this->option('cleanup')) {
            return $this->handleCleanup();
        }

        if ($this->option('sample')) {
            return $this->handleSampleGeneration();
        }

        // Test image conversion capabilities
        $this->testImageConversion();
        
        // Test complete workflow
        $this->testCompleteWorkflow();
        
        // Show recommendations
        $this->showRecommendations();
    }

    /**
     * Test image conversion capabilities
     */
    private function testImageConversion()
    {
        $this->info('📷 Testing Image Conversion Capabilities...');
        
        try {
            $htmlToImageService = new HtmlToImageService();
            $results = $htmlToImageService->testConversion();
            
            // Show available methods
            $this->info('Available conversion methods:');
            if (!empty($results['available_methods'])) {
                foreach ($results['available_methods'] as $method) {
                    $this->line("  ✅ {$method}");
                }
            } else {
                $this->warn('  ❌ No conversion methods available');
            }
            
            // Show test results
            if (isset($results['success']) && $results['success']) {
                $this->info("✅ Image conversion test: PASSED");
                if (isset($results['file_size'])) {
                    $this->line("   File size: " . number_format($results['file_size'] / 1024, 2) . " KB");
                }
            } else {
                $this->error("❌ Image conversion test: FAILED");
                if (isset($results['error'])) {
                    $this->error("   Error: " . $results['error']);
                }
            }
            
        } catch (Exception $e) {
            $this->error("❌ Image conversion test failed: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Test complete HTML to Image to PDF workflow
     */
    private function testCompleteWorkflow()
    {
        $this->info('🔄 Testing Complete HTML → Image → PDF Workflow...');
        
        // Check PHP GD extension first
        if (!extension_loaded('gd')) {
            $this->warn("⚠️  PHP GD extension not installed - skipping internal image generation test");
            $this->line("   This test requires GD extension for image creation");
            $this->line("   External conversion tools (Chrome, wkhtmltoimage) will still work");
            $this->newLine();
            return;
        }
        
        try {
            $imageToPdfService = new ImageToPdfService(new HtmlToImageService());
            $results = $imageToPdfService->testConversion();
            
            if ($results['success']) {
                $this->info("✅ Complete workflow test: PASSED");
                if (isset($results['pdf_size'])) {
                    $this->line("   PDF size: " . number_format($results['pdf_size'] / 1024, 2) . " KB");
                }
            } else {
                $this->error("❌ Complete workflow test: FAILED");
                if (isset($results['error'])) {
                    $this->error("   Error: " . $results['error']);
                }
                if (isset($results['solution'])) {
                    $this->warn("   Solution: " . $results['solution']);
                }
            }
            
        } catch (Exception $e) {
            $this->error("❌ Complete workflow test failed: " . $e->getMessage());
            
            if (strpos($e->getMessage(), 'GD extension') !== false) {
                $this->warn("💡 Install PHP GD extension to enable this test");
            }
        }
        
        $this->newLine();
    }

    /**
     * Generate sample PDF for testing
     */
    private function handleSampleGeneration()
    {
        $this->info('📄 Generating Sample PDF...');
        
        try {
            $sampleHtml = $this->getSampleHtml();
            
            $imageToPdfService = new ImageToPdfService(new HtmlToImageService());
            $pdfFile = $imageToPdfService->convertHtmlToPdfViaImage($sampleHtml, 'sample-invitation-test');
            
            $this->info("✅ Sample PDF generated successfully!");
            $this->line("📁 File: {$pdfFile}");
            $this->line("📊 Size: " . number_format(filesize($pdfFile) / 1024, 2) . " KB");
            
            // Keep the file for manual inspection
            $this->warn("💡 Sample file saved for manual inspection. Delete manually when done.");
            
        } catch (Exception $e) {
            $this->error("❌ Sample generation failed: " . $e->getMessage());
        }
    }

    /**
     * Handle cleanup of old files
     */
    private function handleCleanup()
    {
        $hours = (int) $this->option('hours');
        $this->info("🧹 Cleaning up files older than {$hours} hours...");
        
        try {
            $htmlToImageService = new HtmlToImageService();
            $imageToPdfService = new ImageToPdfService($htmlToImageService);
            
            $imageCleanup = $htmlToImageService->cleanup($hours);
            $pdfCleanup = $imageToPdfService->cleanup($hours);
            
            $total = $imageCleanup + $pdfCleanup;
            
            $this->info("✅ Cleanup completed!");
            $this->line("   Images deleted: {$imageCleanup}");
            $this->line("   PDFs deleted: {$pdfCleanup}");
            $this->line("   Total files: {$total}");
            
        } catch (Exception $e) {
            $this->error("❌ Cleanup failed: " . $e->getMessage());
        }
    }

    /**
     * Show installation recommendations
     */
    private function showRecommendations()
    {
        $this->info('💡 Installation Recommendations:');
        $this->newLine();
        
        $this->line('For best results, install one of the following:');
        $this->line('');
        
        $this->line('1. 🌐 Google Chrome/Chromium (Recommended)');
        $this->line('   Download: https://www.google.com/chrome/');
        $this->line('   - Best rendering quality');
        $this->line('   - Handles modern CSS and fonts');
        $this->line('   - Fast conversion speed');
        $this->line('');
        
        $this->line('2. 🖼️ wkhtmltoimage');
        $this->line('   Download: https://wkhtmltopdf.org/downloads.html');
        $this->line('   - Lightweight alternative');
        $this->line('   - Good for simple layouts');
        $this->line('   - Install to: C:\\wkhtmltopdf\\bin\\');
        $this->line('');
        
        $this->line('3. 🎭 Browsershot + Puppeteer (Advanced)');
        $this->line('   Install: composer require spatie/browsershot');
        $this->line('   Requires: Node.js and npm install puppeteer');
        $this->line('   - Most accurate rendering');
        $this->line('   - Requires Node.js setup');
        $this->line('');
        
        $this->warn('⚠️  If no conversion method is available, the system will fall back to standard PDF export.');
    }

    /**
     * Get sample HTML for testing
     */
    private function getSampleHtml(): string
    {
        return '
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sample Wedding Invitation</title>
            <style>
                body {
                    margin: 0;
                    padding: 40px;
                    font-family: "Georgia", "Times New Roman", serif;
                    background: linear-gradient(135deg, #ffeef8 0%, #f0e6ff 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .invitation-container {
                    max-width: 600px;
                    background: white;
                    padding: 60px 40px;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                    text-align: center;
                    border: 3px solid #f8d7da;
                }
                
                .header {
                    margin-bottom: 40px;
                }
                
                .title {
                    font-size: 28px;
                    color: #d63384;
                    margin-bottom: 10px;
                    font-weight: bold;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                }
                
                .subtitle {
                    font-size: 16px;
                    color: #6c757d;
                    font-style: italic;
                }
                
                .couple-names {
                    margin: 40px 0;
                }
                
                .name {
                    font-size: 36px;
                    color: #495057;
                    margin: 10px 0;
                    font-weight: bold;
                }
                
                .separator {
                    font-size: 24px;
                    color: #d63384;
                    margin: 20px 0;
                }
                
                .details {
                    margin: 40px 0;
                    line-height: 1.8;
                }
                
                .detail-item {
                    margin: 15px 0;
                    font-size: 18px;
                    color: #495057;
                }
                
                .detail-label {
                    font-weight: bold;
                    color: #d63384;
                }
                
                .footer {
                    margin-top: 40px;
                    font-size: 14px;
                    color: #6c757d;
                    font-style: italic;
                }
                
                .decorative {
                    font-size: 24px;
                    color: #d63384;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="invitation-container">
                <div class="header">
                    <div class="title">Wedding Invitation</div>
                    <div class="subtitle">You are cordially invited to celebrate</div>
                </div>
                
                <div class="decorative">♥ ♥ ♥</div>
                
                <div class="couple-names">
                    <div class="name">John Doe</div>
                    <div class="separator">&</div>
                    <div class="name">Jane Smith</div>
                </div>
                
                <div class="decorative">♥ ♥ ♥</div>
                
                <div class="details">
                    <div class="detail-item">
                        <span class="detail-label">Date:</span> 
                        ' . date('d F Y', strtotime('+3 months')) . '
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Time:</span> 
                        10:00 AM
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Venue:</span> 
                        Grand Ballroom, Hotel Elegant
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Address:</span> 
                        Jl. Merdeka No. 123, Jakarta
                    </div>
                </div>
                
                <div class="decorative">♥ ♥ ♥</div>
                
                <div class="footer">
                    <p>Your presence would make our day even more special</p>
                    <p><em>Generated by UndanginAja - ' . date('Y-m-d H:i:s') . '</em></p>
                </div>
            </div>
        </body>
        </html>';
    }
}
