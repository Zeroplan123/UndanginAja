<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class Template extends Model
{
    use HasFactory;
    protected $table = 'templates';

     protected $fillable = [
        'name',
        'preview_image',
        'file_path',
        'slug',
        'description',
        'cover_image',
        'html_content',
        'css_variables',
        'source_type',
    ];

    protected $casts = [
        'css_variables' => 'array',
    ];

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get cover image URL with fallback handling.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        return StorageHelper::getTemplateCoverUrl($this->cover_image);
    }

    /**
     * Method untuk mendapatkan HTML dengan variabel yang sudah diganti
     * PENTING: HTML yang dikembalikan sudah di-sanitasi untuk keamanan
     * 
     * @param array $variables Data untuk replace placeholder
     * @param bool $forPdf Set true untuk PDF export (tidak escape HTML)
     * @return string Sanitized HTML dengan variabel yang sudah diganti
     */
    public function getCompiledHtml($variables = [], $forPdf = false)
    {
        // Get template content from file_path first, then fallback to html_content
        $htmlContent = null;
        
        // Try to get HTML content from file_path first
        if (!empty($this->file_path)) {
            $templatePath = public_path('templates/' . $this->file_path);
            if (file_exists($templatePath)) {
                $htmlContent = file_get_contents($templatePath);
            }
        }
        
        // Fallback to html_content field if file doesn't exist
        if (empty($htmlContent) && !empty($this->html_content)) {
            // Decode HTML entities for proper display
            $htmlContent = html_entity_decode($this->html_content, ENT_QUOTES, 'UTF-8');
        }
        
        if (empty($htmlContent)) {
            return '<div class="text-center py-12 text-red-600"><p>Template content tidak dapat dimuat</p></div>';
        }
        
        // Replace template variables (support both [] and {{}} formats)
        foreach ($variables as $key => $value) {
            // Untuk PDF, tidak perlu escape karena tidak ada risiko XSS
            // Untuk web view, escape untuk mencegah XSS
            $safeValue = $forPdf ? $value : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $htmlContent = str_replace('[' . $key . ']', $safeValue, $htmlContent);
            $htmlContent = str_replace('{{' . $key . '}}', $safeValue, $htmlContent);
        }
        
        // Basic sanitization - remove dangerous scripts but keep styling
        $htmlContent = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $htmlContent);
        
        return $htmlContent;
    }

    /**
     * Accessor untuk mendapatkan HTML yang sudah di-decode (INTERNAL USE ONLY)
     * JANGAN gunakan untuk output langsung - selalu gunakan getCompiledHtml()
     * 
     * @return string Decoded HTML (belum di-sanitasi)
     */
    public function getDecodedHtmlAttribute(): string
    {
        return html_entity_decode($this->html_content, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Mutator untuk memastikan HTML selalu di-escape saat disimpan
     * Sistem keamanan berlapis: escape di client, double-check di server
     * 
     * @param string $value HTML content
     */
    public function setHtmlContentAttribute($value)
    {
        // Jika HTML belum di-escape (tidak mengandung entities), escape dulu
        if (strpos($value, '&lt;') === false && strpos($value, '<') !== false) {
            $sanitizer = app(\App\Services\HtmlSanitizerService::class);
            $value = $sanitizer->escapeForStorage($value);
        }
        
        $this->attributes['html_content'] = $value;
    }

    /**
     * Get safe HTML untuk preview (dengan sanitasi ringan)
     * Digunakan untuk preview real-time di admin panel
     * 
     * @return string Safe HTML for preview
     */
    public function getSafePreviewHtml(): string
    {
        $sanitizer = app(\App\Services\HtmlSanitizerService::class);
        $decodedHtml = html_entity_decode($this->html_content, ENT_QUOTES, 'UTF-8');
        
        return $sanitizer->cleanForPreview($decodedHtml);
    }

}
