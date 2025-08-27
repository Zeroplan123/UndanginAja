<div class="flex {{ $message->isFromUser() ? 'justify-end' : 'justify-start' }} mb-4">
    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->isFromUser() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
        <div class="flex items-center mb-1">
            <span class="text-xs font-semibold">
                {{ $message->isFromUser() ? 'Anda' : 'Admin' }}
            </span>
            <span class="text-xs ml-2 opacity-75">
                {{ $message->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}
            </span>
        </div>
        <p class="text-sm">{{ $message->message }}</p>
    </div>
</div>
