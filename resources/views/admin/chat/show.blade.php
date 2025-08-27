<x-app-layout>
    <x-slot name="title">Chat dengan {{ $conversation->user->name }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Chat dengan {{ $conversation->user->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $conversation->subject ?: 'Chat dengan Admin' }} • 
                    <span class="px-2 py-1 rounded-full text-xs
                        @if($conversation->status === 'open') bg-green-100 text-green-800
                        @elseif($conversation->status === 'closed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($conversation->status) }}
                    </span>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Status Change Buttons -->
                <div class="flex space-x-2">
                    @if($conversation->status !== 'open')
                        <button onclick="updateStatus('open')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fas fa-check mr-1"></i>Buka
                        </button>
                    @endif
                    @if($conversation->status !== 'pending')
                        <button onclick="updateStatus('pending')" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </button>
                    @endif
                    @if($conversation->status !== 'closed')
                        <button onclick="updateStatus('closed')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fas fa-times mr-1"></i>Tutup
                        </button>
                    @endif
                </div>
                <a href="{{ route('admin.chat.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- User Info Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Informasi User</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                    {{ strtoupper(substr($conversation->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $conversation->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $conversation->user->email }}</p>
                                </div>
                            </div>
                            <div class="border-t pt-3">
                                <p class="text-sm text-gray-600">Status: 
                                    <span class="px-2 py-1 rounded-full text-xs
                                        @if($conversation->user->status === 'active') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($conversation->user->status) }}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">Role: 
                                    <span class="font-semibold">{{ ucfirst($conversation->user->role) }}</span>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">Bergabung: {{ $conversation->user->created_at->format('d M Y') }}</p>
                                @if($conversation->user->last_login_at)
                                    <p class="text-sm text-gray-600 mt-1">Login Terakhir: {{ $conversation->user->last_login_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Container -->
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <!-- Chat Messages Container -->
                        <div id="chatContainer" class="h-96 overflow-y-auto p-6 border-b">
                            <div id="messagesContainer" class="space-y-4">
                                @foreach($conversation->messages as $message)
                                    @include('chat.partials.admin-message', ['message' => $message])
                                @endforeach
                            </div>
                        </div>

                        <!-- Message Input -->
                        @if($conversation->status !== 'closed')
                            <div class="p-6">
                                <form id="messageForm" class="flex space-x-4">
                                    @csrf
                                    <div class="flex-1">
                                        <textarea id="messageInput" name="message" rows="2" required
                                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Tulis balasan Anda..."></textarea>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="p-6 bg-gray-100 text-center">
                                <p class="text-gray-600">Percakapan ini telah ditutup. Buka kembali untuk membalas.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const conversationId = {{ $conversation->id }};
        const messagesContainer = document.getElementById('messagesContainer');
        const chatContainer = document.getElementById('chatContainer');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');

        // Auto-scroll to bottom
        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Initial scroll to bottom
        scrollToBottom();

        // Handle message form submission
        if (messageForm) {
            messageForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const message = messageInput.value.trim();
                if (!message) return;
                
                const formData = new FormData();
                formData.append('message', message);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                try {
                    const response = await fetch(`/admin/chat/${conversationId}/message`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Add message to chat
                        messagesContainer.insertAdjacentHTML('beforeend', data.html);
                        messageInput.value = '';
                        scrollToBottom();
                    } else {
                        alert('Terjadi kesalahan saat mengirim pesan.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengirim pesan.');
                }
            });
        }

        // Update conversation status
        async function updateStatus(status) {
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

        // Auto-refresh messages every 5 seconds
        setInterval(async function() {
            try {
                const response = await fetch(`/admin/chat/${conversationId}/messages`);
                const data = await response.json();
                
                if (data.success && data.messages.length > messagesContainer.children.length) {
                    // Reload the page to get new messages
                    location.reload();
                }
            } catch (error) {
                console.error('Error checking for new messages:', error);
            }
        }, 5000);

        // Handle Enter key for sending messages
        if (messageInput) {
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    messageForm.dispatchEvent(new Event('submit'));
                }
            });
        }
    </script>
</x-app-layout>
