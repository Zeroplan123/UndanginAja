<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

class ImageToPdfService
{
    private $htmlToImageService;
    private $outputDir;
    
    public function __construct(HtmlToImageService $htmlToImageService)
    {
        $this->htmlToImageService = $htmlToImageService;
        $this->outputDir = storage_path('app/temp/pdf');
        
        // Create output directory if it doesn't exist
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    /**
     * Convert HTML to PDF via image conversion
     */
    public function convertHtmlToPdfViaImage(string $htmlContent, string $filename = null): string
    {
        try {
            $filename = $filename ?: 'invitation_' . time() . '_' . uniqid();
            
            Log::info('Starting HTML to PDF via image conversion', ['filename' => $filename]);
            
            // Step 1: Convert HTML to image
            $imageFile = $this->htmlToImageService->convertHtmlToImage($htmlContent, $filename);
            
            if (!file_exists($imageFile)) {
                throw new Exception('Image conversion failed - file not found');
            }
            
            Log::info('Image conversion successful', [
                'image_file' => $imageFile,
                'image_size' => filesize($imageFile)
            ]);
            
            // Step 2: Convert image to PDF
            $pdfFile = $this->convertImageToPdf($imageFile, $filename);
            
            // Step 3: Cleanup image file
            if (file_exists($imageFile)) {
                unlink($imageFile);
            }
            
            Log::info('PDF conversion via image completed successfully', [
                'pdf_file' => $pdfFile,
                'pdf_size' => filesize($pdfFile)
            ]);
            
            return $pdfFile;
            
        } catch (Exception $e) {
            Log::error('HTML to PDF via image conversion failed', [
                'error' => $e->getMessage(),
                'filename' => $filename ?? 'unknown'
            ]);
            throw $e;
        }
    }
    
    /**
     * Convert image to PDF using DomPDF
     */
    private function convertImageToPdf(string $imageFile, string $filename): string
    {
        try {
            // Get image dimensions
            $imageInfo = getimagesize($imageFile);
            if (!$imageInfo) {
                throw new Exception('Could not get image dimensions');
            }
            
            $imageWidth = $imageInfo[0];
            $imageHeight = $imageInfo[1];
            
            // Calculate PDF dimensions (A4 = 210mm x 297mm = 595pt x 842pt)
            $a4Width = 595; // points
            $a4Height = 842; // points
            
            // Calculate scaling to fit image on A4 page with margins
            $margin = 20; // points
            $availableWidth = $a4Width - (2 * $margin);
            $availableHeight = $a4Height - (2 * $margin);
            
            $scaleX = $availableWidth / $imageWidth;
            $scaleY = $availableHeight / $imageHeight;
            $scale = min($scaleX, $scaleY, 1); // Don't upscale
            
            $finalWidth = $imageWidth * $scale;
            $finalHeight = $imageHeight * $scale;
            
            // Center the image
            $x = ($a4Width - $finalWidth) / 2;
            $y = ($a4Height - $finalHeight) / 2;
            
            // Convert image to base64
            $imageData = base64_encode(file_get_contents($imageFile));
            $imageMimeType = $imageInfo['mime'];
            
            // Create HTML for PDF with embedded image
            $pdfHtml = $this->createPdfHtmlWithImage($imageData, $imageMimeType, $finalWidth, $finalHeight, $x, $y);
            
            // Configure DomPDF for image-based PDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('dpi', 150);
            $options->set('defaultPaperSize', 'A4');
            $options->set('defaultPaperOrientation', 'portrait');
            
            // Create PDF
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($pdfHtml);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Save PDF
            $pdfFile = $this->outputDir . '/' . $filename . '.pdf';
            file_put_contents($pdfFile, $dompdf->output());
            
            if (!file_exists($pdfFile)) {
                throw new Exception('PDF file was not created');
            }
            
            return $pdfFile;
            
        } catch (Exception $e) {
            Log::error('Image to PDF conversion failed', [
                'error' => $e->getMessage(),
                'image_file' => $imageFile
            ]);
            throw $e;
        }
    }
    
    /**
     * Create HTML template for PDF with embedded image
     */
    private function createPdfHtmlWithImage(string $imageData, string $mimeType, float $width, float $height, float $x, float $y): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Wedding Invitation PDF</title>
            <style>
                @page {
                    margin: 0;
                    size: A4 portrait;
                }
                
                body {
                    margin: 0;
                    padding: 0;
                    width: 100%;
                    height: 100vh;
                    background: white;
                    position: relative;
                }
                
                .image-container {
                    position: absolute;
                    left: ' . $x . 'pt;
                    top: ' . $y . 'pt;
                    width: ' . $width . 'pt;
                    height: ' . $height . 'pt;
                }
                
                .invitation-image {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    display: block;
                }
                
                /* Ensure image prints properly */
                @media print {
                    body {
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    
                    .invitation-image {
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
            </style>
        </head>
        <body>
            <div class="image-container">
                <img src="data:' . $mimeType . ';base64,' . $imageData . '" 
                     alt="Wedding Invitation" 
                     class="invitation-image">
            </div>
        </body>
        </html>';
    }
    
    /**
     * Alternative method: Create PDF directly from image using FPDF
     */
    public function convertImageToPdfWithFpdf(string $imageFile, string $filename): string
    {
        try {
            // Check if FPDF is available
            if (!class_exists('FPDF')) {
                throw new Exception('FPDF library not available');
            }
            
            $pdf = new \FPDF();
            $pdf->AddPage();
            
            // Get image dimensions
            $imageInfo = getimagesize($imageFile);
            $imageWidth = $imageInfo[0];
            $imageHeight = $imageInfo[1];
            
            // Calculate dimensions to fit A4 page (210mm x 297mm)
            $pageWidth = 210;
            $pageHeight = 297;
            $margin = 10;
            
            $availableWidth = $pageWidth - (2 * $margin);
            $availableHeight = $pageHeight - (2 * $margin);
            
            // Calculate scaling
            $scaleX = $availableWidth / ($imageWidth * 0.264583); // Convert pixels to mm
            $scaleY = $availableHeight / ($imageHeight * 0.264583);
            $scale = min($scaleX, $scaleY, 1);
            
            $finalWidth = ($imageWidth * 0.264583) * $scale;
            $finalHeight = ($imageHeight * 0.264583) * $scale;
            
            // Center the image
            $x = ($pageWidth - $finalWidth) / 2;
            $y = ($pageHeight - $finalHeight) / 2;
            
            // Add image to PDF
            $pdf->Image($imageFile, $x, $y, $finalWidth, $finalHeight);
            
            // Save PDF
            $pdfFile = $this->outputDir . '/' . $filename . '.pdf';
            $pdf->Output('F', $pdfFile);
            
            return $pdfFile;
            
        } catch (Exception $e) {
            Log::error('FPDF image to PDF conversion failed', [
                'error' => $e->getMessage(),
                'image_file' => $imageFile
            ]);
            throw $e;
        }
    }
    
    /**
     * Get download response for PDF file
     */
    public function getDownloadResponse(string $pdfFile, string $downloadName = null): BinaryFileResponse
    {
        if (!file_exists($pdfFile)) {
            throw new Exception('PDF file not found');
        }
        
        $downloadName = $downloadName ?: 'wedding-invitation-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return response()->download($pdfFile, $downloadName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
        ])->deleteFileAfterSend(true);
    }
    
    /**
     * Test image to PDF conversion
     */
    public function testConversion(): array
    {
        try {
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                return [
                    'success' => false,
                    'error' => 'PHP GD extension is not installed. Please install php-gd extension.',
                    'gd_available' => false,
                    'solution' => 'Run: composer require intervention/image or install php-gd extension'
                ];
            }
            
            // Create a simple test image
            $testImage = imagecreate(800, 600);
            $white = imagecolorallocate($testImage, 255, 255, 255);
            $black = imagecolorallocate($testImage, 0, 0, 0);
            $pink = imagecolorallocate($testImage, 255, 192, 203);
            
            imagefill($testImage, 0, 0, $white);
            imagefilledrectangle($testImage, 50, 50, 750, 550, $pink);
            imagestring($testImage, 5, 300, 280, 'Test Invitation', $black);
            
            $testImageFile = $this->outputDir . '/test_image.png';
            imagepng($testImage, $testImageFile);
            imagedestroy($testImage);
            
            // Test conversion
            $pdfFile = $this->convertImageToPdf($testImageFile, 'test_conversion');
            
            $result = [
                'success' => file_exists($pdfFile),
                'pdf_file' => $pdfFile,
                'pdf_size' => file_exists($pdfFile) ? filesize($pdfFile) : 0,
                'test_image' => $testImageFile,
                'gd_available' => true
            ];
            
            // Cleanup
            if (file_exists($testImageFile)) unlink($testImageFile);
            if (file_exists($pdfFile)) unlink($pdfFile);
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'gd_available' => extension_loaded('gd')
            ];
        }
    }
    
    /**
     * Cleanup old PDF files
     */
    public function cleanup(int $hoursOld = 24): int
    {
        $deletedCount = 0;
        $cutoffTime = time() - ($hoursOld * 3600);
        
        if (!is_dir($this->outputDir)) {
            return 0;
        }
        
        $files = glob($this->outputDir . '/*.pdf');
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }
        
        Log::info('Cleaned up old PDF files', [
            'deleted_count' => $deletedCount,
            'hours_old' => $hoursOld
        ]);
        
        return $deletedCount;
    }
}
