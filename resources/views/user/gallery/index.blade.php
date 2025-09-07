<x-app-layout>
<x-slot name="title">Memory</x-slot>
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
                    <i class="fas fa-heart text-rose-400 mr-3"></i>
                    My Romantic Gallery
                    <i class="fas fa-heart text-rose-400 ml-3"></i>
                </h1>
                <p class="text-rose-600 text-lg italic font-serif">A treasure chest of beautiful memories</p>
            </div>
            <div class="flex justify-center">
                <a href="{{ route('user.gallery.create') }}" class="romantic-upload-btn group">
                    <i class="fas fa-camera mr-2 group-hover:animate-pulse"></i>
                    Add New Memories
                    <i class="fas fa-sparkles ml-2 group-hover:animate-spin"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">

        @if(session('success'))
            <div class="romantic-success-alert mb-6">
                <i class="fas fa-heart text-rose-500 mr-2"></i>
                {{ session('success') }}
                <i class="fas fa-sparkles text-rose-400 ml-2"></i>
            </div>
        @endif

        @if(session('error'))
            <div class="romantic-error-alert mb-6">
                <i class="fas fa-heart-broken text-red-500 mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Romantic Storage Usage -->
        <div class="romantic-storage-card mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-rose-800 font-serif flex items-center">
                    <i class="fas fa-gem text-rose-500 mr-2"></i>
                    Memory Vault
                </h3>
                <span class="text-sm text-rose-600 font-medium bg-rose-50 px-3 py-1 rounded-full">
                    {{ number_format($totalSize / 1024 / 1024, 2) }} MB / {{ number_format($maxSize / 1024 / 1024, 0) }} MB
                </span>
            </div>
            <div class="romantic-progress-container">
                <div class="romantic-progress-bar" style="width: {{ min($usagePercentage, 100) }}%"></div>
            </div>
            @if($usagePercentage > 80)
                <p class="text-sm text-rose-600 mt-3 italic font-serif flex items-center">
                    <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                    Your memory vault is {{ round($usagePercentage, 1) }}% full. Consider organizing your precious memories.
                </p>
            @endif
        </div>

        <!-- Romantic Search -->
        <div class="romantic-search-card mb-8">
            <form method="GET" action="{{ route('user.gallery.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-rose-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search your precious memories..." 
                               class="romantic-search-input">
                    </div>
                </div>
                <button type="submit" class="romantic-search-btn">
                    <i class="fas fa-heart mr-2"></i>Find Memories
                </button>
                <a href="{{ route('user.gallery.index') }}" class="romantic-clear-btn">
                    <i class="fas fa-refresh mr-2"></i>Show All
                </a>
            </form>
        </div>

        <!-- Romantic Bulk Actions -->
        @if($galleryItems->count() > 0)
            <div class="romantic-bulk-card mb-8" id="bulk-actions" style="display: none;">
                <div class="flex justify-between items-center">
                    <span id="selected-count" class="text-rose-700 font-serif italic">0 memories selected</span>
                    <button type="button" onclick="bulkDelete()" class="romantic-delete-btn">
                        <i class="fas fa-heart-broken mr-2"></i>Remove Selected
                    </button>
                </div>
            </div>
        @endif

        <!-- Romantic Gallery Grid -->
        @if($galleryItems->count() > 0)
            <div class="romantic-gallery-container">
                <div class="romantic-gallery-grid">
                    @foreach($galleryItems as $item)
                        <div class="romantic-photo-frame group">
                            <!-- Photo Frame Shadow -->
                            <div class="romantic-frame-shadow"></div>
                            
                            <!-- Main Photo Frame -->
                            <div class="romantic-frame-main">
                                <div class="romantic-frame-inner">
                                    <!-- Selection Checkbox -->
                                    <input type="checkbox" value="{{ $item->id }}" 
                                           class="gallery-checkbox romantic-checkbox" 
                                           onchange="updateBulkActions()">
                                    
                                    <!-- Photo -->
                                    <div class="romantic-photo-container">
                                        <img src="{{ $item->thumbnail_url ?: $item->file_url }}" 
                                             alt="{{ $item->caption ?: $item->original_name }}"
                                             class="romantic-photo"
                                             onclick="window.location.href='{{ route('user.gallery.show', $item) }}'">
                                        
                                        <!-- Photo Overlay -->
                                        <div class="romantic-photo-overlay" onclick="window.location.href='{{ route('user.gallery.show', $item) }}'">
                                            <div class="romantic-overlay-content">
                                                <i class="fas fa-heart text-white text-2xl mb-2 animate-pulse"></i>
                                                <p class="text-white text-sm font-serif">View Memory</p>
                                            </div>
                                        </div>
                                        
                                        <!-- File Size Badge -->
                                        <div class="romantic-size-badge">
                                            {{ $item->formatted_file_size }}
                                        </div>
                                    </div>
                                    
                                    <!-- Photo Caption/Title -->
                                    <div class="romantic-photo-caption">
                                        <h3 class="romantic-photo-title">{{ Str::limit($item->original_name, 20) }}</h3>
                                        @if($item->caption)
                                            <p class="romantic-photo-description">{{ Str::limit($item->caption, 40) }}</p>
                                        @endif
                                        <div class="romantic-photo-meta">
                                            <span class="romantic-date">{{ $item->created_at->format('M d, Y') }}</span>
                                            @if($item->metadata && isset($item->metadata['width']))
                                                <span class="romantic-dimensions">{{ $item->metadata['width'] }}×{{ $item->metadata['height'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="romantic-photo-actions">
                                        <a href="{{ route('user.gallery.edit', $item) }}" 
                                           class="romantic-action-btn romantic-edit-btn" title="Edit Memory">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('user.gallery.download', $item) }}" 
                                           class="romantic-action-btn romantic-download-btn" title="Save Memory">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form method="POST" action="{{ route('user.gallery.destroy', $item) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="romantic-action-btn romantic-remove-btn"
                                                    onclick="return confirm('Remove this precious memory?')" 
                                                    title="Remove Memory">
                                                <i class="fas fa-heart-broken"></i>
                                            </button>
                                        </form>
                                    </div>
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
            <div class="romantic-empty-gallery">
                <div class="romantic-empty-content">
                    <div class="romantic-empty-icon">
                        <i class="fas fa-heart text-6xl text-rose-300 mb-4 animate-pulse"></i>
                        <i class="fas fa-camera text-4xl text-rose-200 absolute transform -translate-x-2 translate-y-2"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-rose-700 mb-3 font-serif">Your Memory Gallery Awaits</h3>
                    <p class="text-rose-500 mb-6 italic font-serif text-lg">Every love story deserves to be captured and cherished forever</p>
                    <a href="{{ route('user.gallery.create') }}" class="romantic-first-upload-btn">
                        <i class="fas fa-heart mr-2"></i>Create Your First Memory
                        <i class="fas fa-sparkles ml-2"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Romantic Image Modal -->
<div id="imageModal" class="romantic-modal">
    <div class="romantic-modal-backdrop"></div>
    <div class="romantic-modal-container">
        <div class="romantic-modal-content">
            <!-- Close Button -->
            <button onclick="closeModal()" class="romantic-modal-close">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Image Container -->
            <div class="romantic-modal-image-container">
                <img id="modalImage" src="" alt="" class="romantic-modal-image">
            </div>
            
            <!-- Caption and Actions -->
            <div class="romantic-modal-info">
                <div class="romantic-modal-caption-container">
                    <h3 id="modalCaption" class="romantic-modal-caption"></h3>
                </div>
                <div class="romantic-modal-actions">
                    <a id="modalEditLink" href="" class="romantic-modal-btn romantic-modal-edit-btn">
                        <i class="fas fa-edit mr-2"></i>Edit Memory
                    </a>
                    <a id="modalDownloadLink" href="" class="romantic-modal-btn romantic-modal-download-btn">
                        <i class="fas fa-download mr-2"></i>Save Memory
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(imageUrl, caption, itemId) {
    console.log('Opening modal with:', imageUrl, caption, itemId); // Debug log
    
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    const modalEditLink = document.getElementById('modalEditLink');
    const modalDownloadLink = document.getElementById('modalDownloadLink');
    
    if (!modal || !modalImage || !modalCaption || !modalEditLink || !modalDownloadLink) {
        console.error('Modal elements not found');
        return;
    }
    
    modalImage.src = imageUrl;
    modalCaption.textContent = caption || 'Untitled Memory';
    modalEditLink.href = `/user/gallery/${itemId}/edit`;
    modalDownloadLink.href = `/user/gallery/${itemId}/download`;
    
    // Show modal with romantic animation
    modal.style.display = 'flex';
    modal.classList.remove('romantic-modal-hidden');
    modal.classList.add('romantic-modal-show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    if (!modal) return;
    
    modal.classList.remove('romantic-modal-show');
    modal.classList.add('romantic-modal-hidden');
    
    // Hide modal after animation
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.gallery-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = `${checkboxes.length} photo${checkboxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActions.style.display = 'none';
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

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Close modal on background click
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
</x-app-layout>
