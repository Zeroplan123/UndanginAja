<x-app-layout>
<link rel="stylesheet" href="{{ asset('css/professional-analytics.css') }}">
<x-slot name="title">Broadcast Analystic</x-slot>
<div class="min-h-screen bg-gray-50">
    <!-- Professional Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Broadcast Analytics</h1>
                            <p class="text-sm text-gray-500">Monitor engagement and performance metrics</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.broadcasts.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Broadcasts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Broadcasts -->
            <div class="analytics-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-broadcast-tower text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Total Broadcasts</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalBroadcasts) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span>All time messages</span>
                    </div>
                </div>
            </div>

            <!-- Sent Messages -->
            <div class="analytics-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-paper-plane text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Sent Messages</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($sentBroadcasts) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        <span>Successfully delivered</span>
                    </div>
                </div>
            </div>

            <!-- Scheduled -->
            <div class="analytics-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Scheduled</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($scheduledBroadcasts) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-yellow-600">
                        <i class="fas fa-clock text-xs mr-1"></i>
                        <span>Pending delivery</span>
                    </div>
                </div>
            </div>

            <!-- Drafts -->
            <div class="analytics-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-edit text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500 truncate">Drafts</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($draftBroadcasts) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-edit text-xs mr-1"></i>
                        <span>Work in progress</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Recent Broadcast Performance</h3>
                        <p class="mt-1 text-sm text-gray-500">Engagement metrics for your latest messages</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option>Last 30 days</option>
                            <option>Last 7 days</option>
                            <option>Last 24 hours</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($recentBroadcasts->count() > 0)
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Read</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engagement</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentBroadcasts as $broadcast)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($broadcast['title'], 40) }}</div>
                                                <div class="text-sm text-gray-500">#{{ $broadcast['id'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeColors = [
                                                'promo' => 'bg-blue-100 text-blue-800',
                                                'update' => 'bg-green-100 text-green-800',
                                                'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                'announcement' => 'bg-red-100 text-red-800'
                                            ];
                                            $typeIcons = [
                                                'promo' => 'fas fa-tag',
                                                'update' => 'fas fa-info-circle',
                                                'maintenance' => 'fas fa-tools',
                                                'announcement' => 'fas fa-bullhorn'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$broadcast['type']] ?? 'bg-gray-100 text-gray-800' }}">
                                            <i class="{{ $typeIcons[$broadcast['type']] ?? 'fas fa-envelope' }} mr-1"></i>
                                            {{ ucfirst($broadcast['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $broadcast['sent_at']->format('M d, Y') }}</div>
                                        <div class="text-gray-500">{{ $broadcast['sent_at']->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-gray-900">
                                            <i class="fas fa-users text-gray-400 mr-2"></i>
                                            {{ number_format($broadcast['target_count']) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-gray-900">
                                            <i class="fas fa-eye text-gray-400 mr-2"></i>
                                            {{ number_format($broadcast['read_count']) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $broadcast['read_rate'] }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $broadcast['read_rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No analytics data yet</h3>
                    <p class="text-gray-500 mb-6">Send your first broadcast to see engagement metrics</p>
                    <a href="{{ route('admin.broadcasts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Broadcast
                    </a>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-bolt text-blue-500 mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.broadcasts.create') }}" class="group block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-plus text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600">Create New</h4>
                                <p class="text-xs text-gray-500">Send a message</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.broadcasts.index', ['status' => 'scheduled']) }}" class="group block p-4 border border-gray-200 rounded-lg hover:border-yellow-300 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-yellow-600">Scheduled</h4>
                                <p class="text-xs text-gray-500">{{ $scheduledBroadcasts }} pending</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.broadcasts.index', ['status' => 'draft']) }}" class="group block p-4 border border-gray-200 rounded-lg hover:border-gray-400 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-edit text-gray-600"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-gray-600">Drafts</h4>
                                <p class="text-xs text-gray-500">{{ $draftBroadcasts }} saved</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.broadcasts.index') }}" class="group block p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                    <i class="fas fa-list text-green-600"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-green-600">All Messages</h4>
                                <p class="text-xs text-gray-500">View all</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
