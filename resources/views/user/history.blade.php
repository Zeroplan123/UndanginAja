<x-app-layout>
     <script src="{{ asset('js/history.js') }}"></script>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            <!-- Header Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl mb-8">
    <div class="p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Riwayat Undangan Anda</h3>
                <p class="text-gray-600 mt-2">Kelola dan lihat semua undangan yang pernah Anda buat</p>
            </div>
            <a href="{{ route('invitations.create') }}" 
               class="block w-full sm:w-auto sm:inline-block bg-gradient-to-r from-pink-500 to-purple-600 
                      hover:from-pink-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium 
                      transition-all duration-300 shadow-lg hover:shadow-xl text-center">
                + Buat Undangan Baru
            </a>
        </div>
    </div>
</div>

             

            <!-- Tab Content: My Invitations -->
            <div id="content-invitations" class="tab-content">
                @if($userInvitations->count() > 0)
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($userInvitations as $invitation)
                                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-pink-300 hover:shadow-lg transition-all duration-300 group">
                                        <!-- Template Badge -->
                                        <div class="flex justify-between items-start mb-4">
                                            <span class="text-xs font-medium text-pink-600 bg-pink-100 px-3 py-1 rounded-full">
                                                {{ $invitation->template->name }}
                                            </span>
                                            <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">
                                                Aktif
                                            </span>
                                        </div>

                                        <!-- Couple Names -->
                                        <div class="mb-4">
                                            <h6 class="font-bold text-lg text-gray-800 group-hover:text-pink-600 transition-colors">
                                                {{ $invitation->groom_name }} & {{ $invitation->bride_name }}
                                            </h6>
                                        </div>

                                        <!-- Wedding Details -->
                                        <div class="space-y-2 mb-4">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ date('d M Y', strtotime($invitation->wedding_date)) }}
                                            </div>
                                            
                                            @if($invitation->wedding_time)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $invitation->wedding_time }}
                                            </div>
                                            @endif
                                            
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ Str::limit($invitation->venue ?? $invitation->location, 30) }}
                                            </div>
                                        </div>

                                        <!-- Created Date -->
                                        <div class="text-xs text-gray-500 mb-4">
                                            Dibuat {{ $invitation->created_at->diffForHumans() }}
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex space-x-2 mb-3">
                                            <a href="{{ route('user.invitation.preview', $invitation->slug) }}" 
                                               class="flex-1 text-center bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                                                Lihat
                                            </a>
                                            <a href="{{ route('invitations.edit', $invitation) }}" 
                                               class="flex-1 text-center bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                                                Edit
                                            </a>
                                        </div>

                                        <!-- Additional Actions -->
                                        <div class="flex space-x-2 mb-2">
                                            <button class="flex-1 text-center text-gray-600 hover:text-pink-600 text-xs font-medium transition-colors duration-200"
                                                    onclick="copyInvitationLink('{{ route('user.invitation.preview', $invitation->slug) }}')">
                                                📋 Salin Link
                                            </button>
                                            <a href="{{ route('user.export-pdf', $invitation->slug) }}" 
                                               class="flex-1 text-center text-gray-600 hover:text-blue-600 text-xs font-medium transition-colors duration-200">
                                                📄 Download PDF
                                            </a>
                                        </div>
                                        
                                        <!-- Communication Button -->
                                        <div class="mt-2">
                                            <a href="{{ route('communication.show', $invitation->slug) }}" 
                                               class="w-full text-center bg-green-500 text-white px-3 py-2 rounded-lg text-xs font-medium transition-all duration-300 block">
                                                 Kirim via Email & WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State untuk Invitations -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                        <div class="p-12 text-center">
                            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Undangan</h3>
                            <p class="text-gray-600 mb-6">Anda belum membuat undangan apapun. Mulai buat undangan pertama Anda sekarang!</p>
                            <a href="{{ route('invitations.create') }}" 
                               class="inline-block bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-300">
                                Buat Undangan Pertama
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tab Content: Available Templates -->
            <div id="content-templates" class="tab-content hidden">
                @if($templates->count() > 0)
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($templates as $template)
                                    <div class="template-card bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                                        <!-- Template Preview -->
                                        <div class="relative h-48 bg-gradient-to-br from-pink-100 to-purple-100 overflow-hidden">
                                            @if($template->cover_image)
                                                <img src="{{ asset('storage/template_covers/' . $template->cover_image) }}" 
                                                     alt="{{ $template->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <div class="text-center text-gray-400">
                                                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <p class="text-sm">{{ $template->name }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Template Category Badge -->
                                            <div class="absolute top-3 left-3">
                                                <span class="bg-white/90 backdrop-blur-sm text-gray-700 px-2 py-1 rounded-lg text-xs font-medium">
                                                    {{ $template->category ?? 'Wedding' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Template Info -->
                                        <div class="p-4">
                                            <div class="mb-3">
                                                <h5 class="font-bold text-lg text-gray-800 mb-1">{{ $template->name }}</h5>
                                                <p class="text-gray-600 text-sm">{{ Str::limit($template->description ?? 'Template undangan yang elegan dan modern', 100) }}</p>
                                            </div>
                                            
                                            <!-- Template Features -->
                                            <div class="flex flex-wrap gap-1 mb-4">
                                                <span class="bg-pink-100 text-pink-700 px-2 py-1 rounded-full text-xs">Modern</span>
                                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-xs">Elegant</span>
                                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs">Responsive</span>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="flex space-x-2">
                                                <a href="{{ route('templates.preview', $template->id) }}" 
                                                   class="flex-1 text-center border-2 border-pink-500 text-pink-600 hover:bg-pink-500 hover:text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                                                    Preview
                                                </a>
                                                <a href="{{ route('user.create-invitation', $template) }}" 
                                                   class="flex-1 text-center bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300">
                                                    Pilih
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State untuk Templates -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                        <div class="p-12 text-center">
                            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Template</h3>
                            <p class="text-gray-600">Template undangan belum tersedia saat ini.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>