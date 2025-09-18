<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-8xl bg-gradient-to-bl from-fuchsia-200 to-pink-200 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex ">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="UndanginAja Logo" class="w-14 h-14 rounded-lg">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('dashboard')" class="text-neutral-800">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->role === 'admin')
                    <x-nav-link :href="route('admin.broadcasts.index')" :active="request()->routeIs('admin.broadcasts.index')" class="text-neutral-800">
                        {{ __('broatcast') }}
                    </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'admin')
                         <x-nav-link :href="route('templates.index')" :active="request()->routeIs('dashboard')" class="text-neutral-800">
                        {{ __('Template') }}
                    </x-nav-link>
                    @endif

                      @if(auth()->user()->role === 'user')
                     <x-nav-link :href="route('user.history')" :active="request()->routeIs('user.history')" class="text-neutral-800">
                        {{ __('history') }}
                    </x-nav-link>
                  @endif

                  

                   @if(auth()->user()->role === 'admin')
                         <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')" class="text-neutral-800">
                        {{ __('User Control') }}
                    </x-nav-link>
                    @endif
                   @if(auth()->user()->role === 'admin')
                         <x-nav-link :href="route('admin.analytics')" :active="request()->routeIs('admin.analystic')" class="text-neutral-800">
                        {{ __('Analystic') }}
                    </x-nav-link>
                    @endif

                   @if(auth()->user()->role === 'admin')
                    <x-nav-link :href="route('admin.chat.index')" :active="request()->routeIs('admin.chat.*')" class="text-neutral-800 relative">
                        {{ __('Chat Management') }}
                        <span id="adminChatNotificationBadge" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <span id="adminChatNotificationCount">0</span>
                        </span>
                    </x-nav-link>

                         {{-- <x-nav-link :href="route('admin.penyemangat')" :active="request()->routeIs('admin.penyemangat')" class="text-neutral-800">
                        {{ __('Penyemangat') }}
                    </x-nav-link> --}}
                        
                    @endif

                    @if(auth()->user()->role === 'user')
                         <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')" class="text-neutral-800 relative">
                        {{ __('Chat') }}
                        <span id="chatNotificationBadge" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <span id="chatNotificationCount">0</span>
                        </span>
                    </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'user')
                     <x-nav-link :href="route('user.gallery.index')" :active="request()->routeIs('user.gallery.index')" class="text-neutral-800">
                        {{ __('gallery') }}
                    </x-nav-link>
                  @endif

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 bg-white/20 backdrop-blur-md border-2 border-white text-sm leading-4 font-medium rounded-md text-neutral-800 hover:focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500  focus:outline-none  focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('dashboard')" class="text-neutral-800">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
             @if(auth()->user()->role === 'admin')
             <x-responsive-nav-link :href="route('templates.index')" :active="request()->routeIs('dashboard')" class="text-neutral-800">
                        {{ __('Template') }}
            </x-responsive-nav-link>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')" class="text-neutral-800">
            {{ __('User Control') }}
            </x-responsive-nav-link>
        </div>
        
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.analytics')" :active="request()->routeIs('admin.analytics')" class="text-neutral-800">
            {{ __('Analytics') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.penyemangat')" :active="request()->routeIs('admin.penyemangat')" class="text-neutral-800">
            {{ __('Penyemagat') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.broadcasts.index')" :active="request()->routeIs('admin.broadcasts.index')" class="text-neutral-800">
            {{ __('Broadcasts') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.chat.index')" :active="request()->routeIs('admin.chat.*')" class="text-neutral-800 relative">
            {{ __('Chat Management') }}
            <span id="mobileAdminChatNotificationBadge" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                <span id="mobileAdminChatNotificationCount">0</span>
            </span>
            </x-responsive-nav-link>
        </div>
            @endif

            @if(auth()->user()->role === 'user')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')" class="text-neutral-800 relative">
            {{ __('Chat') }}
            <span id="mobileChatNotificationBadge" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                <span id="mobileChatNotificationCount">0</span>
            </span>
            </x-responsive-nav-link>
        </div>
            @endif

                <div class="pt-2 pb-3 space-y-1">
@auth
    @if(auth()->user()->role === 'user')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link 
                :href="route('user.history')" :active="request()->routeIs('user.history')" class="text-neutral-800">
                {{ __('History') }}
            </x-responsive-nav-link>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link 
                :href="route('user.gallery.index')" :active="request()->routeIs('user.gallery.index')" class="text-neutral-800">
                {{ __('Galley') }}
            </x-responsive-nav-link>
        </div>
    @endif
@endauth
        </div>

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Chat Notification Script -->
<script>
    // Function to update notification badges
    function updateChatNotifications() {
        fetch('/chat/api/unread-count')
            .then(response => response.json())
            .then(data => {
                const count = data.count;
                
                // Update desktop badges
                const chatBadge = document.getElementById('chatNotificationBadge');
                const adminChatBadge = document.getElementById('adminChatNotificationBadge');
                const chatCount = document.getElementById('chatNotificationCount');
                const adminChatCount = document.getElementById('adminChatNotificationCount');
                
                // Update mobile badges
                const mobileChatBadge = document.getElementById('mobileChatNotificationBadge');
                const mobileAdminChatBadge = document.getElementById('mobileAdminChatNotificationBadge');
                const mobileChatCount = document.getElementById('mobileChatNotificationCount');
                const mobileAdminChatCount = document.getElementById('mobileAdminChatNotificationCount');
                
                if (count > 0) {
                    // Show badges for appropriate user role
                    @if(auth()->check())
                        @if(auth()->user()->role === 'user')
                            if (chatBadge) {
                                chatBadge.classList.remove('hidden');
                                chatCount.textContent = count;
                            }
                            if (mobileChatBadge) {
                                mobileChatBadge.classList.remove('hidden');
                                mobileChatCount.textContent = count;
                            }
                        @else
                            if (adminChatBadge) {
                                adminChatBadge.classList.remove('hidden');
                                adminChatCount.textContent = count;
                            }
                            if (mobileAdminChatBadge) {
                                mobileAdminChatBadge.classList.remove('hidden');
                                mobileAdminChatCount.textContent = count;
                            }
                        @endif
                    @endif
                } else {
                    // Hide all badges when no unread messages
                    [chatBadge, adminChatBadge, mobileChatBadge, mobileAdminChatBadge].forEach(badge => {
                        if (badge) badge.classList.add('hidden');
                    });
                }
            })
            .catch(error => console.error('Error fetching unread count:', error));
    }

    // Update notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if(auth()->check())
            updateChatNotifications();
            
            // Update every 10 seconds
            setInterval(updateChatNotifications, 10000);
        @endif
    });
</script>
