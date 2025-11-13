<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfExportService
{
    /**
     * Generate PDF from invitation with optimized settings
     */
    public function generateInvitationPdf($invitation, $templateData = [])
    {
        // Get the compiled HTML with forPdf=true to prevent HTML escaping
        $html = $invitation->template->getCompiledHtml($templateData, true);
        
        // Process HTML for PDF compatibility
        $pdfHtml = $this->processPdfHtml($html);
        
        // Generate PDF with optimized settings
        $pdf = Pdf::loadHTML($pdfHtml)
            ->setPaper('a4', 'portrait')
            ->setOptions($this->getPdfOptions());
            
        return $pdf;
    }
    
    /**
     * Process HTML to make it PDF-compatible
     */
    private function processPdfHtml($html)
    {
        // Remove external font imports and replace with PDF-safe fonts
        $html = preg_replace('/@import\s+url\([^)]+\);?/', '', $html);
        
        // Replace Google Fonts with PDF-safe alternatives
        $fontReplacements = [
            'Great Vibes' => 'serif',
            'Crimson Text' => 'serif', 
            'Lato' => 'sans-serif',
            'Playfair Display' => 'serif',
            'Dancing Script' => 'serif',
            'Montserrat' => 'sans-serif',
            'Open Sans' => 'sans-serif',
            'Roboto' => 'sans-serif',
            'Poppins' => 'sans-serif'
        ];
        
        foreach ($fontReplacements as $googleFont => $safeFont) {
            $html = str_replace("'$googleFont'", "'$safeFont'", $html);
            $html = str_replace("\"$googleFont\"", "\"$safeFont\"", $html);
        }
        
        // Replace problematic CSS properties
        $cssReplacements = [
            'backdrop-filter:' => '/* backdrop-filter:',
            'filter:' => '/* filter:',
            'transform:' => '/* transform:',
            'transition:' => '/* transition:',
            'animation:' => '/* animation:',
        ];
        
        foreach ($cssReplacements as $property => $replacement) {
            $html = str_replace($property, $replacement, $html);
        }
        
        // Convert complex gradients to simple colors
        $html = preg_replace('/background:\s*linear-gradient\([^;]+\);/', 'background: #f8f9fa;', $html);
        $html = preg_replace('/background:\s*radial-gradient\([^;]+\);/', 'background: #ffffff;', $html);
        
        // Replace SVG data URIs with simple borders
        $html = preg_replace('/background:\s*url\(\'data:image\/svg\+xml[^\']+\'\)[^;]*;/', 'border: 1px solid #e0e0e0;', $html);
        
        // Add PDF-specific styles
        $pdfStyles = $this->getPdfStyles();
        $html = str_replace('</head>', $pdfStyles . '</head>', $html);
        
        // Replace emoji and special characters with text alternatives
        $emojiReplacements = [
            '💐' => '❀',
            '🌸' => '❀',
            '🌹' => '❀',
            '💕' => '♥',
            '💖' => '♥',
            '💗' => '♥',
            '❤️' => '♥',
            '🤵' => 'Mempelai Pria',
            '👰' => 'Mempelai Wanita',
            '💒' => 'Pernikahan',
            '🎉' => '*',
            '✨' => '*',
            '⭐' => '*',
        ];
        
        foreach ($emojiReplacements as $emoji => $replacement) {
            $html = str_replace($emoji, $replacement, $html);
        }
        
        return $html;
    }
    
    /**
     * Get PDF-specific CSS styles
     */
    private function getPdfStyles()
    {
        return '
        <style type="text/css">
            /* PDF-specific styles */
            @page {
                margin: 15mm;
                size: A4 portrait;
            }
            
            body {
                font-family: "DejaVu Sans", sans-serif !important;
                font-size: 12pt;
                line-height: 1.4;
                color: #333333;
                background: white !important;
                overflow: hidden !important;
            }
            
            /* Hide scrollbars */
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
            
            /* Ensure text is readable */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Fix font rendering */
            h1, h2, h3, h4, h5, h6 {
                font-family: "DejaVu Sans", sans-serif !important;
                font-weight: bold;
                page-break-after: avoid;
            }
            
            /* Ensure borders and backgrounds print */
            .invitation-container,
            .header-section,
            .main-content {
                background: white !important;
                border: 1px solid #333 !important;
            }
            
            /* Fix positioning issues */
            .corner-decoration,
            .floral-border {
                display: none !important;
            }
            
            /* Ensure text contrast */
            .couple-names,
            .wedding-date,
            .invitation-title {
                color: #000000 !important;
                font-weight: bold !important;
            }
            
            /* Fix layout issues */
            .invitation-container {
                max-width: none !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
                box-shadow: none !important;
            }
            
            /* Ensure images and icons are visible */
            img {
                max-width: 100% !important;
                height: auto !important;
            }
            
            /* Fix text alignment and spacing */
            .text-center {
                text-align: center !important;
            }
            
            .venue-section,
            .notes-section {
                margin: 20px 0 !important;
                padding: 15px !important;
                border: 1px solid #ccc !important;
                background: #f9f9f9 !important;
            }
        </style>';
    }
    
    /**
     * Get optimized PDF options
     */
    private function getPdfOptions()
    {
        return [
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'isPhpEnabled' => false,
            'defaultMediaType' => 'print',
            'dpi' => 150,
            'fontHeightRatio' => 1.1,
            'chroot' => public_path(),
            'logOutputFile' => storage_path('logs/dompdf.log'),
            'tempDir' => storage_path('app/temp'),
            'fontDir' => storage_path('fonts'),
            'fontCache' => storage_path('fonts'),
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
            'pdfBackend' => 'CPDF',
            'pdflibLicense' => '',
            'adminUsername' => 'admin',
            'adminPassword' => 'password',
            'compression' => 6,
            'enable_css_float' => true,
            'enable_javascript' => false,
        ];
    }
    
    /**
     * Save PDF to storage and return path
     */
    public function savePdf($pdf, $filename)
    {
        $tempPath = storage_path('app/temp/' . $filename);
        
        // Create temp directory if it doesn't exist
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        $pdf->save($tempPath);
        
        return $tempPath;
    }
    
    /**
     * Generate PDF for email attachment
     */
    public function generateForEmail($invitation, $templateData = [])
    {
        $pdf = $this->generateInvitationPdf($invitation, $templateData);
        $filename = 'invitation_' . $invitation->slug . '_' . time() . '.pdf';
        
        return $this->savePdf($pdf, $filename);
    }
    
    /**
     * Generate PDF for direct download
     */
    public function generateForDownload($invitation, $templateData = [])
    {
        $pdf = $this->generateInvitationPdf($invitation, $templateData);
        $filename = 'undangan-' . Str::slug($invitation->groom_name . '-' . $invitation->bride_name) . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Create PDF-optimized template
     */
    public function createPdfTemplate($originalHtml)
    {
        // Decode HTML entities if needed
        if (strpos($originalHtml, '&lt;') !== false) {
            $originalHtml = html_entity_decode($originalHtml, ENT_QUOTES, 'UTF-8');
        }
        
        // Create a PDF-optimized version of the template
        $pdfTemplate = $this->processPdfHtml($originalHtml);
        
        // Add additional PDF-specific optimizations
        $pdfTemplate = $this->addPdfOptimizations($pdfTemplate);
        
        return $pdfTemplate;
    }
    
    /**
     * Add PDF-specific optimizations
     */
    private function addPdfOptimizations($html)
    {
        // Wrap content in PDF-friendly container
        $pdfWrapper = '
        <div style="
            font-family: \'DejaVu Sans\', sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #333;
            background: white;
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        ">';
        
        // Find body content and wrap it
        if (preg_match('/<body[^>]*>(.*?)<\/body>/s', $html, $matches)) {
            $bodyContent = $matches[1];
            $wrappedContent = $pdfWrapper . $bodyContent . '</div>';
            $html = str_replace($matches[1], $wrappedContent, $html);
        }
        
        return $html;
    }
}
