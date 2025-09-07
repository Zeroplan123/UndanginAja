<x-app-layout>
<x-slot name="title">Tampilkan Memory</x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Photo Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('user.gallery.edit', $gallery) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('user.gallery.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Gallery
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Photo Display -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="relative">
                    <img src="{{ $gallery->file_url }}" alt="{{ $gallery->caption ?: $gallery->original_name }}" 
                         class="w-full rounded-lg shadow-sm cursor-pointer" onclick="openFullscreen()">
                    <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white text-sm px-3 py-1 rounded">
                        {{ $gallery->formatted_file_size }}
                    </div>
                </div>
                
                @if($gallery->caption)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Caption</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $gallery->caption }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Photo Information Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Photo Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Original Name</label>
                        <div class="mt-1 text-sm text-gray-900 break-all">{{ $gallery->original_name }}</div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">File Size</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $gallery->formatted_file_size }}</div>
                    </div>

                    @if($gallery->metadata && isset($gallery->metadata['width']))
                        <div>
                            <label class="text-sm font-medium text-gray-600">Dimensions</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $gallery->metadata['width'] }} × {{ $gallery->metadata['height'] }} pixels</div>
                        </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-600">File Type</label>
                        <div class="mt-1 text-sm text-gray-900">{{ strtoupper(pathinfo($gallery->original_name, PATHINFO_EXTENSION)) }}</div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Uploaded</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $gallery->created_at->format('M d, Y H:i') }}</div>
                    </div>

                    @if($gallery->updated_at != $gallery->created_at)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Last Modified</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $gallery->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('user.gallery.download', $gallery) }}" 
                       class="w-full bg-green-500 hover:bg-green-600 text-white text-center py-3 px-4 rounded-lg transition duration-200 block">
                        <i class="fas fa-download mr-2"></i>Download Original
                    </a>
                    
                    <a href="{{ route('user.gallery.edit', $gallery) }}" 
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white text-center py-3 px-4 rounded-lg transition duration-200 block">
                        <i class="fas fa-edit mr-2"></i>Edit Caption
                    </a>
                    
                    <button onclick="copyImageUrl()" 
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white text-center py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-link mr-2"></i>Copy Image URL
                    </button>
                </div>

                <!-- Delete Section -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <form method="POST" action="{{ route('user.gallery.destroy', $gallery) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white text-center py-3 px-4 rounded-lg transition duration-200"
                                onclick="return confirm('Are you sure you want to delete this photo? This action cannot be undone.')">
                            <i class="fas fa-trash mr-2"></i>Delete Photo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Modal -->
<div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-full max-h-full">
        <button onclick="closeFullscreen()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <i class="fas fa-times text-3xl"></i>
        </button>
        <img src="{{ $gallery->file_url }}" alt="{{ $gallery->caption ?: $gallery->original_name }}" 
             class="max-w-full max-h-full object-contain">
        
        <!-- Navigation buttons if you want to add prev/next functionality -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded">
            <p class="text-center">{{ $gallery->original_name }}</p>
        </div>
    </div>
</div>

<script>
function openFullscreen() {
    document.getElementById('fullscreenModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFullscreen() {
    document.getElementById('fullscreenModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function copyImageUrl() {
    const imageUrl = '{{ $gallery->file_url }}';
    const fullUrl = window.location.origin + imageUrl;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(fullUrl).then(() => {
            showNotification('Image URL copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyTextToClipboard(fullUrl);
        });
    } else {
        fallbackCopyTextToClipboard(fullUrl);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Image URL copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy URL', 'error');
    }
    
    document.body.removeChild(textArea);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Close fullscreen on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFullscreen();
    }
});

// Close fullscreen on background click
document.getElementById('fullscreenModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFullscreen();
    }
});
</script>
</x-app-layout>
