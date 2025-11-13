<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preview Undangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ $invitation->bride_name }} & {{ $invitation->groom_name }}</h3>
                        <div class="space-x-2">
                            <a href="{{ route('invitations.edit', $invitation) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                            <a href="{{ route('invitations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="invitation-preview">
                            @if($invitation->template && ($invitation->template->html_content || $invitation->template->file_path))
                                @php
                                    // Get template content
                                    $htmlContent = null;
                                    
                                    // Try to get HTML content from file_path first
                                    if (!empty($invitation->template->file_path)) {
                                        $templatePath = public_path('templates/' . $invitation->template->file_path);
                                        if (file_exists($templatePath)) {
                                            $htmlContent = file_get_contents($templatePath);
                                        }
                                    }
                                    
                                    // Fallback to html_content field if file doesn't exist
                                    if (empty($htmlContent) && !empty($invitation->template->html_content)) {
                                        // Decode HTML entities for proper display
                                        $htmlContent = html_entity_decode($invitation->template->html_content, ENT_QUOTES, 'UTF-8');
                                    }
                                    
                                    // Replace template variables
                                    if (!empty($htmlContent)) {
                                        foreach ($variables as $key => $value) {
                                            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
                                            $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
                                        }
                                    }
                                @endphp
                                
                                @if(!empty($htmlContent))
                                    {!! $htmlContent !!}
                                @else
                                    <div class="text-center py-12 text-red-600">
                                        <p>❌ Template content tidak dapat dimuat</p>
                                        <p class="text-sm">File path: {{ $invitation->template->file_path ?? 'Tidak ada' }}</p>
                                        <p class="text-sm">HTML content: {{ !empty($invitation->template->html_content) ? 'Ada' : 'Kosong' }}</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-12">
                                    <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $invitation->bride_name }} & {{ $invitation->groom_name }}</h1>
                                    <p class="text-xl text-gray-600 mb-2">Mengundang Anda dalam acara pernikahan kami</p>
                                    <p class="text-lg text-gray-700 mb-2"><strong>Tanggal:</strong> {{ $variables['wedding_date'] }}</p>
                                    <p class="text-lg text-gray-700"><strong>Tempat:</strong> {{ $invitation->venue }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-800 mb-2">Informasi Undangan</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <strong>Template:</strong> {{ $invitation->template->name ?? 'Template tidak ditemukan' }}
                            </div>
                            <div>
                                <strong>Dibuat:</strong> {{ $invitation->created_at->format('d F Y H:i') }}
                            </div>
                            <div>
                                <strong>Terakhir diupdate:</strong> {{ $invitation->updated_at->format('d F Y H:i') }}
                            </div>
                            <div>
                                <strong>Slug:</strong> {{ $invitation->slug }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .invitation-preview {
            min-height: 400px;
            background: white;
            border-radius: 8px;
            padding: 20px;
        }
        
        /* Style untuk template content */
        .invitation-preview h1, .invitation-preview h2, .invitation-preview h3 {
            margin-bottom: 1rem;
        }
        
        .invitation-preview p {
            margin-bottom: 0.5rem;
        }
    </style>
</x-app-layout>
