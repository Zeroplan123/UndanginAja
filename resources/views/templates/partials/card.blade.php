<div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
    @if($template->preview_image)
        <img src="{{ asset('storage/' . $template->preview_image) }}" alt="{{ $template->name }}" class="w-full h-48 object-cover">
    @elseif($template->cover_image)
        <img src="{{ $template->cover_image_url }}" alt="{{ $template->name }}" class="w-full h-48 object-cover">
    @else
        <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
            <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-gray-500 text-sm">{{ $template->name }}</span>
            </div>
        </div>
    @endif
    
    <div class="p-4">
        <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ $template->name }}</h3>
        
        @if($template->description)
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $template->description }}</p>
        @endif
        
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                Template
            </span>
            
            <div class="flex space-x-2">
                @if(isset($showPreview) && $showPreview)
                    <a href="{{ route('templates.preview', $template) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Preview
                    </a>
                @endif
                
                @if(isset($showSelect) && $showSelect)
                    <button type="button" 
                            onclick="selectTemplate({{ $template->id }})"
                            class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded">
                        Pilih
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
