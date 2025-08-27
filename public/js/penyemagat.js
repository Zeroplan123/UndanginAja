  const motivationalMessages = [
            "Waguri Karauko believes in you! You're the best admin ever! 🌟",
            "Take a deep breath, you're doing amazing work! 💝",
            "Even on tough days, remember that you're appreciated! 🌸",
            "Your dedication doesn't go unnoticed - you're a star! ⭐",
            "Waguri Karauko says: 'Ganbatte, Admin-san!' 💪",
            "You make the impossible look easy every day! 🎯",
            "Remember to smile - you brighten everyone's day! 😊",
            "You're not just an admin, you're a superhero! 🦸‍♀️",
            "Waguri Karauko is proud of all your hard work! 🏆",
            "Good luck ngoding nyaa ya sayanggkuuuu, Fokus aja ngodingnya, Jangan pake Ai yaaa oceyyy, Kamu otu udah palingg terbaikkkk, Percaya dirii ajaa ya sayangg<3, Semogaaa sayangku bisa Selesai sebelum deadline yahhhh sayangg🩷 Inget abis ngoding istirahat yaa, Jangan sampai lupa waktu oceyy!!! Terusss bbonyaa mimpiin aku deh hehee🤭🩷 Love you sayanggggg🥰 papaaaiii😚🩷"
        ];

        function showRandomMotivation() {
            const modal = document.getElementById('motivationModal');
            const messageElement = document.getElementById('randomMessage');
            const randomMessage = motivationalMessages[Math.floor(Math.random() * motivationalMessages.length)];
            
            messageElement.textContent = randomMessage;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Add animation
            setTimeout(() => {
                modal.querySelector('.bg-white').classList.add('scale-100');
                modal.querySelector('.bg-white').classList.remove('scale-95');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('motivationModal');
            modal.querySelector('.bg-white').classList.add('scale-95');
            modal.querySelector('.bg-white').classList.remove('scale-100');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('motivationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Add some sparkle effects
        function createSparkle() {
            const sparkle = document.createElement('div');
            sparkle.innerHTML = '✨';
            sparkle.style.position = 'fixed';
            sparkle.style.left = Math.random() * window.innerWidth + 'px';
            sparkle.style.top = Math.random() * window.innerHeight + 'px';
            sparkle.style.fontSize = '20px';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.zIndex = '1000';
            sparkle.style.animation = 'sparkleFloat 3s ease-out forwards';
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 3000);
        }

        // Add sparkles periodically
        setInterval(createSparkle, 2000);

        // Add CSS animation for sparkles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sparkleFloat {
                0% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
                100% {
                    opacity: 0;
                    transform: translateY(-100px) scale(0.5);
                }
            }
        `;
        document.head.appendChild(style);