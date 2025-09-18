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
    
    // Replace variabel dengan data sample untuk preview
    let previewHtml = htmlContent
        .replace(/\[bride_name\]/g, 'Siti Nurhaliza')
        .replace(/\[groom_name\]/g, 'Ahmad Dhani')
        .replace(/\[wedding_date\]/g, '25 Desember 2024')
        .replace(/\[wedding_time\]/g, '10:00 WIB')
        .replace(/\[venue\]/g, 'Hotel Grand Indonesia')
        .replace(/\[location\]/g, 'Jl. MH Thamrin No.1, Jakarta Pusat')
        .replace(/\[additional_notes\]/g, 'Mohon kehadiran Bapak/Ibu/Saudara/i')
        // Legacy support
        .replace(/\[nama_mempelai_pria\]/g, 'Ahmad Dhani')
        .replace(/\[nama_mempelai_wanita\]/g, 'Siti Nurhaliza')
        .replace(/\[tanggal_pernikahan\]/g, '25 Desember 2024')
        .replace(/\[waktu_pernikahan\]/g, '10:00 WIB')
        .replace(/\[lokasi_pernikahan\]/g, 'Hotel Grand Indonesia, Jakarta');

    const previewFrame = document.getElementById('previewFrame');
    const blob = new Blob([previewHtml], { type: 'text/html' });
    previewFrame.src = URL.createObjectURL(blob);
    
    document.getElementById('previewModal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
    const previewFrame = document.getElementById('previewFrame');
    URL.revokeObjectURL(previewFrame.src);
}

// Close modal when clicking outside
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreview();
    }
});

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