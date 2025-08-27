<x-app-layout>
    <x-slot name="title">Chat dengan Admin</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $conversation->subject ?: 'Chat dengan Admin' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Status: 
                    <span class="px-2 py-1 rounded-full text-xs
                        @if($conversation->status === 'open') bg-green-100 text-green-800
                        @elseif($conversation->status === 'closed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($conversation->status) }}
                    </span>
                </p>
            </div>
            <a href="{{ route('chat.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Chat Messages Container -->
                <div id="chatContainer" class="h-96 overflow-y-auto p-6 border-b">
                    <div id="messagesContainer" class="space-y-4">
                        @foreach($conversation->messages as $message)
                            @include('chat.partials.message', ['message' => $message])
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
                                          placeholder="Tulis pesan Anda..."></textarea>
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
                        <p class="text-gray-600">Percakapan ini telah ditutup oleh admin.</p>
                    </div>
                @endif
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
                    const response = await fetch(`/chat/${conversationId}/message`, {
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

        // Auto-refresh messages every 5 seconds
        setInterval(async function() {
            try {
                const response = await fetch(`/chat/${conversationId}/messages`);
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
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</x-app-layout>
