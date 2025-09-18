<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/preview_invitation.css') }}"?v={{ time() }}>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" 
                   class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 leading-tight">
                        Preview Undangan
                    </h2>
                    <p class="text-gray-600 text-sm sm:text-base">{{ $invitation->groom_name }} & {{ $invitation->bride_name }}</p>
                </div>
            </div>
            
            <!-- Desktop Actions -->
            <div class="hidden sm:flex items-center space-x-3">
                <a href="{{ route('user.export-pdf', $invitation->slug) }}" 
                   class="inline-flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export PDF</span>
                </a>
                <button onclick="shareInvitation()" 
                        class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                    <span>Share</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mobile Actions Bar -->
            <div class="sm:hidden mb-4">
                <div class="bg-white rounded-lg shadow-md p-3">
                    <div class="flex space-x-2">
                        <a href="{{ route('user.export-pdf', $invitation->slug) }}" 
                           class="flex-1 inline-flex items-center justify-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>PDF</span>
                        </a>
                        <button onclick="copyInvitationLink()" 
                                class="flex-1 inline-flex items-center justify-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Copy</span>
                        </button>
                        <button onclick="toggleMobileMenu()" 
                                class="flex-1 inline-flex items-center justify-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span>Share</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Share Menu (Hidden by default) -->
            <div id="mobile-share-menu" class="sm:hidden mb-4 hidden">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h5 class="font-semibold text-gray-800 mb-3">Bagikan Undangan</h5>
                    <div class="grid grid-cols-3 gap-2">
                        
                        <button onclick="shareToFacebook()" 
                                class="flex flex-col items-center space-y-1 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="text-xs">Facebook</span>
                        </button>
                        
                        <button onclick="shareToTwitter()" 
                                class="flex flex-col items-center space-y-1 bg-sky-500 hover:bg-sky-600 text-white p-3 rounded-lg transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                            <span class="text-xs">Twitter</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-4 lg:gap-8">
                <!-- Invitation Preview -->
                <div class="xl:col-span-3 order-2 xl:order-1">
                    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:rounded-2xl">
                        <!-- Preview Header -->
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-2 sm:space-y-0">
                                <h3 class="text-lg font-semibold text-gray-800">Preview Undangan</h3>
                                <div class="flex items-center space-x-2">
                                    <button onclick="toggleFullscreen()" 
                                            class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                                            title="Fullscreen">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                    </button>
                                    <button onclick="refreshPreview()" 
                                            class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                                            title="Refresh">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Preview Content -->
                        <div class="invitation-preview bg-gray-50 p-2 sm:p-4 lg:p-8">
                            <div id="invitation-frame" class="invitation-container">
                                {!! $compiledHtml !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar with Details and Actions -->
                <div class="xl:col-span-1 order-1 xl:order-2 space-y-4 sm:space-y-6">
                    <!-- Details Card -->
                    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:rounded-2xl">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-800">Detail Undangan</h4>
                        </div>
                        
                        <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Template</label>
                                <p class="text-gray-900 text-sm sm:text-base">{{ $invitation->template->name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Mempelai Pria</label>
                                <p class="text-gray-900 text-sm sm:text-base">{{ $invitation->groom_name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Mempelai Wanita</label>
                                <p class="text-gray-900 text-sm sm:text-base">{{ $invitation->bride_name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal</label>
                                <p class="text-gray-900 text-sm sm:text-base">{{ date('d F Y', strtotime($invitation->wedding_date)) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Waktu</label>
                                <p class="text-gray-900 text-sm sm:text-base">{{ $invitation->wedding_time }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Lokasi</label>
                                <p class="text-gray-900 text-xs sm:text-sm break-words">{{ $invitation->location }}</p>
                            </div>
                            
                            @if($invitation->additional_notes)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Catatan</label>
                                    <p class="text-gray-900 text-xs sm:text-sm break-words">{{ $invitation->additional_notes }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Dibuat</label>
                                <p class="text-gray-900 text-xs sm:text-sm">{{ $invitation->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Actions Card -->
                    <div class="hidden sm:block bg-white overflow-hidden shadow-xl rounded-lg sm:rounded-2xl">
                        <div class="p-4 sm:p-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Aksi</h4>
                            
                            <div class="space-y-3">
                                <a href="{{ route('user.export-pdf', $invitation->slug) }}" 
                                   class="w-full inline-flex items-center justify-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Download PDF</span>
                                </a>
                                
                                <a href="{{ route('user.history') }}" 
                                   class="w-full inline-flex items-center justify-center space-x-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span>Kembali ke History</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Share Card -->
                    <div class="hidden sm:block bg-white overflow-hidden shadow-xl rounded-lg sm:rounded-2xl">
                        <div class="p-4 sm:p-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Bagikan</h4>
                            
                            <div class="space-y-2">
                                
                                <button onclick="shareToFacebook()" 
                                        class="w-full inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span>Facebook</span>
                                </button>
                                
                                <button onclick="shareToTwitter()" 
                                        class="w-full inline-flex items-center justify-center space-x-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    <span>Twitter</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
      
    </style>

    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-share-menu');
            menu.classList.toggle('hidden');
        }
        
        // Fullscreen functionality
        function toggleFullscreen() {
            const element = document.getElementById('invitation-frame');
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
            }
        }
        
        // Refresh preview
        function refreshPreview() {
            location.reload();
        }
        
        // Copy invitation link with enhanced error handling
        function copyInvitationLink() {
            const url = window.location.href;
            
            // Modern clipboard API
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('Link berhasil disalin!', 'success');
                }).catch(function(err) {
                    console.error('Clipboard API failed: ', err);
                    fallbackCopyToClipboard(url);
                });
            } else {
                // Fallback method
                fallbackCopyToClipboard(url);
            }
        }
        
        // Fallback copy method for older browsers
        function fallbackCopyToClipboard(text) {
            try {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                
                if (successful) {
                    showToast('Link berhasil disalin!', 'success');
                } else {
                    showToast('Gagal menyalin link. Silakan salin manual.', 'error');
                }
            } catch (err) {
                console.error('Fallback copy failed: ', err);
                showToast('Gagal menyalin link. Silakan salin manual.', 'error');
            }
        }
        
        // Native share API with fallback
        function shareInvitation() {
            const shareData = {
                title: 'Undangan Pernikahan {{ $invitation->groom_name }} & {{ $invitation->bride_name }}',
                text: 'Anda diundang ke pernikahan kami!',
                url: window.location.href
            };
            
            if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
                navigator.share(shareData).catch(function(err) {
                    console.error('Share failed: ', err);
                    copyInvitationLink();
                });
            } else {
                copyInvitationLink();
            }
        }
        
        // Social media sharing functions
        function shareToWhatsApp() {
            const text = encodeURIComponent(`Undangan Pernikahan {{ $invitation->groom_name }} & {{ $invitation->bride_name }}

Tanggal: {{ date('d F Y', strtotime($invitation->wedding_date)) }}
Waktu: {{ $invitation->wedding_time }}
Lokasi: {{ $invitation->location }}

Lihat undangan lengkap: ${window.location.href}`);
            
            // Detect mobile device
            const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const whatsappURL = isMobile ? 
                `whatsapp://send?text=${text}` : 
                `https://wa.me/?text=${text}`;
            
            window.open(whatsappURL, '_blank');
            toggleMobileMenu(); // Close mobile menu if open
        }
        
        function shareToFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
            toggleMobileMenu();
        }
        
        function shareToTwitter() {
            const text = encodeURIComponent(`Undangan Pernikahan {{ $invitation->groom_name }} & {{ $invitation->bride_name }}`);
            const url = encodeURIComponent(window.location.href);
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
            toggleMobileMenu();
        }
        
        // Enhanced toast notification system
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            
            // Update message
            toastMessage.textContent = message;
            
            // Update styling based on type
            toast.className = `fixed top-4 right-4 px-4 sm:px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 z-50 max-w-sm`;
            
            if (type === 'success') {
                toast.classList.add('bg-green-500', 'text-white');
            } else if (type === 'error') {
                toast.classList.add('bg-red-500', 'text-white');
            } else {
                toast.classList.add('bg-blue-500', 'text-white');
            }
            
            // Show toast
            toast.classList.remove('translate-x-full');
            
            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 3000);
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-share-menu');
            const shareButton = event.target.closest('button[onclick="toggleMobileMenu()"]');
            
            if (!mobileMenu.contains(event.target) && !shareButton && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
            }
        });
        
        // Handle window resize for responsive adjustments
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Optional: Add any resize-specific logic here
                console.log('Window resized to:', window.innerWidth, 'x', window.innerHeight);
            }, 250);
        });
        
        // Show success message if exists
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('success') }}', 'success');
            });
        @endif
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // F11 for fullscreen
            if (e.key === 'F11') {
                e.preventDefault();
                toggleFullscreen();
            }
            
            // Ctrl/Cmd + C for copy link
            if ((e.ctrlKey || e.metaKey) && e.key === 'c' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                copyInvitationLink();
            }
            
            // Escape to close mobile menu
            if (e.key === 'Escape') {
                const mobileMenu = document.getElementById('mobile-share-menu');
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Preview invitation page loaded');
            
            // Add loading state management if needed
            const invitationFrame = document.getElementById('invitation-frame');
            if (invitationFrame) {
                invitationFrame.addEventListener('load', function() {
                    console.log('Invitation content loaded');
                });
            }
        });
    </script>
</x-app-layout>