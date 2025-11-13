<x-app-layout> 
<script src="{{ asset('js/template.js') }}"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template: ') . $template->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('templates.update', $template->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden source_type field -->
                    <input type="hidden" name="source_type" value="manual">

                    <!-- Nama Template -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $template->name) }}"
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
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $template->description) }}</textarea>
                        @error('description')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Cover Image -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                        
                        @if($template->cover_image)
                            <div class="mb-2">
                                <img src="{{ $template->cover_image_url }}" 
                                     alt="Current cover" 
                                     class="h-32 w-48 object-cover rounded border">
                                <p class="text-sm text-gray-600 mt-1">Cover image saat ini</p>
                            </div>
                        @endif
                        
                        <input type="file" 
                               name="cover_image" 
                               accept="image/*"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <small class="text-gray-500">Format: JPG, PNG, GIF. Max: 2MB. Kosongkan jika tidak ingin mengubah cover.</small>
                        @error('cover_image')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- HTML Content -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Konten HTML Template
                            <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- Info untuk Edit -->
                        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-start">
                                <i class="fas fa-code text-blue-500 mt-1 mr-2"></i>
                                <div class="text-sm text-blue-700">
                                    <strong>Edit HTML Template:</strong>
                                    <ul class="mt-1 list-disc list-inside space-y-1">
                                        <li>Edit HTML code langsung di textarea</li>
                                        <li>Gunakan variable seperti [bride_name], [groom_name], dll</li>
                                        <li>HTML akan disimpan dan ditampilkan sesuai yang Anda ketik</li>
                                        <li>Gunakan Preview untuk melihat hasil render</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <!-- Variable Buttons -->
                            <div class="bg-gray-50 px-4 py-2 border-b">
                                <div class="flex space-x-2 flex-wrap">
                                    <button type="button" onclick="insertVariable('[bride_name]')" class="text-xs bg-pink-100 text-pink-800 px-2 py-1 rounded mb-1 hover:bg-pink-200 transition-colors">Nama Mempelai Wanita</button>
                                    <button type="button" onclick="insertVariable('[groom_name]')" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded mb-1 hover:bg-blue-200 transition-colors">Nama Mempelai Pria</button>
                                    <button type="button" onclick="insertVariable('[wedding_date]')" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded mb-1 hover:bg-green-200 transition-colors">Tanggal Pernikahan</button>
                                    <button type="button" onclick="insertVariable('[wedding_time]')" class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded mb-1 hover:bg-yellow-200 transition-colors">Waktu Pernikahan</button>
                                    <button type="button" onclick="insertVariable('[venue]')" class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded mb-1 hover:bg-purple-200 transition-colors">Tempat Acara</button>
                                    <button type="button" onclick="insertVariable('[location]')" class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded mb-1 hover:bg-indigo-200 transition-colors">Alamat Lengkap</button>
                                    <button type="button" onclick="insertVariable('[additional_notes]')" class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded mb-1 hover:bg-gray-200 transition-colors">Catatan Tambahan</button>
                                </div>
                            </div>
                            
                            <!-- HTML Textarea -->
                            <textarea name="html_content" 
                                      id="html_content"
                                      rows="20"
                                      class="w-full p-4 border-0 focus:ring-0 font-mono text-sm"
                                      placeholder="Masukkan HTML template di sini..."
                                      required>{!! old('html_content', $template->html_content) !!}</textarea>
                        </div>
                        
                        <!-- Info -->
                        <div class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Info:</strong> HTML code ditampilkan apa adanya. Edit langsung untuk mengubah template.
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
                            👁️ Preview Template
                        </button>
                    </div>

                    <!-- CSS Variables (Opsional) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">CSS Variables (Opsional)</label>
                        <textarea name="css_variables" 
                                  rows="5"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                  placeholder='{"primary_color": "#667eea", "secondary_color": "#764ba2", "font_family": "Georgia, serif"}'>{{ old('css_variables', $template->css_variables ? json_encode($template->css_variables, JSON_PRETTY_PRINT) : '') }}</textarea>
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
                            💾 Update Template
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