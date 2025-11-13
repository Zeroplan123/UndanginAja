function insertVariable(variable) {
    const textarea = document.getElementById('html_content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + variable.length;
    textarea.focus();
}

function previewTemplate() {
    const htmlContent = document.getElementById('html_content').value;
    const modal = document.getElementById('previewModal');
    const previewFrame = document.getElementById('previewFrame');
    
    if (!htmlContent.trim()) {
        alert('Tidak ada konten HTML untuk di-preview');
        return;
    }

    try {
        // Show loading
        modal.classList.remove('hidden');
        
        // Sample data untuk preview
        const sampleData = {
            bride_name: 'Siti Nurhaliza',
            groom_name: 'Ahmad Dhani',
            wedding_date: '25 Desember 2024',
            wedding_time: '10:00 WIB',
            venue: 'Hotel Grand Indonesia',
            location: 'Jl. MH Thamrin No.1, Jakarta Pusat',
            additional_notes: 'Mohon kehadiran Bapak/Ibu/Saudara/i untuk berbagi kebahagiaan bersama kami'
        };
        
        // Replace variables dengan sample data
        let previewHtml = htmlContent;
        for (const [key, value] of Object.entries(sampleData)) {
            const regex = new RegExp(`\\[${key}\\]`, 'g');
            previewHtml = previewHtml.replace(regex, value);
        }
        
        // Basic sanitization - remove dangerous tags
        previewHtml = sanitizeHtmlForPreview(previewHtml);
        
        // Create complete HTML document
        const fullHtml = `
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Template</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
        }
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="preview-container">
        ${previewHtml}
    </div>
</body>
</html>`;
        
        // Create blob and object URL
        const blob = new Blob([fullHtml], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        previewFrame.src = url;
        
        // Cleanup URL after load
        previewFrame.onload = () => {
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        };

    } catch (error) {
        console.error('Preview failed:', error);
        previewFrame.src = 'data:text/html,<div style="padding:20px;text-align:center;color:red;font-family:sans-serif;">❌ Preview gagal dimuat. Periksa konten HTML Anda.<br><small>' + error.message + '</small></div>';
    }
}

/**
 * Basic HTML sanitization untuk preview
 * Menghapus tag dan atribut berbahaya
 */
function sanitizeHtmlForPreview(html) {
    // Remove script tags
    html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    
    // Remove iframe tags
    html = html.replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '');
    
    // Remove object/embed tags
    html = html.replace(/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/gi, '');
    html = html.replace(/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/gi, '');
    
    // Remove form tags
    html = html.replace(/<form\b[^<]*(?:(?!<\/form>)<[^<]*)*<\/form>/gi, '');
    
    // Remove dangerous event handlers
    html = html.replace(/\son\w+\s*=\s*["'][^"']*["']/gi, '');
    html = html.replace(/\son\w+\s*=\s*[^\s>]*/gi, '');
    
    // Remove javascript: protocol
    html = html.replace(/javascript:/gi, '');
    
    return html;
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    const previewFrame = document.getElementById('previewFrame');
    URL.revokeObjectURL(previewFrame.src);
}

// Close modal when clicking outside - moved to DOMContentLoaded
// (handled in template_create.blade.php inline script)

// Real-time template name validation
let nameCheckTimeout;
function checkTemplateName() {
    const nameInput = document.querySelector('input[name="name"]');
    const errorDiv = document.getElementById('name-error');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (!nameInput) return;
    
    const templateName = nameInput.value.trim();
    
    // Clear previous timeout
    clearTimeout(nameCheckTimeout);
    
    // Clear previous error
    if (errorDiv) {
        errorDiv.remove();
    }
    
    if (templateName.length < 3) {
        return;
    }
    
    // Add loading indicator
    nameInput.classList.add('border-yellow-300');
    
    nameCheckTimeout = setTimeout(() => {
        // Get current template ID for edit mode
        const form = nameInput.closest('form');
        const method = form.querySelector('input[name="_method"]');
        const isEdit = method && method.value === 'PUT';
        const templateId = isEdit ? form.action.split('/').pop() : null;
        
        fetch('/admin/templates/check-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                name: templateName,
                ignore_id: templateId
            })
        })
        .then(response => response.json())
        .then(data => {
            nameInput.classList.remove('border-yellow-300');
            
            if (data.exists) {
                // Show error
                nameInput.classList.add('border-red-500');
                nameInput.classList.remove('border-green-500');
                
                const errorDiv = document.createElement('div');
                errorDiv.id = 'name-error';
                errorDiv.className = 'text-red-600 text-sm mt-1';
                errorDiv.textContent = 'Nama template sudah digunakan. Silakan pilih nama yang berbeda.';
                nameInput.parentNode.appendChild(errorDiv);
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else {
                // Show success
                nameInput.classList.remove('border-red-500');
                nameInput.classList.add('border-green-500');
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        })
        .catch(error => {
            console.error('Error checking template name:', error);
            nameInput.classList.remove('border-yellow-300', 'border-red-500', 'border-green-500');
        });
    }, 500);
}

// Initialize name checking when page loads
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    if (nameInput) {
        nameInput.addEventListener('input', checkTemplateName);
        nameInput.addEventListener('blur', checkTemplateName);
    }
});