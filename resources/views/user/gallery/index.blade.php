<x-app-layout>
<x-slot name="title">Gallery</x-slot>

<!-- LightGallery Styles -->
<link rel="stylesheet" href="{{ asset('css/lightgallery-custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/instagram-gallery.css') }}">

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Gallery</h1>
                    <p class="text-gray-600 mt-1">{{ $galleryItems->total() }} photos</p>
                </div>
                <a href="{{ route('user.gallery.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Upload Photos
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Storage Usage -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Storage Usage</h3>
                <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    {{ number_format($totalSize / 1024 / 1024, 2) }} MB / {{ number_format($maxSize / 1024 / 1024, 0) }} MB
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ min($usagePercentage, 100) }}%"></div>
            </div>
            @if($usagePercentage > 80)
                <p class="text-sm text-amber-600 mt-3 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Storage is {{ round($usagePercentage, 1) }}% full. Consider removing some photos.
                </p>
            @endif
        </div>

        <!-- Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('user.gallery.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search photos..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Search
                </button>
                <a href="{{ route('user.gallery.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Clear
                </a>
            </form>
        </div>

        <!-- Bulk Actions -->
        @if($galleryItems->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 hidden" id="bulk-actions">
                <div class="flex justify-between items-center">
                    <span id="selected-count" class="text-gray-700 font-medium">0 photos selected</span>
                    <button type="button" onclick="bulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete Selected
                    </button>
                </div>
            </div>
        @endif

        <!-- Modern LightGallery Grid -->
        @if($galleryItems->count() > 0)
            <div class="gallery-container" id="lightgallery">
                <!-- Perfect Instagram Grid Layout with LightGallery Integration -->
                <div class="instagram-grid">
                    @foreach($galleryItems as $index => $item)
                        <div class="gallery-item gallery-item-enhanced group gallery-item-link" 
                             data-src="{{ asset('storage/' . $item->file_path) }}"
                             data-sub-html="<h4>{{ $item->original_name }}</h4><p>{{ $item->caption ?: 'Uploaded on ' . $item->created_at->format('M d, Y') }}</p>">
                            
                            <!-- Selection Checkbox -->
                            <input type="checkbox" value="{{ $item->id }}" 
                                   class="gallery-checkbox absolute top-2 left-2 z-30 w-4 h-4 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200" 
                                   onchange="updateBulkActions()" onclick="event.stopPropagation()">
                            
                            <!-- Photo Container with Perfect Square Aspect Ratio -->
                            <div class="photo-container">
                                <img src="{{ asset('storage/' . $item->file_path) }}" 
                                     alt="{{ $item->caption ?: $item->original_name }}"
                                     loading="lazy"
                                     onerror="console.log('Image failed to load:', this.src); this.src='{{ asset('images/placeholder.jpg') }}'">
                                
                                <!-- Subtle Professional Hover Overlay -->
                                <div class="photo-overlay absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                                    <div class="overlay-content opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-90 group-hover:scale-100 text-center">
                                        <button class="view-photo-btn bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center shadow-lg">
                                            <i class="fas fa-eye mr-2"></i>
                                            View Photo
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- File Size Badge -->
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    {{ $item->formatted_file_size }}
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-all duration-300 flex space-x-1">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- Pagination -->
        @if($galleryItems->hasPages())
            <div class="mt-6">
                {{ $galleryItems->appends(request()->query())->links() }}
            </div>
        @endif
        @else
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">No Photos Yet</h3>
                    <p class="text-gray-500 mb-6">Start building your gallery by uploading your first photo</p>
                    <a href="{{ route('user.gallery.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Upload First Photo
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- LightGallery will handle the lightbox modal -->

<!-- LightGallery JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/fullscreen/lg-fullscreen.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/autoplay/lg-autoplay.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/share/lg-share.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/rotate/lg-rotate.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const galleryElement = document.getElementById('lightgallery');
    
    if (galleryElement) {
        // Initialize LightGallery with all features
        const gallery = lightGallery(galleryElement, {
            // Core settings
            speed: 500,
            
            // Plugins
            plugins: [lgThumbnail, lgZoom, lgFullscreen, lgAutoplay, lgShare, lgRotate],
            
            // Thumbnail settings
            thumbnail: true,
            thumbWidth: 100,
            thumbHeight: 80,
            thumbMargin: 5,
            animateThumb: true,
            currentPagerPosition: 'middle',
            
            // Zoom settings
            zoom: true,
            scale: 1,
            enableZoomAfter: 300,
            actualSize: true,
            showZoomInOutIcons: true,
            
            // Fullscreen
            fullScreen: true,
            
            // Autoplay
            autoplay: false,
            pause: 3000,
            progressBar: true,
            
            // Share
            share: true,
            facebook: true,
            facebookDropdownText: 'Facebook',
            twitter: true,
            twitterDropdownText: 'Twitter',
            pinterest: true,
            pinterestDropdownText: 'Pinterest',
            
            // Rotate
            rotate: true,
            flipHorizontal: true,
            flipVertical: true,
            
            // Mobile settings
            mobileSettings: {
                controls: true,
                showCloseIcon: true,
                download: true,
                rotate: true
            },
            
            // Swipe settings
            swipeThreshold: 50,
            enableSwipe: true,
            enableDrag: true,
            
            // Animation settings
            mode: 'lg-slide',
            cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)',
            
            // Controls
            controls: true,
            download: true,
            counter: true,
            closable: true,
            showMaximizeIcon: true,
            appendSubHtmlTo: '.lg-sub-html',
            subHtmlSelectorRelative: true,
            
            // Preload settings
            preload: 2,
            showAfterLoad: true,
            
            // Custom selectors
            selector: '.gallery-item-link',
            
            // Event callbacks
            onBeforeOpen: function() {
                console.log('Gallery opening...');
            },
            
            onAfterOpen: function() {
                console.log('Gallery opened');
            },
            
            onBeforeClose: function() {
                console.log('Gallery closing...');
            },
            
            onAfterClose: function() {
                console.log('Gallery closed');
            }
        });
        
        // Add custom keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (gallery.lgOpened) {
                switch(e.key) {
                    case 'z':
                    case 'Z':
                        e.preventDefault();
                        // Toggle zoom
                        break;
                    case 'r':
                    case 'R':
                        e.preventDefault();
                        // Rotate image
                        break;
                    case 'f':
                    case 'F':
                        e.preventDefault();
                        // Toggle fullscreen
                        break;
                }
            }
        });
    }
});

// Bulk actions functionality
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.gallery-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (checkboxes.length > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = `${checkboxes.length} photo${checkboxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActions.classList.add('hidden');
    }
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.gallery-checkbox:checked');
    if (checkboxes.length === 0) return;
    
    if (!confirm(`Delete ${checkboxes.length} selected photo${checkboxes.length > 1 ? 's' : ''}?`)) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("user.gallery.bulk-delete") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'gallery_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>
</x-app-layout>
