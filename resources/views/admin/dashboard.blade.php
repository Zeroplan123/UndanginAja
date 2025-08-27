<x-app-layout>
<x-slot name="title">Dashboard Admin</x-slot>
@vite('public/css/AdminDashboard.css')

<div class="h-full bg-gray-50">

    <div class="min-w-full mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Enhanced Statistics Cards --}}
       <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Users Card with Real Growth --}}
    <div class="bg-white mt-4 rounded-xl elegant-shadow card-hover p-6 elegant-border">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 wedding-gradient-bg rounded-xl flex items-center justify-center text-white mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold metric-number">
                        {{ number_format($totalUsers) }}
                    </p>
                    <p class="text-sm text-gray-500 font-medium">Total Pengguna</p>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between">
                @if($userGrowth['percentage'] != 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                        {{ $userGrowth['is_positive'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} border">
                        <i class="fas fa-arrow-{{ $userGrowth['is_positive'] ? 'up' : 'down' }} mr-1"></i>
                        {{ $userGrowth['is_positive'] ? '+' : '' }}{{ $userGrowth['percentage'] }}% bulan ini
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                        <i class="fas fa-minus mr-1"></i>
                        Tidak ada perubahan
                    </span>
                @endif
                <span class="text-xs text-gray-400">vs bulan lalu</span>
            </div>
            @if($userGrowth['new_users'] > 0)
                <div class="mt-2 text-xs text-gray-500">
                    +{{ number_format($userGrowth['new_users']) }} pengguna baru bulan ini
                </div>
            @endif
        </div>
    </div>

    {{-- Total Templates Card with Real Data --}}
    <div class="bg-white mt-4 rounded-xl elegant-shadow card-hover p-6 elegant-border">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center text-white mr-4">
                    <i class="fas fa-palette text-xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold metric-number">
                        {{ number_format($totalTemplates) }}
                    </p>
                    <p class="text-sm text-gray-500 font-medium">Template Tersedia</p>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="fas fa-plus mr-1"></i>
                    {{ $templateGrowth['new_templates'] }} template baru
                </span>
                <span class="text-xs text-gray-400">minggu ini</span>
            </div>
        </div>
    </div>

    {{-- Total Invitations Card with Real Growth --}}
    <div class="bg-white mt-4 rounded-xl elegant-shadow card-hover p-6 elegant-border">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center text-white mr-4">
                    <i class="fas fa-heart text-xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold metric-number">
                        {{ number_format($totalInvitations) }}
                    </p>
                    <p class="text-sm text-gray-500 font-medium">Total Undangan</p>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between">
                @if($invitationGrowth['percentage'] != 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                        {{ $invitationGrowth['is_positive'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} border">
                        <i class="fas fa-arrow-{{ $invitationGrowth['is_positive'] ? 'up' : 'down' }} mr-1"></i>
                        {{ $invitationGrowth['is_positive'] ? '+' : '' }}{{ $invitationGrowth['percentage'] }}% minggu ini
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                        <i class="fas fa-minus mr-1"></i>
                        Tidak ada perubahan
                    </span>
                @endif
                <span class="text-xs text-gray-400">vs minggu lalu</span>
            </div>
        </div>
    </div>

    {{-- Total Revenue Card with Real Data --}}
    <div class="bg-white mt-4 rounded-xl elegant-shadow card-hover p-6 elegant-border">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white mr-4">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div>
                    <p class="text-3xl font-bold metric-number">
                        Rp {{ $revenueData['formatted'] }}
                    </p>
                    <p class="text-sm text-gray-500 font-medium">Total Pendapatan</p>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between">
                @if($revenueData['growth_percentage'] != 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                        {{ $revenueData['is_positive'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} border">
                        <i class="fas fa-arrow-{{ $revenueData['is_positive'] ? 'up' : 'down' }} mr-1"></i>
                        {{ $revenueData['is_positive'] ? '+' : '' }}{{ $revenueData['growth_percentage'] }}% bulan ini
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                        <i class="fas fa-minus mr-1"></i>
                        Tidak ada perubahan
                    </span>
                @endif
                <span class="text-xs text-gray-400">vs bulan lalu</span>
            </div>
        </div>
    </div>
</div>
        {{-- Enhanced Activities and Actions Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Recent Activities --}}
            <div class="bg-white rounded-xl elegant-shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Aktivitas Terbaru</h3>
                        <p class="text-sm text-gray-500">Update terkini sistem</p>
                    </div>
                    <button class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                        Lihat Semua
                    </button>
                </div>
                <div class="space-y-4">
                    @php
                        $defaultActivities = [
                            ['type' => 'user', 'message' => 'Pengguna baru mendaftar: Sarah & Ahmad', 'time' => '5 menit yang lalu', 'priority' => 'high'],
                            ['type' => 'template', 'message' => 'Template "Elegant Rose Gold" berhasil dipublikasi', 'time' => '2 jam yang lalu', 'priority' => 'medium'],
                            ['type' => 'order', 'message' => 'Pesanan premium dari Andi & Sari telah selesai', 'time' => '4 jam yang lalu', 'priority' => 'high'],
                            ['type' => 'review', 'message' => 'Review 5 bintang dari Dimas & Rita', 'time' => '1 hari yang lalu', 'priority' => 'medium'],
                            ['type' => 'system', 'message' => 'Backup otomatis berhasil dilakukan', 'time' => '2 hari yang lalu', 'priority' => 'low']
                        ];
                    @endphp
                    
                    @forelse($recentActivities ?? $defaultActivities as $activity)
                        <div class="activity-item rounded-lg p-3 -mx-3">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    @if($activity['type'] == 'user')
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-user-plus text-white text-sm"></i>
                                        </div>
                                    @elseif($activity['type'] == 'template')
                                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-palette text-white text-sm"></i>
                                        </div>
                                    @elseif($activity['type'] == 'order')
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-heart text-white text-sm"></i>
                                        </div>
                                    @elseif($activity['type'] == 'review')
                                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-star text-white text-sm"></i>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-cog text-white text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">{{ $activity['message'] }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                        @if(isset($activity['priority']) && $activity['priority'] == 'high')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Penting
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada aktivitas terbaru</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Enhanced Quick Actions --}}
            <div class="bg-white rounded-xl elegant-shadow p-6">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Panel Kontrol</h3>
                    <p class="text-sm text-gray-500">Akses cepat ke fungsi utama</p>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-800">Template Management</h4>
                                <p class="text-sm text-gray-600">Kelola koleksi template</p>
                            </div>
                            <button class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition duration-200 text-sm font-medium">
                                <i class="fas fa-palette mr-2"></i>
                                <a href="{{ route('templates.index') }}">Kelola</a>
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-800">User Management</h4>
                                <p class="text-sm text-gray-600">Administrasi pengguna</p>
                            </div>
                            <button class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:from-blue-700 hover:to-cyan-700 transition duration-200 text-sm font-medium">
                                <i class="fas fa-users mr-2"></i>
                                <a href="{{ route('admin.users.index') }}">Kelola</a>
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-800">Orders & Analytics</h4>
                                <p class="text-sm text-gray-600">Laporan dan statistik</p>
                            </div>
                            <button class="px-5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 transition duration-200 text-sm font-medium">
                                <i class="fas fa-chart-bar mr-2"></i>
                                <a href="{{ route('admin.analytics') }}">Lihat</a>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-gradient-to-r from-fuchsia-50 to-sky-50 rounded-xl border border-sky-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-800">Chat</h4>
                                <p class="text-sm text-gray-600">Chat Management Control</p>
                            </div>
                            <button class="px-5 py-2 bg-gradient-to-r from-fuchsia-600 to-sky-600 text-white rounded-lg hover:from-fuchsia-700 hover:to-sky-700 transition duration-200 text-sm font-medium">
                                <i class="fa-solid fa-comments"></i>
                                <a href="{{ route('admin.chat.index') }}">Lihat</a>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 w-">

    </div>
</div>
</x-app-layout>
