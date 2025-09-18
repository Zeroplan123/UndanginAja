<x-app-layout>
    <x-slot name="title">Kirim Undangan - {{ $invitation->groom_name }} & {{ $invitation->bride_name }}</x-slot>
    
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl mb-8">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">📤 Kirim Undangan</h3>
                            <p class="text-gray-600 mt-1">{{ $invitation->groom_name }} & {{ $invitation->bride_name }}</p>
                        </div>
                        <a href="{{ route('user.history') }}" 
                           class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- WhatsApp Links Display -->
            @if(session('whatsapp_links'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-800 mb-3">📱 Link WhatsApp Siap Dikirim:</h4>
                    <div class="space-y-2">
                        @foreach(session('whatsapp_links') as $link)
                            <div class="flex items-center justify-between bg-white p-3 rounded border">
                                <span class="text-sm text-gray-600">{{ $link['phone'] }}</span>
                                <a href="{{ $link['url'] }}" target="_blank" 
                                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                    Kirim WhatsApp
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Email Section -->
                @if(config('app.email_maintenance', true))
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl opacity-75">
                @else
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                @endif
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            @if(config('app.email_maintenance', true))
                            <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-500">📧 Kirim via Email</h4>
                                <p class="text-sm text-gray-500">Sedang dalam maintenance</p>
                            </div>
                            <span class="ml-auto bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Maintenance</span>
                            @else
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">📧 Kirim via Email</h4>
                                <p class="text-sm text-gray-600">Kirim undangan dengan attachment PDF</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if(config('app.email_maintenance', true))
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Fitur Sedang Maintenance</h4>
                            <p class="text-gray-500 mb-4">Pengiriman undangan via email sedang dalam perbaikan dan akan segera tersedia kembali.</p>
                            
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-orange-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-left">
                                        <p class="text-sm text-orange-800 font-medium mb-1">🔧 Sedang Diperbaiki</p>
                                        <p class="text-sm text-orange-700">
                                            Kami sedang melakukan perbaikan pada sistem email untuk memberikan pengalaman yang lebih baik.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-700">
                                    <strong>Sementara ini:</strong> Gunakan fitur WhatsApp atau download PDF untuk membagikan undangan kepada tamu Anda.
                                </p>
                            </div>
                        </div>
                        @else
                        <form action="{{ route('communication.send-email', $invitation->slug) }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label for="emails" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Penerima <span class="text-red-500">*</span>
                                </label>
                                <textarea name="emails" id="emails" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="email1@example.com, email2@example.com, ..."
                                          required></textarea>
                                <p class="text-xs text-gray-500 mt-1">Pisahkan multiple email dengan koma</p>
                            </div>

                            <div>
                                <label for="recipient_names" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Penerima (Opsional)
                                </label>
                                <textarea name="recipient_names" id="recipient_names" rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Nama 1, Nama 2, ..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">Pisahkan nama dengan koma, sesuai urutan email</p>
                            </div>

                            <div>
                                <label for="custom_message" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pesan Khusus (Opsional)
                                </label>
                                <textarea name="custom_message" id="custom_message" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Tambahkan pesan personal untuk tamu undangan..."></textarea>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="include_pdf" id="include_pdf" value="1" checked
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="include_pdf" class="ml-2 block text-sm text-gray-700">
                                    Sertakan attachment PDF undangan
                                </label>
                            </div>

                            <div class="pt-4">
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Kirim Email
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- WhatsApp Section -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.106"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">📱 Kirim via WhatsApp</h4>
                                <p class="text-gray-600 text-sm">Buat link WhatsApp untuk kirim undangan</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('communication.send-whatsapp', $invitation->slug) }}" method="POST" class="p-6">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp *</label>
                                <textarea name="phone_numbers" rows="3" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="08123456789, 081234567890, +6281234567891"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma untuk multiple nomor</p>
                            </div>
                            
                            <!-- WhatsApp Info -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm text-green-800 font-medium">💡 Tips WhatsApp</p>
                                        <p class="text-xs text-green-700 mt-1">
                                            Pesan akan berisi teks lengkap undangan dengan format yang rapi dan formal.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                                📱 Buat Link WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-800">👀 Preview Undangan</h4>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="text-center">
                            <h5 class="font-bold text-xl text-pink-600 mb-2">
                                {{ $invitation->groom_name }} & {{ $invitation->bride_name }}
                            </h5>
                            <div class="text-gray-600 space-y-1">
                                <p>📅 {{ date('d F Y', strtotime($invitation->wedding_date)) }}</p>
                                @if($invitation->wedding_time)
                                    <p>🕐 {{ $invitation->wedding_time }}</p>
                                @endif
                                <p>📍 {{ $invitation->venue ?? $invitation->location }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('user.invitation.preview', $invitation->slug) }}" target="_blank"
                           class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            🌐 Lihat Online
                        </a>
                        <a href="{{ route('user.export-pdf', $invitation->slug) }}" 
                           class="flex-1 text-center bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            📄 Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
