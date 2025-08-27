  document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                const hamburgerIcon = document.getElementById('hamburger-icon');
                const closeIcon = document.getElementById('close-icon');

                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', function() {
                        const isMenuOpen = !mobileMenu.classList.contains('pointer-events-none');
                        
                        if (isMenuOpen) {
                            closeMenu();
                        } else {
                            openMenu();
                        }
                    });

                    function openMenu() {
                        // Show menu with animation
                        mobileMenu.classList.remove('pointer-events-none', 'opacity-0', 'scale-95');
                        mobileMenu.classList.add('pointer-events-auto', 'opacity-100', 'scale-100');
                        
                        // Animate hamburger to close icon
                        hamburgerIcon.classList.add('opacity-0', 'scale-0', 'rotate-90');
                        closeIcon.classList.remove('opacity-0', 'scale-0');
                        closeIcon.classList.add('opacity-100', 'scale-100');
                        
                        // Animate menu items with stagger
                        const menuItems = mobileMenu.querySelectorAll('a');
                        menuItems.forEach((item, index) => {
                            setTimeout(() => {
                                item.classList.remove('translate-y-2', 'opacity-0');
                                item.classList.add('translate-y-0', 'opacity-100');
                            }, index * 100);
                        });
                    }

                    function closeMenu() {
                        // Hide menu items first
                        const menuItems = mobileMenu.querySelectorAll('a');
                        menuItems.forEach((item) => {
                            item.classList.add('translate-y-2', 'opacity-0');
                            item.classList.remove('translate-y-0', 'opacity-100');
                        });
                        
                        // Then hide menu with delay
                        setTimeout(() => {
                            mobileMenu.classList.add('pointer-events-none', 'opacity-0', 'scale-95');
                            mobileMenu.classList.remove('pointer-events-auto', 'opacity-100', 'scale-100');
                        }, 150);
                        
                        // Animate close to hamburger icon
                        closeIcon.classList.add('opacity-0', 'scale-0');
                        closeIcon.classList.remove('opacity-100', 'scale-100');
                        hamburgerIcon.classList.remove('opacity-0', 'scale-0', 'rotate-90');
                        hamburgerIcon.classList.add('opacity-100', 'scale-100');
                    }

                    // Close menu when clicking outside
                    document.addEventListener('click', function(event) {
                        const isMenuOpen = !mobileMenu.classList.contains('pointer-events-none');
                        if (isMenuOpen && !mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                            closeMenu();
                        }
                    });

                    // Close menu when clicking on menu links
                    const menuLinks = mobileMenu.querySelectorAll('a');
                    menuLinks.forEach(link => {
                        link.addEventListener('click', function() {
                            closeMenu();
                        });
                    });
                }
            });