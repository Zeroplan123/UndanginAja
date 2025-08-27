<x-app-layout>
    <x-slot name="title">Chat dengan Admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat dengan Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($conversation)
                <!-- Direct to chat if conversation exists -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold">Chat dengan Admin</h3>
                            <span class="px-3 py-1 rounded-full text-sm
                                @if($conversation->status === 'open') bg-green-100 text-green-800
                                @elseif($conversation->status === 'closed') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($conversation->status) }}
                            </span>
                        </div>
                        
                        <!-- Chat Messages Container -->
                        <div id="chatContainer" class="h-96 overflow-y-auto border rounded-lg p-4 mb-4 bg-gray-50">
                            <div id="messagesContainer" class="space-y-4">
                                @foreach($conversation->messages as $message)
                                    @include('chat.partials.message', ['message' => $message])
                                @endforeach
                            </div>
                        </div>

                        <!-- Message Input -->
                        @if($conversation->status !== 'closed')
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
                        @else
                            <div class="p-4 bg-gray-100 text-center rounded-lg">
                                <p class="text-gray-600">Percakapan ini telah ditutup oleh admin.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- First time chat - show welcome message -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <i class="fas fa-comments text-blue-500 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold mb-4">Mulai Chat dengan Admin</h3>
                        <p class="text-gray-600 mb-6">Kirim pesan pertama Anda untuk memulai percakapan dengan admin.</p>
                        
                        <form id="firstMessageForm" class="max-w-md mx-auto">
                            @csrf
                            <div class="mb-4">
                                <textarea id="firstMessage" name="message" rows="4" required
                                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Tulis pesan Anda..."></textarea>
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        @if($conversation)
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
        @else
        // Handle first message form
        document.getElementById('firstMessageForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const message = document.getElementById('firstMessage').value.trim();
            if (!message) return;
            
            const formData = new FormData();
            formData.append('message', message);
            formData.append('subject', 'Chat dengan Admin');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            try {
                const response = await fetch('{{ route("chat.store") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim pesan.');
            }
        });
        @endif
    </script>
</x-app-layout>
