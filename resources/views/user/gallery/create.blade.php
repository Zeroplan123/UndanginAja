<x-app-layout>
<x-slot name="title">Upload Memory</x-slot>
<link rel="stylesheet" href="{{ asset('css/romantic-gallery.css') }}">
<div class="min-h-screen bg-gradient-to-br from-rose-50 via-pink-50 to-white">
    <!-- Romantic Header -->
    <div class="relative overflow-hidden bg-gradient-to-r from-rose-100 via-pink-50 to-rose-100 py-12">
        <div class="absolute inset-0 bg-white/20 backdrop-blur-sm"></div>
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-4 left-4 w-16 h-16 bg-rose-200/30 rounded-full animate-pulse"></div>
            <div class="absolute top-8 right-8 w-8 h-8 bg-pink-200/40 rounded-full animate-bounce"></div>
            <div class="absolute bottom-4 left-1/3 w-12 h-12 bg-rose-100/50 rounded-full"></div>
        </div>
        <div class="relative container mx-auto px-4">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-rose-800 mb-2 font-serif">
                    <i class="fas fa-camera text-rose-400 mr-3"></i>
                    Capture New Memories
                    <i class="fas fa-heart text-rose-400 ml-3"></i>
                </h1>
                <p class="text-rose-600 text-lg italic font-serif">Upload your precious moments to the memory vault</p>
            </div>
            <div class="flex justify-center">
                <a href="{{ route('user.gallery.index') }}" class="romantic-back-btn group">
                    <i class="fas fa-arrow-left mr-2 group-hover:animate-pulse"></i>
                    Back to Gallery
                    <i class="fas fa-images ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">

        @if($errors->any())
            <div class="romantic-error-alert mb-6">
                <i class="fas fa-heart-broken text-red-500 mr-2"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Romantic Upload Form -->
        <div class="romantic-upload-container">
            <form method="POST" action="{{ route('user.gallery.store') }}" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <!-- Romantic File Upload Area -->
                <div class="mb-8">
                    <label class="romantic-upload-label">
                        <i class="fas fa-heart text-rose-500 mr-2"></i>
                        Select Your Precious Memories
                        <i class="fas fa-sparkles text-rose-400 ml-2"></i>
                    </label>
                    <div id="dropZone" class="romantic-drop-zone">
                        <div id="dropZoneContent" class="romantic-drop-content">
                            <div class="romantic-upload-icon">
                                <i class="fas fa-cloud-upload-alt text-6xl text-rose-300 mb-4 animate-bounce"></i>
                                <i class="fas fa-heart text-2xl text-rose-400 absolute transform translate-x-4 -translate-y-2 animate-pulse"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-rose-700 mb-2 font-serif">Share Your Love Story</h3>
                            <p class="text-lg text-rose-600 mb-2 italic font-serif">Drag & drop your beautiful moments here</p>
                            <p class="text-sm text-rose-500 mb-6 font-serif">or click to browse your memories</p>
                            <input type="file" id="fileInput" name="photos[]" multiple accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('fileInput').click()" 
                                    class="romantic-choose-btn">
                                <i class="fas fa-images mr-2"></i>
                                Choose Memories
                                <i class="fas fa-heart ml-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="romantic-upload-info">
                        <i class="fas fa-info-circle text-rose-400 mr-2"></i>
                        <span class="font-serif italic">Supported: JPEG, PNG, JPG, GIF, WebP • Max: 10MB per memory • Up to 50 memories at once</span>
                    </div>
                </div>

                <!-- Romantic Preview Area -->
                <div id="previewArea" class="mb-8 hidden">
                    <h3 class="romantic-preview-title">
                        <i class="fas fa-images text-rose-500 mr-2"></i>
                        Your Selected Memories
                        <i class="fas fa-heart text-rose-400 ml-2"></i>
                    </h3>
                    <div id="previewContainer" class="romantic-preview-grid">
                        <!-- Preview items will be inserted here -->
                    </div>
                </div>

                <!-- Romantic Submit Buttons -->
                <div class="romantic-submit-container">
                    <a href="{{ route('user.gallery.index') }}" class="romantic-cancel-btn">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" id="uploadButton" disabled class="romantic-upload-submit-btn">
                        <i class="fas fa-heart mr-2"></i>Save Memories
                        <i class="fas fa-sparkles ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedFiles = [];
const maxFiles = 10;
const maxFileSize = 10 * 1024 * 1024; // 10MB

function dragOverHandler(ev) {
    ev.preventDefault();
    ev.dataTransfer.dropEffect = "copy";
}

function dragEnterHandler(ev) {
    ev.preventDefault();
    document.getElementById('dropZone').classList.add('border-blue-400', 'bg-blue-50');
}

function dragLeaveHandler(ev) {
    ev.preventDefault();
    document.getElementById('dropZone').classList.remove('border-blue-400', 'bg-blue-50');
}

function dropHandler(ev) {
    ev.preventDefault();
    document.getElementById('dropZone').classList.remove('border-blue-400', 'bg-blue-50');
    
    const files = Array.from(ev.dataTransfer.files);
    handleFiles(files);
}

function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    handleFiles(files);
}

function handleFiles(files) {
    // Filter image files only
    const imageFiles = files.filter(file => file.type.startsWith('image/'));
    
    if (imageFiles.length === 0) {
        alert('Please select only image files.');
        return;
    }
    
    // Check file count limit
    if (selectedFiles.length + imageFiles.length > maxFiles) {
        alert(`You can only upload up to ${maxFiles} files at once.`);
        return;
    }
    
    // Check file size
    const oversizedFiles = imageFiles.filter(file => file.size > maxFileSize);
    if (oversizedFiles.length > 0) {
        alert(`Some files are too large. Maximum file size is 10MB.`);
        return;
    }
    
    // Add files to selection
    imageFiles.forEach(file => {
        selectedFiles.push(file);
    });
    
    updatePreview();
    updateUploadButton();
}

function updatePreview() {
    const previewArea = document.getElementById('previewArea');
    const previewContainer = document.getElementById('previewContainer');
    
    if (selectedFiles.length === 0) {
        previewArea.classList.add('hidden');
        return;
    }
    
    previewArea.classList.remove('hidden');
    previewContainer.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'bg-white border rounded-lg p-4 relative';
            previewItem.innerHTML = `
                <button type="button" onclick="removeFile(${index})" 
                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                    ×
                </button>
                <img src="${e.target.result}" alt="${file.name}" class="w-full h-32 object-cover rounded mb-2">
                <p class="text-sm font-medium text-gray-800 truncate">${file.name}</p>
                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                <div class="mt-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Caption (optional)</label>
                    <input type="text" name="captions[${index}]" placeholder="Add a caption..." 
                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            `;
            previewContainer.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
    updateUploadButton();
    updateFileInput();
}

function updateFileInput() {
    const photoInput = document.getElementById('photoInput');
    const dt = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dt.items.add(file);
    });
    
    photoInput.files = dt.files;
}

function updateUploadButton() {
    const uploadButton = document.getElementById('uploadButton');
    uploadButton.disabled = selectedFiles.length === 0;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Handle form submission
document.querySelector('form').addEventListener('submit', function(e) {
    if (selectedFiles.length === 0) {
        e.preventDefault();
        alert('Please select at least one photo to upload.');
        return;
    }
    
    // Update file input with selected files
    updateFileInput();
    
    // Show loading state
    const uploadButton = document.getElementById('uploadButton');
    uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    uploadButton.disabled = true;
});

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    document.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}
</script>
</x-app-layout>
