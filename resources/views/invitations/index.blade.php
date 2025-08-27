<x-app-layout>
    <x-slot name="title">Undangan Saya</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Undangan Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Undangan Saya</h3>
                        <a href="{{ route('invitations.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Buat Undangan Baru
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($invitations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($invitations as $invitation)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden">
                                    @if($invitation->template && $invitation->template->preview_image)
                                        <img src="{{ asset('storage/' . $invitation->template->preview_image) }}" alt="Template Preview" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">No Preview</span>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        <h4 class="font-semibold text-lg mb-2">{{ $invitation->bride_name }} & {{ $invitation->groom_name }}</h4>
                                        <p class="text-gray-600 text-sm mb-1">
                                            <strong>Tanggal:</strong> {{ $invitation->wedding_date->format('d F Y') }}
                                        </p>
                                        <p class="text-gray-600 text-sm mb-1">
                                            <strong>Tempat:</strong> {{ $invitation->venue }}
                                        </p>
                                        <p class="text-gray-600 text-sm mb-4">
                                            <strong>Template:</strong> {{ $invitation->template->name ?? 'Template tidak ditemukan' }}
                                        </p>
                                        
                                        <div class="flex space-x-2">
                                            <a href="{{ route('invitations.preview', $invitation) }}" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                Preview
                                            </a>
                                            <a href="{{ route('invitations.edit', $invitation) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                Edit
                                            </a>
                                            <form action="{{ route('invitations.destroy', $invitation) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus undangan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $invitations->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">Anda belum memiliki undangan.</p>
                            <a href="{{ route('invitations.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Buat Undangan Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
