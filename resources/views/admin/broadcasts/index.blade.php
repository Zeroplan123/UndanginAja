<x-app-layout>
<x-slot name="title">Broadcast</x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Broadcast Management</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.broadcasts.analytics') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-chart-bar mr-2"></i>Analytics
            </a>
            <a href="{{ route('admin.broadcasts.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>New Broadcast
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.broadcasts.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search broadcasts..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="promo" {{ request('type') === 'promo' ? 'selected' : '' }}>Promo</option>
                    <option value="update" {{ request('type') === 'update' ? 'selected' : '' }}>Update</option>
                    <option value="maintenance" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="announcement" {{ request('type') === 'announcement' ? 'selected' : '' }}>Announcement</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.broadcasts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                Clear
            </a>
        </form>
    </div>

    <!-- Broadcasts Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($broadcasts as $broadcast)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($broadcast->title, 30) }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($broadcast->message, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $broadcast->getTypeBadgeColor() }}">
                                    {{ ucfirst($broadcast->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($broadcast->status === 'sent') bg-green-100 text-green-800
                                    @elseif($broadcast->status === 'scheduled') bg-yellow-100 text-yellow-800
                                    @elseif($broadcast->status === 'draft') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($broadcast->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $broadcast->getPriorityBadgeColor() }}">
                                    {{ $broadcast->getPriorityText() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($broadcast->target_type === 'all')
                                    All Users
                                @else
                                    {{ count($broadcast->target_users ?? []) }} Users
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($broadcast->scheduled_at)
                                    {{ $broadcast->scheduled_at->setTimezone('Asia/Jakarta')->format('M d, Y H:i') }}
                                    <span class="text-xs text-gray-500 block">WIB</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $broadcast->created_at->setTimezone('Asia/Jakarta')->format('M d, Y H:i') }}
                                <span class="text-xs text-gray-500 block">WIB</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.broadcasts.show', $broadcast) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(in_array($broadcast->status, ['draft', 'scheduled']))
                                        <a href="{{ route('admin.broadcasts.edit', $broadcast) }}" 
                                           class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if($broadcast->status === 'scheduled')
                                        <form method="POST" action="{{ route('admin.broadcasts.send', $broadcast) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Send Now"
                                                    onclick="return confirm('Send this broadcast now?')">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.broadcasts.cancel', $broadcast) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Cancel"
                                                    onclick="return confirm('Cancel this broadcast?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($broadcast->status, ['draft', 'cancelled']))
                                        <form method="POST" action="{{ route('admin.broadcasts.destroy', $broadcast) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete"
                                                    onclick="return confirm('Delete this broadcast?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No broadcasts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($broadcasts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $broadcasts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>
