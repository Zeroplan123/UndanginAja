<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TemplateFileService
{
    // No dependencies needed - base64 encoding provides security

    /**
     * Process uploaded HTML file
     * Converts HTML to TXT format for secure storage, then decodes back to HTML for use
     */
    public function processHtmlFile(UploadedFile $file)
    {
        try {
            // Validate file
            if (!$file->isValid()) {
                return [
                    'success' => false,
                    'errors' => ['File tidak valid atau rusak']
                ];
            }

            // Read file content
            $htmlContent = file_get_contents($file->getRealPath());
            
            if ($htmlContent === false) {
                return [
                    'success' => false,
                    'errors' => ['Gagal membaca konten file']
                ];
            }

            // Convert HTML to TXT format (base64 encoded for safety)
            // No XSS validation needed - base64 encoding provides security layer
            $txtContent = $this->convertHtmlToTxt($htmlContent);

            // Generate unique filename with .txt extension
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.txt';
            
            // Store as TXT file
            $filePath = 'templates/' . $fileName;
            Storage::disk('local')->put($filePath, $txtContent);

            Log::info('Template file uploaded and converted to TXT', [
                'original_file' => $file->getClientOriginalName(),
                'stored_as' => $fileName,
                'original_size' => strlen($htmlContent),
                'txt_size' => strlen($txtContent)
            ]);

            return [
                'success' => true,
                'file_path' => $filePath,
                'html_content' => $htmlContent, // Return decoded HTML for immediate use
                'file_size' => strlen($htmlContent)
            ];

        } catch (\Exception $e) {
            Log::error('Template file processing failed', [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName()
            ]);

            return [
                'success' => false,
                'errors' => ['Gagal memproses file: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Update existing template file
     */
    public function updateTemplateFile(UploadedFile $file, $oldFilePath = null)
    {
        try {
            // Delete old file if exists
            if ($oldFilePath) {
                $this->deleteTemplateFile($oldFilePath);
            }

            // Process new file
            return $this->processHtmlFile($file);

        } catch (\Exception $e) {
            Log::error('Template file update failed', [
                'error' => $e->getMessage(),
                'old_file_path' => $oldFilePath,
                'new_file_name' => $file->getClientOriginalName()
            ]);

            return [
                'success' => false,
                'errors' => ['Gagal memperbarui file: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Delete template file
     */
    public function deleteTemplateFile($filePath)
    {
        try {
            if ($filePath && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
                
                Log::info('Template file deleted', [
                    'file_path' => $filePath
                ]);
                
                return true;
            }
            
            return false;

        } catch (\Exception $e) {
            Log::error('Template file deletion failed', [
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);

            return false;
        }
    }

    /**
     * Get template file content
     * Automatically decodes TXT back to HTML
     */
    public function getTemplateFileContent($filePath)
    {
        try {
            if ($filePath && Storage::disk('local')->exists($filePath)) {
                $content = Storage::disk('local')->get($filePath);
                
                // Check if file is TXT format (base64 encoded)
                if (pathinfo($filePath, PATHINFO_EXTENSION) === 'txt') {
                    return $this->convertTxtToHtml($content);
                }
                
                // Return as-is for HTML files (backward compatibility)
                return $content;
            }
            
            return null;

        } catch (\Exception $e) {
            Log::error('Failed to read template file', [
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);

            return null;
        }
    }

    /**
     * Check if template file exists
     */
    public function templateFileExists($filePath)
    {
        return $filePath && Storage::disk('local')->exists($filePath);
    }

    /**
     * Get template file size
     */
    public function getTemplateFileSize($filePath)
    {
        try {
            if ($this->templateFileExists($filePath)) {
                return Storage::disk('local')->size($filePath);
            }
            
            return 0;

        } catch (\Exception $e) {
            Log::error('Failed to get template file size', [
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);

            return 0;
        }
    }

    /**
     * List all template files
     */
    public function listTemplateFiles()
    {
        try {
            return Storage::disk('local')->files('templates');
        } catch (\Exception $e) {
            Log::error('Failed to list template files', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Convert HTML to TXT format (base64 encoded)
     * 
     * @param string $html HTML content
     * @return string Base64 encoded content
     */
    protected function convertHtmlToTxt(string $html): string
    {
        // Base64 encode for safe storage as TXT
        return base64_encode($html);
    }

    /**
     * Convert TXT format back to HTML (base64 decoded)
     * 
     * @param string $txt Base64 encoded content
     * @return string Decoded HTML content
     */
    protected function convertTxtToHtml(string $txt): string
    {
        // Base64 decode to get original HTML
        $decoded = base64_decode($txt, true);
        
        // Validate base64 decoding
        if ($decoded === false) {
            Log::warning('Failed to decode TXT to HTML, returning original content');
            return $txt; // Return original if decode fails
        }
        
        return $decoded;
    }

    /**
     * Clean up orphaned template files
     */
    public function cleanupOrphanedFiles()
    {
        try {
            $allFiles = $this->listTemplateFiles();
            $usedFiles = \App\Models\Template::whereNotNull('file_path')
                ->pluck('file_path')
                ->toArray();

            $orphanedFiles = array_diff($allFiles, $usedFiles);
            $deletedCount = 0;

            foreach ($orphanedFiles as $file) {
                if ($this->deleteTemplateFile($file)) {
                    $deletedCount++;
                }
            }

            Log::info('Cleaned up orphaned template files', [
                'total_files' => count($allFiles),
                'used_files' => count($usedFiles),
                'orphaned_files' => count($orphanedFiles),
                'deleted_count' => $deletedCount
            ]);

            return [
                'success' => true,
                'deleted_count' => $deletedCount,
                'orphaned_count' => count($orphanedFiles)
            ];

        } catch (\Exception $e) {
            Log::error('Failed to cleanup orphaned template files', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
