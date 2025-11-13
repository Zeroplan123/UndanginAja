<x-app-layout>
    <!-- Di layout user -->
<script src="{{ asset('js/broadcast-notifications.js') }}"></script>
<meta name="user-role" content="{{ auth()->user()->role ?? 'guest' }}">
    <x-slot name="title">Dashboard</x-slot>
    <div class="py-8 bg-white">
            <!-- Template Selection Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 border-b border-gray-200">
                    <h4 class="text-xl font-semibold text-gray-800">Pilih Template Undangan</h4>
                    <p class="text-gray-600 mt-1">Pilih template yang sesuai dengan tema pernikahan Anda</p>
                </div>
                
                @if($templates->count() > 0)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($templates as $template)
                                <div class="template-card bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                                    <!-- Template Preview -->
                                    <div class="relative h-48 bg-gradient-to-br from-pink-100 to-purple-100 overflow-hidden">
                                        @if($template->cover_image)
                                            <img src="{{ $template->cover_image_url }}" 
                                                 alt="{{ $template->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full">
                                                <div class="text-center">
                                                    <svg class="w-16 h-16 mx-auto text-pink-300 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <p class="text-pink-400 font-medium">{{ $template->name }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                    </div>
                                    
                                    <!-- Template Info -->
                                    <div class="p-4">
                                        <h5 class="font-semibold text-lg text-gray-800 mb-2">{{ $template->name }}</h5>
                                        @if($template->description)
                                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $template->description }}</p>
                                        @endif
                                        
                                        <div class="flex space-x-2">
                                            <a href="{{ route('templates.preview', $template->id) }}" 
                                               class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                                Preview
                                            </a>
                                            <a href="{{ route('user.create-invitation', $template) }}" 
                                               class="flex-1 text-center bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                                Gunakan Template
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Template</h3>
                        <p class="text-gray-600">Template undangan akan muncul di sini setelah admin menambahkannya.</p>
                    </div>
                @endif
            </div>
</x-app-layout>