<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>UndanginAja</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script src="{{ asset('js/welcome.js') }}"></script>
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('build/assets/logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('build/assets/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('build/assets/logo.png') }}">
        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
         
        @endif
    </head>
   <body class="min-h-screen bg-gradient-wedding dark:bg-gradient-dark text-neutral-900 dark:text-white overflow-x-hidden">
        
        <!-- Floating Elements -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-20 left-10 w-20 h-20 bg-pink-300 rounded-full opacity-20 animate-float"></div>
            <div class="absolute top-40 right-20 w-16 h-16 bg-rose-300 rounded-full opacity-25 animate-float-delayed"></div>
            <div class="absolute bottom-32 left-1/4 w-12 h-12 bg-pink-400 rounded-full opacity-15 animate-float"></div>
            <div class="absolute bottom-20 right-1/3 w-24 h-24 bg-rose-200 rounded-full opacity-20 animate-float-delayed"></div>
        </div>

  <header class="relative z-20">
            @if (Route::has('login'))
                <nav class="flex items-center justify-between p-6 lg:px-12">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <img src="{{asset('build/assets/logo.png')}}" alt="" class="w-14 h-14 rounded-lg">
                    </div>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        @auth
                            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" 
                               class="glass-effect text-neutral-700 hover:text-pink-500 transition-colors px-6 py-2 rounded-full text-sm font-medium hover:bg-white/20 transition-all">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="text-neutral-700 hover:text-pink-500 transition-colors px-4 py-2 text-sm font-medium">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="btn-wedding text-neutral-700 hover:text-pink-500 px-6 py-2 rounded-full text-sm font-medium">
                                    Daftar Gratis
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Mobile Hamburger Button -->
                    <button id="mobile-menu-button" class="md:hidden text-pink-200 glass-effect p-2 rounded-lg hover:bg-white/20 transition-all">
                        <svg id="hamburger-icon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="close-icon" class="w-6 h-6 absolute inset-0 m-auto opacity-0 scale-0 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Mobile Menu -->
                    <div id="mobile-menu" class="md:hidden  bg-pink-200 absolute top-full left-0 right-0 glass-effect border border-white/20 rounded-2xl  mt-2 overflow-hidden opacity-0 scale-95 transform origin-top transition-all duration-300 ease-out pointer-events-none">
                        <div class="flex flex-col py-4">
                            @auth
                                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" 
                                   class="px-6 py-3 text-neutral-700 text-sm font-medium hover:bg-white/20 transition-all transform translate-y-2 opacity-0">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="px-6 py-3 text-neutral-700 text-sm font-medium text-center hover:bg-white/20 hover:text-pink-500 transition-all transform translate-y-2 opacity-0">
                                    Masuk
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" 
                                       class="mx-4 my-2 btn-wedding text-neutral-700 text-center px-6 py-3 rounded-full text-sm font-medium hover:text-pink-500 transition-all transform translate-y-2 opacity-0">
                                        Daftar Gratis
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </nav>
            @endif
        </header>


        <!-- Hero Section -->
        <main class="relative z-10">
            <section class="text-center px-6 py-12 lg:py-20">
                <div class="max-w-4xl mx-auto">
                    <!-- Main Heading -->
                    <h1 class="font-script text-4xl lg:text-7xl font-bold text-shadow mb-6">
                        <span class="bg-gradient-to-r from-pink-600 via-rose-500 to-orange-400 bg-clip-text text-transparent">
                            Undangan Pernikahan
                        </span>
                        <br>
                        <span class="font-script text-pink-500 dark:text-pink-400">
                            Digital Terbaik
                        </span>
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-lg lg:text-xl text-neutral-700 dark:text-neutral-500 mb-8 max-w-2xl mx-auto leading-relaxed">
                        Buat undangan pernikahan digital yang memukau dengan template-template cantik kami. 
                        Mudah dikustomisasi, hemat waktu, dan ramah lingkungan.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="btn-wedding text-pink-400 px-8 py-4 rounded-full text-lg font-semibold w-full sm:w-auto">
                                🎉 Mulai Buat Undangan
                            </a>
                        @endif
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 max-w-3xl mx-auto">
                        <div class="text-center">
                            <div class="font-bold text-2xl lg:text-3xl text-pink-500">500+</div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">Template Cantik</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-2xl lg:text-3xl text-rose-500">10K+</div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">Pasangan Bahagia</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-2xl lg:text-3xl text-orange-500">100%</div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">Mudah Digunakan</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-2xl lg:text-3xl text-pink-500">24/7</div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">Customer Support</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="px-6 py-16" id="features">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="font-script text-3xl lg:text-5xl font-bold mb-4 text-shadow">
                            <span class="bg-gradient-to-r from-pink-500 to-rose-500 bg-clip-text text-transparent">
                                Mengapa Memilih Kami?
                            </span>
                        </h2>
                        <p class="text-lg text-neutral-600 dark:text-neutral-400 max-w-2xl mx-auto">
                            Platform terlengkap untuk membuat undangan pernikahan digital yang tak terlupakan
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="feature-card glass-effect p-8 rounded-2xl text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl">🎨</span>
                            </div>
                            <h3 class="font-serif text-purple-300 text-xl font-semibold mb-4">Design Premium</h3>
                            <p class="text-neutral-600 dark:text-neutral-400">
                                Template-template eksklusif yang dirancang khusus oleh desainer profesional
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="feature-card glass-effect p-8 rounded-2xl text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-rose-400 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl">⚡</span>
                            </div>
                            <h3 class="font-serif  text-purple-300 text-xl font-semibold mb-4">Super Mudah</h3>
                            <p class="text-neutral-600 dark:text-neutral-400">
                                Buat undangan cantik hanya dalam 5 menit tanpa perlu skill desain
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="feature-card glass-effect p-8 rounded-2xl text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl">📱</span>
                            </div>
                            <h3 class="font-serif  text-purple-300 text-xl font-semibold mb-4">Mobile Friendly</h3>
                            <p class="text-neutral-600 dark:text-neutral-400">
                                Undangan tampil sempurna di semua perangkat, smartphone hingga desktop
                            </p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="feature-card glass-effect p-8 rounded-2xl text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl">🌍</span>
                            </div>
                            <h3 class="font-serif  text-purple-300 text-xl font-semibold mb-4">Ramah Lingkungan</h3>
                            <p class="text-neutral-600 dark:text-neutral-400">
                                Hemat kertas, hemat biaya, dan bantu jaga kelestarian lingkungan
                            </p>
                        </div>

                        <!-- Feature 5 -->
                        <div class="feature-card glass-effect p-8 rounded-2xl text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl">🎵</span>
                            </div>
                            <h3 class="font-serif  text-purple-300 text-xl font-semibold mb-4">Fitur Lengkap</h3>
                            <p class="text-neutral-600 dark:text-neutral-400">
                                RSVP online, musik background, galeri foto, dan masih banyak lagi
                            </p>
                        </div>

                        <!-- Feature 6 -->
                        <div class="feature-card glass-effect p-8 rounded-2xl text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl">💝</span>
                            </div>
                            <h3 class="font-serif  text-purple-300 text-xl font-semibold mb-4">Harga Terjangkau</h3>
                            <p class="text-neutral-600 dark:text-neutral-400">
                                Mulai dari gratis! Paket premium dengan harga yang sangat terjangkau
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="px-6 py-16" id="templates">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="glass-effect p-12 rounded-3xl">
                        <h2 class="font-script text-4xl lg:text-6xl font-bold text-pink-500 mb-6">
                            Siap Membuat Undangan Impian?
                        </h2>
                        <p class="text-lg text-neutral-700 dark:text-neutral-400 mb-8 max-w-2xl mx-auto">
                            Bergabunglah dengan ribuan pasangan yang telah mempercayai kami untuk hari bahagia mereka
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="btn-wedding text-pink-600 px-10 py-4 rounded-full text-lg font-semibold">
                                    🚀 Mulai Sekarang - Gratis!
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" 
                                   class="glass-effect text-pink-600 px-10 py-4 rounded-full text-lg font-semibold hover:bg-white/20 transition-all">
                                    Sudah Punya Akun? Masuk
                                </a>
                            @endif
                        </div>
                        <p class="text-sm text-neutral-950 dark:text-neutral-400 mt-4">
                            ✨ Tanpa biaya tersembunyi • ⭐ Dukungan 24/7 • 🎯 Template unlimited
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 text-center py-8 px-6 border-t border-white/20">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <div class="w-6 h-6 bg-gradient-to-br from-pink-400 to-rose-500 rounded-md flex items-center justify-center">
                        <span class="text-white font-bold text-xs">U</span>
                    </div>
                    <span class="font-script text-xl font-semibold bg-gradient-to-r from-pink-500 to-rose-500 bg-clip-text text-transparent">
                        UndanginAja
                    </span>
                </div>
                <p class="text-neutral-600 dark:text-neutral-400 text-sm">
                    © 2025 UndanginAja. Semua hak cipta dilindungi. Dibuat dengan ❤️ untuk pasangan bahagia di Indonesia.
                </p>
            </div>
        </footer>
    </body>
</html>
