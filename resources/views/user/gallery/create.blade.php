<x-app-layout>
<x-slot name="title">Upload Photos</x-slot>

<div class="min-h-screen bg-gray-50">
    <!-- Professional Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Upload Photos</h1>
                    <p class="text-gray-600 mt-1">Add new photos to your gallery</p>
                </div>
                <a href="{{ route('user.gallery.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Gallery
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2 mt-0.5"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Professional Upload Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('user.gallery.store') }}" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <!-- File Upload Area -->
                <div class="mb-8">
                    <label class="block text-lg font-semibold text-gray-900 mb-4">
                        Select Photos to Upload
                    </label>
                    <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-colors duration-200">
                        <div id="dropZoneContent">
                            <div class="mb-4">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Upload Your Photos</h3>
                            <p class="text-gray-600 mb-2">Drag and drop your photos here</p>
                            <p class="text-sm text-gray-500 mb-6">or click to browse files</p>
                            <input type="file" id="fileInput" name="photos[]" multiple accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('fileInput').click()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center">
                                <i class="fas fa-images mr-2"></i>
                                Choose Files
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-gray-600 flex items-center">
                        <i class="fas fa-info-circle text-gray-400 mr-2"></i>
                        <span>Supported formats: JPEG, PNG, JPG, GIF, WebP • Max size: 10MB per file • Up to 10 files at once</span>
                    </div>
                </div>

                <!-- Preview Area -->
                <div id="previewArea" class="mb-8 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-images text-blue-600 mr-2"></i>
                        Selected Photos
                    </h3>
                    <div id="previewContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <!-- Preview items will be inserted here -->
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('user.gallery.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" id="uploadButton" disabled 
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-upload mr-2"></i>Upload Photos
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
    document.getElementById('dropZone').classList.add('border-blue-500', 'bg-blue-50');
}

function dragLeaveHandler(ev) {
    ev.preventDefault();
    document.getElementById('dropZone').classList.remove('border-blue-500', 'bg-blue-50');
}

function dropHandler(ev) {
    ev.preventDefault();
    document.getElementById('dropZone').classList.remove('border-blue-500', 'bg-blue-50');
    
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
            previewItem.className = 'bg-white border border-gray-200 rounded-lg p-3 relative shadow-sm';
            previewItem.innerHTML = `
                <button type="button" onclick="removeFile(${index})" 
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors duration-200 z-10">
                    ×
                </button>
                <img src="${e.target.result}" alt="${file.name}" class="w-full h-24 object-cover rounded mb-2">
                <p class="text-xs font-medium text-gray-800 truncate mb-1">${file.name}</p>
                <p class="text-xs text-gray-500 mb-2">${formatFileSize(file.size)}</p>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Caption (optional)</label>
                    <input type="text" name="captions[${index}]" placeholder="Add caption..." 
                           class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
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
    const fileInput = document.getElementById('fileInput');
    const dt = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dt.items.add(file);
    });
    
    fileInput.files = dt.files;
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

// Add event listeners and prevent default drag behaviors
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dropZone').addEventListener('dragover', dragOverHandler);
    document.getElementById('dropZone').addEventListener('dragenter', dragEnterHandler);
    document.getElementById('dropZone').addEventListener('dragleave', dragLeaveHandler);
    document.getElementById('dropZone').addEventListener('drop', dropHandler);
    document.getElementById('fileInput').addEventListener('change', handleFileSelect);
});

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    document.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}
</script>
</x-app-layout>
