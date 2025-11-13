<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Preview: {{ $template->name }}</h1>
                @if($template->description)
                    <p class="text-gray-600">{{ $template->description }}</p>
                @endif
            </div>
            <a href="{{ route('dashboard') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white border-2 border-gray-200 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-pink-500 to-purple-600 px-6 py-4">
                <p class="text-white text-sm font-medium">Preview Template - Data di bawah ini adalah contoh</p>
            </div>
            <div class="p-8">
                {!! $template->getCompiledHtml([
                    'bride_name' => 'Sarah Wijaya',
                    'groom_name' => 'Budi Santoso',
                    'wedding_date' => '25 Desember 2025',
                    'wedding_time' => '10:00 WIB',
                    'venue' => 'Gedung Pernikahan Indah',
                    'location' => 'Jl. Contoh No. 123, Jakarta',
                    'additional_notes' => 'Merupakan kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.',
                    // Legacy support
                    'nama_mempelai_pria' => 'Budi Santoso',
                    'nama_mempelai_wanita' => 'Sarah Wijaya',
                    'tanggal_pernikahan' => '25 Desember 2025',
                    'waktu_pernikahan' => '10:00 WIB',
                    'lokasi_pernikahan' => 'Jl. Contoh No. 123, Jakarta',
                    'catatan_tambahan' => 'Merupakan kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.',
                ]) !!}
            </div>
        </div>

        <div class="mt-6 flex justify-center">
            <a href="{{ route('user.create-invitation', $template) }}" 
               class="bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Gunakan Template Ini
            </a>
        </div>
    </div>
</x-app-layout>
