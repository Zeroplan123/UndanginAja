<x-app-layout>
<x-slot name="title">Edit Memory</x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Photo</h1>
        <div class="flex space-x-3">
            <a href="{{ route('user.gallery.show', $gallery) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-eye mr-2"></i>View
            </a>
            <a href="{{ route('user.gallery.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Gallery
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Photo Preview -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Photo Preview</h3>
            <div class="relative">
                <img src="{{ $gallery->file_url }}" alt="{{ $gallery->caption ?: $gallery->original_name }}" 
                     class="w-full rounded-lg shadow-sm">
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                    {{ $gallery->formatted_file_size }}
                </div>
            </div>
            
            <!-- Photo Details -->
            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span class="font-medium">Filename:</span>
                    <span>{{ $gallery->original_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">File Size:</span>
                    <span>{{ $gallery->formatted_file_size }}</span>
                </div>
                @if($gallery->metadata && isset($gallery->metadata['width']))
                    <div class="flex justify-between">
                        <span class="font-medium">Dimensions:</span>
                        <span>{{ $gallery->metadata['width'] }} × {{ $gallery->metadata['height'] }} pixels</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="font-medium">Uploaded:</span>
                    <span>{{ $gallery->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">File Type:</span>
                    <span>{{ strtoupper(pathinfo($gallery->original_name, PATHINFO_EXTENSION)) }}</span>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Caption</h3>
            
            <form method="POST" action="{{ route('user.gallery.update', $gallery) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <label for="caption" class="block text-sm font-medium text-gray-700 mb-2">
                        Caption
                    </label>
                    <textarea id="caption" name="caption" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Add a caption to describe your photo...">{{ old('caption', $gallery->caption) }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">Maximum 500 characters</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between">
                    <div class="flex space-x-3">
                        <a href="{{ route('user.gallery.download', $gallery) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-download mr-2"></i>Download
                        </a>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('user.gallery.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>

            <!-- Delete Section -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-md font-semibold text-red-600 mb-2">Danger Zone</h4>
                <p class="text-sm text-gray-600 mb-4">
                    Once you delete this photo, it cannot be recovered. Please be certain.
                </p>
                <form method="POST" action="{{ route('user.gallery.destroy', $gallery) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200"
                            onclick="return confirm('Are you sure you want to delete this photo? This action cannot be undone.')">
                        <i class="fas fa-trash mr-2"></i>Delete Photo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter for caption
document.getElementById('caption').addEventListener('input', function() {
    const maxLength = 500;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    // You can add a character counter display here if needed
    if (currentLength > maxLength) {
        this.value = this.value.substring(0, maxLength);
    }
});
</script>
</x-app-layout>