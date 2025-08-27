<x-app-layout>
    <x-slot name="title">Manajemen Chat</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Chat') }}
            </h2>
            <div class="flex items-center space-x-4">
                <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-envelope mr-1"></i>
                    {{ $totalUnread }} Pesan Belum Dibaca
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-500 text-white">
                                    <i class="fas fa-comments text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Chat</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $conversations->total() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-500 text-white">
                                    <i class="fas fa-check-circle text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Chat Aktif</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $conversations->where('status', 'open')->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-500 text-white">
                                    <i class="fas fa-clock text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Pending</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $conversations->where('status', 'pending')->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-500 text-white">
                                    <i class="fas fa-envelope text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-red-600">Belum Dibaca</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $totalUnread }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8">
                                <a href="?status=all" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status', 'all') === 'all' ? 'border-blue-500 text-blue-600' : '' }}">
                                    Semua Chat
                                </a>
                                <a href="?status=open" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'open' ? 'border-blue-500 text-blue-600' : '' }}">
                                    Aktif
                                </a>
                                <a href="?status=pending" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'pending' ? 'border-blue-500 text-blue-600' : '' }}">
                                    Pending
                                </a>
                                <a href="?status=closed" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'closed' ? 'border-blue-500 text-blue-600' : '' }}">
                                    Ditutup
                                </a>
                            </nav>
                        </div>
                    </div>

                    <!-- Chat List -->
                    <div class="space-y-4">
                        @forelse($conversations as $conversation)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer" onclick="openChat({{ $conversation->id }})">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <!-- User Avatar -->
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($conversation->user->name, 0, 1)) }}
                                        </div>
                                        
                                        <!-- Chat Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="font-semibold text-lg">{{ $conversation->user->name }}</h3>
                                                <span class="text-sm text-gray-500">({{ $conversation->user->email }})</span>
                                            </div>
                                            <p class="text-gray-800 font-medium">{{ $conversation->subject ?: 'Chat dengan Admin' }}</p>
                                            @if($conversation->messages->count() > 0)
                                                <p class="text-gray-600 text-sm mt-1">
                                                    {{ Str::limit($conversation->messages->first()->message, 100) }}
                                                </p>
                                            @endif
                                            <div class="flex items-center mt-2 text-xs text-gray-500">
                                                <span>{{ $conversation->last_message_at ? $conversation->last_message_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : $conversation->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span>
                                                <span class="mx-2">•</span>
                                                <span class="px-2 py-1 rounded-full text-xs
                                                    @if($conversation->status === 'open') bg-green-100 text-green-800
                                                    @elseif($conversation->status === 'closed') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800
                                                    @endif">
                                                    {{ ucfirst($conversation->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Unread Badge & Actions -->
                                    <div class="flex items-center space-x-2">
                                        @if($conversation->unreadMessagesForAdmin() > 0)
                                            <div class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                                {{ $conversation->unreadMessagesForAdmin() }}
                                            </div>
                                        @endif
                                        
                                        <!-- Quick Actions -->
                                        <div class="flex space-x-1">
                                            @if($conversation->status === 'open')
                                                <button onclick="event.stopPropagation(); updateStatus({{ $conversation->id }}, 'closed')" 
                                                        class="text-red-600 hover:text-red-800 p-1" title="Tutup Chat">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @elseif($conversation->status === 'closed')
                                                <button onclick="event.stopPropagation(); updateStatus({{ $conversation->id }}, 'open')" 
                                                        class="text-green-600 hover:text-green-800 p-1" title="Buka Chat">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-comments text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">Belum ada percakapan dari user.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $conversations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openChat(conversationId) {
            window.location.href = `/admin/chat/${conversationId}`;
        }

        async function updateStatus(conversationId, status) {
            try {
                const response = await fetch(`/admin/chat/${conversationId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert('Terjadi kesalahan saat mengubah status.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengubah status.');
            }
        }

        // Auto-refresh every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</x-app-layout>
