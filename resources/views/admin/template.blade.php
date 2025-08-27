<x-app-layout>
    @vite('public/css/template.css')
    <x-slot name="title"> Template </x-slot>
    <div class="container mx-auto p-6">
        <div class="block lg:flex justify-between items-center mb-6">
            <h1 class="text-3xl sm:text-xl md:text-3xl lg:text-4xl font-bold text-gray-800">Daftar Template Undangan</h1>
            <a href="{{ route('templates.create') }}" 
               class=" bg-pink-500 hover:bg-pink-700 text-white px-4 py-2 sm:px-6 sm:py-3 text-sm sm:text-base lg:text-lg rounded-lg shadow-lg transition duration-200 flex items-center justify-center w-full sm:w-auto mt-4  sm:mt-0">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Template
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Grid Layout untuk Template --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($templates as $template)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-200">
                    {{-- Cover Image --}}
                    <div class="h-48 bg-gray-200 overflow-hidden">
                        @if($template->cover_image)
                            <img src="{{ asset('storage/template_covers/'.$template->cover_image) }}" 
                                 alt="{{ $template->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-purple-500 text-white">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Template Info --}}
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $template->name }}</h3>
                        
                        @if($template->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $template->description }}</p>
                        @endif

                        <div class="text-xs text-gray-500 mb-4">
                            <span>Dibuat: {{ $template->created_at->format('d M Y') }}</span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-2">
                            {{-- Preview Button --}}
                            <a href="{{ route('admin.templates.preview', $template->id) }}" 
                               target="_blank"
                               class="flex-1 bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-2 rounded text-center transition duration-200">
                                 Preview
                            </a>

                            {{-- Edit Button --}}
                            <a href="{{ route('templates.edit', $template->id) }}" 
                               class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-2 rounded text-center transition duration-200">
                                 Edit
                            </a>

                            {{-- Delete Button --}}
                            <form action="{{ route('templates.destroy', $template->id) }}" 
                                  method="POST" 
                                  class="flex-1"
                                  onsubmit="return confirm('Yakin hapus template \"{{ $template->name }}\"?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded transition duration-200">
                                     Hapus
                                </button>
                            </form>
                        </div>

                        {{-- Additional Info --}}
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Slug: {{ $template->slug }}</span>
                                <span>{{ $template->invitations_count}} digunakan</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada template</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat template undangan pertama Anda.</p>
                        <div class="mt-6">
                            <a href="{{ route('templates.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Template
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($templates->hasPages())
            <div class="mt-8">
                {{ $templates->links() }}
            </div>
        @endif
    </div>


</x-app-layout>