<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service untuk sanitasi HTML template guna mencegah stored XSS attacks
 * 
 * Sistem keamanan berlapis:
 * 1. Client-side: Escape HTML sebelum submit
 * 2. Server-side: Sanitasi saat output/display
 * 3. Whitelist: Hanya izinkan tag dan atribut yang aman
 */
class HtmlSanitizerService
{
    /**
     * Whitelist tag HTML yang diizinkan untuk template undangan
     * Tag-tag ini aman dan umum digunakan untuk styling undangan
     */
    private $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'div', 'span', 'img', 'a', 'ul', 'ol', 'li', 'table', 'tr', 'td', 'th',
        'thead', 'tbody', 'center', 'font', 'small', 'big', 'hr', 'blockquote',
        'pre', 'code', 'sub', 'sup', 'del', 'ins'
    ];
    
    /**
     * Whitelist atribut yang diizinkan
     * Atribut ini aman dan diperlukan untuk styling template
     */
    private $allowedAttributes = [
        'class', 'id', 'style', 'src', 'alt', 'href', 'title', 'width', 'height',
        'color', 'size', 'face', 'align', 'bgcolor', 'border', 'cellpadding',
        'cellspacing', 'target', 'rel'
    ];
    
    /**
     * Tag berbahaya yang harus selalu dihapus
     */
    private $dangerousTags = [
        'script', 'object', 'embed', 'link', 'style', 'iframe', 'frame', 
        'frameset', 'applet', 'meta', 'form', 'input', 'button', 'select', 
        'textarea', 'option', 'base', 'head', 'html', 'body'
    ];

    /**
     * Escape HTML untuk penyimpanan aman di database
     * Mengubah karakter khusus menjadi HTML entities
     * 
     * @param string $html Raw HTML content
     * @return string Escaped HTML content
     */
    public function escapeForStorage(string $html): string
    {
        // Log untuk debugging
        Log::info('HtmlSanitizerService: Escaping HTML for storage', [
            'original_length' => strlen($html),
            'contains_script' => strpos($html, '<script') !== false
        ]);

        return htmlspecialchars($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitasi HTML untuk output yang aman
     * Decode HTML entities dan sanitasi untuk mencegah XSS
     * 
     * @param string $escapedHtml Escaped HTML dari database
     * @return string Sanitized HTML siap untuk output
     */
    public function sanitizeForOutput(string $escapedHtml): string
    {
        // 1. Decode HTML entities kembali ke HTML
        $decodedHtml = html_entity_decode($escapedHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // 2. Sanitasi HTML untuk keamanan
        $sanitizedHtml = $this->performSanitization($decodedHtml);
        
        // Log untuk monitoring
        Log::info('HtmlSanitizerService: Sanitized HTML for output', [
            'original_length' => strlen($decodedHtml),
            'sanitized_length' => strlen($sanitizedHtml),
            'removed_scripts' => substr_count($decodedHtml, '<script') - substr_count($sanitizedHtml, '<script')
        ]);

        return $sanitizedHtml;
    }

    /**
     * Sanitasi HTML menggunakan built-in PHP functions
     * Metode berlapis untuk keamanan maksimal
     * 
     * @param string $html Raw HTML content
     * @return string Sanitized HTML
     */
    private function performSanitization(string $html): string
    {
        // 1. Hapus semua tag berbahaya
        foreach ($this->dangerousTags as $tag) {
            // Hapus tag pembuka dan penutup (case insensitive)
            $html = preg_replace('/<\/?' . preg_quote($tag, '/') . '\b[^>]*>/i', '', $html);
        }

        // 2. Hapus semua event handlers (onclick, onload, onmouseover, dll)
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);

        // 3. Hapus javascript: URLs
        $html = preg_replace('/javascript:/i', '', $html);

        // 4. Hapus data: URLs yang mencurigakan (kecuali gambar)
        $html = preg_replace('/data:(?!image\/)[^;]*;[^"\']*["\']?/i', '', $html);

        // 5. Hapus expression() dalam CSS (IE vulnerability)
        $html = preg_replace('/expression\s*\(/i', '', $html);

        // 6. Hapus @import dalam style (bisa load external CSS)
        $html = preg_replace('/@import[^;]*;/i', '', $html);

        // 7. Sanitasi atribut style untuk menghapus expression dan javascript
        $html = preg_replace_callback('/style\s*=\s*["\']([^"\']*)["\']/', function($matches) {
            $style = $matches[1];
            // Hapus javascript dan expression dari CSS
            $style = preg_replace('/javascript:|expression\s*\(|@import/i', '', $style);
            return 'style="' . $style . '"';
        }, $html);

        // 8. Filter hanya tag yang diizinkan (opsional - lebih ketat)
        if (config('app.strict_html_filtering', false)) {
            $html = strip_tags($html, '<' . implode('><', $this->allowedTags) . '>');
        }

        return $html;
    }

    /**
     * Validasi apakah HTML aman untuk disimpan
     * Cek apakah masih ada konten berbahaya setelah sanitasi
     * 
     * @param string $html HTML content to validate
     * @return array Validation result with status and issues
     */
    public function validateHtmlSafety(string $html): array
    {
        $issues = [];
        
        // Cek script tags
        if (preg_match('/<script\b/i', $html)) {
            $issues[] = 'Mengandung tag script';
        }
        
        // Cek event handlers
        if (preg_match('/\s*on\w+\s*=/i', $html)) {
            $issues[] = 'Mengandung event handler JavaScript';
        }
        
        // Cek javascript: URLs
        if (preg_match('/javascript:/i', $html)) {
            $issues[] = 'Mengandung JavaScript URL';
        }
        
        // Cek tag berbahaya lainnya
        foreach ($this->dangerousTags as $tag) {
            if (preg_match('/<' . preg_quote($tag, '/') . '\b/i', $html)) {
                $issues[] = "Mengandung tag berbahaya: $tag";
            }
        }

        return [
            'is_safe' => empty($issues),
            'issues' => $issues,
            'risk_level' => $this->calculateRiskLevel($issues)
        ];
    }

    /**
     * Hitung tingkat risiko berdasarkan isu yang ditemukan
     * 
     * @param array $issues List of security issues
     * @return string Risk level (low, medium, high)
     */
    private function calculateRiskLevel(array $issues): string
    {
        if (empty($issues)) {
            return 'none';
        }

        $highRiskKeywords = ['script', 'javascript', 'event handler'];
        $mediumRiskKeywords = ['iframe', 'object', 'embed'];

        foreach ($issues as $issue) {
            foreach ($highRiskKeywords as $keyword) {
                if (stripos($issue, $keyword) !== false) {
                    return 'high';
                }
            }
        }

        foreach ($issues as $issue) {
            foreach ($mediumRiskKeywords as $keyword) {
                if (stripos($issue, $keyword) !== false) {
                    return 'medium';
                }
            }
        }

        return 'low';
    }

    /**
     * Generate laporan keamanan HTML
     * Untuk monitoring dan debugging
     * 
     * @param string $html HTML content to analyze
     * @return array Security report
     */
    public function generateSecurityReport(string $html): array
    {
        $validation = $this->validateHtmlSafety($html);
        
        return [
            'timestamp' => now(),
            'html_length' => strlen($html),
            'is_safe' => $validation['is_safe'],
            'risk_level' => $validation['risk_level'],
            'issues_found' => $validation['issues'],
            'tags_count' => substr_count($html, '<'),
            'script_tags' => substr_count(strtolower($html), '<script'),
            'event_handlers' => preg_match_all('/\s*on\w+\s*=/i', $html),
            'javascript_urls' => substr_count(strtolower($html), 'javascript:'),
        ];
    }

    /**
     * Clean HTML untuk preview yang aman
     * Versi ringan sanitasi untuk preview real-time
     * 
     * @param string $html Raw HTML
     * @return string Cleaned HTML for preview
     */
    public function cleanForPreview(string $html): string
    {
        // Sanitasi ringan untuk preview
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/javascript:/i', '', $html);
        
        return $html;
    }
}
