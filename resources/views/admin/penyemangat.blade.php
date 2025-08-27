<x-app-layout>
    <script src="{{ asset('js/penyemagat.js') }}"></script>

     <x-slot name="title"> Penyemangat </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-100 via-purple-50 to-indigo-100 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-6xl font-bold bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 bg-clip-text text-transparent mb-4">
                    がんばって！ Admin-san! 💪
                </h1>
                <p class="text-xl text-gray-700 mb-2">Waguri Karauko is here to cheer you on!</p>
                <p class="text-lg text-gray-600">Every great admin needs a moment to recharge ✨</p>
            </div>

            <!-- Motivational Messages -->
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-pink-200">
                    <div class="text-center">
                        <div class="text-4xl mb-3">🌟</div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">You're Amazing!</h3>
                        <p class="text-gray-600">Your hard work keeps everything running smoothly!</p>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-purple-200">
                    <div class="text-center">
                        <div class="text-4xl mb-3">💝</div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Take a Break!</h3>
                        <p class="text-gray-600">Even the best admins need time to rest and recharge!</p>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-indigo-200">
                    <div class="text-center">
                        <div class="text-4xl mb-3">🎯</div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">You Got This!</h3>
                        <p class="text-gray-600">Every challenge is just another opportunity to shine!</p>
                    </div>
                </div>
            </div>

            <!-- Waguri Karauko Gallery -->
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl p-8 shadow-xl border border-pink-100 mb-8">
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
                    Waguri Karauko's Motivation Gallery 📸
                </h2>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <img src="https://i.pinimg.com/736x/98/a7/c9/98a7c9594c361918acef3e4168aa162b.jpg" 
                             alt="Waguri Karauko - Cheerful" 
                             class="w-full h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-4 left-4 text-white">
                                <p class="text-lg font-semibold">Keep Smiling! 😊</p>
                                <p class="text-sm">Your positive energy is contagious!</p>
                            </div>
                        </div>
                    </div>

                    <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <img src="https://i.pinimg.com/736x/86/4c/cf/864ccfa4402eb9b2542c73064eed01ea.jpg" 
                             alt="Waguri Karauko - Supportive" 
                             class="w-full h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-4 left-4 text-white">
                                <p class="text-lg font-semibold">Stay Strong! 💪</p>
                                <p class="text-sm">You're doing an incredible job!</p>
                            </div>
                        </div>
                    </div>

                    <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                        <img src="https://i.pinimg.com/736x/a2/f6/94/a2f694c10cc0294b62d136e1c54a7731.jpg" 
                             alt="Waguri Karauko - Encouraging" 
                             class="w-full h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-4 left-4 text-white">
                                <p class="text-lg font-semibold">Never Give Up! ✨</p>
                                <p class="text-sm">Tomorrow is a new opportunity!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Affirmations -->
            <div class="bg-gradient-to-r from-pink-500 to-purple-600 rounded-3xl p-8 text-white text-center shadow-xl">
                <h3 class="text-2xl font-bold mb-4">Daily Admin Affirmations 🌸</h3>
                <div class="grid md:grid-cols-2 gap-6 text-lg">
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <p>"I am capable of handling any challenge that comes my way"</p>
                    </div>
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <p>"My work makes a positive difference every day"</p>
                    </div>
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <p>"I deserve rest and appreciation for my efforts"</p>
                    </div>
                    <div class="bg-white/20 rounded-xl p-4 backdrop-blur-sm">
                        <p>"I am growing stronger with every experience"</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-12">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="showRandomMotivation()" 
                            class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-8 py-3 rounded-full font-semibold hover:from-pink-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        Get Random Motivation! 🎲
                    </button>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="bg-gradient-to-r from-indigo-500 to-blue-600 text-white px-8 py-3 rounded-full font-semibold hover:from-indigo-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        Back to Dashboard 🏠
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Random Motivation Modal -->
    <div id="motivationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-3xl p-8 max-w-md mx-4 text-center shadow-2xl transform scale-95 transition-transform duration-300">
            <div class="text-6xl mb-4">🌟</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Special Message!</h3>
            <p id="randomMessage" class="text-lg text-gray-600 mb-6"></p>
            <button onclick="closeModal()" 
                    class="bg-gradient-to-r from-pink-500 to-purple-600 text-white px-6 py-2 rounded-full font-semibold hover:from-pink-600 hover:to-purple-700 transition-all duration-300">
                Arigatou! ✨
            </button>
        </div>
    </div>
</x-app-layout>