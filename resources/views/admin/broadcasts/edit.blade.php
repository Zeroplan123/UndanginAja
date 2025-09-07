<x-app-layout>
<x-slot name="title">Edit Broadcast</x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Broadcast</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.broadcasts.show', $broadcast) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-eye mr-2"></i>View
            </a>
            <a href="{{ route('admin.broadcasts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.broadcasts.update', $broadcast) }}" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $broadcast->title) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter broadcast title">
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                <select id="type" name="type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Type</option>
                    <option value="promo" {{ old('type', $broadcast->type) === 'promo' ? 'selected' : '' }}>Promo</option>
                    <option value="update" {{ old('type', $broadcast->type) === 'update' ? 'selected' : '' }}>Feature Update</option>
                    <option value="maintenance" {{ old('type', $broadcast->type) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="announcement" {{ old('type', $broadcast->type) === 'announcement' ? 'selected' : '' }}>Announcement</option>
                </select>
            </div>

            <!-- Priority -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                <select id="priority" name="priority" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('priority', $broadcast->priority) == '1' ? 'selected' : '' }}>Low</option>
                    <option value="2" {{ old('priority', $broadcast->priority) == '2' ? 'selected' : '' }}>Medium</option>
                    <option value="3" {{ old('priority', $broadcast->priority) == '3' ? 'selected' : '' }}>High</option>
                </select>
            </div>

            <!-- Message -->
            <div class="md:col-span-2">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                <textarea id="message" name="message" rows="6" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Enter your broadcast message...">{{ old('message', $broadcast->message) }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Maximum 5000 characters</p>
            </div>

            <!-- Target Type -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience *</label>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="radio" name="target_type" value="all" 
                               {{ old('target_type', $broadcast->target_type) === 'all' ? 'checked' : '' }}
                               class="mr-2" onchange="toggleUserSelection()">
                        <span>All Users</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="target_type" value="specific" 
                               {{ old('target_type', $broadcast->target_type) === 'specific' ? 'checked' : '' }}
                               class="mr-2" onchange="toggleUserSelection()">
                        <span>Specific Users</span>
                    </label>
                </div>
            </div>

            <!-- User Selection -->
            <div id="user-selection" class="md:col-span-2 {{ old('target_type', $broadcast->target_type) === 'specific' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Users</label>
                <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3">
                    @foreach($users as $user)
                        <label class="flex items-center py-1">
                            <input type="checkbox" name="target_users[]" value="{{ $user->id }}"
                                   {{ in_array($user->id, old('target_users', $broadcast->target_users ?? [])) ? 'checked' : '' }}
                                   class="mr-2">
                            <span class="text-sm">{{ $user->name }} ({{ $user->email }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Scheduling -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Scheduling</label>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="send_now" value="1" 
                               {{ old('send_now') ? 'checked' : '' }}
                               class="mr-2" onchange="toggleScheduling()">
                        <span>Send immediately</span>
                    </label>
                    <div id="schedule-section" class="{{ old('send_now') ? 'hidden' : '' }}">
                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">Schedule for later (optional)</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" 
                               value="{{ old('scheduled_at', $broadcast->scheduled_at ? $broadcast->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Leave empty to save as draft</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.broadcasts.show', $broadcast) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200">
                <i class="fas fa-save mr-2"></i>Update Broadcast
            </button>
        </div>
    </form>
</div>

<script>
function toggleUserSelection() {
    const targetType = document.querySelector('input[name="target_type"]:checked').value;
    const userSelection = document.getElementById('user-selection');
    
    if (targetType === 'specific') {
        userSelection.classList.remove('hidden');
    } else {
        userSelection.classList.add('hidden');
    }
}

function toggleScheduling() {
    const sendNow = document.querySelector('input[name="send_now"]').checked;
    const scheduleSection = document.getElementById('schedule-section');
    
    if (sendNow) {
        scheduleSection.classList.add('hidden');
        document.getElementById('scheduled_at').value = '';
    } else {
        scheduleSection.classList.remove('hidden');
    }
}
</script>
</x-app-layout>
