<x-app-layout>
    <x-slot name="title">Edit Undangan</x-slot>
     <script src="{{ asset('js/edit.js') }}"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Undangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('invitations.update', $invitation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Template Selection -->
                        <div class="mb-6">
                            <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Template</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($templates as $template)
                                    <div class="relative">
                                        <input type="radio" name="template_id" value="{{ $template->id }}" id="template_{{ $template->id }}" class="sr-only" 
                                               {{ (old('template_id', $invitation->template_id) == $template->id) ? 'checked' : '' }}>
                                        <label for="template_{{ $template->id }}" class="block cursor-pointer">
                                            <div class="border-2 border-gray-200 rounded-lg overflow-hidden hover:border-blue-500 transition-colors template-card">
                                                @if($template->cover_image)
                                                    <img src="{{ $template->cover_image_url }}" 
                                                         alt="{{ $template->name }}"
                                                         class="w-full h-32 object-cover">
                                                @else
                                                    <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                                                        <span class="text-gray-500">{{ $template->name }}</span>
                                                    </div>
                                                @endif
                                                <div class="p-3">
                                                    <h4 class="font-medium text-sm">{{ $template->name }}</h4>
                                                    @if($template->description)
                                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($template->description, 50) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('template_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bride Name -->
                            <div>
                                <label for="bride_name" class="block text-sm font-medium text-gray-700">Nama Mempelai Wanita</label>
                                <input type="text" name="bride_name" id="bride_name" value="{{ old('bride_name', $invitation->bride_name) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('bride_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Groom Name -->
                            <div>
                                <label for="groom_name" class="block text-sm font-medium text-gray-700">Nama Mempelai Pria</label>
                                <input type="text" name="groom_name" id="groom_name" value="{{ old('groom_name', $invitation->groom_name) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('groom_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Wedding Date -->
                            <div>
                                <label for="wedding_date" class="block text-sm font-medium text-gray-700">Tanggal Pernikahan</label>
                                <input type="date" name="wedding_date" id="wedding_date" value="{{ old('wedding_date', $invitation->wedding_date->format('Y-m-d')) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('wedding_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Wedding Time -->
                            <div>
                                <label for="wedding_time" class="block text-sm font-medium text-gray-700">Waktu Pernikahan</label>
                                <input type="text" name="wedding_time" id="wedding_time" value="{{ old('wedding_time', $invitation->wedding_time) }}" 
                                       placeholder="Contoh: 08:00 WIB"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('wedding_time')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Venue -->
                            <div>
                                <label for="venue" class="block text-sm font-medium text-gray-700">Tempat Pernikahan</label>
                                <input type="text" name="venue" id="venue" value="{{ old('venue', $invitation->venue) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('venue')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location (detailed address) -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                <textarea name="location" id="location" rows="2" 
                                          placeholder="Alamat lengkap tempat pernikahan"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('location', $invitation->location) }}</textarea>
                                @error('location')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mt-6">
                            <label for="additional_notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                            <textarea name="additional_notes" id="additional_notes" rows="3" 
                                      placeholder="Catatan atau informasi tambahan untuk undangan"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('additional_notes', $invitation->additional_notes) }}</textarea>
                            @error('additional_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('invitations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Undangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Template card hover effect */
        .template-card {
            transition: all 0.3s ease;
        }
        
        .template-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Hide radio button visually but keep it accessible */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>

</x-app-layout>
