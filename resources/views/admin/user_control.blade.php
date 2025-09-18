<x-app-layout>
    <x-slot name="title">Kontrol Pengguna</x-slot>

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="mt-2 text-gray-600">Manage users, roles, and permissions</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($activeUsers) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Banned Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Banned Users</p>
                        <p class="text-3xl font-bold text-red-600">{{ number_format($bannedUsers) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-user-slash text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Admin Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Admin Users</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($adminUsers) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-user-shield text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4 lg:space-y-0 lg:flex lg:items-center lg:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by name or email..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Role Filter -->
                <div>
                    <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6" id="bulkActions" style="display: none;">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">
                    <span id="selectedCount">0</span> users selected
                </span>
                <div class="space-x-2">
                    <button onclick="bulkAction('ban')" class="px-6 py-2 bg-red-600 mb-2 ms-2 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                        <i class="fas fa-ban mr-1"></i>Ban Selected
                    </button>
                    <button onclick="bulkAction('unban')" class="px-4 py-2 bg-green-600 mb-2 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <i class="fas fa-user-check mr-1"></i>Unban Selected
                    </button>
                    <button onclick="bulkAction('delete')" class="px-4 py-2 bg-gray-600 mb-2 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                        <i class="fas fa-trash mr-1"></i>Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invitations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="user-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $user->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $status = $user->is_banned ? 'banned' : 'active';
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'banned' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$status] }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $user->invitations->count() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium space-x-2">
                                @if($user->is_banned)
                                    <button onclick="unbanUser({{ $user->id }})" 
                                            class="text-green-600 hover:text-green-900 transition-colors"
                                            title="Unban User">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                @else
                                    @if($user->role !== 'admin')
                                        <button onclick="banUser({{ $user->id }})" 
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Ban User">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @endif
                                @endif
                                @if($user->role !== 'admin')
                                    <button onclick="deleteUser({{ $user->id }})" 
                                            class="text-red-600 hover:text-red-900 transition-colors ml-2"
                                            title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg">No users found</p>
                                <p class="text-sm">Try adjusting your search or filter criteria</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- User Detail Modal -->
<div id="userDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">User Details</h3>
                <button onclick="closeModal('userDetailModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="userDetailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit User</h3>
                <button onclick="closeModal('editUserModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="editUserForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="editName" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="editEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="editRole" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="editStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="banned">Banned</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password (optional)</label>
                        <input type="password" id="editPassword" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('editUserModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ban User Modal -->
<div id="banUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Ban User</h3>
                <button onclick="closeModal('banUserModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-gray-600">Apakah Anda yakin ingin mem-ban user ini? User akan langsung di-ban secara permanen.</p>
            </div>
            <form id="banUserForm">
                <input type="hidden" id="banUserId" name="user_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Ban (Opsional)</label>
                    <textarea name="ban_reason" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" rows="3" placeholder="Masukkan alasan ban..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('banUserModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Ya, Ban User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/user_control.js') }}?v={{ time() }}" defer></script>

<script>
// Fallback functions in case external JS fails to load
window.addEventListener('DOMContentLoaded', function() {
    // Check if functions are loaded, if not define them
    if (typeof deleteUser === 'undefined') {
        window.deleteUser = async function(userId) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
            
            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('User berhasil dihapus');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error deleting user: ' + error.message);
            }
        };
    }
    
    if (typeof banUser === 'undefined') {
        window.banUser = function(userId) {
            document.getElementById('banUserId').value = userId;
            document.getElementById('banUserModal').classList.remove('hidden');
        };
    }
    
    if (typeof unbanUser === 'undefined') {
        window.unbanUser = async function(userId) {
            if (!confirm('Are you sure you want to unban this user?')) return;
            
            try {
                const response = await fetch('/admin/users/unban', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ user_id: userId })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('User berhasil di-unban');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error unbanning user: ' + error.message);
            }
        };
    }
});
</script>
</x-app-layout>