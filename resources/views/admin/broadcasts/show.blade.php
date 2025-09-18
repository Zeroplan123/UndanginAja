<x-app-layout>
<x-slot name="title">Tampilan Broadcast</x-slot>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Broadcast Details</h1>
        <div class="flex space-x-3">
            @if(in_array($broadcast->status, ['draft', 'scheduled']))
                <a href="{{ route('admin.broadcasts.edit', $broadcast) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('admin.broadcasts.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $broadcast->title }}</h2>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $broadcast->getTypeBadgeColor() }}">
                            {{ ucfirst($broadcast->type) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $broadcast->getPriorityBadgeColor() }}">
                            {{ $broadcast->getPriorityText() }}
                        </span>
                    </div>
                </div>

                <div class="prose max-w-none mb-6">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $broadcast->message }}</p>
                </div>

                <!-- Action Buttons -->
                @if($broadcast->status === 'scheduled')
                    <div class="flex space-x-3 pt-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('admin.broadcasts.send', $broadcast) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200"
                                    onclick="return confirm('Send this broadcast now?')">
                                <i class="fas fa-paper-plane mr-2"></i>Send Now
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.broadcasts.cancel', $broadcast) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200"
                                    onclick="return confirm('Cancel this broadcast?')">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Read Statistics -->
            @if($broadcast->status === 'sent')
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Read Statistics</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $totalTargets }}</div>
                            <div class="text-sm text-gray-600">Total Recipients</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $readCount }}</div>
                            <div class="text-sm text-gray-600">Read</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $totalTargets - $readCount }}</div>
                            <div class="text-sm text-gray-600">Unread</div>
                        </div>
                    </div>

                    @if($totalTargets > 0)
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Read Rate</span>
                                <span>{{ round(($readCount / $totalTargets) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($readCount / $totalTargets) * 100 }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Broadcast Info</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if($broadcast->status === 'sent') bg-green-100 text-green-800
                                @elseif($broadcast->status === 'scheduled') bg-yellow-100 text-yellow-800
                                @elseif($broadcast->status === 'draft') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($broadcast->status) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Created By</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $broadcast->creator->name }}</div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Created At</label>
                        <div class="mt-1 text-sm text-gray-900">
                            {{ $broadcast->created_at->setTimezone('Asia/Jakarta')->format('M d, Y H:i') }} WIB
                            <span class="text-xs text-gray-500 block">{{ $broadcast->created_at->setTimezone('Asia/Jakarta')->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if($broadcast->scheduled_at)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Scheduled For</label>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ $broadcast->scheduled_at->setTimezone('Asia/Jakarta')->format('M d, Y H:i') }} WIB
                                <span class="text-xs text-gray-500 block">{{ $broadcast->scheduled_at->setTimezone('Asia/Jakarta')->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endif

                    @if($broadcast->sent_at)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Sent At</label>
                            <div class="mt-1 text-sm text-gray-900">
                                {{ $broadcast->sent_at->setTimezone('Asia/Jakarta')->format('M d, Y H:i') }} WIB
                                <span class="text-xs text-gray-500 block">{{ $broadcast->sent_at->setTimezone('Asia/Jakarta')->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-600">Target Audience</label>
                        <div class="mt-1 text-sm text-gray-900">
                            @if($broadcast->target_type === 'all')
                                All Users ({{ $targetUsers->count() }} users)
                            @else
                                Specific Users ({{ $targetUsers->count() }} selected)
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Users -->
            @if($broadcast->target_type === 'specific' && $targetUsers->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Target Users</h3>
                    <div class="max-h-64 overflow-y-auto">
                        @foreach($targetUsers as $user)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </div>
                                @if($broadcast->status === 'sent')
                                    <div>
                                        @if($broadcast->isReadBy($user))
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Read
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Unread
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
