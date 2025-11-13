/**
 * Template Security Manager
 * Sistem keamanan berlapis untuk mencegah XSS attacks pada template HTML
 * 
 * Fitur:
 * - Escape HTML sebelum submit ke server
 * - Validasi keamanan real-time
 * - Preview yang aman dengan sanitasi
 * - Monitoring dan logging security events
 */

class TemplateSecurityManager {
    
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.securityCheckTimeout = null;
        this.previewTimeout = null;
        this.isFormSubmitting = false;
        
        this.init();
    }

    /**
     * Initialize security manager
     */
    init() {
        this.setupFormSecurity();
        this.setupRealTimeValidation();
        this.setupSecurePreview();
        this.addSecurityIndicators();
        
        console.log('TemplateSecurityManager initialized');
    }

    /**
     * Escape HTML untuk penyimpanan aman
     * Mengubah karakter khusus menjadi HTML entities
     */
    static escapeHtml(unsafe) {
        if (!unsafe) return '';
        
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Unescape HTML untuk preview (HANYA untuk demo internal)
     * JANGAN gunakan untuk output final - gunakan sanitasi server
     */
    static unescapeHtml(safe) {
        if (!safe) return '';
        
        return safe
            .replace(/&amp;/g, "&")
            .replace(/&lt;/g, "<")
            .replace(/&gt;/g, ">")
            .replace(/&quot;/g, '"')
            .replace(/&#039;/g, "'");
    }

    /**
     * Sanitasi basic di client (fallback ringan)
     * Untuk preview real-time sebelum validasi server
     */
    static basicClientSanitize(html) {
        if (!html) return '';
        
        // Hapus script tags
        html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
        
        // Hapus event handlers
        html = html.replace(/\s*on\w+\s*=\s*["'][^"']*["']/gi, '');
        
        // Hapus javascript: URLs
        html = html.replace(/javascript:/gi, '');
        
        // Hapus data: URLs yang mencurigakan (kecuali gambar)
        html = html.replace(/data:(?!image\/)[^;]*;[^"']*["']?/gi, '');
        
        return html;
    }

    /**
     * Setup form security dengan escape sebelum submit
     */
    setupFormSecurity() {
        const form = document.querySelector('form[action*="templates"]');
        const textarea = document.getElementById('html_content');
        
        if (!form || !textarea) return;

        form.addEventListener('submit', (e) => {
            if (this.isFormSubmitting) return;
            
            e.preventDefault();
            this.isFormSubmitting = true;
            
            // Escape HTML content sebelum submit
            const originalContent = textarea.value;
            const escapedContent = TemplateSecurityManager.escapeHtml(originalContent);
            
            // Buat atau update hidden input untuk data yang di-escape
            let hiddenInput = document.getElementById('escaped_html_content');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'html_content';
                hiddenInput.id = 'escaped_html_content';
                form.appendChild(hiddenInput);
            }
            
            hiddenInput.value = escapedContent;
            
            // Disable textarea asli agar tidak terkirim
            textarea.disabled = true;
            
            // Log security action
            console.log('HTML content escaped before submission', {
                original_length: originalContent.length,
                escaped_length: escapedContent.length,
                contains_html: originalContent.includes('<')
            });
            
            // Submit form
            this.submitFormSafely(form, textarea);
        });
    }

    /**
     * Submit form dengan error handling
     */
    submitFormSafely(form, textarea) {
        try {
            form.submit();
        } catch (error) {
            console.error('Form submission failed:', error);
            
            // Restore textarea jika gagal
            textarea.disabled = false;
            this.isFormSubmitting = false;
            
            alert('Gagal menyimpan template. Silakan coba lagi.');
        }
    }

    /**
     * Setup validasi keamanan real-time
     */
    setupRealTimeValidation() {
        const textarea = document.getElementById('html_content');
        if (!textarea) return;

        // Debounced security check
        textarea.addEventListener('input', () => {
            clearTimeout(this.securityCheckTimeout);
            this.securityCheckTimeout = setTimeout(() => {
                this.performSecurityCheck(textarea.value);
            }, 1000); // Check 1 detik setelah user berhenti mengetik
        });

        // Check saat blur
        textarea.addEventListener('blur', () => {
            this.performSecurityCheck(textarea.value);
        });
    }

    /**
     * Perform security check via AJAX
     */
    async performSecurityCheck(htmlContent) {
        if (!htmlContent.trim()) {
            this.updateSecurityIndicator('safe', 'Tidak ada konten untuk divalidasi');
            return;
        }

        try {
            const response = await fetch('/admin/templates/validate-security', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    html_content: htmlContent
                })
            });

            const result = await response.json();
            
            if (result.is_safe) {
                this.updateSecurityIndicator('safe', result.message);
            } else {
                this.updateSecurityIndicator(result.risk_level, result.message);
            }

        } catch (error) {
            console.error('Security validation failed:', error);
            this.updateSecurityIndicator('unknown', 'Gagal memvalidasi keamanan');
        }
    }

    /**
     * Update security indicator UI
     */
    updateSecurityIndicator(status, message) {
        let indicator = document.getElementById('security-indicator');
        
        if (!indicator) {
            indicator = this.createSecurityIndicator();
        }

        // Reset classes
        indicator.className = 'security-indicator';
        
        // Set status class dan icon
        switch (status) {
            case 'safe':
                indicator.classList.add('safe');
                indicator.innerHTML = `
                    <i class="fas fa-shield-alt text-green-500"></i>
                    <span class="text-green-700">Aman</span>
                `;
                break;
            case 'low':
                indicator.classList.add('low-risk');
                indicator.innerHTML = `
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    <span class="text-yellow-700">Risiko Rendah</span>
                `;
                break;
            case 'medium':
                indicator.classList.add('medium-risk');
                indicator.innerHTML = `
                    <i class="fas fa-exclamation-triangle text-orange-500"></i>
                    <span class="text-orange-700">Risiko Sedang</span>
                `;
                break;
            case 'high':
                indicator.classList.add('high-risk');
                indicator.innerHTML = `
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span class="text-red-700">Risiko Tinggi</span>
                `;
                break;
            default:
                indicator.classList.add('unknown');
                indicator.innerHTML = `
                    <i class="fas fa-question-circle text-gray-500"></i>
                    <span class="text-gray-700">Tidak Diketahui</span>
                `;
        }

        // Update tooltip message
        indicator.title = message;
        
        // Show/hide submit button based on risk
        this.toggleSubmitButton(status !== 'high');
    }

    /**
     * Create security indicator element
     */
    createSecurityIndicator() {
        const textarea = document.getElementById('html_content');
        if (!textarea) return null;

        const indicator = document.createElement('div');
        indicator.id = 'security-indicator';
        indicator.className = 'security-indicator';
        
        // Insert after textarea
        textarea.parentNode.insertBefore(indicator, textarea.nextSibling);
        
        return indicator;
    }

    /**
     * Toggle submit button berdasarkan security status
     */
    toggleSubmitButton(isEnabled) {
        const submitButton = document.querySelector('button[type="submit"]');
        if (!submitButton) return;

        if (isEnabled) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            submitButton.title = 'Simpan template';
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            submitButton.title = 'Template mengandung konten berbahaya dan tidak dapat disimpan';
        }
    }

    /**
     * Setup secure preview dengan sanitasi
     */
    setupSecurePreview() {
        // Override existing preview function jika ada
        window.previewTemplate = () => {
            this.showSecurePreview();
        };

        // Setup auto-preview saat mengetik (opsional)
        const textarea = document.getElementById('html_content');
        if (textarea) {
            textarea.addEventListener('input', () => {
                clearTimeout(this.previewTimeout);
                this.previewTimeout = setTimeout(() => {
                    this.updateLivePreview(textarea.value);
                }, 2000); // Update preview 2 detik setelah berhenti mengetik
            });
        }
    }

    /**
     * Show secure preview dalam modal
     */
    async showSecurePreview() {
        const textarea = document.getElementById('html_content');
        const modal = document.getElementById('previewModal');
        const iframe = document.getElementById('previewFrame');
        
        if (!textarea || !modal || !iframe) {
            alert('Preview tidak tersedia');
            return;
        }

        const htmlContent = textarea.value;
        if (!htmlContent.trim()) {
            alert('Tidak ada konten untuk di-preview');
            return;
        }

        try {
            // Show loading
            iframe.src = 'data:text/html,<div style="padding:20px;text-align:center;">Loading preview...</div>';
            modal.classList.remove('hidden');

            // Get secure preview dari server
            const response = await fetch('/admin/templates/secure-preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'text/html'
                },
                body: JSON.stringify({
                    html_content: htmlContent
                })
            });

            // Always get the response text (could be HTML or error HTML)
            const responseText = await response.text();
            
            if (response.ok) {
                // Success - display the preview
                const blob = new Blob([responseText], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                iframe.src = url;
                
                // Cleanup URL setelah load
                iframe.onload = () => {
                    setTimeout(() => URL.revokeObjectURL(url), 1000);
                };
            } else {
                // Error - but we still display the error HTML
                console.error('Preview failed with status:', response.status);
                console.error('Error response:', responseText);
                
                // Display error HTML in iframe
                const blob = new Blob([responseText], { type: 'text/html' });
                const url = URL.createObjectURL(blob);
                iframe.src = url;
                
                iframe.onload = () => {
                    setTimeout(() => URL.revokeObjectURL(url), 1000);
                };
            }

        } catch (error) {
            console.error('Preview failed:', error);
            iframe.src = 'data:text/html,<div style="padding:20px;text-align:center;color:red;">Preview gagal dimuat. Periksa konten HTML Anda.</div>';
        }
    }

    /**
     * Update live preview (jika ada container preview)
     */
    updateLivePreview(htmlContent) {
        const previewContainer = document.getElementById('live-preview-container');
        if (!previewContainer) return;

        if (!htmlContent.trim()) {
            previewContainer.innerHTML = '<p class="text-gray-500">Preview akan muncul di sini...</p>';
            return;
        }

        // Basic client-side sanitization untuk live preview
        const sanitizedHtml = TemplateSecurityManager.basicClientSanitize(htmlContent);
        previewContainer.innerHTML = sanitizedHtml;
    }

    /**
     * Add security indicators dan styling
     */
    addSecurityIndicators() {
        // Add CSS untuk security indicators
        if (!document.getElementById('security-styles')) {
            const style = document.createElement('style');
            style.id = 'security-styles';
            style.textContent = `
                .security-indicator {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 12px;
                    margin-top: 8px;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 500;
                }
                .security-indicator.safe {
                    background-color: #f0fdf4;
                    border: 1px solid #bbf7d0;
                }
                .security-indicator.low-risk {
                    background-color: #fffbeb;
                    border: 1px solid #fed7aa;
                }
                .security-indicator.medium-risk {
                    background-color: #fff7ed;
                    border: 1px solid #fdba74;
                }
                .security-indicator.high-risk {
                    background-color: #fef2f2;
                    border: 1px solid #fca5a5;
                }
                .security-indicator.unknown {
                    background-color: #f9fafb;
                    border: 1px solid #d1d5db;
                }
            `;
            document.head.appendChild(style);
        }
    }
}

// Utility functions untuk backward compatibility
window.insertVariable = function(variable) {
    const textarea = document.getElementById('html_content');
    if (textarea) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const before = text.substring(0, start);
        const after = text.substring(end, text.length);
        
        textarea.value = before + variable + after;
        textarea.selectionStart = textarea.selectionEnd = start + variable.length;
        textarea.focus();
        
        // Trigger security check
        textarea.dispatchEvent(new Event('input'));
    }
};

window.closePreview = function() {
    const modal = document.getElementById('previewModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

// Initialize saat DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize security manager
    window.templateSecurity = new TemplateSecurityManager();
    
    console.log('Template security system loaded');
});
