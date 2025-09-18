<x-app-layout> 
    <script src="{{ asset('js/template.js') }}"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Template Baru') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('templates.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Nama Template -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Masukkan nama template yang unik..."
                               required>
                        <p class="text-xs text-gray-500 mt-1">Nama template harus unik dan tidak boleh sama dengan template lain</p>
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" 
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Cover Image -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                        <input type="file" 
                               name="cover_image" 
                               accept="image/*"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <small class="text-gray-500">Format: JPG, PNG, GIF. Max: 2MB</small>
                        @error('cover_image')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- HTML Content -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konten HTML Template</label>
                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b">
                                <div class="flex space-x-2 flex-wrap">
                                    <button type="button" onclick="insertVariable('[bride_name]')" class="text-xs bg-pink-100 text-pink-800 px-2 py-1 rounded mb-1">Nama Mempelai Wanita</button>
                                    <button type="button" onclick="insertVariable('[groom_name]')" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded mb-1">Nama Mempelai Pria</button>
                                    <button type="button" onclick="insertVariable('[wedding_date]')" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded mb-1">Tanggal Pernikahan</button>
                                    <button type="button" onclick="insertVariable('[wedding_time]')" class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded mb-1">Waktu Pernikahan</button>
                                    <button type="button" onclick="insertVariable('[venue]')" class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded mb-1">Tempat Acara</button>
                                    <button type="button" onclick="insertVariable('[location]')" class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded mb-1">Alamat Lengkap</button>
                                    <button type="button" onclick="insertVariable('[additional_notes]')" class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded mb-1">Catatan Tambahan</button>
                                </div>
                            </div>
                            <textarea name="html_content" 
                                      id="html_content"
                                      rows="20"
                                      class="w-full p-4 border-0 focus:ring-0 font-mono text-sm"
                                      placeholder="Masukkan HTML template di sini..."> </textarea>
                        </div>
                        @error('html_content')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Preview Section -->
                    <div class="mb-6">
                        <button type="button" 
                                onclick="previewTemplate()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Preview Template
                        </button>
                    </div>

                    <!-- CSS Variables (Opsional) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">CSS Variables (Opsional)</label>
                        <textarea name="css_variables" 
                                  rows="5"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                  placeholder='{"primary_color": "#667eea", "secondary_color": "#764ba2", "font_family": "Georgia, serif"}'>{{ old('css_variables') }}</textarea>
                        <small class="text-gray-500">Format JSON untuk variabel CSS yang bisa disesuaikan</small>
                        @error('css_variables')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('templates.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Kembali
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                             Simpan Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Preview Template</h3>
                    <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
                <div class="border border-gray-300 rounded-md">
                    <iframe id="previewFrame" class="w-full h-96 rounded-md"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>