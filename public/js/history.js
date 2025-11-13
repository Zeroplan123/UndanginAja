  function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('border-pink-500', 'text-pink-600');
                tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            activeTab.classList.add('border-pink-500', 'text-pink-600');
        }

        // Copy invitation link function
        function copyInvitationLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                // Simple notification
                alert('Link undangan berhasil disalin!');
            }).catch(function(err) {
                console.error('Gagal menyalin link: ', err);
                // Fallback method
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Link undangan berhasil disalin!');
            });
        }

        // Toggle PDF export menu
        function togglePdfMenu(invitationId) {
            const menu = document.getElementById('pdf-menu-' + invitationId);
            const allMenus = document.querySelectorAll('[id^="pdf-menu-"]');
            
            // Close all other menus
            allMenus.forEach(otherMenu => {
                if (otherMenu.id !== 'pdf-menu-' + invitationId) {
                    otherMenu.classList.add('hidden');
                }
            });
            
            // Toggle current menu
            menu.classList.toggle('hidden');
        }

        // Close PDF menus when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick^="togglePdfMenu"]') && !event.target.closest('[id^="pdf-menu-"]')) {
                document.querySelectorAll('[id^="pdf-menu-"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Initialize default tab
        document.addEventListener('DOMContentLoaded', function() {
            showTab('invitations');
        });