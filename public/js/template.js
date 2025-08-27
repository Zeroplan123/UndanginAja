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