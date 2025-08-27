<x-app-layout>
        <script src="{{ asset('js/create_invitation.js') }}"></script>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard') }}" 
               class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Buat Undangan - {{ $template->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Form Section -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Data Pernikahan</h3>
                        <p class="text-gray-600 text-sm mt-1">Isi data pernikahan untuk membuat undangan Anda</p>
                    </div>
                    
                    <form action="{{ route('user.store-invitation', $template) }}" method="POST" enctype="multipart/form-data" class="p-6">
                        @csrf
                        
                        <!-- Nama Mempelai -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="groom_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Mempelai Pria *
                                </label>
                                <input type="text" 
                                       name="groom_name" 
                                       id="groom_name" 
                                       value="{{ old('groom_name') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 @error('groom_name') border-red-500 @enderror"
                                       placeholder="Masukkan nama mempelai pria">
                                @error('groom_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="bride_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Mempelai Wanita *
                                </label>
                                <input type="text" 
                                       name="bride_name" 
                                       id="bride_name" 
                                       value="{{ old('bride_name') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 @error('bride_name') border-red-500 @enderror"
                                       placeholder="Masukkan nama mempelai wanita">
                                @error('bride_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal dan Waktu -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="wedding_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Pernikahan *
                                </label>
                                <input type="date" 
                                       name="wedding_date" 
                                       id="wedding_date" 
                                       value="{{ old('wedding_date') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 @error('wedding_date') border-red-500 @enderror">
                                @error('wedding_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="wedding_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Waktu Acara *
                                </label>
                                <input type="text" 
                                       name="wedding_time" 
                                       id="wedding_time" 
                                       value="{{ old('wedding_time') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 @error('wedding_time') border-red-500 @enderror"
                                       placeholder="Contoh: 10:00 WIB">
                                @error('wedding_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Lokasi -->
                        <div class="mb-6">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Lokasi Acara *
                            </label>
                            <textarea name="location" 
                                      id="location" 
                                      rows="3" 
                                      required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 @error('location') border-red-500 @enderror"
                                      placeholder="Masukkan alamat lengkap lokasi pernikahan">{{ old('location') }}</textarea>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- <!-- Catatan Tambahan -->
                        <div class="mb-6">
                            <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Tambahan (Opsional)
                            </label>
                            <textarea name="additional_notes" 
                                      id="additional_notes" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 @error('additional_notes') border-red-500 @enderror"
                                      placeholder="Pesan khusus, dresscode, atau informasi tambahan lainnya">{{ old('additional_notes') }}</textarea>
                            @error('additional_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> --}}

                     

                        <!-- Submit Button -->
                        <div class="flex space-x-4">
                            <a href="{{ route('dashboard') }}" 
                               class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Kembali
                            </a>
                            <button type="submit" 
                                    class="flex-1 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                Buat Undangan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Template Preview Section -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Preview Template</h3>
                        <p class="text-gray-600 text-sm mt-1">{{ $template->name }}</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            @if($template->cover_image)
                                <img src="{{ asset('storage/template_covers/' . $template->cover_image) }}" 
                                     alt="{{ $template->name }}"
                                     class="w-full h-64 object-cover">
                            @else
                                <div class="h-64 bg-gradient-to-br from-pink-100 to-purple-100 flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-16 h-16 mx-auto text-pink-300 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-pink-400 font-medium">{{ $template->name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        @if($template->description)
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-medium text-gray-800 mb-2">Deskripsi Template</h4>
                                <p class="text-gray-600 text-sm">{{ $template->description }}</p>
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('templates.preview', $template) }}" 
                               target="_blank"
                               class="inline-flex items-center space-x-2 text-pink-600 hover:text-pink-700 font-medium text-sm transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                <span>Preview Template Lengkap</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>